<?php

namespace App\Http\Controllers;

use App\Models\ProductionRun;
use App\Models\ProductionApproval;
use App\Models\Machine;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Shift;
use App\Models\OperatorSession;
use App\Services\NotificationService;
use App\Services\OeeService;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected OeeService $oeeService
    ) {
        
    }

    public function index(Request $request)
    {
        $query = ProductionRun::with(['machine', 'operator', 'product', 'shift', 'approval'])
            ->orderByDesc('start_time');

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        if ($request->has('shift_id')) {
            $query->forShift($request->shift_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $productionRuns = $query->paginate(25);

        return view('production.index', compact('productionRuns'));
    }

    public function create()
    {
        $operatorIds = OperatorSession::active()->pluck('employee_id');
        $machines = Machine::running()->orWhere('status', 'idle')->get();
        $operators = Employee::active()->whereIn('id', $operatorIds)->get();
        $products = Product::active()->get();
        $shifts = Shift::active()->get();
        return view('production.create', compact('machines', 'operators', 'products', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'operator_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:products,id',
            'shift_id' => 'required|exists:shifts,id',
            'operator_session_id' => 'nullable|exists:operator_sessions,id',
            'start_time' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'status' => 'required|in:in_progress,completed,stopped',
        ]);

        $validated['runtime_minutes'] = now()->diffInMinutes($validated['start_time']);
        if ($validated['status'] === 'completed') {
            $validated['end_time'] = now();
        }

        $productionRun = ProductionRun::create($validated);

        // Create approval record
        $approval = ProductionApproval::create([
            'production_run_id' => $productionRun->id,
            'submitted_by' => auth()->user()->employee_id ?? $validated['operator_id'],
            'submitted_at' => now(),
            'status' => 'pending',
        ]);

        // Notify supervisors
        $this->notificationService->notifyApprovalPending(
            $approval->id,
            $productionRun->operator->name
        );

        $this->activityLog()->log(
            "Production run created for {$productionRun->product->name} on {$productionRun->machine->name}",
            $productionRun,
            'created',
            'production'
        );

        return redirect()->route('production.index')
            ->with('success', 'Production entry created successfully.');
    }

    public function show(ProductionRun $productionRun)
    {
        $productionRun->load(['machine', 'operator', 'product', 'shift', 'approval', 'rejections']);
        return view('production.show', compact('productionRun'));
    }

    public function edit(ProductionRun $productionRun)
    {
        if ($productionRun->approval?->is_locked) {
            return redirect()->route('production.index')
                ->with('error', 'This record has been approved and cannot be edited.');
        }

        $machines = Machine::active()->get();
        $operators = Employee::active()->get();
        $products = Product::active()->get();
        $shifts = Shift::active()->get();

        return view('production.edit', compact('productionRun', 'machines', 'operators', 'products', 'shifts'));
    }

    public function update(Request $request, ProductionRun $productionRun)
    {
        if ($productionRun->approval?->is_locked) {
            return back()->with('error', 'This record has been approved and cannot be edited.');
        }

        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'operator_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:products,id',
            'shift_id' => 'required|exists:shifts,id',
            'weight' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'status' => 'required|in:in_progress,completed,stopped',
        ]);

        if ($validated['status'] === 'completed' && !$productionRun->end_time) {
            $validated['end_time'] = now();
            $validated['runtime_minutes'] = $productionRun->start_time->diffInMinutes(now());
        }

        $productionRun->update($validated);

        $this->activityLog()->log(
            "Production run #{$productionRun->id} updated",
            $productionRun,
            'updated',
            'production'
        );

        return redirect()->route('production.index')
            ->with('success', 'Production entry updated successfully.');
    }

    public function complete(ProductionRun $productionRun)
    {
        $productionRun->complete();

        // Calculate OEE
        $this->oeeService->calculateOee(
            $productionRun->machine_id,
            $productionRun->start_time->toDateString(),
            $productionRun->shift_id
        );

        $this->activityLog()->log(
            "Production run #{$productionRun->id} completed",
            $productionRun,
            'completed',
            'production'
        );

        return back()->with('success', 'Production run completed.');
    }

    public function submitForApproval(ProductionRun $productionRun)
    {
        $approval = $productionRun->approval;

        if (!$approval) {
            $approval = ProductionApproval::create([
                'production_run_id' => $productionRun->id,
                'submitted_by' => auth()->user()->employee_id,
                'submitted_at' => now(),
                'status' => 'pending',
            ]);
        } else {
            $approval->update([
                'status' => 'pending',
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_comments' => null,
                'rejection_reason' => null,
            ]);
        }

        $this->notificationService->notifyApprovalPending(
            $approval->id,
            auth()->user()->name
        );

        $this->activityLog()->log(
            "Production run #{$productionRun->id} submitted for approval",
            $productionRun,
            'submitted_for_approval',
            'production'
        );

        return back()->with('success', 'Submitted for approval.');
    }

    // API Methods
    public function apiIndex(Request $request)
    {
        $query = ProductionRun::with(['machine', 'operator', 'product', 'shift']);

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        return response()->json($query->latest()->paginate(25));
    }

    public function apiMachineData($machineId)
    {
        // Active operator sessions on this machine
        $operators = OperatorSession::with('employee')
            ->where('machine_id', $machineId)
            ->where('status', 'active')
            ->get()
            ->pluck('employee');

        // Products tagged to this machine
        $products = Machine::find($machineId)?->products()->active()->get() ?? collect();

        return response()->json([
            'operators' => $operators,
            'products' => $products,
        ]);
    }

    public function apiOperatorData($operatorId)
    {
        // Find active session for this operator
        $session = OperatorSession::with('machine')
            ->where('employee_id', $operatorId)
            ->where('status', 'active')
            ->latest('login_time')
            ->first();

        if (!$session) {
            return response()->json([
                'machine' => null,
                'products' => collect(),
                'message' => 'No active machine session for this operator.',
            ]);
        }

        $products = $session->machine->products()->active()->get();

        return response()->json([
            'machine' => $session->machine,
            'session_id' => $session->id,
            'shift_id' => $session->shift_id,
            'products' => $products,
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'operator_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:products,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'runtime_minutes' => 'nullable|integer',
            'weight' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'status' => 'required|in:in_progress,completed,stopped',
        ]);

        $productionRun = ProductionRun::create($validated);

        return response()->json($productionRun->load(['machine', 'operator', 'product']), 201);
    }
}
