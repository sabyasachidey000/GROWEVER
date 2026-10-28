<?php

namespace App\Services;

use App\Models\Machine;
use App\Models\ProductionRun;
use App\Models\Rejection;
use App\Models\OperatorSession;
use App\Models\MachineBreakdown;
use App\Models\OeeRecord;
use App\Models\QualityInspection;
use App\Models\Employee;
use App\Models\EmployeeRfidPunch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getTodayStats(): array
    {
        $today = today();
        $today_prod=DB::select("
            SELECT
                COUNT(rfid_limit_switch_logs.id) AS production
            FROM machines
            LEFT JOIN rfid_limit_switch_logs
                ON machines.rfid_device_id = rfid_limit_switch_logs.device_id
                AND rfid_limit_switch_logs.created_at::date = CURRENT_DATE
        ");

        return [
            'running_machines' => Machine::running()->count(),
            'idle_machines' => Machine::idle()->count(),
            'breakdown_machines' => Machine::onBreakdown()->count(),
            'total_machines' => Machine::active()->count(),
            'production_today' => $today_prod[0]->production ?? 0,
            'rejections_today' => 0,
            'good_quantity_today' => ProductionRun::forDate($today)->whereNotNull('weight')->sum('weight'),
            'operator_count' => EmployeeRfidPunch::activeEmployeeCount(),
            'active_sessions' => EmployeeRfidPunch::activeEmployeeCount(),
            'open_breakdowns' => MachineBreakdown::open()->orWhere('status', 'in_progress')->count(),
            'pending_approvals' => \App\Models\ProductionApproval::pending()->count(),
        ];
    }

    public function getMachineStatus(): \Illuminate\Support\Collection
    {
        return Machine::active()->with(['activeBreakdown'])->get()->map(function ($machine) {
            $currentSession = OperatorSession::getActiveSessionForMachine($machine->id);
            return [
                'id' => $machine->id,
                'machine_code' => $machine->machine_code,
                'name' => $machine->name,
                'status' => $machine->status,
                'operator' => $currentSession?->employee?->name,
                'product' => $currentSession?->productionRuns()->latest()->first()?->product?->name,
                'breakdown_type' => $machine->activeBreakdown?->breakdown_type,
                'utilization' => $machine->getUtilizationForDate(today()),
            ];
        });
    }

    public function getProductionChartData($days = 7): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $runs = ProductionRun::forDate($date)->get();
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('D'),
                'production' => $runs->whereNotNull('weight')->sum('weight'),
                'rejection' => 0,
                'good' => $runs->whereNotNull('weight')->sum('weight'),
            ];
        }
        return $data;
    }

    public function getRejectionByReason($days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Rejection::where('rejection_date', '>=', Carbon::today()->subDays($days))
            ->select('rejection_reason')
            ->selectRaw('SUM(quantity) as total')
            ->groupBy('rejection_reason')
            ->orderByDesc('total')
            ->get();
    }

    public function getBreakdownChartData($days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return MachineBreakdown::where('start_time', '>=', Carbon::today()->subDays($days))
            ->select(DB::raw('DATE(start_time) as date'))
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(duration_minutes) as total_downtime')
            ->groupBy(DB::raw('DATE(start_time)'))
            ->orderBy('date')
            ->get();
    }

    public function getOeeSummary(int $machineId = null, $startDate = null, $endDate = null): array
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

    public function getTopOperators(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $startDate = Carbon::today()->startOfMonth();

        return Employee::select('employees.*')
            ->selectRaw('COALESCE(SUM(pr.weight), 0) as total_production')
            ->selectRaw('0 as total_rejection')
            ->leftJoin('production_runs as pr', 'employees.id', '=', 'pr.operator_id')
            ->where('pr.start_time', '>=', $startDate)
            ->orWhereNull('pr.id')
            ->groupBy('employees.id')
            ->orderByDesc('total_production')
            ->limit($limit)
            ->get();
    }

    public function getRecentBreakdowns(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return MachineBreakdown::with(['machine', 'reportedBy'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
