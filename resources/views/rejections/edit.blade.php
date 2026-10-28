@extends('layouts.app')
@section('title', 'Edit Rejection - MES')
@section('page-title', 'Edit Rejection')
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Rejection</h5></div>
    <div class="card-body">
        <form action="{{ route('rejections.update', $rejection) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select" required>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $rejection->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Reason <span class="text-danger">*</span></label>
                    <select name="rejection_reason" class="form-select" required>
                        @foreach(['crack','dimension_error','surface_defect','burr','material_defect','tool_mark','porosity','misrun','other'] as $r)
                            <option value="{{ $r }}" {{ $rejection->rejection_reason === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $rejection->quantity) }}" min="1" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $rejection->description) }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Corrective Action</label>
                    <textarea name="corrective_action" class="form-control" rows="2">{{ old('corrective_action', $rejection->corrective_action) }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            <a href="{{ route('rejections.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
