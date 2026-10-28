@extends('layouts.app')
@section('title', $productType->name . ' - MES')
@section('page-title', $productType->name)
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>{{ $productType->code }}</h5>
                <table class="table table-sm">
                    <tr><th>Name</th><td>{{ $productType->name }}</td></tr>
                    <tr><th>Code</th><td>{{ $productType->code }}</td></tr>
                    <tr><th>Description</th><td>{{ $productType->description ?: '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $productType->is_active ? 'success' : 'danger' }}">{{ $productType->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                    <tr><th>Created</th><td>{{ $productType->created_at->format('d M Y, h:i A') }}</td></tr>
                </table>
                <div class="mt-3">
                    <a href="{{ route('product-types.edit', $productType) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
                    <a href="{{ route('product-types.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Assigned Products</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Code</th><th>Name</th><th>Category</th></tr></thead>
                    <tbody>
                        @forelse($productType->products as $product)
                        <tr><td>{{ $product->product_code }}</td><td>{{ $product->name }}</td><td>{{ $product->category }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">No products assigned</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
