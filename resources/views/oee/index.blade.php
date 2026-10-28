@extends('layouts.app')

@section('title', 'OEE Analysis - MES')
@section('page-title', 'OEE (Overall Equipment Effectiveness)')

@section('content')
<!-- OEE Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-primary fw-bold">{{ $oeeSummary['oee'] ?? 0 }}%</h2>
                <p class="text-muted mb-0">Overall OEE</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-info fw-bold">{{ $oeeSummary['availability'] ?? 0 }}%</h2>
                <p class="text-muted mb-0">Availability</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-success fw-bold">{{ $oeeSummary['performance'] ?? 0 }}%</h2>
                <p class="text-muted mb-0">Performance</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-warning fw-bold">{{ $oeeSummary['quality'] ?? 0 }}%</h2>
                <p class="text-muted mb-0">Quality</p>
            </div>
        </div>
    </div>
</div>

<!-- Machine Ranking -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Machine OEE Ranking - {{ $date }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Machine</th>
                        <th>Availability</th>
                        <th>Performance</th>
                        <th>Quality</th>
                        <th>OEE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ranking as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['machine_name'] }} ({{ $item['machine_code'] }})</td>
                            <td>{{ $item['availability'] }}%</td>
                            <td>{{ $item['performance'] }}%</td>
                            <td>{{ $item['quality'] }}%</td>
                            <td>
                                <span class="badge bg-{{ $item['oee'] >= 85 ? 'success' : ($item['oee'] >= 60 ? 'warning' : 'danger') }}">
                                    {{ $item['oee'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No OEE data available</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Calculate OEE Button -->
<div class="d-flex gap-2">
    <form action="{{ route('oee.calculate-daily') }}" method="POST">
        @csrf
        <button class="btn btn-primary"><i class="bi bi-calculator me-1"></i>Calculate Daily OEE</button>
    </form>
</div>
@endsection
