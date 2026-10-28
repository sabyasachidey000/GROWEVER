@extends('layouts.app')
@section('page-title', 'Shifts')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Shift Management</h5>
        <a href="{{ route('shifts.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Shift</a>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Name</th><th>Start</th><th>End</th><th>Night Shift</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($shifts as $s)
                <tr>
                    <td><strong>{{ $s->code }}</strong></td><td>{{ $s->name }}</td>
                    <td>{{ $s->start_time }}</td><td>{{ $s->end_time }}</td>
                    <td>{{ $s->is_night_shift ? 'Yes' : 'No' }}</td>
                    <td><a href="{{ route('shifts.edit', $s) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No shifts configured</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
