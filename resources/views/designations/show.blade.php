@extends('layouts.app')
@section('title', $designation->name . ' - MES')
@section('page-title', $designation->name)
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>{{ $designation->code }}</h5>
                <table class="table table-sm">
                    <tr><th>Name</th><td>{{ $designation->name }}</td></tr>
                    <tr><th>Code</th><td>{{ $designation->code }}</td></tr>
                    <tr><th>Level</th><td>{{ ucfirst($designation->level) }}</td></tr>
                    <tr><th>Description</th><td>{{ $designation->description ?: '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $designation->is_active ? 'success' : 'danger' }}">{{ $designation->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                    <tr><th>Created</th><td>{{ $designation->created_at->format('d M Y, h:i A') }}</td></tr>
                </table>
                <div class="mt-3">
                    <a href="{{ route('designations.edit', $designation) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('designations.menu-permissions.edit', $designation) }}" class="btn btn-sm btn-primary"><i class="bi bi-shield-lock me-1"></i>Menu Permissions</a>
                    @endif
                    <a href="{{ route('designations.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Assigned Employees</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Name</th><th>Department</th></tr></thead>
                    <tbody>
                        @forelse($designation->employees as $emp)
                        <tr><td>{{ $emp->name }}</td><td>{{ $emp->department?->name ?? '-' }}</td></tr>
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
