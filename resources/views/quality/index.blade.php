@extends('layouts.app')
@section('page-title', 'Quality Inspections')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Quality Inspections</h5>
        <a href="{{ route('quality.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Inspection</a>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable">
            <thead><tr><th>Date</th><th>Product</th><th>Inspector</th><th>Sample</th><th>Passed</th><th>Failed</th><th>Rate</th><th>Result</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($inspections as $qi)
                <tr>
                    <td>{{ $qi->inspection_date->format('d M Y') }}</td><td>{{ $qi->product->name }}</td>
                    <td>{{ $qi->inspector->name }}</td><td>{{ $qi->sample_size }}</td>
                    <td>{{ $qi->passed_quantity }}</td><td>{{ $qi->failed_quantity }}</td>
                    <td>{{ $qi->getAcceptanceRate() }}%</td>
                    <td><span class="badge bg-{{ $qi->result === 'pass' ? 'success' : 'danger' }}">{{ ucfirst($qi->result) }}</span></td>
                    <td><a href="{{ route('quality.edit', $qi) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted">No inspections found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $inspections->links() }}</div>
    </div>
</div>
@endsection
