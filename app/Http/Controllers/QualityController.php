<?php

namespace App\Http\Controllers;

use App\Models\QualityInspection;
use App\Models\Product;
use App\Models\Machine;
use App\Models\Employee;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index(Request $request)
    {
        $query = QualityInspection::with(['product', 'inspector', 'machine'])
            ->orderByDesc('inspection_date');

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        if ($request->has('result')) {
            $query->where('result', $request->result);
        }

        $inspections = $query->paginate(25);
        return view('quality.index', compact('inspections'));
    }

    public function create()
    {
        $products = Product::active()->get();
        $machines = Machine::active()->get();
        $inspectors = Employee::active()->get();
        return view('quality.create', compact('products', 'machines', 'inspectors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'production_run_id' => 'nullable|exists:production_runs,id',
            'product_id' => 'required|exists:products,id',
            'inspector_id' => 'required|exists:employees,id',
            'machine_id' => 'nullable|exists:machines,id',
            'inspection_date' => 'required|date',
            'inspection_type' => 'required|in:first_piece,in_process,final,patrol,receiving',
            'sample_size' => 'required|integer|min:1',
            'passed_quantity' => 'required|integer|min:0',
            'failed_quantity' => 'required|integer|min:0',
            'measurements' => 'nullable|array',
            'remarks' => 'nullable|string',
            'result' => 'required|in:pass,fail,conditional',
        ]);

        if ($validated['passed_quantity'] + $validated['failed_quantity'] > $validated['sample_size']) {
            return back()->withErrors([
                'failed_quantity' => 'Passed + Failed cannot exceed sample size.',
            ]);
        }

        $inspection = QualityInspection::create($validated);

        $this->activityLog()->log(
            "Quality inspection for {$inspection->product->name}: {$inspection->result}",
            $inspection,
            'created',
            'quality'
        );

        return redirect()->route('quality.index')
            ->with('success', 'Quality inspection recorded successfully.');
    }

    public function show(QualityInspection $inspection)
    {
        $inspection->load(['product', 'inspector', 'machine']);
        return view('quality.show', compact('inspection'));
    }

    public function edit(QualityInspection $inspection)
    {
        $products = Product::active()->get();
        $machines = Machine::active()->get();
        $inspectors = Employee::active()->get();
        return view('quality.edit', compact('inspection', 'products', 'machines', 'inspectors'));
    }

    public function update(Request $request, QualityInspection $inspection)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'inspector_id' => 'required|exists:employees,id',
            'machine_id' => 'nullable|exists:machines,id',
            'inspection_date' => 'required|date',
            'inspection_type' => 'required|in:first_piece,in_process,final,patrol,receiving',
            'sample_size' => 'required|integer|min:1',
            'passed_quantity' => 'required|integer|min:0',
            'failed_quantity' => 'required|integer|min:0',
            'measurements' => 'nullable|array',
            'remarks' => 'nullable|string',
            'result' => 'required|in:pass,fail,conditional',
        ]);

        $inspection->update($validated);

        return redirect()->route('quality.index')
            ->with('success', 'Quality inspection updated successfully.');
    }

    public function destroy(QualityInspection $inspection)
    {
        $inspection->delete();

        return redirect()->route('quality.index')
            ->with('success', 'Quality inspection deleted successfully.');
    }

    public function apiIndex(Request $request)
    {
        $query = QualityInspection::with(['product', 'inspector']);

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        return response()->json($query->latest('inspection_date')->paginate(25));
    }
}
