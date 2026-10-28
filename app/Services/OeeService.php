<?php

namespace App\Services;

use App\Models\OeeRecord;
use App\Models\ProductionRun;
use App\Models\Machine;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OeeService
{
    public function calculateOee(int $machineId, $date, int $shiftId): OeeRecord
    {
        $runs = ProductionRun::where('machine_id', $machineId)
            ->whereDate('start_time', $date)
            ->where('shift_id', $shiftId)
            ->where('status', 'completed')
            ->get();

        $shift = Shift::find($shiftId);
        $machine = Machine::find($machineId);

        $plannedTime = $shift?->getDurationInMinutes() ?? 480;
        $runtime = $runs->sum('runtime_minutes');
        $totalWeight = $runs->whereNotNull('weight')->sum('weight');
        $actualOutput = $totalWeight;

        // Ideal output based on machine capacity
        $idealRate = $machine?->production_capacity_per_hour ?? 0;
        $idealOutput = $idealRate > 0 ? round(($runtime / 60) * $idealRate, 0) : $totalWeight;

        $availability = $plannedTime > 0 ? round($runtime / $plannedTime, 4) : 0;
        $performance = $idealOutput > 0 ? round($actualOutput / $idealOutput, 4) : 0;
        $quality = 1.0;
        $oee = round($availability * $performance * $quality, 4);

        $record = OeeRecord::updateOrCreate(
            [
                'machine_id' => $machineId,
                'shift_id' => $shiftId,
                'record_date' => $date,
            ],
            [
                'planned_production_time' => $plannedTime,
                'runtime' => $runtime,
                'actual_output' => $actualOutput,
                'ideal_output' => $idealOutput,
                'total_quantity' => $totalWeight,
                'good_quantity' => $totalWeight,
                'availability' => $availability,
                'performance' => $performance,
                'quality_rate' => $quality,
                'oee' => $oee,
            ]
        );

        return $record;
    }

    public function calculateDailyOee($date = null): void
    {
        $date = $date ?? today();

        $machines = Machine::active()->get();
        $shifts = Shift::active()->get();

        foreach ($machines as $machine) {
            foreach ($shifts as $shift) {
                $this->calculateOee($machine->id, $date, $shift->id);
            }
        }
    }

    public function getMachineOeeHistory(int $machineId, int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return OeeRecord::where('machine_id', $machineId)
            ->where('record_date', '>=', Carbon::today()->subDays($days))
            ->orderBy('record_date')
            ->get();
    }

    public function getOeeSummary(?int $machineId = null, $startDate = null, $endDate = null): array
    {
        $query = OeeRecord::query();

        if ($machineId) {
            $query->where('machine_id', $machineId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('record_date', [$startDate, $endDate]);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            return ['availability' => 0, 'performance' => 0, 'quality' => 0, 'oee' => 0];
        }

        return [
            'availability' => round($records->avg(fn($r) => $r->getAvailability()), 2),
            'performance' => round($records->avg(fn($r) => $r->getPerformance()), 2),
            'quality' => round($records->avg(fn($r) => $r->getQuality()), 2),
            'oee' => round($records->avg(fn($r) => $r->getOverallOee()), 2),
        ];
    }

    public function getOeeComparison($startDate, $endDate): array
    {
        return $this->getOeeSummary(null, $startDate, $endDate);
    }

    public function getMachineOeeRanking($date): \Illuminate\Support\Collection
    {
        return OeeRecord::where('record_date', $date)
            ->with('machine')
            ->get()
            ->map(function ($record) {
                return [
                    'machine_id' => $record->machine_id,
                    'machine_name' => $record->machine->name,
                    'machine_code' => $record->machine->machine_code,
                    'availability' => $record->getAvailability(),
                    'performance' => $record->getPerformance(),
                    'quality' => $record->getQuality(),
                    'oee' => $record->getOverallOee(),
                ];
            })
            ->sortByDesc('oee')
            ->values();
    }
}
