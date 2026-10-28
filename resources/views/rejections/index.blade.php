@extends('layouts.app')
@section('page-title', 'Rejections')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-x-octagon me-2"></i>Rejection Records</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('rejections.analysis') }}" class="btn btn-info btn-sm text-white"><i class="bi bi-graph-down me-1"></i>Analysis</a>
            <a href="{{ route('rejections.create') }}" class="btn btn-danger btn-sm"><i class="bi bi-plus-lg me-1"></i>Record Rejection</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover datatable">
            <thead><tr><th>Date</th><th>Product</th><th>Machine</th><th>Operator</th><th>Reason</th><th>Qty</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($rejections as $r)
                <tr>
                    <td>{{ $r->rejection_date->format('d M Y') }}</td><td>{{ $r->product->name }}</td>
                    <td>{{ $r->machine?->name ?? '-' }}</td><td>{{ $r->operator?->name ?? '-' }}</td>
                    <td>{{ str_replace('_', ' ', $r->rejection_reason) }}</td><td class="text-danger fw-bold">{{ $r->quantity }}</td>
                    <td><a href="{{ route('rejections.edit', $r) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No rejections found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">{{ $rejections->links() }}</div>
    </div>
</div>
@endsection
