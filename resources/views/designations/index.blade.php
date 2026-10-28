@extends('layouts.app')
@section('page-title', 'Designations')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-briefcase me-2"></i>Designations</h5>
        <a href="{{ route('designations.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Designation</a>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable">
            <thead><tr><th>Code</th><th>Name</th><th>Level</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($designations as $d)
                <tr>
                    <td><strong>{{ $d->code }}</strong></td>
                    <td>{{ $d->name }}</td>
                    <td>{{ ucfirst($d->level) }}</td>
                    <td><span class="badge bg-{{ $d->is_active ? 'success' : 'danger' }}">{{ $d->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('designations.show', $d) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('designations.edit', $d) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('designations.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this designation?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No designations found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $designations->links() }}</div>
    </div>
</div>
@endsection
