@extends('layouts.app')

@section('title', 'Edit Inspection - MES')
@section('page-title', 'Edit Quality Inspection #' . $inspection->id)

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Quality Inspection</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('quality.update', $inspection) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $inspection->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->product_code }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Inspector <span class="text-danger">*</span></label>
                    <select name="inspector_id" class="form-select" required>
                        @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}" {{ $inspection->inspector_id == $inspector->id ? 'selected' : '' }}>
                                {{ $inspector->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine</label>
                    <select name="machine_id" class="form-select">
                        <option value="">Select (optional)</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ $inspection->machine_id == $machine->id ? 'selected' : '' }}>
                                {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Inspection Date <span class="text-danger">*</span></label>
                    <input type="date" name="inspection_date" class="form-control" value="{{ old('inspection_date', $inspection->inspection_date) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Inspection Type <span class="text-danger">*</span></label>
                    <select name="inspection_type" class="form-select" required>
                        @foreach(['first_piece','in_process','final','patrol','receiving'] as $type)
                            <option value="{{ $type }}" {{ $inspection->inspection_type === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Sample Size <span class="text-danger">*</span></label>
                    <input type="number" name="sample_size" class="form-control" value="{{ old('sample_size', $inspection->sample_size) }}" min="1" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Result <span class="text-danger">*</span></label>
                    <select name="result" class="form-select" required>
                        @foreach(['pass','fail','conditional'] as $r)
                            <option value="{{ $r }}" {{ $inspection->result === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Passed Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="passed_quantity" class="form-control" value="{{ old('passed_quantity', $inspection->passed_quantity) }}" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Failed Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="failed_quantity" class="form-control" value="{{ old('failed_quantity', $inspection->failed_quantity) }}" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $inspection->remarks) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('quality.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
