@extends('layouts.app')
@section('title', 'Breakdown Entry - MES')
@section('page-title', 'Breakdown Entry')

@php
    $operatorPhoto = $employee->profile_photo
        ? (str_starts_with($employee->profile_photo, 'http') ? $employee->profile_photo : asset('storage/' . ltrim($employee->profile_photo, '/')))
        : null;
@endphp

@section('content')
@if($completedBreakdown)
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Breakdown #{{ $completedBreakdown->id }} for {{ $completedBreakdown->machine->name }} is {{ str_replace('_', ' ', $completedBreakdown->status) }}.
    </div>
@endif

<div class="mb-3">
    <a href="{{ route('operator.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-tools me-2"></i>Report Breakdown</h5></div>
            <div class="card-body">
                <form action="{{ route('operator.breakdowns.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Breakdown Type</label>
                        <select name="breakdown_type" class="form-select @error('breakdown_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            @foreach(['mechanical','electrical','hydraulic','pneumatic','tool_failure','material_shortage','software','other'] as $type)
                                <option value="{{ $type }}" {{ old('breakdown_type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('breakdown_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Severity</label>
                        <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                            @foreach(['low','medium','high','critical'] as $severity)
                                <option value="{{ $severity }}" {{ old('severity', 'medium') === $severity ? 'selected' : '' }}>
                                    {{ ucfirst($severity) }}
                                </option>
                            @endforeach
                        </select>
                        @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Problem Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" data-kiosk-keyboard required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-danger"><i class="bi bi-exclamation-triangle me-1"></i>Report Breakdown</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.kiosk-keyboard')
@endpush
