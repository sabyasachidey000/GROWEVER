@extends('layouts.app')

@section('title', 'Breakdown Details - MES')
@section('page-title', 'Breakdown #' . $breakdown->id)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Breakdown Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">Machine</th><td>{{ $breakdown->machine->name }}</td></tr>
                    <tr><th>Type</th><td>{{ ucfirst(str_replace('_', ' ', $breakdown->breakdown_type)) }}</td></tr>
                    <tr><th>Severity</th><td><span class="badge bg-{{ $breakdown->severity === 'critical' ? 'danger' : ($breakdown->severity === 'high' ? 'warning' : 'info') }}">{{ ucfirst($breakdown->severity) }}</span></td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $breakdown->status === 'open' ? 'danger' : ($breakdown->status === 'in_progress' ? 'warning' : ($breakdown->status === 'resolved' ? 'info' : 'success')) }}">{{ ucfirst(str_replace('_', ' ', $breakdown->status)) }}</span></td></tr>
                    <tr><th>Start Time</th><td>{{ $breakdown->start_time->format('d M Y H:i') }}</td></tr>
                    <tr><th>End Time</th><td>{{ $breakdown->end_time?->format('d M Y H:i') ?? 'In Progress' }}</td></tr>
                    <tr><th>Duration</th><td>{{ $breakdown->duration_minutes ? $breakdown->duration_minutes . ' minutes (' . $breakdown->getDowntimeHours() . ' hrs)' : 'Calculating...' }}</td></tr>
                    <tr><th>Reported By</th><td>{{ $breakdown->reportedBy->name }}</td></tr>
                    <tr><th>Assigned To</th><td>{{ $breakdown->assignedTo?->name ?? 'Unassigned' }}</td></tr>
                    <tr><th>Repair Cost</th><td>{{ $breakdown->repair_cost ? '$' . number_format($breakdown->repair_cost, 2) : '-' }}</td></tr>
                    <tr><th>Description</th><td>{{ $breakdown->description }}</td></tr>
                    <tr><th>Root Cause</th><td>{{ $breakdown->root_cause ?? '-' }}</td></tr>
                    <tr><th>Repair Action</th><td>{{ $breakdown->repair_action ?? '-' }}</td></tr>
                    <tr><th>Spare Parts</th><td>{{ $breakdown->spare_parts_used ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Quick Actions</h6></div>
            <div class="card-body">
                <a href="{{ route('breakdowns.edit', $breakdown) }}" class="btn btn-warning btn-sm w-100 mb-2"><i class="bi bi-pencil me-1"></i>Edit</a>
                @if($breakdown->status !== 'closed')
                <form action="{{ route('breakdowns.close', $breakdown) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm w-100 mb-2"><i class="bi bi-check-lg me-1"></i>Close Breakdown</button>
                </form>
                @endif
                <a href="{{ route('breakdowns.index') }}" class="btn btn-secondary btn-sm w-100"><i class="bi bi-arrow-left me-1"></i>Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
