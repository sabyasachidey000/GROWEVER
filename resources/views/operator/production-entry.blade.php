@extends('layouts.app')
@section('title', 'Production Entry - MES')
@section('page-title', 'Production Entry')

@php
    $operatorPhoto = $employee->profile_photo
        ? (str_starts_with($employee->profile_photo, 'http') ? $employee->profile_photo : asset('storage/' . ltrim($employee->profile_photo, '/')))
        : null;
@endphp

@section('content')
<div class="mb-3">
    <a href="{{ route('operator.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Complete Production</h5></div>
            <div class="card-body">
                <form action="{{ route('operator.production.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->product_code }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Production Weight (kg)</label>
                        <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror"
                               value="{{ old('weight') }}" min="0.01" step="0.01" inputmode="decimal"
                               data-kiosk-keyboard required>
                        @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" data-kiosk-keyboard>{{ old('remarks') }}</textarea>
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Completed Weight</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.kiosk-keyboard')
@endpush
