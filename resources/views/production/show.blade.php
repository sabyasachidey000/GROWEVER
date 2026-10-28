@extends('layouts.app')
@section('title', 'Production Details - MES')
@section('page-title', 'Production Run #' . $productionRun->id)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Production Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">Machine</th><td>{{ $productionRun->machine?->name ?? 'N/A' }}</td></tr>
                    <tr><th>Operator</th><td>{{ $productionRun->operator?->name ?? 'N/A' }}</td></tr>
                    <tr><th>Product</th><td>{{ $productionRun->product?->name ?? 'N/A' }}</td></tr>
                    <tr><th>Shift</th><td>{{ $productionRun->shift?->name ?? 'N/A' }}</td></tr>
                    <tr><th>Start Time</th><td>{{ $productionRun->start_time?->format('d M Y H:i') ?? '-' }}</td></tr>
                    <tr><th>End Time</th><td>{{ $productionRun->end_time?->format('d M Y H:i') ?? '-' }}</td></tr>
                    <tr><th>Runtime</th><td>{{ $productionRun->runtime_minutes }} minutes</td></tr>
                    <tr><th>Weight</th><td class="fw-bold">{{ $productionRun->weight ? number_format($productionRun->weight, 2) . ' kg' : '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $productionRun->status === 'completed' ? 'success' : 'primary' }}">{{ $productionRun->status }}</span></td></tr>
                    <tr><th>Remarks</th><td>{{ $productionRun->remarks ?? '-' }}</td></tr>
                </table>
            </div>
        </div>

        @if($productionRun->approval)
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Approval Status</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">Status</th><td><span class="badge bg-{{ $productionRun->approval->status === 'approved' ? 'success' : ($productionRun->approval->status === 'rejected' ? 'danger' : 'warning') }}">{{ $productionRun->approval->status }}</span></td></tr>
                    <tr><th>Submitted By</th><td>{{ $productionRun->approval->submittedBy?->name ?? 'N/A' }}</td></tr>
                    <tr><th>Submitted At</th><td>{{ $productionRun->approval->submitted_at->format('d M Y H:i') }}</td></tr>
                    @if($productionRun->approval->reviewedBy)
                    <tr><th>Reviewed By</th><td>{{ $productionRun->approval->reviewedBy->name }}</td></tr>
                    <tr><th>Reviewed At</th><td>{{ $productionRun->approval->reviewed_at?->format('d M Y H:i') }}</td></tr>
                    @endif
                    @if($productionRun->approval->review_comments)
                    <tr><th>Comments</th><td>{{ $productionRun->approval->review_comments }}</td></tr>
                    @endif
                    @if($productionRun->approval->rejection_reason)
                    <tr><th>Rejection Reason</th><td class="text-danger">{{ $productionRun->approval->rejection_reason }}</td></tr>
                    @endif
                    <tr><th>Locked</th><td>{{ $productionRun->approval->is_locked ? 'Yes (Cannot Edit)' : 'No' }}</td></tr>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Rejections</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Reason</th><th>Qty</th></tr></thead>
                    <tbody>
                        @forelse($productionRun->rejections as $rej)
                        <tr><td>{{ $rej->rejection_reason }}</td><td>{{ $rej->quantity }}</td></tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">No rejections</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('production.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back to List</a>
</div>
@endsection
