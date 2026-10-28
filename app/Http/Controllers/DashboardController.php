<?php

namespace App\Http\Controllers;

use App\Models\DashboardWidget;
use App\Models\Machine;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
        
    }

    public function index()
    {
        $stats = $this->dashboardService->getTodayStats();
        $machineStatus = $this->dashboardService->getMachineStatus();
        $productionChart = $this->dashboardService->getProductionChartData(7);
        $rejectionByReason = $this->dashboardService->getRejectionByReason(30);
        $recentBreakdowns = $this->dashboardService->getRecentBreakdowns(5);
        $oeeSummary = $this->dashboardService->getOeeSummary();

        return view('dashboard.index', compact(
            'stats',
            'machineStatus',
            'productionChart',
            'rejectionByReason',
            'recentBreakdowns',
            'oeeSummary'
        ));
    }

    public function getStats()
    {
        return response()->json($this->dashboardService->getTodayStats());
    }

    public function getMachineStatus()
    {
        return response()->json($this->dashboardService->getMachineStatus());
    }

    public function getProductionChart(Request $request)
    {
        $days = $request->integer('days', 7);
        return response()->json($this->dashboardService->getProductionChartData($days));
    }

    public function getOeeSummary()
    {
        return response()->json($this->dashboardService->getOeeSummary());
    }
    public function machine_scada()
    {
        
        $rec = DB::select("
            WITH current_shift AS (
                SELECT
                    s.*,
                    CASE
                        WHEN s.start_time < s.end_time THEN
                            CURRENT_DATE + s.start_time
                        WHEN CURRENT_TIME < s.end_time THEN
                            CURRENT_DATE - INTERVAL '1 day' + s.start_time
                        ELSE
                            CURRENT_DATE + s.start_time
                    END AS shift_start,
                    CASE
                        WHEN s.start_time < s.end_time THEN
                            CURRENT_DATE + s.end_time
                        WHEN CURRENT_TIME < s.end_time THEN
                            CURRENT_DATE + s.end_time
                        ELSE
                            CURRENT_DATE + INTERVAL '1 day' + s.end_time
                    END AS shift_end
                FROM shifts s
                WHERE s.code IN ('A', 'B')
                  AND s.is_active = TRUE
                  AND s.deleted_at IS NULL
                  AND (
                        (
                            s.start_time < s.end_time
                            AND CURRENT_TIME >= s.start_time
                            AND CURRENT_TIME < s.end_time
                        )
                        OR
                        (
                            s.start_time > s.end_time
                            AND (
                                CURRENT_TIME >= s.start_time
                                OR CURRENT_TIME < s.end_time
                            )
                        )
                  )
                LIMIT 1
            )
            SELECT
                m.id,
                m.machine_code,
                m.name,
                m.rfid_device_id,
                m.cycle_count,
        
                (
                    SELECT p.name
                    FROM machine_product mp
                    JOIN products p
                        ON p.id = mp.product_id
                    WHERE mp.machine_id = m.id
                    ORDER BY p.id
                    LIMIT 1
                ) AS product_name,
        
                current_shift.name AS shift_name,
                current_shift.code AS shift_code,
                current_shift.shift_start,
                current_shift.shift_end,
        
                machine_breakdowns.severity AS breakdown_severity,
        
                COUNT(rfid_limit_switch_logs.id) AS production,
                MAX(rfid_limit_switch_logs.created_at) AS last_cycle
        
            FROM machines m
        
            CROSS JOIN current_shift
        
            LEFT JOIN rfid_limit_switch_logs
                ON m.rfid_device_id = rfid_limit_switch_logs.device_id
               AND rfid_limit_switch_logs.created_at >= current_shift.shift_start
               AND rfid_limit_switch_logs.created_at < current_shift.shift_end
        
            LEFT JOIN machine_breakdowns
                ON machine_breakdowns.machine_id = m.id
               AND machine_breakdowns.status = 'open'
        
            GROUP BY
                m.id,
                m.machine_code,
                m.name,
                m.rfid_device_id,
                m.cycle_count,
                current_shift.name,
                current_shift.code,
                current_shift.shift_start,
                current_shift.shift_end,
                machine_breakdowns.severity
        
            ORDER BY m.machine_code
        ");


        return view('dashboard.machine_scada', compact('rec'));
    }
}
