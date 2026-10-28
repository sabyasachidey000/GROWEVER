<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use DB;

class MachineApiController extends Controller
{
    public function index()
    {
        return response()->json(Machine::active()->orderBy('machine_code')->paginate(25));
    }

    public function show(Machine $machine)
    {
        return response()->json($machine);
    }

    public function status(Machine $machine)
    {
        return response()->json([
            'status' => $machine->status,
            'current_operator' => $machine->operatorSessions()->active()->latest()->first()?->employee?->name,
            'active_breakdown' => $machine->activeBreakdown?->breakdown_type,
        ]);
    }
    public function machineScadaApi()
    {
       /* $rec = DB::select("
            SELECT
                machines.id,
                machines.machine_code,
                machines.name,
                machines.rfid_device_id,
                machines.cycle_count,
                machine_breakdowns.severity AS breakdown_severity,

                COUNT(rfid_limit_switch_logs.id) AS production,

                MAX(rfid_limit_switch_logs.created_at) AS last_cycle

            FROM machines

            LEFT JOIN rfid_limit_switch_logs
                ON machines.rfid_device_id = rfid_limit_switch_logs.device_id
                AND rfid_limit_switch_logs.created_at::date = CURRENT_DATE

            LEFT JOIN machine_breakdowns
                ON machine_breakdowns.machine_id = machines.id
                AND machine_breakdowns.status = 'open'

            GROUP BY
                machines.id,
                machines.machine_code,
                machines.name,
                machines.rfid_device_id,
                machines.cycle_count,
                machine_breakdowns.severity

            ORDER BY
                machines.machine_code
        ");*/
        $rec = DB::select("
                WITH current_shift AS (
            
                    SELECT
                        s.*,
            
                        CASE
                            -- Normal shift: 08:00 -> 20:00
                            WHEN s.start_time < s.end_time THEN
                                CURRENT_DATE + s.start_time
            
                            -- Night shift and current time is after midnight
                            -- Example: 20:00 -> 08:00
                            WHEN CURRENT_TIME < s.end_time THEN
                                CURRENT_DATE - INTERVAL '1 day' + s.start_time
            
                            ELSE
                                CURRENT_DATE + s.start_time
                        END AS shift_start,
            
                        CASE
                            -- Normal shift
                            WHEN s.start_time < s.end_time THEN
                                CURRENT_DATE + s.end_time
            
                            -- Night shift and current time is after midnight
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
                            -- Normal shift
                            (
                                s.start_time < s.end_time
                                AND CURRENT_TIME >= s.start_time
                                AND CURRENT_TIME < s.end_time
                            )
            
                            OR
            
                            -- Night shift
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
                    machines.id,
                    machines.machine_code,
                    machines.name,
                    machines.rfid_device_id,
                    machines.cycle_count,
            
                    current_shift.name AS shift_name,
                    current_shift.code AS shift_code,
                    current_shift.shift_start,
                    current_shift.shift_end,
            
                    machine_breakdowns.severity AS breakdown_severity,
            
                    COUNT(rfid_limit_switch_logs.id) AS production,
            
                    MAX(rfid_limit_switch_logs.created_at) AS last_cycle
            
                FROM machines
            
                CROSS JOIN current_shift
            
                LEFT JOIN rfid_limit_switch_logs
                    ON machines.rfid_device_id = rfid_limit_switch_logs.device_id
            
                    AND rfid_limit_switch_logs.created_at >= current_shift.shift_start
            
                    AND rfid_limit_switch_logs.created_at < current_shift.shift_end
            
                LEFT JOIN machine_breakdowns
                    ON machine_breakdowns.machine_id = machines.id
                    AND machine_breakdowns.status = 'open'
            
                GROUP BY
                    machines.id,
                    machines.machine_code,
                    machines.name,
                    machines.rfid_device_id,
                    machines.cycle_count,
            
                    current_shift.name,
                    current_shift.code,
                    current_shift.shift_start,
                    current_shift.shift_end,
            
                    machine_breakdowns.severity
            
                ORDER BY
                    machines.machine_code
            ");


        $machines = [];


        foreach ($rec as $row) {

            $production = (int) $row->production;

            $lastCycle = $row->last_cycle;

            $status = 'stopped';

            $lastCycleText = '-';

           /* $get_ops=DB::table('operator_sessions')->select('employee_id','login_time')->where('machine_id',$row->id)->where('status','active')->first();
            $get_op = DB::table('operator_sessions')
                ->select('employee_id', 'login_time', 'logout_time', 'status')
                ->where('machine_id', $row->id)->where('employee_id',$get_ops->employee_id)
                ->whereDate('login_time', today())
                ->orderBy('login_time', 'desc')
                ->get();
            $get_emp=DB::table('employees')->where('id',$get_op->employee_id ?? 0)->first();*/
            // Present / Active Operator
            $get_ops = DB::table('operator_sessions')
                ->select('employee_id', 'login_time')
                ->where('machine_id', $row->id)
                ->where('status', 'active')
                ->first();
            
            
            // Present operator-এর আজকের complete login/logout history
            $get_op = collect();
            
            if ($get_ops) {
            
                $get_op = DB::table('operator_sessions')
                    ->select(
                        'employee_id',
                        'login_time',
                        'logout_time',
                        'status'
                    )
                    ->where('machine_id', $row->id)
                    ->where('employee_id', $get_ops->employee_id)
                    ->whereDate('login_time', today())
                    ->orderBy('login_time', 'desc')
                    ->get();
            }
            
            
            // Employee
            $get_emp = null;
            
            if ($get_ops) {
            
                $get_emp = DB::table('employees')
                    ->select('id', 'name')
                    ->where('id', $get_ops->employee_id)
                    ->first();
            }
            /*
            |--------------------------------------------------------------------------
            | BREAKDOWN CHECK
            |--------------------------------------------------------------------------
            |
            | If machine has an OPEN breakdown,
            | breakdown status gets highest priority.
            |
            */

            if (!empty($row->breakdown_severity)) {

                $status = 'breakdown';

            }
            else {

                /*
                |--------------------------------------------------------------------------
                | NORMAL MACHINE STATUS
                |--------------------------------------------------------------------------
                */

                if ($lastCycle) {

                    $lastCycleTime = \Carbon\Carbon::parse($lastCycle);

                    $secondsSinceLastCycle =
                        now()->diffInSeconds($lastCycleTime);


                    if ($secondsSinceLastCycle <= 30) {

                        $status = 'running';

                    }
                    elseif ($secondsSinceLastCycle <= 300) {

                        $status = 'idle';

                    }
                    else {

                        $status = 'stopped';

                    }


                    $lastCycleText =
                        $lastCycleTime->diffForHumans();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | MACHINE DATA
            |--------------------------------------------------------------------------
            */

            $machines[] = [

                'machine_code' => $row->machine_code,
                
                'op_name' => $get_emp?->name
                    ? $get_emp->name . (
                        $get_ops?->login_time
                            ? ' (Logged In at ' .
                                \Carbon\Carbon::parse($get_ops->login_time)->format('H:i:s') .
                              ')'
                            : ''
                    )
                    : 'Not Assigned',

                'name' => $row->name,

                'status' => $status,

                'breakdown_severity' =>
                    $row->breakdown_severity ?? null,

                'production_today' => $production,

                'cycle_count' => $production,

                'cycle_time' => null,
                'operator_history' => $get_op->map(function ($session) {
                    return [
                        'login_time' => $session->login_time,
                        'logout_time' => $session->logout_time,
                        'status' => $session->status,
                    ];
                })->values(),

                'last_cycle' => $lastCycleText,
                'cycle_count_tap' =>$row->cycle_count,
                'total_product_cycle'=>$row->cycle_count* $production,

                'stopped_since' =>
                    $status === 'stopped'
                        ? $lastCycleText
                        : null,

                'line' => 'line1',

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $running = collect($machines)
            ->where('status', 'running')
            ->count();

        $idle = collect($machines)
            ->where('status', 'idle')
            ->count();

        $stopped = collect($machines)
            ->where('status', 'stopped')
            ->count();

        $breakdown = collect($machines)
            ->where('status', 'breakdown')
            ->count();

        $total = count($machines);

        $productionToday = collect($machines)
            ->sum('production_today');


        /*
        |--------------------------------------------------------------------------
        | JSON RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'summary' => [

                'running' => $running,

                'idle' => $idle,

                'stopped' => $stopped,

                'breakdown' => $breakdown,

                'total' => $total,

                'production_today' => $productionToday,

            ],

            'machines' => $machines,

        ]);
    }
}
