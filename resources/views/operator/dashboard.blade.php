@extends('layouts.app')
@section('title', 'Operator Dashboard - MES')
@section('page-title', 'Operator Dashboard')

@php
    $operatorPhoto = $employee->profile_photo
        ? (str_starts_with($employee->profile_photo, 'http') ? $employee->profile_photo : asset('storage/' . ltrim($employee->profile_photo, '/')))
        : null;
@endphp

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($operatorPhoto)
                        <img src="{{ $operatorPhoto }}" alt="{{ $employee->name }}" class="rounded-circle border object-fit-cover" style="width: 64px; height: 64px;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 64px; height: 64px; font-size: 1.5rem;">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="text-muted mb-2">Operator</h6>
                        <h4 class="mb-1">{{ $employee->name }}</h4>
                        <div class="text-muted">{{ $employee->employee_code }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Assigned Machine</h6>
                <h4 class="mb-1">{{ $machine->machine_code }}</h4>
                <div class="text-muted">{{ $machine->name }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Current Session</h6>
                    <h4 class="mb-1">{{ $activeSession->login_time->format('H:i') }}</h4>
                    <div class="text-muted">{{ $activeSession->login_time->diffForHumans() }}</div>
                </div>
                <form action="{{ route('operator.logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($completedBreakdown)
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Breakdown #{{ $completedBreakdown->id }} for {{ $completedBreakdown->machine->name }} is {{ str_replace('_', ' ', $completedBreakdown->status) }}.
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <a href="{{ route('operator.production-entry') }}" class="btn btn-primary btn-lg w-100 py-3">
                    <i class="bi bi-speedometer2 me-2"></i>Production Entry
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('operator.breakdown-entry') }}" class="btn btn-danger btn-lg w-100 py-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>Breakdown Report
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Attendance Logs</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Time</th><th>Machine</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse($attendanceLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M, H:i') }}</td>
                            <td>{{ $log->machine?->machine_code ?? '-' }}</td>
                            <td>{{ str_replace('_', ' ', $log->action) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">No attendance logs</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-clipboard-pulse me-2"></i>Breakdown Status</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>ID</th><th>Reported</th><th>Type</th><th>Severity</th><th>Status</th><th>Assigned</th></tr></thead>
                    <tbody>
                        @forelse($breakdowns as $breakdown)
                        <tr>
                            <td>#{{ $breakdown->id }}</td>
                            <td>{{ $breakdown->start_time->format('d M, H:i') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $breakdown->breakdown_type)) }}</td>
                            <td>
                                <span class="badge bg-{{ $breakdown->severity === 'critical' ? 'danger' : ($breakdown->severity === 'high' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($breakdown->severity) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $breakdown->status === 'closed' ? 'success' : ($breakdown->status === 'resolved' ? 'info' : ($breakdown->status === 'in_progress' ? 'primary' : 'warning')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $breakdown->status)) }}
                                </span>
                            </td>
                            <td>{{ $breakdown->assignedTo?->name ?? 'Pending' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No breakdown reports</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Last One Month Production</h5>
                <span class="badge bg-primary">{{ number_format($monthlyTotalWeight, 2) }} kg</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Date</th><th>Product</th><th>Machine</th><th>Weight</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($productionRuns as $run)
                        <tr>
                            <td>{{ $run->start_time->format('d M Y, H:i') }}</td>
                            <td>{{ $run->product?->product_code }} - {{ $run->product?->name }}</td>
                            <td>{{ $run->machine?->machine_code }}</td>
                            <td>{{ number_format((float) $run->weight, 2) }} kg</td>
                            <td><span class="badge bg-success">{{ str_replace('_', ' ', $run->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No production records in the last month</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2 px-3">{{ $productionRuns->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.kiosk-keyboard')
@endpush
