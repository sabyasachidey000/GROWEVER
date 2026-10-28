@extends('layouts.app')
@section('title', 'Record Rejection - MES')
@section('page-title', 'Record Rejection')
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-x-octagon me-2"></i>New Rejection Record</h5></div>
    <div class="card-body">
        <form action="{{ route('rejections.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Select</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine</label>
                    <select name="machine_id" class="form-select">
                        <option value="">Select (optional)</option>
                        @foreach($machines as $m)
                            <option value="{{ $m->id }}" {{ old('machine_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Operator</label>
                    <select name="operator_id" class="form-select">
                        <option value="">Select (optional)</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <select name="rejection_reason" class="form-select" required>
                        @foreach(['crack','dimension_error','surface_defect','burr','material_defect','tool_mark','porosity','misrun','other'] as $r)
                            <option value="{{ $r }}" {{ old('rejection_reason') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="rejection_date" class="form-control" value="{{ old('rejection_date', today()) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Shift</label>
                    <select name="shift_id" class="form-select">
                        <option value="">Select (optional)</option>
                        @foreach($shifts as $s)
                            <option value="{{ $s->id }}" {{ old('shift_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Corrective Action</label>
                    <textarea name="corrective_action" class="form-control" rows="2">{{ old('corrective_action') }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-danger"><i class="bi bi-check-lg me-1"></i>Record Rejection</button>
            <a href="{{ route('rejections.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
