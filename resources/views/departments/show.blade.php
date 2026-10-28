@extends('layouts.app')
@section('title', $department->name . ' - MES')
@section('page-title', $department->name)
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>{{ $department->code }}</h5>
                <table class="table table-sm">
                    <tr><th>Name</th><td>{{ $department->name }}</td></tr>
                    <tr><th>Code</th><td>{{ $department->code }}</td></tr>
                    <tr><th>Description</th><td>{{ $department->description ?: '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">{{ $department->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                    <tr><th>Created</th><td>{{ $department->created_at->format('d M Y, h:i A') }}</td></tr>
                </table>
                <div class="mt-3">
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
                    <a href="{{ route('departments.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Assigned Employees</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Name</th><th>Designation</th></tr></thead>
                    <tbody>
                        @forelse($department->employees as $emp)
                        <tr><td>{{ $emp->name }}</td><td>{{ $emp->designation?->name ?? '-' }}</td></tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">No employees assigned</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
