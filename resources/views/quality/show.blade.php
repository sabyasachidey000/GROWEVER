@extends('layouts.app')
@section('title', 'Inspection Details - MES')
@section('page-title', 'Inspection #' . $inspection->id)
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Inspection Details</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th width="200">Product</th><td>{{ $inspection->product->name }}</td></tr>
            <tr><th>Inspector</th><td>{{ $inspection->inspector->name }}</td></tr>
            <tr><th>Machine</th><td>{{ $inspection->machine?->name ?? '-' }}</td></tr>
            <tr><th>Date</th><td>{{ $inspection->inspection_date->format('d M Y') }}</td></tr>
            <tr><th>Type</th><td>{{ ucfirst(str_replace('_', ' ', $inspection->inspection_type)) }}</td></tr>
            <tr><th>Sample Size</th><td>{{ $inspection->sample_size }}</td></tr>
            <tr><th>Passed</th><td class="text-success">{{ $inspection->passed_quantity }}</td></tr>
            <tr><th>Failed</th><td class="text-danger">{{ $inspection->failed_quantity }}</td></tr>
            <tr><th>Acceptance Rate</th><td>{{ $inspection->getAcceptanceRate() }}%</td></tr>
            <tr><th>Result</th><td><span class="badge bg-{{ $inspection->result === 'pass' ? 'success' : 'danger' }}">{{ ucfirst($inspection->result) }}</span></td></tr>
            <tr><th>Remarks</th><td>{{ $inspection->remarks ?? '-' }}</td></tr>
        </table>
    </div>
</div>
<div class="mt-3"><a href="{{ route('quality.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a></div>
@endsection
