@extends('layouts.app')
@section('title', $machine->name . ' - MES')
@section('page-title', $machine->name)
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>{{ $machine->machine_code }}</h5>
                <table class="table table-sm">
                    <tr><th>Type</th><td>{{ $machine->type }}</td></tr>
                    <tr><th>Operation</th><td>{{ ucfirst(str_replace('_', ' ', $machine->operation_type)) }}</td></tr>
                    <tr><th>Manufacturer</th><td>{{ $machine->manufacturer ?? '-' }}</td></tr>
                    <tr><th>Model</th><td>{{ $machine->model_number ?? '-' }}</td></tr>
                    <tr><th>Location</th><td>{{ $machine->location ?? '-' }}</td></tr>
                    <tr><th>Capacity/hr</th><td>{{ $machine->production_capacity_per_hour ?? '-' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-status status-{{ $machine->status }}">{{ ucfirst($machine->status) }}</span></td></tr>
                    <tr><th>Today Utilization</th><td>{{ $utilization }}%</td></tr>
                    <tr><th>Current Operator</th><td>{{ $activeSession?->employee?->name ?? 'None' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-white"><h6 class="mb-0">Recent Production</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Operator</th><th>Product</th><th>Weight (kg)</th></tr></thead>
                    <tbody>
                        @forelse($machine->productionRuns()->with(['operator','product'])->latest()->limit(10)->get() as $run)
                        <tr><td>{{ $run->start_time->format('d M') }}</td><td>{{ $run->operator->name }}</td><td>{{ $run->product->name }}</td><td>{{ $run->weight ? number_format($run->weight, 2) : '-' }}</td></tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No production records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">Recent Breakdowns</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Type</th><th>Severity</th><th>Status</th><th>Duration</th></tr></thead>
                    <tbody>
                        @forelse($machine->breakdowns()->latest()->limit(10)->get() as $bd)
                        <tr><td>{{ $bd->breakdown_type }}</td><td>{{ $bd->severity }}</td><td>{{ $bd->status }}</td><td>{{ $bd->duration_minutes ? $bd->duration_minutes . 'min' : '-' }}</td></tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No breakdowns</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
