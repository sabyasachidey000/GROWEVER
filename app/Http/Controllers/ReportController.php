<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Shift;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductionRun;
use App\Models\ProductionApproval;
use App\Models\QualityInspection;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
        
    }

    public function index()
    {
        $machines = Machine::active()->get();
        $shifts = Shift::active()->get();

        return view('reports.index', compact('machines', 'shifts'));
    }

    public function dailyProduction(Request $request)
    {
        $date = $request->get('date', today());
        $data = $this->reportService->dailyProductionReport($date);

        return $this->formatResponse($request, $data, 'daily_production_' . $date);
    }

    public function shiftProduction(Request $request)
    {
        $date = $request->get('date', today());
        $shiftId = $request->get('shift_id');

        if (!$shiftId) {
            return back()->with('error', 'Please select a shift.');
        }

        $data = $this->reportService->shiftProductionReport($date, (int) $shiftId);

        return $this->formatResponse($request, $data, 'shift_production_' . $date);
    }

    public function machineUtilization(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $data = $this->reportService->machineUtilizationReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'machine_utilization_' . $startDate . '_' . $endDate);
    }

    public function breakdown(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $data = $this->reportService->breakdownReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'breakdown_report_' . $startDate . '_' . $endDate);
    }

    public function operatorPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $data = $this->reportService->operatorPerformanceReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'operator_performance_' . $startDate . '_' . $endDate);
    }

    public function rejectionAnalysis(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $data = $this->reportService->rejectionAnalysisReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'rejection_analysis_' . $startDate . '_' . $endDate);
    }

    public function oeeReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $data = $this->reportService->oeeReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'oee_report_' . $startDate . '_' . $endDate);
    }

    public function maintenanceReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $data = $this->reportService->maintenanceReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'maintenance_report_' . $startDate . '_' . $endDate);
    }

    public function machineEnergyConsumption(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $data = $this->reportService->machineEnergyConsumptionReport($startDate, $endDate);

        return $this->formatResponse($request, $data, 'machine_energy_consumption_' . $startDate . '_' . $endDate);
    }

    /**
     * Print-friendly in-process QC sheet.  This is read-only and groups the
     * saved one-machine inspections by date and shift.
     */
    public function qcInspection(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $date = Carbon::parse($validated['date'] ?? now())->toDateString();
        $shiftId = $validated['shift_id'] ?? null;
        $shifts = Shift::active()->orderBy('start_time')->get();

        $inspections = QualityInspection::with(['machine', 'product', 'shift', 'inspector'])
            ->whereDate('inspection_date', $date)
            ->when($shiftId, fn ($query) => $query->where('shift_id', $shiftId))
            ->orderBy('shift_id')
            ->orderBy('machine_id')
            ->orderBy('inspection_time')
            ->get();

        return view('reports.qc-inspection', compact('date', 'shiftId', 'shifts', 'inspections'));
    }

    /** Print-friendly daily component production sheet using production_runs. */
    public function componentDailyProduction(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $date = Carbon::parse($validated['date'] ?? now())->toDateString();
        $shiftId = $validated['shift_id'] ?? null;
        $shifts = Shift::active()->orderBy('start_time')->get();

        $productionRuns = ProductionRun::with(['machine', 'operator', 'product', 'shift'])
            ->withSum('rejections as rejection_quantity', 'quantity')
            ->whereDate('start_time', $date)
            ->when($shiftId, fn ($query) => $query->where('shift_id', $shiftId))
            ->orderBy('shift_id')
            ->orderBy('machine_id')
            ->orderBy('start_time')
            ->get();
           

        return view('reports.component-daily-production', compact('date', 'shiftId', 'shifts', 'productionRuns'));
    }

    protected function formatResponse(Request $request, $data, string $filename)
    {
        $format = $request->get('format', 'view');

        if ($format === 'pdf') {
            return $this->exportPdf($data, $filename);
        }

        if ($format === 'excel') {
            return $this->exportExcel($data, $filename);
        }

        return view('reports.view', compact('data', 'filename'));
    }

    protected function exportPdf($data, string $filename)
    {
        $pdf = Pdf::loadView('reports.pdf', compact('data'));
        return $pdf->download($filename . '.pdf');
    }

    protected function exportExcel($data, string $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($data->isNotEmpty()) {
            $columns = array_keys((array) $data->first());
            $colIndex = 1;
            foreach ($columns as $column) {
                $sheet->setCellValueByColumnAndRow($colIndex, 1, ucfirst(str_replace('_', ' ', $column)));
                $colIndex++;
            }

            $rowIndex = 2;
            foreach ($data as $row) {
                $colIndex = 1;
                foreach ((array) $row as $value) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);
                    $colIndex++;
                }
                $rowIndex++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), 'mes_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename . '.xlsx')->deleteFileAfterSend(true);
    }
    
    public function daily_production_report_insert()
    {
        try {
            DB::beginTransaction();
    
            $yesterday = Carbon::now('Asia/Kolkata')
                ->subDay()
                ->toDateString();
    
           $from = Carbon::parse($yesterday . ' 08:00:00', 'Asia/Kolkata');
            $to = $from->copy()->addDay();
           /* $from = Carbon::parse('2026-09-29 08:00:00', 'Asia/Kolkata');
            $to   = Carbon::parse('2026-10-11 08:00:00', 'Asia/Kolkata');*/
    
            $da = DB::select("
                SELECT
                    l.user_id,
                    m.id AS machine_id,
                    m.cycle_count,
                    s.id AS shift_id,
                    s.name AS shift_name,
                    s.start_time AS shift_start,
                    s.end_time AS shift_end,
    
                    CASE
                        WHEN s.is_night_shift IS TRUE
                             AND l.created_at::time < s.end_time
                        THEN l.created_at::date - 1
                        ELSE l.created_at::date
                    END AS log_date,
    
                    l.device_id,
                    COUNT(l.id) AS total,
                    MIN(l.created_at) AS first_punch,
                    MAX(l.created_at) AS last_punch
    
                FROM rfid_limit_switch_logs l
    
                INNER JOIN machines m
                    ON m.rfid_device_id = l.device_id
    
                INNER JOIN shifts s
                    ON s.is_active IS TRUE
                    AND (
                        (
                            s.is_night_shift IS FALSE
                            AND l.created_at::time >= s.start_time
                            AND l.created_at::time < s.end_time
                        )
                        OR
                        (
                            s.is_night_shift IS TRUE
                            AND (
                                l.created_at::time >= s.start_time
                                OR l.created_at::time < s.end_time
                            )
                        )
                    )
    
                WHERE l.created_at >= ?
                  AND l.created_at < ?
    
                GROUP BY
                    l.user_id,
                    m.id,
                    m.cycle_count,
                    s.id,
                    s.name,
                    s.start_time,
                    s.end_time,
                    s.is_night_shift,
                    l.device_id,
                    log_date
    
                ORDER BY log_date, shift_id, l.user_id, l.device_id
            ", [
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s')
            ]);
    
            foreach ($da as $rws) {
    
                $startTime = Carbon::parse($rws->first_punch);
                $endTime   = Carbon::parse($rws->last_punch);
    
                $runtimeMinutes = $startTime->diffInMinutes($endTime);
    
                $weight = (int) $rws->total * (int) $rws->cycle_count;
    
                /*
                 * Find production run.
                 * Same machine + operator + shift + production date.
                 */
                $productionRun = ProductionRun::where(
                        'machine_id', $rws->machine_id
                    )
                    ->where('operator_id', $rws->user_id)
                    ->where('shift_id', $rws->shift_id)
                    ->whereDate('start_time', $rws->log_date)
                    ->first();
                $product_id=DB::table('machine_product')->where('machine_id',$rws->machine_id)->first();
                $productionData = [
                    'machine_id'          => $rws->machine_id,
                    'operator_id'         => $rws->user_id,
                    'product_id'          => $product_id->product_id,
                    'shift_id'            => $rws->shift_id,
                    'operator_session_id' => null,
                    'start_time'          => $startTime,
                    'end_time'            => $endTime ,
                    'weight'              => $weight,
                    'remarks'             => null,
                    'status'              => 'in_progress',
                    'runtime_minutes'     => $runtimeMinutes,
                ];
    
                if ($productionRun) {
                    $productionRun->update($productionData);
                } else {
                    $productionRun = ProductionRun::create($productionData);
                }
    
                /*
                 * Create or update approval.
                 */
                $approval = ProductionApproval::where(
                    'production_run_id',
                    $productionRun->id
                )->first();
    
                $approvalData = [
                    'submitted_by' => auth()->user()->employee_id
                        ?? $rws->user_id,
                    'submitted_at' => now('Asia/Kolkata'),
                    'status' => 'pending',
                ];
    
                if ($approval) {
                    $approval->update($approvalData);
                } else {
                    ProductionApproval::create([
                        'production_run_id' => $productionRun->id,
                        ...$approvalData,
                    ]);
                }
    
                Log::info('Shift Production Processed', [
                    'production_run_id' => $productionRun->id,
                    'machine_id' => $rws->machine_id,
                    'operator_id' => $rws->user_id,
                    'shift_id' => $rws->shift_id,
                    'shift_name' => $rws->shift_name,
                    'log_date' => $rws->log_date,
                    'total_count' => $rws->total,
                    'weight' => $weight,
                    'runtime_minutes' => $runtimeMinutes,
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Shift-wise production report processed successfully.',
                'total_groups' => count($da),
            ]);
    
        } catch (\Throwable $e) {
            DB::rollBack();
    
            Log::error('Shift Production Report Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'time' => now('Asia/Kolkata')->format('Y-m-d H:i:s'),
            ]);
    
            return response()->json([
                'status' => false,
                'message' => 'Failed to process shift-wise production report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function shift_daily_production_report_insert()
    {
         try {
    
            DB::beginTransaction();
             /*
            |--------------------------------------------------------------------------
            | Current Date
            |--------------------------------------------------------------------------
            */
            $today = Carbon::now('Asia/Kolkata')->toDateString();
            $yesterday = Carbon::now('Asia/Kolkata')
            ->subDay()
            ->toDateString();
            
            
         }
          catch (\Throwable $e) {
              
          }
         
    }
    public function downloadQcInspectionPdf(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'shift_id' => 'nullable|integer|exists:shifts,id',
        ]);
    
       $date = Carbon::parse($validated['date'] ?? now())->toDateString();
        $shiftId = $validated['shift_id'] ?? null;
        $shifts = Shift::active()->orderBy('start_time')->get();

        $inspections = QualityInspection::with(['machine', 'product', 'shift', 'inspector'])
            ->whereDate('inspection_date', $date)
            ->when($shiftId, fn ($query) => $query->where('shift_id', $shiftId))
            ->orderBy('shift_id')
            ->orderBy('machine_id')
            ->orderBy('inspection_time')
            ->get();
    
       $logoPath = public_path('images/mts.png');

        $pdf = Pdf::loadView('reports.qc-inspection-pdf', [
            'date' => $date,
            'shiftId' => $shiftId,
            'shifts' => $shifts,
            'inspections' => $inspections,
            'logoPath' => $logoPath,
        ])->setPaper('a4', 'landscape');
    
        return $pdf->download('QC-Inspection-' . $date . '.pdf');
    }
    public function downloaddailyPdf(Request $request)
    {
       $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $date = Carbon::parse($validated['date'] ?? now())->toDateString();
        $shiftId = $validated['shift_id'] ?? null;
        $shifts = Shift::active()->orderBy('start_time')->get();

        $productionRuns = ProductionRun::with(['machine', 'operator', 'product', 'shift'])
            ->withSum('rejections as rejection_quantity', 'quantity')
            ->whereDate('start_time', $date)
            ->when($shiftId, fn ($query) => $query->where('shift_id', $shiftId))
            ->orderBy('shift_id')
            ->orderBy('machine_id')
            ->orderBy('start_time')
            ->get(); 
        $logoPath = public_path('images/mts.png');
        $pdf = PDF::loadView('reports.downloaddailyPdf', [
    'date' => $date,
    'shiftId' => $shiftId,
    'shifts' => $shifts,
    'productionRuns' => $productionRuns,
    'logoPath' => $logoPath,
]);
    
        return $pdf->download('Daily Production Pdf-' . $date . '.pdf');
    }
    
}
