<?php

namespace App\Services;

use App\Models\ProductionRun;
use App\Models\MachineBreakdown;
use App\Models\Rejection;
use App\Models\QualityInspection;
use App\Models\Machine;
use App\Models\Employee;
use App\Models\OeeRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        protected EnergyConsumptionService $energyConsumptionService
    ) {
    }

    public function dailyProductionReport($date): Collection
    {
        return ProductionRun::with(['machine', 'operator', 'product', 'shift'])
            ->forDate($date)
            ->orderBy('start_time')
            ->get()
            ->map(fn($run) => [
                'machine' => $run->machine->name,
                'operator' => $run->operator->name,
                'product' => $run->product->name,
                'shift' => $run->shift->name,
                'start_time' => $run->start_time->format('H:i'),
                'end_time' => $run->end_time?->format('H:i') ?? '-',
                'runtime' => $run->runtime_minutes . ' min',
                'Count' => $run->weight ? number_format($run->weight, 2) . ' ' : '-',
                'status' => $run->status,
            ]);
    }

    public function shiftProductionReport($date, int $shiftId): Collection
    {
        return ProductionRun::with(['machine', 'operator', 'product'])
            ->forDate($date)
            ->where('shift_id', $shiftId)
            ->orderBy('start_time')
            ->get()
            ->map(fn($run) => [
                'machine' => $run->machine->name,
                'operator' => $run->operator->name,
                'product' => $run->product->name,
                'Count' => $run->weight ? number_format($run->weight, 2) . ' kg' : '-',
                'runtime' => $run->runtime_minutes . ' min',
            ]);
    }

    public function machineUtilizationReport($startDate, $endDate): Collection
    {
        return Machine::active()->withCount(['productionRuns' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_time', [$startDate, $endDate]);
        }])->get()->map(function ($machine) use ($startDate, $endDate) {
            $totalRuntime = $machine->productionRuns()
                ->whereBetween('start_time', [$startDate, $endDate])
                ->sum('runtime_minutes');

            $totalAvailable = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) * 480;

            return [
                'machine_code' => $machine->machine_code,
                'machine_name' => $machine->name,
                'type' => $machine->type,
                'total_runs' => $machine->production_runs_count,
                'total_runtime' => $totalRuntime . ' min',
                'utilization' => $totalAvailable > 0 ? round(($totalRuntime / $totalAvailable) * 100, 2) : 0,
            ];
        });
    }

    public function breakdownReport($startDate, $endDate): Collection
    {
        return MachineBreakdown::with(['machine', 'reportedBy', 'assignedTo'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time')
            ->get()
            ->map(fn($bd) => [
                'machine' => $bd->machine->name,
                'type' => $bd->breakdown_type,
                'severity' => $bd->severity,
                'start_time' => $bd->start_time->format('Y-m-d H:i'),
                'end_time' => $bd->end_time?->format('Y-m-d H:i') ?? '-',
                'duration' => $bd->duration_minutes ? $bd->duration_minutes . ' min' : 'In Progress',
                'reported_by' => $bd->reportedBy->name,
                'assigned_to' => $bd->assignedTo?->name ?? '-',
                'status' => $bd->status,
                'root_cause' => $bd->root_cause ?? '-',
            ]);
    }

    public function operatorPerformanceReport($startDate, $endDate): Collection
    {
        return Employee::whereHas('productionRuns')
            ->withCount(['productionRuns' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_time', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($employee) use ($startDate, $endDate) {
                $runs = $employee->productionRuns()->whereBetween('start_time', [$startDate, $endDate])->get();

                return [
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->name,
                    'department' => $employee->department?->name ?? '-',
                    'total_runs' => $employee->production_runs_count,
                    'total_count' => $runs->whereNotNull('weight')->sum('weight'),
                    'avg_runtime' => $runs->avg('runtime_minutes') ? round($runs->avg('runtime_minutes')) . ' min' : '0',
                    'rejection_rate' => '0%',
                ];
            })->sortByDesc('total_production')->values();
    }

    public function rejectionAnalysisReport($startDate, $endDate): Collection
    {
        return Rejection::with(['product', 'machine', 'operator'])
            ->whereBetween('rejection_date', [$startDate, $endDate])
            ->select('rejection_reason')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('COUNT(*) as occurrences')
            ->groupBy('rejection_reason')
            ->orderByDesc('total_quantity')
            ->get()
            ->map(fn($row) => [
                'rejection_reason' => $row->rejection_reason,
                'total_quantity' => $row->total_quantity,
                'occurrences' => $row->occurrences,
            ]);
    }

    public function oeeReport($startDate, $endDate): Collection
    {
        return OeeRecord::with(['machine', 'shift'])
            ->whereBetween('record_date', [$startDate, $endDate])
            ->get()
            ->map(fn($record) => [
                'machine' => $record->machine->name,
                'shift' => $record->shift->name,
                'date' => $record->record_date->format('Y-m-d'),
                'availability' => $record->getAvailability() . '%',
                'performance' => $record->getPerformance() . '%',
                'quality' => $record->getQuality() . '%',
                'oee' => $record->getOverallOee() . '%',
            ]);
    }

    public function maintenanceReport($startDate, $endDate): Collection
    {
        return MachineBreakdown::whereBetween('start_time', [$startDate, $endDate])
            ->select('breakdown_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('COALESCE(SUM(duration_minutes), 0) as total_downtime')
            ->selectRaw('COALESCE(SUM(repair_cost), 0) as total_cost')
            ->selectRaw('COALESCE(AVG(duration_minutes), 0) as avg_downtime')
            ->groupBy('breakdown_type')
            ->orderByDesc('count')
            ->get()
            ->map(fn($row) => [
                'breakdown_type' => str_replace('_', ' ', ucfirst($row->breakdown_type)),
                'count' => $row->count,
                'total_downtime' => round($row->total_downtime, 0) . ' min',
                'total_cost' => number_format($row->total_cost, 2),
                'avg_downtime' => round($row->avg_downtime, 0) . ' min',
            ]);
    }

    public function machineEnergyConsumptionReport($startDate, $endDate): Collection
    {
        return $this->energyConsumptionService->machineWiseReport($startDate, $endDate);
    }
}
