<?php

namespace App\Http\Controllers;

use App\Models\ProductionApproval;
use App\Models\ProductionRun;
use App\Services\OeeService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(
        protected OeeService $oeeService
    ) {
        
    }

    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'all');
       // print_r($statusFilter);
       // exit();
        $pendingQuery = ProductionRun::with(['machine', 'operator', 'product', 'shift', 'approval.submittedBy'])
            ->where('status', 'in_progress')
            ->orderByDesc('start_time');

        $approvedQuery = ProductionRun::with(['machine', 'operator', 'product', 'shift', 'approval.submittedBy'])
            ->where('status', 'completed')
            ->orderByDesc('end_time');

        if ($request->has('date')) {
            $pendingQuery->whereDate('start_time', $request->date);
            $approvedQuery->whereDate('end_time', $request->date);
        }

        $pendingCount = $pendingQuery->count();
        $approvedCount = $approvedQuery->count();

        $pendingRuns = $pendingQuery->paginate(15);
        $approvedRuns = $approvedQuery->paginate(15);

        return view('approvals.index', compact(
            'pendingRuns', 'approvedRuns', 'pendingCount', 'approvedCount', 'statusFilter'
        ));
    }

    public function show(ProductionApproval $approval)
    { 
        
        $approval->load(['productionRun', 'productionRun.machine', 'productionRun.operator', 'productionRun.product', 'submittedBy', 'reviewedBy']);
        return view('approvals.show', compact('approval'));
    }

    public function approve(Request $request, ProductionApproval $approval)
    {
        if ($approval->status !== 'pending') {
            return back()->with('error', 'This approval has already been processed.');
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
            'total_reject' => 'required|integer|min:0',
            'total_actual_count' => 'required|integer|min:1',
        ]);

        $approval->approve(auth()->user()->employee_id, $validated['comments'] ?? null, $validated['total_reject'], $validated['total_actual_count']);

        $this->activityLog()->log(
            "Production run #{$approval->production_run_id} approved by " . auth()->user()->name,
            $approval,
            'approved',
            'approvals'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Production data approved successfully.');
    }

    public function reject(Request $request, ProductionApproval $approval)
    {
        if ($approval->status !== 'pending') {
            return back()->with('error', 'This approval has already been processed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $approval->reject(auth()->user()->employee_id, $validated['reason']);

        $this->activityLog()->log(
            "Production run #{$approval->production_run_id} rejected: {$validated['reason']}",
            $approval,
            'rejected',
            'approvals'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Production data rejected.');
    }

    public function apiPending()
    {
        $approvals = ProductionApproval::with(['productionRun.machine', 'productionRun.operator', 'productionRun.product'])
            ->pending()
            ->latest('submitted_at')
            ->paginate(25);

        return response()->json($approvals);
    }
}
