<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Machine;
use App\Models\MachineBreakdown;
use App\Models\RfidLog;
use App\Models\EmployeeRfidPunch;
use App\Models\OperatorSession;
use App\Models\ProductionRun;
use App\Models\ProductionApproval;
use App\Services\RfidService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\WhatsAppController;
use App\Models\RfidLimitSwitchLog;
use Illuminate\Support\Facades\Log;
use DB;

class RfidController extends Controller
{
    public function __construct(
        protected RfidService $rfidService,
        protected NotificationService $notificationService
    ) {
    }

    // RFID Management - assign/unassign cards
    public function index()
    {
        $assignments = OperatorSession::with(['employee', 'machine'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('machine_id');

        $machines = Machine::orderBy('machine_code')->get();
        $employees = Employee::active()->orderBy('name')->get();

        return view('rfid.index', compact('machines', 'employees', 'assignments'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'employee_id' => 'required|exists:employees,id',
            'rfid_card_number' => 'required|string|max:100',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        if ($employee->rfid_card_number) {
            return back()->with('error', 'This operator already has an RFID card assigned. Unassign first.');
        }

        $existing = Employee::where('rfid_card_number', $validated['rfid_card_number'])->first();
        if ($existing) {
            return back()->with('error', "This RFID card is already assigned to {$existing->name}.");
        }

        $employee->update(['rfid_card_number' => $validated['rfid_card_number']]);

        $this->activityLog()->log(
            "RFID card {$validated['rfid_card_number']} tagged to {$employee->name}",
            $employee,
            'rfid_tagged',
            'rfid'
        );

        return back()->with('success', "RFID card tagged to {$employee->name}.");
    }

    public function unassign(Employee $employee)
    {
        $cardNumber = $employee->rfid_card_number;
        $employee->update(['rfid_card_number' => null]);

        $this->activityLog()->log(
            "RFID card {$cardNumber} untagged from {$employee->name}",
            $employee,
            'rfid_untagged',
            'rfid'
        );

        return back()->with('success', "RFID card untagged from {$employee->name}.");
    }

    // RFID Swipe page - process swipes, logs, force logout
    public function swipe()
    {
        $machines = Machine::with(['activeBreakdown'])
            ->orderBy('machine_code')
            ->get();

        foreach ($machines as $machine) {
            $machine->currentOperator = $this->rfidService->getCurrentOperator($machine->id);
        }

        $activeSessions = OperatorSession::with(['employee', 'machine', 'shift'])
            ->where('status', 'active')
            ->latest('login_time')
            ->get();

        $logs = RfidLog::with(['employee', 'machine'])
            ->latest()
            ->paginate(50);

        return view('rfid.swipe', compact('machines', 'activeSessions', 'logs'));
    }

    public function processSwipe(Request $request)
    {
        $validated = $request->validate([
            'rfid_card_number' => 'required|string|max:100',
            'machine_id' => 'required|exists:machines,id',
            'action' => 'required|in:swipe_in,swipe_out',
            'reader_id' => 'nullable|string|max:50',
        ]);

        if ($validated['action'] === 'swipe_in') {
            $result = $this->rfidService->processSwipe(
                $validated['rfid_card_number'],
                $validated['machine_id'],
                $validated['reader_id'] ?? null
            );
        } else {
            $result = $this->rfidService->processLogout(
                $validated['rfid_card_number'],
                $validated['machine_id']
            );
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 400);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function forceLogout(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:operator_sessions,id',
        ]);

        $session = OperatorSession::with(['employee', 'machine'])->findOrFail($validated['session_id']);

        if ($session->status !== 'active') {
            return back()->with('error', 'Session is already closed.');
        }

        $session->logout();
        $session->machine->update(['status' => 'idle']);

        RfidLog::logSwipe(
            $session->employee->rfid_card_number ?? 'manual',
            'swipe_out',
            $session->machine_id
        );

        $this->activityLog()->log(
            "Force logged out {$session->employee->name} from {$session->machine->name}",
            $session,
            'rfid_force_logout',
            'rfid'
        );

        return back()->with('success', "{$session->employee->name} force logged out from {$session->machine->name}.");
    }

    public function apiSwipe(Request $request)
    {
        return $this->processSwipe($request);
    }

    public function logs(Request $request)
    {
        $query = RfidLog::with(['employee', 'machine'])->latest();

        if ($request->has('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        if ($request->has('action')) {
            $query->byAction($request->action);
        }

        return response()->json($query->paginate(50));
    }

    public function machineStatus(int $machineId)
    {
        $machine = Machine::with(['activeBreakdown'])->find($machineId);
        $currentSession = $this->rfidService->getCurrentOperator($machineId);

        return response()->json([
            'machine' => $machine,
            'current_operator' => $currentSession?->employee,
            'session' => $currentSession,
        ]);
    }

    /**
     * Show successful ESP32/RC522 attendance punches.
     */
    public function punchLogs(Request $request)
    {
        $laterLogout = function ($logoutQuery) {
            $logoutQuery->selectRaw('1')
                ->from('employee_rfid_punches as later_logout')
                ->whereColumn('later_logout.employee_id', 'employee_rfid_punches.employee_id')
                ->where('later_logout.punch_type', 'LOGOUT')
                ->whereRaw('(later_logout.punch_time > employee_rfid_punches.punch_time OR (later_logout.punch_time = employee_rfid_punches.punch_time AND later_logout.id > employee_rfid_punches.id))');
        };

        $query = EmployeeRfidPunch::with(['employee', 'machine'])
            ->where('punch_type', 'LOGIN')
            ->latest('punch_time');

        if ($request->filled('date')) {
            $query->whereDate('punch_time', $request->date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('machine_id')) {
            $query->where('machine_id', $request->machine_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($employeeQuery) use ($search) {
                $employeeQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('rfid_card_number', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'active') {
            $query->whereNotExists($laterLogout);
        }

        if ($request->status === 'out') {
            $query->whereExists($laterLogout);
        }

        $punches = $query->paginate(50)->withQueryString();
        $punches->getCollection()->each(function (EmployeeRfidPunch $punch) {
            $punch->logoutPunch = EmployeeRfidPunch::where('employee_id', $punch->employee_id)
                ->where('punch_type', 'LOGOUT')
                ->where(function ($query) use ($punch) {
                    $query->where('punch_time', '>', $punch->punch_time)
                        ->orWhere(function ($tieQuery) use ($punch) {
                            $tieQuery->where('punch_time', $punch->punch_time)->where('id', '>', $punch->id);
                        });
                })
                ->oldest('punch_time')
                ->oldest('id')
                ->first();
        });
        $employees = Employee::active()->orderBy('name')->get(['id', 'name', 'employee_code']);
        $machines = Machine::orderBy('machine_code')->get(['id', 'machine_code', 'name']);

        return view('rfid.punch-logs', compact('punches', 'employees', 'machines'));
    }

    /**
     * Return successful punch records for authenticated dashboard clients.
     */
    public function apiPunchLogs()
    {
        return response()->json(
            EmployeeRfidPunch::with(['employee', 'machine'])->latest('punch_time')->paginate(50)
        );
    }
    public function store(Request $request)
    {
        $request->validate([
            'device_id'  => 'required',
            'machine_id' => 'required'
        ]);

       $id = DB::table('device_logs')->insertGetId([
            'device_id'  => $request->device_id,
            'machine_id' => $request->machine_id,
            'rfid'       => $request->rfid,
            'status'     => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Saved Successfully',
            'id'      => $id
        ]);
    }
    public function punch(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
            'uid'       => 'required|string|max:100',
        ]);
        
         Log::info('API Called', [
        'api' => $request->path(),
        'method' => $request->method(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'request' => $request->except(['password', 'token']),
        'time' => now()->format('Y-m-d H:i:s'),
        ]);

        $result = $this->rfidService->processDevicePunch(
            $validated['device_id'],
            $validated['uid'],
            $request->ip()
        );

        if (! $result['success']) {
            return response()->json($result + ['registered' => false, 'assigned' => false], 422);
        }

        return response()->json([
            'success' => true,
            'duplicate' => $result['duplicate'],
            'message' => $result['message'],
            'action' => $result['action'],
            'registered' => true,
            'assigned' => true,
            'punch' => $result['punch'] ?? null,
            'raw_punch_id' => $result['raw_punch_id'],
            'machine' => ['id' => $result['machine']->id, 'code' => $result['machine']->machine_code],
            'person' => [
                'id' => $result['employee']->id,
                'employee_code' => $result['employee']->employee_code,
                'name' => $result['employee']->name,
            ],
        ], $result['duplicate'] ? 200 : 201);
    }
    public function button(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
            'machine_id' => 'nullable|integer|exists:machines,id',
            'reported_by' => 'nullable|integer|exists:employees,id',
            'description' => 'nullable|string|max:2000',
        ]);

        $deviceId = trim($validated['device_id']);
        // Device-to-machine identity is maintained on machines.rfid_device_id.
        // Never trust a client-supplied machine_id for a hardware button event.
         Log::info('API Called', [
        'api' => $request->path(),
        'method' => $request->method(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'request' => $request->except(['password', 'token']),
        'time' => now()->format('Y-m-d H:i:s'),
        ]);
        
        $machine = Machine::active()
            ->where('rfid_device_id', $deviceId)
            ->first();


        
        if (!$machine) {
            return response()->json([
                'success' => false,
                'message' => 'RFID device is not mapped to an active machine.',
            ], 422);
        }

        $reportedById = $validated['reported_by'] ?? OperatorSession::active()
            ->forMachine($machine->id)
            ->latest('login_time')
            ->value('employee_id');
        
        $reporter = $reportedById
            ? Employee::active()->find($reportedById)
            : null;

        if (!$reporter) {
            return response()->json([
                'success' => false,
                'message' => 'No active operator is assigned to this machine. Send reported_by with the request.',
                'machine_id' => $machine->id,
            ], 422);
        }

        $existing = MachineBreakdown::where('machine_id', $machine->id)
            ->whereIn('status', ['open', 'in_progress', 'resolved'])
            ->latest('start_time')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'created' => false,
                'message' => 'An active breakdown request already exists for this machine.',
                'breakdown' => $this->buttonBreakdownResponse($existing),
            ]);
        }

        $breakdown = MachineBreakdown::create([
            'machine_id' => $machine->id,
            'reported_by' => $reporter->id,
            'breakdown_type' => 'other',
            'severity' => 'high',
            'description' => $validated['description'] ?? "Breakdown requested from RFID button on device {$deviceId}.",
            'start_time' => now(),
            'status' => 'open',
        ]);
        $whatsapp = new WhatsAppController();
        $whatsapp->send($machine->name,$machine->machine_code);

        $machine->update(['status' => 'breakdown']);
        $this->notificationService->notifyMachineBreakdown(
            $machine->id,
            $breakdown->breakdown_type,
            $machine->name
        );

        return response()->json([
            'success' => true,
            'created' => true,
            'message' => 'Breakdown request created successfully.',
            'breakdown' => $this->buttonBreakdownResponse($breakdown),
        ], 201);
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
    /*public function limitSwitch(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
            'sensor'    => 'required|string|max:50',
            'status'    => 'required|string|max:20',
        ]);

        $deviceId = trim($validated['device_id']);

        // Find machine from RFID device ID
        $machine = Machine::active()
            ->where('rfid_device_id', $deviceId)
            ->first();

        if (!$machine) {
            return response()->json([
                'success' => false,
                'message' => 'RFID device is not mapped to an active machine.',
            ], 422);
        }
        //machine Breakdown
        $existing = MachineBreakdown::where('machine_id', $machine->id)
        ->whereIn('status', ['open', 'in_progress', 'resolved'])
        ->latest('start_time')
        ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'created' => false,
                'message' => 'An active breakdown request already exists for this machine.',
                'breakdown' => $this->buttonBreakdownResponse($existing),
            ]);
        }

        // Find active operator for this machine
        $reportedById = OperatorSession::active()
            ->forMachine($machine->id)
            ->latest('login_time')
            ->value('employee_id');

        // Verify employee
        $reporter = $reportedById
            ? Employee::active()->find($reportedById)
            : null;

        if (!$reporter) {
            return response()->json([
                'success' => false,
                'message' => 'No active operator is assigned to this machine.',
                'machine_id' => $machine->id,
            ], 422);
        }

        // Get previous counter
        $lastCounter = RfidLimitSwitchLog::where(
            'device_id',
            $deviceId
        )->max('counter');

        $counter = ((int) $lastCounter) + 1;

        // Save limit switch event
        $log = RfidLimitSwitchLog::create([
            'device_id' => $deviceId,
            'user_id'   => $reporter->id,
            'sensor'    => $validated['sensor'],
            'status'    => $validated['status'],
            'counter'   => $counter,
        ]);
        $operatorSession = OperatorSession::active()
        ->forMachine($machine->id)
        ->latest('login_time')
        ->first();
        $productionRun = ProductionRun::where('machine_id', $machine->id)->where('operator_id',$operatorSession->employee_id)
            ->where('status', 'in_progress')
            ->latest('start_time')
            ->first();
        $products=DB::table('machine_product')->where('machine_id',$machine->id)->first();
        if ($productionRun) {

            // Existing production → +1
            $productionRun->increment('weight', 1);

        } else {

            // No production run → CREATE
            $productionRun = ProductionRun::create([
                'machine_id'          => $machine->id,
                'operator_id'         => $operatorSession->employee_id,
                'product_id'          => $products->product_id,
                'shift_id'            => $operatorSession->shift_id,
                'operator_session_id' => $operatorSession->id,
                'start_time'          => now(),
                'runtime_minutes'     => 0,
                'weight'              => 1,
                'status'              => 'in_progress',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Limit switch recorded successfully.',
            'data' => [
                'id'         => $log->id,
                'device_id'  => $log->device_id,
                'machine_id' => $machine->id,
                'user_id'    => $reporter->id,
                'counter'    => $log->counter,
                'status'     => $log->status,
                'created_at' => $log->created_at,
            ]
        ], 200);
    }*/
    public function limitSwitch(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
            'sensor'    => 'required|string|max:50',
            'status'    => 'required|string|max:20',
        ]);

        $deviceId = trim($validated['device_id']);
         Log::info('API Called', [
        'api' => $request->path(),
        'method' => $request->method(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'request' => $request->except(['password', 'token']),
        'time' => now()->format('Y-m-d H:i:s'),
        ]);
        
        try {

            return DB::transaction(function () use ($validated, $deviceId) {

                // =====================================================
                // 1. FIND ACTIVE MACHINE
                // =====================================================

                $machine = Machine::active()
                    ->where('rfid_device_id', $deviceId)
                    ->first();

                if (!$machine) {
                    return response()->json([
                        'success' => false,
                        'message' => 'RFID device is not mapped to an active machine.',
                    ], 422);
                }


                // =====================================================
                // 2. MACHINE BREAKDOWN CHECK
                // =====================================================
                // Only open / in_progress should block production.
                // resolved should NOT block production.

                $existing = MachineBreakdown::where('machine_id', $machine->id)
                    ->whereIn('status', ['open', 'in_progress'])
                    ->latest('start_time')
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success'   => true,
                        'created'   => false,
                        'message'   => 'An active breakdown request already exists for this machine.',
                        'breakdown' => $this->buttonBreakdownResponse($existing),
                    ], 200);
                }


                // =====================================================
                // 3. FIND ACTIVE OPERATOR SESSION
                // =====================================================

                $operatorSession = OperatorSession::active()
                    ->forMachine($machine->id)
                    ->latest('login_time')
                    ->first();

                if (!$operatorSession) {
                    return response()->json([
                        'success'   => false,
                        'message'   => 'No active operator session is assigned to this machine.',
                        'machine_id' => $machine->id,
                    ], 422);
                }


                // =====================================================
                // 4. FIND OPERATOR
                // =====================================================

                $reporter = Employee::active()
                    ->find($operatorSession->employee_id);

                if (!$reporter) {
                    return response()->json([
                        'success'    => false,
                        'message'    => 'Active operator employee not found.',
                        'machine_id' => $machine->id,
                    ], 422);
                }


                // =====================================================
                // 5. FIND PRODUCT
                // =====================================================

                $product = DB::table('machine_product')
                    ->where('machine_id', $machine->id)
                    ->first();

                if (!$product) {
                    return response()->json([
                        'success'    => false,
                        'message'    => 'No product is assigned to this machine.',
                        'machine_id' => $machine->id,
                    ], 422);
                }


                // =====================================================
                // 6. GET LAST COUNTER
                // =====================================================
                // Lock the latest record to reduce concurrent counter
                // conflicts.

                $lastCounterLog = RfidLimitSwitchLog::where(
                    'device_id',
                    $deviceId
                )
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                $counter = $lastCounterLog
                    ? ((int) $lastCounterLog->counter + 1)
                    : 1;


                // =====================================================
                // 7. SAVE LIMIT SWITCH LOG
                // =====================================================

                $log = RfidLimitSwitchLog::create([
                    'device_id' => $deviceId,
                    'user_id'   => $reporter->id,
                    'sensor'    => $validated['sensor'],
                    'status'    => $validated['status'],
                    'counter'   => $counter,
                ]);


                // =====================================================
                // 8. FIND CURRENT PRODUCTION RUN
                // =====================================================

                $productionRun = ProductionRun::where(
                    'machine_id',
                    $machine->id
                )
                    ->where(
                        'operator_id',
                        $operatorSession->employee_id
                    )
                    ->where('status', 'in_progress')
                    ->latest('start_time')
                    ->lockForUpdate()
                    ->first();


                // =====================================================
                // 9. UPDATE / CREATE PRODUCTION RUN
                // =====================================================

                if ($productionRun) {

                    // Existing production
                   // $productionRun->increment('weight', 1);

                    //$productionRun->refresh();

                } else {

                    // No active production run
                   /* $productionRun = ProductionRun::create([
                        'machine_id'          => $machine->id,
                        'operator_id'         => $operatorSession->employee_id,
                        'product_id'          => $product->product_id,
                        'shift_id'            => $operatorSession->shift_id,
                        'operator_session_id' => $operatorSession->id,
                        'start_time'          => now(),
                        'runtime_minutes'     => 0,
                        'weight'              => 1,
                        'status'              => 'in_progress',
                    ]);*/
                }


                // =====================================================
                // 10. SUCCESS RESPONSE
                // =====================================================

                return response()->json([
                    'success' => true,
                    'message' => 'Limit switch recorded successfully.',

                    'data' => [
                        'id'         => $log->id,
                        'device_id'  => $log->device_id,
                        'machine_id' => $machine->id,
                        'user_id'    => $reporter->id,
                        'counter'    => $log->counter,
                        'status'     => $log->status,
                        'created_at' => $log->created_at,

                        
                    ]

                ], 200);

            });

        } catch (\Throwable $e) {

            \Log::error('Limit Switch API Error', [
                'device_id' => $deviceId,
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Limit switch processing failed.',
                'error'   => config('app.debug')
                    ? $e->getMessage()
                    : 'Internal server error.',
            ], 500);
        }
    }
    public function breakdownStatus(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
        ]);
        
        $deviceId = trim($validated['device_id']);
        Log::info('API Called', [
        'api' => $request->path(),
        'method' => $request->method(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'request' => $request->except(['password', 'token']),
        'time' => now()->format('Y-m-d H:i:s'),
        ]);
        $machine = Machine::active()
            ->where('rfid_device_id', $deviceId)
            ->first();

        if (!$machine) {
            return response()->json([
                'success' => false,
                'message' => 'RFID device is not mapped to an active machine.',
                'breakdown' => false,
            ], 422);
        }

        $breakdown = MachineBreakdown::where('machine_id', $machine->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->exists();

        return response()->json([
            'success' => true,
            'machine_id' => $machine->id,
            'breakdown' => $breakdown,
        ]);
    }
}
