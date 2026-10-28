@extends('layouts.app')
@section('page-title', 'Add Machine')
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Register New Machine</h5></div>
    <div class="card-body">
        <form action="{{ route('machines.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine Code <span class="text-danger">*</span></label>
                    <input type="text" name="machine_code" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine Type <span class="text-danger">*</span></label>
                    <input type="text" name="type" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Operation Type <span class="text-danger">*</span></label>
                    <select name="operation_type" class="form-select" required>
                        <option value="cnc">CNC</option><option value="manual">Manual</option>
                        <option value="semi_auto">Semi-Automatic</option><option value="auto">Automatic</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" name="manufacturer" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Production Capacity/hr</label>
                    <input type="number" name="production_capacity_per_hour" class="form-control" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="idle">Idle</option><option value="running">Running</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">RFID Device ID</label>
                    <input type="text" name="rfid_device_id" class="form-control" value="{{ old('rfid_device_id') }}" placeholder="e.g. ESP32_001">
                    <small class="text-muted">Maps this machine to its RFID reader/button.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cycle Count</label>
                    <input type="text" name="cycle_count" class="form-control" value="{{ old('cycle_count') }}" placeholder="e.g. 1">
                    <small class="text-muted">Production Count per Tap.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Assigned Products</label>
                    <select name="product_id" class="form-control">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            {{ old('product_id', $selectedProductId ?? '') == $product->id ? 'selected' : '' }}>
                            {{ $product->product_code }} — {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                    <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple products</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Machine</button>
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
