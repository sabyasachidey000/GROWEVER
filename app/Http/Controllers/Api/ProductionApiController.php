<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductionRun;
use Illuminate\Http\Request;

class ProductionApiController extends Controller
{
    public function index(Request $request)
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

    public function store(Request $request)
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

    public function show(ProductionRun $productionRun)
    {
        return response()->json($productionRun->load(['machine', 'operator', 'product', 'shift', 'approval']));
    }
}
