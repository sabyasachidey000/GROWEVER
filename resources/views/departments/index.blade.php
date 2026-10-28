@extends('layouts.app')
@section('page-title', 'Departments')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Departments</h5>
        <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Department</a>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable">
            <thead><tr><th>Code</th><th>Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($departments as $d)
                <tr>
                    <td><strong>{{ $d->code }}</strong></td>
                    <td>{{ $d->name }}</td>
                    <td>{{ Str::limit($d->description, 50) }}</td>
                    <td><span class="badge bg-{{ $d->is_active ? 'success' : 'danger' }}">{{ $d->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('departments.show', $d) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('departments.edit', $d) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('departments.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No departments found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $departments->links() }}</div>
    </div>
</div>
@endsection
