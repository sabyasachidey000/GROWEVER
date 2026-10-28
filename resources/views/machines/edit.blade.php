@extends('layouts.app')
@section('title', 'Edit Machine - MES')
@section('page-title', 'Edit: ' . $machine->name)
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Machine</h5></div>
    <div class="card-body">
        <form action="{{ route('machines.update', $machine) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine Code <span class="text-danger">*</span></label>
                    <input type="text" name="machine_code" class="form-control" value="{{ old('machine_code', $machine->machine_code) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $machine->name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type', $machine->type) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Operation Type</label>
                    <select name="operation_type" class="form-select" required>
                        @foreach(['cnc','manual','semi_auto','auto'] as $t)
                            <option value="{{ $t }}" {{ $machine->operation_type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $machine->location) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Capacity/hr</label>
                    <input type="number" name="production_capacity_per_hour" class="form-control" value="{{ $machine->production_capacity_per_hour }}" step="0.01">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" name="manufacturer" class="form-control" value="{{ $machine->manufacturer }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['running','idle','breakdown','maintenance','offline'] as $s)
                            <option value="{{ $s }}" {{ $machine->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">IP</label>
                    <input type="text" name="machine_ip" class="form-control" value="{{ $machine->machine_ip }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">RFID Device ID</label>
                    <input type="text" name="rfid_device_id" class="form-control" readonly value="{{ old('rfid_device_id', $machine->rfid_device_id) }}" placeholder="e.g. ESP32_001">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cycle Count</label>
                    <input type="text" name="cycle_count" class="form-control" value="{{ old('rfid_device_id', $machine->cycle_count) }}" placeholder="e.g. 1">
                    <small class="text-muted">Production Count per Tap.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Assigned Products</label>
                    <!--<select name="product_ids[]" class="form-select" multiple size="8">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $machine->products->contains($product->id) ? 'selected' : '' }}>{{ $product->product_code }} — {{ $product->name }}</option>
                        @endforeach
                    </select>-->
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
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
