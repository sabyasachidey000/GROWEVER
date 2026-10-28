<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductionApproval;

class ApprovalApiController extends Controller
{
    public function pending()
    {
        return response()->json(
            ProductionApproval::with(['productionRun.machine', 'productionRun.operator', 'productionRun.product'])
                ->pending()
                ->latest('submitted_at')
                ->paginate(25)
        );
    }

    public function approve(ProductionApproval $approval)
    {
        if ($approval->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        $approval->approve(auth()->user()->employee_id, request('comments'));

        return response()->json(['message' => 'Approved successfully']);
    }

    public function reject(ProductionApproval $approval)
    {
        if ($approval->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        $approval->reject(auth()->user()->employee_id, request('reason'));

        return response()->json(['message' => 'Rejected']);
    }
}
