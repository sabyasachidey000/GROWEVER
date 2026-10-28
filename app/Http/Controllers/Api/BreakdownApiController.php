<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MachineBreakdown;
use Illuminate\Http\Request;

class BreakdownApiController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineBreakdown::with(['machine', 'reportedBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
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
            'reported_by' => 'required|exists:employees,id',
            'breakdown_type' => 'required|in:mechanical,electrical,hydraulic,pneumatic,tool_failure,material_shortage,software,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
        ]);

        $validated['start_time'] = now();
        $validated['status'] = 'open';

        $breakdown = MachineBreakdown::create($validated);
        $breakdown->machine->update(['status' => 'breakdown']);

        return response()->json($breakdown, 201);
    }

    public function show(MachineBreakdown $breakdown)
    {
        return response()->json($breakdown->load(['machine', 'reportedBy', 'assignedTo']));
    }
}
