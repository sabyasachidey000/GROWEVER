@extends('layouts.app')
@section('title', 'Rejection Analysis - MES')
@section('page-title', 'Rejection Analysis')
@section('content')
<div class="row g-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <form class="row g-2">
                    <div class="col-auto">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="col-auto">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">By Reason</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Reason</th><th>Total Qty</th><th>Occurrences</th></tr></thead>
                    <tbody>
                        @forelse($byReason as $r)
                        <tr><td>{{ str_replace('_', ' ', $r->rejection_reason) }}</td><td>{{ $r->total_quantity }}</td><td>{{ $r->occurrence_count }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">By Product</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Product</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($byProduct as $p)
                        <tr><td>{{ $p->product->name }}</td><td>{{ $p->total }}</td></tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">By Machine</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Machine</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($byMachine as $m)
                        <tr><td>{{ $m->machine?->name ?? '-' }}</td><td>{{ $m->total }}</td></tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="mt-3">
    <h5>Total Rejections: <span class="text-danger">{{ $totalQuantity }}</span></h5>
</div>
@endsection
