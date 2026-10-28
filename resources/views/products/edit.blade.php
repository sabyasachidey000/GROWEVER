@extends('layouts.app')
@section('title', 'Edit Product - MES')
@section('page-title', 'Edit: ' . $product->name)
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Product</h5></div>
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Code <span class="text-danger">*</span></label>
                    <input type="text" name="product_code" class="form-control" value="{{ old('product_code', $product->product_code) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $product->category) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">HSN Code</label>
                    <input type="text" name="hsn_code" class="form-control" value="{{ old('hsn_code', $product->hsn_code) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Category Master Base</label>
                    <input type="text" name="product_category_master_base" class="form-control" value="{{ old('product_category_master_base', $product->product_category_master_base) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Type Master Base</label>
                    <input type="text" name="product_type_master_base" class="form-control" value="{{ old('product_type_master_base', $product->product_type_master_base) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Type <span class="text-danger">*</span></label>
                    <select name="product_type_id" class="form-select" required>
                        <option value="">Select Type</option>
                        @foreach($productTypes as $pt)
                            <option value="{{ $pt->id }}" {{ old('product_type_id', $product->product_type_id) == $pt->id ? 'selected' : '' }}>{{ $pt->name }} ({{ $pt->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Unit of Measure</label>
                    <input type="text" name="unit_of_measure" class="form-control" value="{{ $product->unit_of_measure }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Std Production Rate/hr</label>
                    <input type="number" name="standard_production_rate" class="form-control" value="{{ $product->standard_production_rate }}" step="0.01">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Specifications</label>
                <textarea name="specifications" class="form-control" rows="2">{{ old('specifications', $product->specifications) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
