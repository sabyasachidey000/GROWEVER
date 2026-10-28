@extends('layouts.app')
@section('title', 'Approval Details - MES')
@section('page-title', 'Approval #' . $approval->id)
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Approval Details</h5></div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">Production Run</th><td>#{{ $approval->production_run_id }}</td></tr>
                    <tr><th>Machine</th><td>{{ $approval->productionRun->machine->name }}</td></tr>
                    <tr><th>Operator</th><td>{{ $approval->productionRun->operator->name }}</td></tr>
                    <tr><th>Product</th><td>{{ $approval->productionRun->product->name }}</td></tr>
                    <tr><th>Weight</th><td>{{ $approval->productionRun->weight ? number_format($approval->productionRun->weight, 2) . ' kg' : '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($approval->status) }}</span></td></tr>
                    <tr><th>Submitted By</th><td>{{ $approval->submittedBy->name }}</td></tr>
                    <tr><th>Submitted At</th><td>{{ $approval->submitted_at->format('d M Y H:i') }}</td></tr>
                    @if($approval->reviewedBy)
                    <tr><th>Reviewed By</th><td>{{ $approval->reviewedBy->name }}</td></tr>
                    <tr><th>Reviewed At</th><td>{{ $approval->reviewed_at?->format('d M Y H:i') }}</td></tr>
                    @endif
                    @if($approval->review_comments)
                    <tr><th>Comments</th><td>{{ $approval->review_comments }}</td></tr>
                    @endif
                    @if($approval->rejection_reason)
                    <tr><th>Rejection Reason</th><td class="text-danger">{{ $approval->rejection_reason }}</td></tr>
                    @endif
                    <tr><th>Locked</th><td>{{ $approval->is_locked ? 'Yes' : 'No' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Actions</h6></div>
            <div class="card-body">
                @if($approval->status === 'pending')
                <a href="{{ route('approvals.index') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">Back</a>
                @else
                <a href="{{ route('approvals.index') }}" class="btn btn-sm btn-outline-secondary w-100">Back</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
