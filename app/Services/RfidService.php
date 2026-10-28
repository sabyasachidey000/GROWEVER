<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Machine;
use App\Models\OperatorSession;
use App\Models\Shift;
use App\Models\RfidLog;
use App\Models\RfidPunchLog;
use App\Models\EmployeeRfidPunch;
use App\Models\MachineBreakdown;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class RfidService
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    /**
     * Process RFID card swipe for operator login/logout.
     */
    public function processSwipe(
        string $rfidNumber,
        int $machineId,
        string $readerId = null
    ): array {
        return DB::transaction(function () use (
            $rfidNumber,
            $machineId,
            $readerId
        ) {

            $employee = Employee::findByRfid($rfidNumber);

            if (!$employee) {
                RfidLog::logSwipe(
                    $rfidNumber,
                    'invalid_card',
                    $machineId,
                    null,
                    $readerId
                );

                return [
                    'success' => false,
                    'message' => 'Invalid RFID card. No employee found.',
                    'action' => 'invalid_card',
                ];
            }

            if ($employee->status !== 'active') {
                RfidLog::logSwipe(
                    $rfidNumber,
                    'access_denied',
                    $machineId,
                    null,
                    $readerId
                );

                return [
                    'success' => false,
                    'message' => 'Employee account is not active.',
                    'action' => 'access_denied',
                ];
            }

            $machine = Machine::find($machineId);

            if (!$machine || !$machine->is_active) {
                RfidLog::logSwipe(
                    $rfidNumber,
                    'access_denied',
                    $machineId,
                    null,
                    $readerId
                );

                return [
                    'success' => false,
                    'message' => 'Machine is not available.',
                    'action' => 'access_denied',
                ];
            }

            // Existing active operator on this machine
            $existingSession =
                OperatorSession::getActiveSessionForMachine($machineId);

            if ($existingSession) {

                $existingOperatorName =
                    $existingSession->employee->name ?? 'Unknown Operator';

                $existingSession->logout();

                $this->activityLogService->log(
                    "Operator {$existingOperatorName} logged out from {$machine->name}",
                    $existingSession,
                    'rfid_logout'
                );
            }

            // Get current shift
            $currentShift = Shift::getCurrentShift();

            if (!$currentShift) {
                return [
                    'success' => false,
                    'message' => 'No active shift found.',
                    'action' => 'access_denied',
                ];
            }

            // Create new operator session
            $session = OperatorSession::create([
                'employee_id' => $employee->id,
                'machine_id' => $machineId,
                'shift_id' => $currentShift->id,
                'login_time' => now(),
                'status' => 'active',
            ]);

            // Machine is now running
            $machine->update([
                'status' => 'running',
            ]);

            // Log RFID swipe
            RfidLog::logSwipe(
                $rfidNumber,
                'swipe_in',
                $machineId,
                null,
                $readerId
            );

            // Activity log
            $this->activityLogService->log(
                "Operator {$employee->name} logged in to {$machine->name}",
                $session,
                'rfid_login'
            );

            return [
                'success' => true,
                'message' => "Welcome, {$employee->name}",
                'action' => 'swipe_in',

                'data' => [
                    'employee' => $employee->load([
                        'department',
                        'designation'
                    ]),
                    'machine' => $machine,
                    'shift' => $currentShift,
                    'session' => $session,
                ],
            ];
        });
    }


    /**
     * Process operator logout via RFID.
     */
    public function processLogout(
        string $rfidNumber,
        int $machineId
    ): array {

        return DB::transaction(function () use (
            $rfidNumber,
            $machineId
        ) {

            $session = OperatorSession::where('machine_id', $machineId)
                ->where('status', 'active')
                ->whereHas(
                    'employee',
                    fn ($q) =>
                        $q->where(
                            'rfid_card_number',
                            $rfidNumber
                        )
                )
                ->latest('login_time')
                ->first();

            if (!$session) {
                return [
                    'success' => false,
                    'message' =>
                        'No active session found for this operator.',
                ];
            }

            $operatorName =
                $session->employee->name ?? 'Unknown Operator';

            $machine = $session->machine;

            $session->logout();

            if ($machine) {
                $machine->update([
                    'status' => 'idle',
                ]);
            }

            RfidLog::logSwipe(
                $rfidNumber,
                'swipe_out',
                $machineId
            );

            $this->activityLogService->log(
                "Operator {$operatorName} logged out from {$machine->name}",
                $session,
                'rfid_logout'
            );

            return [
                'success' => true,
                'message' => "Goodbye, {$operatorName}",
                'action' => 'swipe_out',

                'data' => [
                    'session' => $session,
                    'duration_minutes' =>
                        $session->getDurationInMinutes(),
                ],
            ];
        });
    }


    /**
     * Get current operator for a machine.
     */
    public function getCurrentOperator(
        int $machineId
    ): ?OperatorSession {

        return OperatorSession::getActiveSessionForMachine(
            $machineId
        );
    }


    /**
     * Process ESP32 / RC522 RFID punch.
     *
     * LOGIN:
     * - Creates EmployeeRfidPunch
     * - Creates OperatorSession
     * - Sets machine status to running
     *
     * LOGOUT:
     * - Creates EmployeeRfidPunch
     * - Closes OperatorSession
     * - Sets machine status to idle
     */
    public function processDevicePunch(
        string $deviceId,
        string $uid,
        ?string $ipAddress = null
    ): array {

        $deviceId = trim($deviceId);
        $uid = strtoupper(trim($uid));

        return DB::transaction(function () use (
            $deviceId,
            $uid,
            $ipAddress
        ) {

            /*
             * ---------------------------------------------------------
             * 1. Find machine by ESP32 device ID
             * ---------------------------------------------------------
             */
            $machine = Machine::active()
                ->where('rfid_device_id', $deviceId)
                ->lockForUpdate()
                ->first();

            if (!$machine) {
                return $this->punchFailure(
                    'unknown_device',
                    'RFID device is not mapped to an active machine.'
                );
            }
            //check machine in Breakdown Mode
            $machine = Machine::active()
            ->where('rfid_device_id', $deviceId)
            ->first();
            $existing = MachineBreakdown::where('machine_id', $machine->id)
            ->whereIn('status', ['open', 'in_progress', 'resolved'])
            ->latest('start_time')
            ->first();

            if ($existing) {
                $employees_e = Employee::with([
                    'department',
                    'designation'
                ])
                ->whereRaw(
                    'UPPER(TRIM(rfid_card_number)) = ?',
                    [$uid]
                )
                ->lockForUpdate()
                ->first();
                if($employees_e->designation_id=='4')
                {
                    $rawLog = RfidPunchLog::create([
                        'employee_id' => $employees_e->id,
                        'device_id' => $deviceId,
                        'uid' => $uid,
                        'punch_time' => now(),
                        'ip_address' => $ipAddress,
                        'employee_code' => $employees_e->employee_code,
                        'employee_name' => $employees_e->name,
                        'department_name' =>
                            $employees_e->department?->name,
                        'designation_name' =>
                            $employees_e->designation?->name,
                    ]);
                    $UPdateAttendanceEng=MachineBreakdown::where('id',$existing->id)->update(array('attened_by'=>$employees_e->id));
                    
                    $punchType='LOGIN';
                    $punch = EmployeeRfidPunch::create([
                            'employee_id' => $employees_e->id,
                            'machine_id' => $machine->id,
                            'rfid_punch_log_id' => $rawLog->id,
                            'rfid_card_number' =>
                                $employees_e->rfid_card_number,
                            'device_id' => $deviceId,
                            'punch_type' => $punchType,
                            'punch_time' => now(),
                        ]);
                   return [
                        'success' => true,
                        'duplicate' => false,
                        'message' =>
                            "{$punchType} recorded for {$employees_e->name}.",
                        'action' => $punchType,
                        'punch' => $punch,
                        'raw_punch_id' => $rawLog->id,
                        'employee' => $employees_e,
                        'machine' => $machine,
                        'session' => $session ?? null,
                    ];
                }
                

                return [
                    'success' => false,
                    'created' => false,
                    'message' => 'An active breakdown request already exists for this machine.',
                    'breakdown' => $this->buttonBreakdownResponse($existing),
                ];
            }

            /*
             * ---------------------------------------------------------
             * 2. Find employee by RFID
             * ---------------------------------------------------------
             */
            $employee = Employee::with([
                'department',
                'designation'
            ])
                ->whereRaw(
                    'UPPER(TRIM(rfid_card_number)) = ?',
                    [$uid]
                )->where('designation_id','!=',4)
                ->lockForUpdate()
                ->first();

            if (!$employee) {
                return $this->punchFailure(
                    'unknown_card',
                    'RFID card is not registered.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 3. Check employee status
             * ---------------------------------------------------------
             */
            if ($employee->status !== 'active') {
                return $this->punchFailure(
                    'inactive_employee',
                    'Employee account is not active.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 4. Save raw hardware event
             * ---------------------------------------------------------
             */
            $rawLog = RfidPunchLog::create([
                'employee_id' => $employee->id,
                'device_id' => $deviceId,
                'uid' => $uid,
                'punch_time' => now(),
                'ip_address' => $ipAddress,
                'employee_code' => $employee->employee_code,
                'employee_name' => $employee->name,
                'department_name' =>
                    $employee->department?->name,
                'designation_name' =>
                    $employee->designation?->name,
            ]);


            /*
             * ---------------------------------------------------------
             * 5. Get latest business punch
             * ---------------------------------------------------------
             */
            $latestPunch = EmployeeRfidPunch::where(
                    'employee_id',
                    $employee->id
                )
                ->latest('punch_time')
                ->latest('id')
                ->lockForUpdate()
                ->first();


            /*
             * ---------------------------------------------------------
             * 6. Debounce duplicate scan
             * ---------------------------------------------------------
             */
            $debounceSeconds = max(
                0,
                (int) config(
                    'rfid.debounce_seconds',
                    5
                )
            );

            if (
                $latestPunch &&
                $debounceSeconds > 0 &&
                $latestPunch->punch_time
                    ->greaterThan(
                        now()->subSeconds($debounceSeconds)
                    )
            ) {

                return [
                    'success' => true,
                    'duplicate' => true,
                    'message' =>
                        'Rapid duplicate scan ignored.',
                    'action' =>
                        $latestPunch->punch_type,
                    'raw_punch_id' =>
                        $rawLog->id,
                    'employee' => $employee,
                    'machine' => $machine,
                ];
            }


            /*
             * ---------------------------------------------------------
             * 7. Determine LOGIN / LOGOUT
             * ---------------------------------------------------------
             */
            $punchType =
                $latestPunch?->punch_type === 'LOGIN'
                    ? 'LOGOUT'
                    : 'LOGIN';
            $latestPunch_machine = EmployeeRfidPunch::where(
                    'machine_id',
                    $machine->id
                )
                ->latest('punch_time')
                ->latest('id')
                ->first();
               // print_r($latestPunch_machine->punch_type);
               // exit();
            if($latestPunch_machine->punch_type=='LOGIN' && $punchType=='LOGIN')
            {
                $punch = EmployeeRfidPunch::create([
                    'employee_id' => $latestPunch_machine->employee_id,
                    'machine_id' => $latestPunch_machine->machine_id,
                    'rfid_punch_log_id' => $rawLog->id,
                    'rfid_card_number' =>
                        $latestPunch_machine->rfid_card_number,
                    'device_id' => $latestPunch_machine->device_id,
                    'punch_type' => 'LOGOUT',
                    'punch_time' => now(),
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 8. Create business punch
             * ---------------------------------------------------------
             */
            $punch = EmployeeRfidPunch::create([
                'employee_id' => $employee->id,
                'machine_id' => $machine->id,
                'rfid_punch_log_id' => $rawLog->id,
                'rfid_card_number' =>
                    $employee->rfid_card_number,
                'device_id' => $deviceId,
                'punch_type' => $punchType,
                'punch_time' => now(),
            ]);


            /*
             * ---------------------------------------------------------
             * 9. LOGIN
             * ---------------------------------------------------------
             */
            if ($punchType === 'LOGIN') {

                // Check existing operator on this machine
                $existingSession =
                    OperatorSession::getActiveSessionForMachine(
                        $machine->id
                    );

                if ($existingSession) {

                    $previousOperator =
                        $existingSession->employee->name
                        ?? 'Unknown Operator';

                    $existingSession->logout();

                    $this->activityLogService->log(
                        "Operator {$previousOperator} logged out from {$machine->name}",
                        $existingSession,
                        'rfid_logout'
                    );
                }


                // Get current shift
                $currentShift = Shift::getCurrentShift();

                if (!$currentShift) {
                    return $this->punchFailure(
                        'no_shift',
                        'No active shift found.'
                    );
                }


                // Create active operator session
                $session = OperatorSession::create([
                    'employee_id' => $employee->id,
                    'machine_id' => $machine->id,
                    'shift_id' => $currentShift->id,
                    'login_time' => now(),
                    'status' => 'active',
                ]);


                // Machine is running
                $machine->update([
                    'status' => 'running',
                ]);


                // Activity log
                $this->activityLogService->log(
                    "Operator {$employee->name} logged in to {$machine->name}",
                    $session,
                    'rfid_login',
                    'rfid'
                );
            }


            /*
             * ---------------------------------------------------------
             * 10. LOGOUT
             * ---------------------------------------------------------
             */
            else {

                // Find active session of this employee
                // on this machine.
                $session = OperatorSession::where(
                        'employee_id',
                        $employee->id
                    )
                    ->where(
                        'machine_id',
                        $machine->id
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->latest('login_time')
                    ->first();


                if ($session) {

                    $session->logout();

                    $this->activityLogService->log(
                        "Operator {$employee->name} logged out from {$machine->name}",
                        $session,
                        'rfid_logout',
                        'rfid'
                    );
                }


                // Machine becomes idle only if there is
                // no other active operator.
                $anotherActiveSession =
                    OperatorSession::getActiveSessionForMachine(
                        $machine->id
                    );

                if (!$anotherActiveSession) {

                    $machine->update([
                        'status' => 'idle',
                    ]);
                }
            }


            /*
             * ---------------------------------------------------------
             * 11. General activity log
             * ---------------------------------------------------------
             */
            $this->activityLogService->log(
                "RFID {$punchType} recorded for {$employee->name} at {$machine->name}",
                $punch,
                strtolower($punchType) === 'login'
                    ? 'rfid_login'
                    : 'rfid_logout',
                'rfid'
            );


            /*
             * ---------------------------------------------------------
             * 12. Return result
             * ---------------------------------------------------------
             */

            return [
                'success' => true,
                'duplicate' => false,
                'message' =>
                    "{$punchType} recorded for {$employee->name}.",
                'action' => $punchType,
                'punch' => $punch,
                'raw_punch_id' => $rawLog->id,
                'employee' => $employee,
                'machine' => $machine,
                'session' => $session ?? null,
            ];
        });
    }
    private function buttonBreakdownResponse(MachineBreakdown $breakdown): array
    {
        return [
            'id' => $breakdown->id,
            'machine_id' => $breakdown->machine_id,
            'reported_by' => $breakdown->reported_by,
            'status' => $breakdown->status,
            'severity' => $breakdown->severity,
            'start_time' => $breakdown->start_time->toIso8601String(),
        ];
    }


    /**
     * Standard punch failure response.
     */
    private function punchFailure(
        string $reason,
        string $message
    ): array {

        return [
            'success' => false,
            'reason' => $reason,
            'message' => $message,
        ];
    }
}