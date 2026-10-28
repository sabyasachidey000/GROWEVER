@extends('layouts.app')

@section('title', 'QC Inspection Reports - MES')
@section('page-title', 'QC Inspection Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div><h4 class="mb-1"><i class="bi bi-clipboard2-check me-2"></i>QC Inspection Reports</h4><p class="text-muted mb-0">Saved single-machine quality inspections.</p></div>
        <a href="{{ route('qc-inspections.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Create QC Entry</a>
    </div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th class="ps-3">#</th><th>Date / Time</th><th>Machine</th><th>Product</th><th>Shift</th><th>Inspector</th><th class="text-end">Sample</th><th class="text-end">Passed</th><th class="text-end">Rejected</th><th>Result</th></tr></thead>
                <tbody>
                @forelse($inspections as $inspection)
                    <tr>
                        <td class="ps-3">{{ $inspection->id }}</td>
                        <td>{{ $inspection->inspection_date?->format('d M Y') }} {{ $inspection->inspection_time ? \Carbon\Carbon::parse($inspection->inspection_time)->format('H:i') : '' }}</td>
                        <td>{{ $inspection->machine?->machine_code ?? '—' }}<small class="d-block text-muted">{{ $inspection->machine?->name }}</small></td>
                        <td>{{ $inspection->product?->product_code ?? '—' }}<small class="d-block text-muted">{{ $inspection->product?->name }}</small></td>
                        <td>{{ $inspection->shift?->code ?? '—' }}</td>
                        <td>{{ $inspection->inspector?->name ?? '—' }}</td>
                        <td class="text-end">{{ number_format($inspection->sample_size) }}</td>
                        <td class="text-end text-success">{{ number_format($inspection->passed_quantity) }}</td>
                        <td class="text-end {{ $inspection->failed_quantity ? 'text-danger' : '' }}">{{ number_format($inspection->failed_quantity) }}</td>
                        <td><span class="badge text-bg-{{ $inspection->result === 'pass' ? 'success' : ($inspection->result === 'fail' ? 'danger' : 'warning') }}">{{ ucfirst($inspection->result) }}</span><small class="d-block text-muted">{{ number_format((float) $inspection->acceptance_rate, 1) }}% pass</small></td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-5">No QC inspection reports have been saved yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($inspections->hasPages())<div class="card-footer">{{ $inspections->links() }}</div>@endif
    </div>
</div>
@endsection
