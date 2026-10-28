@extends('layouts.app')

@section('title', 'Edit Production - MES')
@section('page-title', 'Edit Production #' . ($productionRun->id ?? 'N/A'))

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Production Entry</h5>
        @if($productionRun->approval?->is_locked)
            <span class="badge bg-warning"><i class="bi bi-lock me-1"></i>Approved — Locked</span>
        @else
            <span class="badge bg-{{ $productionRun->status === 'completed' ? 'success' : 'primary' }}">{{ $productionRun->status }}</span>
        @endif
    </div>
    <div class="card-body">
        @if($productionRun->approval?->is_locked)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>This record has been approved and cannot be edited.
            </div>
        @else
            <form action="{{ url('production/' . $productionRun->id) }}" method="POST">
                @csrf
                @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Operator <span class="text-danger">*</span></label>
                    <select name="operator_id" class="form-select @error('operator_id') is-invalid @enderror" required>
                        <option value="">Select Operator</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id', $productionRun->operator_id) == $op->id ? 'selected' : '' }}>
                                {{ $op->employee_code }} - {{ $op->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('operator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine <span class="text-danger">*</span></label>
                    <input type="hidden" name="machine_id" value="{{ old('machine_id', $productionRun->machine_id) }}">
                    <input type="text" class="form-control" readonly value="{{ $productionRun->machine?->machine_code ?? '-' }} - {{ $productionRun->machine?->name ?? 'N/A' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $productionRun->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->product_code }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                    <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id', $productionRun->shift_id) == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Start Time</label>
                    <input type="text" class="form-control" readonly value="{{ $productionRun->start_time?->format('d M Y H:i') ?? '-' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" class="form-control" value="{{ old('weight', $productionRun->weight) }}" step="0.01">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="in_progress" {{ old('status', $productionRun->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $productionRun->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="stopped" {{ old('status', $productionRun->status) == 'stopped' ? 'selected' : '' }}>Stopped</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $productionRun->remarks) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Production Entry</button>
                <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
            </form>
        @endif
    </div>
</div>
@endsection
