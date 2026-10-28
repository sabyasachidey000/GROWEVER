<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\ProductionRun;
use App\Models\ProductionApproval;

class ProcessShiftProductionReport extends Command
{
    protected $signature = 'production:shift-report';

    protected $description = 'Process completed shift production report';

    public function handle()
    {
        $now = Carbon::now('Asia/Kolkata');

        /*
         * 08:05 AM -> Process previous Shift B
         * 08:00 PM -> Process previous Shift A
         */
        if ($now->hour == 8) {

            $shiftCode = 'B';

            $shiftEnd = $now->copy()->startOfDay()
                ->setTime(8, 0, 0);

            $shiftStart = $shiftEnd->copy()
                ->subDay()
                ->setTime(20, 0, 0);

        } elseif ($now->hour == 20) {

            $shiftCode = 'A';

            $shiftStart = $now->copy()->startOfDay()
                ->setTime(8, 0, 0);

            $shiftEnd = $now->copy()->startOfDay()
                ->setTime(20, 0, 0);

        } else {
            $this->error('Run this command at 08:00 or 20:00 hour.');
            return self::FAILURE;
        }

        $shift = DB::table('shifts')
            ->where('code', $shiftCode)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$shift) {
            $this->error("Shift {$shiftCode} not found.");
            return self::FAILURE;
        }

        DB::beginTransaction();

        try {

            /*
             * Read logs within the completed shift.
             * Do not use created_at::date = CURRENT_DATE.
             */
            $logs = DB::table('rfid_limit_switch_logs as l')
                ->join('machines as m', 'm.rfid_device_id', '=', 'l.device_id')
                ->where('l.created_at', '>=', $shiftStart->toDateTimeString())
                ->where('l.created_at', '<', $shiftEnd->toDateTimeString())
                ->select(
                    'l.user_id',
                    'l.device_id',
                    'm.id as machine_id',
                    'm.cycle_count'
                )
                ->selectRaw('COUNT(l.id) AS total')
                ->selectRaw('MIN(l.created_at) AS first_punch')
                ->selectRaw('MAX(l.created_at) AS last_punch')
                ->groupBy(
                    'l.user_id',
                    'l.device_id',
                    'm.id',
                    'm.cycle_count'
                )
                ->get();

            foreach ($logs as $row) {

                $startTime = Carbon::parse($row->first_punch);
                $endTime = Carbon::parse($row->last_punch);

                $runtimeMinutes = $startTime->diffInMinutes($endTime);

                $productionData = [
                    'machine_id'          => $row->machine_id,
                    'operator_id'         => $row->user_id,
                    'product_id'          => 1,
                    'shift_id'            => $shift->id,
                    'operator_session_id' => null,
                    'start_time'          => $startTime,
                    'weight'              => (int) $row->total
                                             * (int) $row->cycle_count,
                    'remarks'             => null,
                    'status'              => 'completed',
                    'runtime_minutes'     => $runtimeMinutes,
                ];

                /*
                 * Idempotency:
                 * Re-running the same shift should update
                 * its existing production run, not duplicate it.
                 */
                $productionRun = ProductionRun::where(
                        'machine_id',
                        $row->machine_id
                    )
                    ->where('operator_id', $row->user_id)
                    ->where('shift_id', $shift->id)
                    ->where('start_time', $startTime)
                    ->first();

                if ($productionRun) {
                    $productionRun->update($productionData);
                } else {
                    $productionRun = ProductionRun::create($productionData);
                }

                $approval = ProductionApproval::where(
                    'production_run_id',
                    $productionRun->id
                )->first();

                if (!$approval) {
                    ProductionApproval::create([
                        'production_run_id' => $productionRun->id,
                        'submitted_by' => $row->user_id,
                        'submitted_at' => $now,
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            Log::info('Shift production report completed', [
                'shift' => $shiftCode,
                'shift_start' => $shiftStart->toDateTimeString(),
                'shift_end' => $shiftEnd->toDateTimeString(),
                'rows_processed' => $logs->count(),
            ]);

            $this->info("Shift {$shiftCode} report processed successfully.");

            return self::SUCCESS;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Shift production report failed', [
                'message' => $e->getMessage(),
                'shift' => $shiftCode,
            ]);

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}