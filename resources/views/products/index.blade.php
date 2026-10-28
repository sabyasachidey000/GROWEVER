@extends('layouts.app')
@section('page-title', 'Products')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Products</h5>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable">
            <thead><tr><th>Code</th><th>Name</th><th>Category</th><th>HSN</th><th>Type</th><th>UOM</th><th>Rate/hr</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td><strong>{{ $p->product_code }}</strong></td><td>{{ $p->name }}</td>
                    <td>{{ $p->category }}</td><td>{{ $p->hsn_code ?? '-' }}</td><td>{{ str_replace('_', ' ', $p->product_type) }}</td>
                    <td>{{ $p->unit_of_measure }}</td><td>{{ $p->standard_production_rate ?? '-' }}</td>
                    <td>
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">No products found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $products->links() }}</div>
    </div>
</div>
@endsection
