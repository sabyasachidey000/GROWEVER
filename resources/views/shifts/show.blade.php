@extends('layouts.app')
@section('title', $shift->name . ' - MES')
@section('page-title', $shift->name)
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Shift {{ $shift->code }}</h5>
                <table class="table table-sm">
                    <tr><th>Start</th><td>{{ $shift->start_time }}</td></tr>
                    <tr><th>End</th><td>{{ $shift->end_time }}</td></tr>
                    <tr><th>Break</th><td>{{ $shift->break_start ?? '-' }} to {{ $shift->break_end ?? '-' }}</td></tr>
                    <tr><th>Night Shift</th><td>{{ $shift->is_night_shift ? 'Yes' : 'No' }}</td></tr>
                    <tr><th>Duration</th><td>{{ $shift->getDurationInMinutes() }} minutes</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $shift->is_active ? 'success' : 'danger' }}">{{ $shift->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Assignments</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Employee</th><th>Machine</th></tr></thead>
                    <tbody>
                        @forelse($shift->shiftAssignments()->with(['employee','machine'])->latest()->limit(10)->get() as $a)
                        <tr><td>{{ $a->assignment_date->format('d M Y') }}</td><td>{{ $a->employee->name }}</td><td>{{ $a->machine?->name ?? '-' }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">No assignments</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
