<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\FaceAttendanceLog;
use App\Models\Machine;
use App\Models\OperatorSession;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class FaceRecognitionService
{
    private const MATCH_THRESHOLD = 0.60;
    private const OPERATOR_LOGIN_MIN_CONFIDENCE = 0.30;
    private const DESCRIPTOR_LENGTH = 128;

    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function enroll(Employee $employee, array $descriptors, ?int $machineId = null): void
    {
        $employee->update([
            'face_descriptor' => $this->normalizeStoredDescriptors($descriptors),
            'face_machine_id' => $machineId,
        ]);

        $this->activityLogService->log(
            "Face descriptor enrolled for {$employee->name}",
            $employee,
            'face_enrolled',
            'face_recognition'
        );
    }

    public function removeEnrollment(Employee $employee): void
    {
        $employee->update([
            'face_descriptor' => null,
            'face_machine_id' => null,
        ]);

        $this->activityLogService->log(
            "Face descriptor removed for {$employee->name}",
            $employee,
            'face_removed',
            'face_recognition'
        );
    }

    public function loginOperator(array $descriptor): array
    {
        return DB::transaction(function () use ($descriptor) {
            $match = $this->findBestMatch($descriptor);

            if (!$match) {
                $this->logAttempt(null, null, 'operator_login_failed', null, null, $descriptor);

                return [
                    'success' => false,
                    'message' => 'Face not recognized. Please contact supervisor for enrollment.',
                ];
            }

            $employee = $match['employee']->load('faceMachine');
            $machine = $employee->faceMachine;

            if ($match['confidence'] <= self::OPERATOR_LOGIN_MIN_CONFIDENCE) {
                $this->logAttempt($employee->id, $machine?->id, 'operator_login_failed', $match['confidence'], $match['distance'], $descriptor);

                return [
                    'success' => false,
                    'message' => 'Face authentication confidence is too low. Please try again.',
                ];
            }

            if (!$machine || !$machine->is_active) {
                $this->logAttempt($employee->id, $machine?->id, 'access_denied', $match['confidence'], $match['distance'], $descriptor);

                return [
                    'success' => false,
                    'message' => 'No active machine is assigned to your face registration.',
                ];
            }

            $currentShift = Shift::getCurrentShift();

            if (!$currentShift) {
                $this->logAttempt($employee->id, $machine->id, 'access_denied', $match['confidence'], $match['distance'], $descriptor);

                return [
                    'success' => false,
                    'message' => 'No active shift found.',
                ];
            }

            $existingSession = OperatorSession::where('employee_id', $employee->id)
                ->where('machine_id', $machine->id)
                ->where('status', 'active')
                ->latest('login_time')
                ->first();

            if (!$existingSession) {
                $machineSession = OperatorSession::getActiveSessionForMachine($machine->id);

                if ($machineSession && $machineSession->employee_id !== $employee->id) {
                    $machineSession->logout();
                    $this->activityLogService->log(
                        "Operator {$machineSession->employee->name} logged out from {$machine->name}",
                        $machineSession,
                        'face_auto_logout',
                        'operator_portal'
                    );
                }

                $existingSession = OperatorSession::create([
                    'employee_id' => $employee->id,
                    'machine_id' => $machine->id,
                    'shift_id' => $currentShift->id,
                    'login_time' => now(),
                    'status' => 'active',
                    'remarks' => 'Operator face login panel',
                ]);

                $machine->update(['status' => 'running']);
            }

            $this->logAttempt($employee->id, $machine->id, 'operator_login', $match['confidence'], $match['distance'], $descriptor);

            return [
                'success' => true,
                'message' => "Welcome, {$employee->name}",
                'data' => [
                    'employee' => $employee,
                    'machine' => $machine,
                    'shift' => $currentShift,
                    'session' => $existingSession,
                    'confidence' => round($match['confidence'], 4),
                ],
            ];
        });
    }

    public function processAttendance(array $descriptor, int $machineId, string $action): array
    {
        return DB::transaction(function () use ($descriptor, $machineId, $action) {
            $match = $this->findBestMatch($descriptor);

            if (!$match) {
                $this->logAttempt(null, $machineId, 'unknown_face', null, null, $descriptor);

                return [
                    'success' => false,
                    'message' => 'Face not recognized. Please enroll this operator first.',
                    'action' => 'unknown_face',
                ];
            }

            $employee = $match['employee'];
            $machine = Machine::find($machineId);

            if ($employee->status !== 'active') {
                $this->logAttempt($employee->id, $machineId, 'access_denied', $match['confidence'], $match['distance'], $descriptor);

                return [
                    'success' => false,
                    'message' => 'Employee account is not active.',
                    'action' => 'access_denied',
                ];
            }

            if (!$machine || !$machine->is_active) {
                $this->logAttempt($employee->id, $machineId, 'access_denied', $match['confidence'], $match['distance'], $descriptor);

                return [
                    'success' => false,
                    'message' => 'Machine is not available.',
                    'action' => 'access_denied',
                ];
            }

            if ($action === 'face_out') {
                return $this->processFaceOut($employee, $machine, $match, $descriptor);
            }

            return $this->processFaceIn($employee, $machine, $match, $descriptor);
        });
    }

    public function findBestMatch(array $descriptor): ?array
    {
        $normalized = $this->normalizeDescriptor($descriptor);
        $best = null;

        Employee::active()
            ->withFaceDescriptor()
            ->get()
            ->each(function (Employee $employee) use ($normalized, &$best) {
                foreach ($this->normalizeStoredDescriptors($employee->face_descriptor ?? []) as $storedDescriptor) {
                    if (count($storedDescriptor) !== count($normalized)) {
                        continue;
                    }

                    $distance = $this->euclideanDistance($normalized, $storedDescriptor);

                    if (!$best || $distance < $best['distance']) {
                        $best = [
                            'employee' => $employee,
                            'distance' => $distance,
                            'confidence' => max(0, 1 - ($distance / self::MATCH_THRESHOLD)),
                        ];
                    }
                }
            });

        if (!$best || $best['distance'] > self::MATCH_THRESHOLD) {
            return null;
        }

        return $best;
    }

    private function processFaceIn(Employee $employee, Machine $machine, array $match, array $descriptor): array
    {
        $existingSession = OperatorSession::getActiveSessionForMachine($machine->id);

        if ($existingSession) {
            $existingSession->logout();
            $this->activityLogService->log(
                "Operator {$existingSession->employee->name} logged out from {$machine->name}",
                $existingSession,
                'face_auto_logout',
                'face_recognition'
            );
        }

        $currentShift = Shift::getCurrentShift();

        if (!$currentShift) {
            $this->logAttempt($employee->id, $machine->id, 'access_denied', $match['confidence'], $match['distance'], $descriptor);

            return [
                'success' => false,
                'message' => 'No active shift found.',
                'action' => 'access_denied',
            ];
        }

        $session = OperatorSession::create([
            'employee_id' => $employee->id,
            'machine_id' => $machine->id,
            'shift_id' => $currentShift->id,
            'login_time' => now(),
            'status' => 'active',
            'remarks' => 'Browser face recognition attendance',
        ]);

        $machine->update(['status' => 'running']);
        $this->logAttempt($employee->id, $machine->id, 'face_in', $match['confidence'], $match['distance'], $descriptor);

        $this->activityLogService->log(
            "Operator {$employee->name} logged in to {$machine->name} by face recognition",
            $session,
            'face_login',
            'face_recognition'
        );

        return [
            'success' => true,
            'message' => "Welcome, {$employee->name}",
            'action' => 'face_in',
            'data' => [
                'employee' => $employee->load(['department', 'designation']),
                'machine' => $machine,
                'shift' => $currentShift,
                'session' => $session,
                'confidence' => round($match['confidence'], 4),
            ],
        ];
    }

    private function processFaceOut(Employee $employee, Machine $machine, array $match, array $descriptor): array
    {
        $session = OperatorSession::where('machine_id', $machine->id)
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            $this->logAttempt($employee->id, $machine->id, 'access_denied', $match['confidence'], $match['distance'], $descriptor);

            return [
                'success' => false,
                'message' => 'No active session found for this operator.',
            ];
        }

        $session->logout();
        $machine->update(['status' => 'idle']);
        $this->logAttempt($employee->id, $machine->id, 'face_out', $match['confidence'], $match['distance'], $descriptor);

        $this->activityLogService->log(
            "Operator {$employee->name} logged out from {$machine->name} by face recognition",
            $session,
            'face_logout',
            'face_recognition'
        );

        return [
            'success' => true,
            'message' => "Goodbye, {$employee->name}",
            'action' => 'face_out',
            'data' => [
                'session' => $session,
                'duration_minutes' => $session->getDurationInMinutes(),
                'confidence' => round($match['confidence'], 4),
            ],
        ];
    }

    private function logAttempt(?int $employeeId, ?int $machineId, string $action, ?float $confidence, ?float $distance, array $descriptor): void
    {
        FaceAttendanceLog::create([
            'employee_id' => $employeeId,
            'machine_id' => $machineId,
            'action' => $action,
            'confidence' => $confidence,
            'distance' => $distance,
            'raw_descriptor' => $this->normalizeDescriptor($descriptor),
            'ip_address' => request()?->ip(),
        ]);
    }

    private function normalizeDescriptor(array $descriptor): array
    {
        return array_values(array_map('floatval', $descriptor));
    }

    private function normalizeStoredDescriptors(array $descriptors): array
    {
        if ($this->isSingleDescriptor($descriptors)) {
            return [$this->normalizeDescriptor($descriptors)];
        }

        return collect($descriptors)
            ->filter(fn ($descriptor) => is_array($descriptor) && count($descriptor) === self::DESCRIPTOR_LENGTH)
            ->map(fn (array $descriptor) => $this->normalizeDescriptor($descriptor))
            ->values()
            ->all();
    }

    private function isSingleDescriptor(array $descriptor): bool
    {
        return count($descriptor) === self::DESCRIPTOR_LENGTH
            && collect($descriptor)->every(fn ($value) => is_numeric($value));
    }

    private function euclideanDistance(array $left, array $right): float
    {
        $sum = 0.0;

        foreach ($left as $index => $value) {
            $difference = $value - $right[$index];
            $sum += $difference * $difference;
        }

        return sqrt($sum);
    }
}
