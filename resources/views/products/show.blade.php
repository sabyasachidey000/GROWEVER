@extends('layouts.app')
@section('title', $product->name . ' - MES')
@section('page-title', $product->name)
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>{{ $product->product_code }}</h5>
                <table class="table table-sm">
                    <tr><th>Name</th><td>{{ $product->name }}</td></tr>
                    <tr><th>Category</th><td>{{ $product->category }}</td></tr>
                    <tr><th>HSN Code</th><td>{{ $product->hsn_code ?? '-' }}</td></tr>
                    <tr><th>Category Master Base</th><td>{{ $product->product_category_master_base ?? '-' }}</td></tr>
                    <tr><th>Type Master Base</th><td>{{ $product->product_type_master_base ?? '-' }}</td></tr>
                    <tr><th>Type</th><td>{{ str_replace('_', ' ', $product->product_type) }}</td></tr>
                    <tr><th>UOM</th><td>{{ $product->unit_of_measure }}</td></tr>
                    <tr><th>Cycle Time</th><td>{{ $product->target_cycle_time ? $product->target_cycle_time . 's' : '-' }}</td></tr>
                    <tr><th>Production Rate</th><td>{{ $product->standard_production_rate ?? '-' }}/hr</td></tr>
                    <tr><th>Material</th><td>{{ $product->material_grade ?? '-' }}</td></tr>
                    <tr><th>Weight</th><td>{{ $product->weight ? $product->weight . ' kg' : '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Recent Production</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Machine</th><th>Weight (kg)</th></tr></thead>
                    <tbody>
                        @forelse($product->productionRuns()->latest()->limit(5)->get() as $run)
                        <tr><td>{{ $run->start_time->format('d M') }}</td><td>{{ $run->machine->name }}</td><td>{{ $run->weight ? number_format($run->weight, 2) : '-' }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">No production</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
