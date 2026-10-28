@extends('layouts.app')
@section('page-title', 'Product Types')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-tag me-2"></i>Product Types</h5>
        <a href="{{ route('product-types.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Product Type</a>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable">
            <thead><tr><th>Code</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($productTypes as $pt)
                <tr>
                    <td><strong>{{ $pt->code }}</strong></td>
                    <td>{{ $pt->name }}</td>
                    <td><span class="badge bg-{{ $pt->is_active ? 'success' : 'danger' }}">{{ $pt->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('product-types.show', $pt) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('product-types.edit', $pt) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('product-types.destroy', $pt) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product type?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No product types found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $productTypes->links() }}</div>
    </div>
</div>
@endsection
