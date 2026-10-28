@extends('layouts.app')

@section('title', 'New Inspection - MES')
@section('page-title', 'New Quality Inspection')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Record Quality Inspection</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('quality.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->product_code }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Inspector <span class="text-danger">*</span></label>
                    <select name="inspector_id" class="form-select @error('inspector_id') is-invalid @enderror" required>
                        <option value="">Select Inspector</option>
                        @foreach($inspectors as $inspector)
                            <option value="{{ $inspector->id }}" {{ old('inspector_id') == $inspector->id ? 'selected' : '' }}>
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
                            <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Inspection Date <span class="text-danger">*</span></label>
                    <input type="date" name="inspection_date" class="form-control" value="{{ old('inspection_date', today()) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Inspection Type <span class="text-danger">*</span></label>
                    <select name="inspection_type" class="form-select" required>
                        <option value="first_piece" {{ old('inspection_type') === 'first_piece' ? 'selected' : '' }}>First Piece</option>
                        <option value="in_process" {{ old('inspection_type') === 'in_process' ? 'selected' : '' }}>In Process</option>
                        <option value="final" {{ old('inspection_type') === 'final' ? 'selected' : '' }}>Final</option>
                        <option value="patrol" {{ old('inspection_type') === 'patrol' ? 'selected' : '' }}>Patrol</option>
                        <option value="receiving" {{ old('inspection_type') === 'receiving' ? 'selected' : '' }}>Receiving</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Sample Size <span class="text-danger">*</span></label>
                    <input type="number" name="sample_size" class="form-control" value="{{ old('sample_size', 1) }}" min="1" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Result <span class="text-danger">*</span></label>
                    <select name="result" class="form-select" required>
                        <option value="pass" {{ old('result') === 'pass' ? 'selected' : '' }}>Pass</option>
                        <option value="fail" {{ old('result') === 'fail' ? 'selected' : '' }}>Fail</option>
                        <option value="conditional" {{ old('result') === 'conditional' ? 'selected' : '' }}>Conditional</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Passed Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="passed_quantity" class="form-control" value="{{ old('passed_quantity', 0) }}" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Failed Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="failed_quantity" class="form-control" value="{{ old('failed_quantity', 0) }}" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Inspection</button>
                <a href="{{ route('quality.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
