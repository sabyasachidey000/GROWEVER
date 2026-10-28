@extends('layouts.app')
@section('title', 'Add Product - MES')
@section('page-title', 'Add New Product')
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>New Product</h5></div>
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Code <span class="text-danger">*</span></label>
                    <input type="text" name="product_code" class="form-control" value="{{ old('product_code') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">HSN Code</label>
                    <input type="text" name="hsn_code" class="form-control" value="{{ old('hsn_code') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Category</label>
                    <input type="text" name="product_category_master_base" class="form-control" value="{{ old('product_category_master_base') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product Type <span class="text-danger">*</span></label>
                    <select name="product_type_id" class="form-select" required>
                        <option value="">Select Type</option>
                        @foreach($productTypes as $pt)
                            <option value="{{ $pt->id }}" {{ old('product_type_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }} ({{ $pt->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Unit of Measure</label>
                    <input type="text" name="unit_of_measure" class="form-control" value="{{ old('unit_of_measure', 'pcs') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Target Cycle Time (sec)</label>
                    <input type="number" name="target_cycle_time" class="form-control" value="{{ old('target_cycle_time') }}" step="0.01">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Std Production Rate/hr</label>
                    <input type="number" name="standard_production_rate" class="form-control" value="{{ old('standard_production_rate') }}" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Material Grade</label>
                    <input type="text" name="material_grade" class="form-control" value="{{ old('material_grade') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" step="0.001">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Specifications</label>
                    <textarea name="specifications" class="form-control" rows="2">{{ old('specifications') }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dimensions</label>
                    <input type="text" name="dimensions" class="form-control" value="{{ old('dimensions') }}">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
