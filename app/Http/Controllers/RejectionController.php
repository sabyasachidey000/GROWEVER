<?php

namespace App\Http\Controllers;

use App\Models\Rejection;
use App\Models\Product;
use App\Models\Machine;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;

class RejectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Rejection::with(['product', 'machine', 'operator', 'shift'])
            ->orderByDesc('rejection_date');

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        if ($request->has('product_id')) {
            $query->forProduct($request->product_id);
        }

        if ($request->has('machine_id')) {
            $query->forMachine($request->machine_id);
        }

        if ($request->has('reason')) {
            $query->byReason($request->reason);
        }

        $rejections = $query->paginate(25);
        return view('rejections.index', compact('rejections'));
    }

    public function create()
    {
        $products = Product::active()->get();
        $machines = Machine::active()->get();
        $operators = Employee::active()->get();
        $shifts = Shift::active()->get();
        return view('rejections.create', compact('products', 'machines', 'operators', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'production_run_id' => 'nullable|exists:production_runs,id',
            'machine_id' => 'nullable|exists:machines,id',
            'product_id' => 'required|exists:products,id',
            'operator_id' => 'nullable|exists:employees,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'rejection_reason' => 'required|in:crack,dimension_error,surface_defect,burr,material_defect,tool_mark,porosity,misrun,other',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'rejection_date' => 'required|date',
        ]);

        $rejection = Rejection::create($validated);

        $this->activityLog()->log(
            "Rejection recorded: {$validated['quantity']} units of {$rejection->product->name} - {$rejection->rejection_reason}",
            $rejection,
            'created',
            'rejections'
        );

        return redirect()->route('rejections.index')
            ->with('success', 'Rejection recorded successfully.');
    }

    public function show(Rejection $rejection)
    {
        $rejection->load(['product', 'machine', 'operator', 'shift']);
        return view('rejections.show', compact('rejection'));
    }

    public function edit(Rejection $rejection)
    {
        $products = Product::active()->get();
        $machines = Machine::active()->get();
        $operators = Employee::active()->get();
        $shifts = Shift::active()->get();
        return view('rejections.edit', compact('rejection', 'products', 'machines', 'operators', 'shifts'));
    }

    public function update(Request $request, Rejection $rejection)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rejection_reason' => 'required|in:crack,dimension_error,surface_defect,burr,material_defect,tool_mark,porosity,misrun,other',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'rejection_date' => 'required|date',
        ]);

        $rejection->update($validated);

        return redirect()->route('rejections.index')
            ->with('success', 'Rejection updated successfully.');
    }

    public function destroy(Rejection $rejection)
    {
        $rejection->delete();

        return redirect()->route('rejections.index')
            ->with('success', 'Rejection deleted successfully.');
    }

    public function analysis(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $byReason = Rejection::getAnalysisByReason($startDate, $endDate);
        $byProduct = Rejection::whereBetween('rejection_date', [$startDate, $endDate])
            ->with('product')
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->get();
        $byMachine = Rejection::whereBetween('rejection_date', [$startDate, $endDate])
            ->with('machine')
            ->select('machine_id')
            ->selectRaw('SUM(quantity) as total')
            ->groupBy('machine_id')
            ->orderByDesc('total')
            ->get();
        $totalQuantity = Rejection::whereBetween('rejection_date', [$startDate, $endDate])->sum('quantity');

        return view('rejections.analysis', compact('byReason', 'byProduct', 'byMachine', 'totalQuantity', 'startDate', 'endDate'));
    }

    public function apiIndex(Request $request)
    {
        $query = Rejection::with(['product', 'machine']);

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        return response()->json($query->latest('rejection_date')->paginate(25));
    }
}
