@extends('layouts.app')

@section('title', 'Breakdown History - MES')
@section('page-title', 'Breakdown History')

@section('content')

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-primary">{{ $machineStats->sum('breakdowns_count') }}</h2>
                <small class="text-muted">Total Breakdowns</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-danger">{{ round($machineStats->sum('breakdowns_sum_duration_minutes') / 60, 1) }}h</h2>
                <small class="text-muted">Total Downtime</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-warning">{{ $machineStats->avg('breakdowns_count') > 0 ? round($machineStats->sum('breakdowns_sum_duration_minutes') / max(1, $machineStats->sum('breakdowns_count')), 0) : 0 }}m</h2>
                <small class="text-muted">Avg Repair Time</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h2 class="text-info">{{ $machineStats->filter(fn($m) => $m->breakdowns_count > 0)->count() }}</h2>
                <small class="text-muted">Machines Affected</small>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form class="d-flex gap-2 flex-wrap align-items-end">
            <div>
                <label class="form-label form-label-sm mb-1">Machine</label>
                <select name="machine_id" class="form-select form-select-sm" style="min-width:200px">
                    <option value="">All Machines</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" {{ request('machine_id') == $machine->id ? 'selected' : '' }}>
                            {{ $machine->machine_code }} - {{ $machine->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label form-label-sm mb-1">Status</label>
                <select name="status" class="form-select form-select-sm" style="min-width:150px">
                    <option value="">All</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div>
                <label class="form-label form-label-sm mb-1">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="form-label form-label-sm mb-1">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
        </form>
    </div>
</div>

{{-- Machine-Wise Breakdown Table --}}
<div class="card mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Machine-Wise Summary</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th class="text-center">Total Breakdowns</th>
                        <th class="text-center">Total Downtime</th>
                        <th class="text-center">Avg Downtime</th>
                        <th class="text-center">Open</th>
                        <th class="text-center">Closed</th>
                        <th>Last Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machineStats as $stat)
                        @if($stat->breakdowns_count > 0)
                        <tr>
                            <td>
                                <strong>{{ $stat->machine_code }}</strong> — {{ $stat->name }}
                                <span class="badge status-{{ $stat->status }} ms-2">{{ ucfirst($stat->status) }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $stat->breakdowns_count }}</td>
                            <td class="text-center">
                                @if($stat->breakdowns_sum_duration_minutes)
                                    {{ round($stat->breakdowns_sum_duration_minutes / 60, 1) }}h
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($stat->breakdowns_sum_duration_minutes && $stat->breakdowns_count > 0)
                                    {{ round($stat->breakdowns_sum_duration_minutes / $stat->breakdowns_count, 0) }}m
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">{{ $stat->breakdowns()->whereIn('status', ['open', 'in_progress'])->count() }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $stat->breakdowns()->whereIn('status', ['resolved', 'closed'])->count() }}</span>
                            </td>
                            <td>
                                @php($lastBreakdown = $stat->breakdowns()->latest('start_time')->first())
                                @if($lastBreakdown)
                                    <small>{{ $lastBreakdown->start_time->format('d M Y, H:i') }}</small>
                                    <span class="badge bg-{{ $lastBreakdown->breakdown_type === 'electrical' ? 'danger' : ($lastBreakdown->breakdown_type === 'mechanical' ? 'warning' : 'info') }} ms-1">
                                        {{ $lastBreakdown->breakdown_type }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No breakdown records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recent Breakdowns Detail --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Breakdowns</h6>
        <a href="{{ route('breakdowns.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($breakdowns as $bd)
                    <tr>
                        <td><strong>{{ $bd->machine->machine_code }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $bd->breakdown_type === 'electrical' ? 'danger' : ($bd->breakdown_type === 'mechanical' ? 'warning' : 'info') }}">
                                {{ str_replace('_', ' ', $bd->breakdown_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $bd->severity === 'critical' ? 'danger' : ($bd->severity === 'high' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($bd->severity) }}
                            </span>
                        </td>
                        <td><small>{{ $bd->start_time->format('d M Y, H:i') }}</small></td>
                        <td><small>{{ $bd->end_time?->format('d M Y, H:i') ?? '-' }}</small></td>
                        <td>{{ $bd->duration_minutes ? $bd->duration_minutes . 'm' : '-' }}</td>
                        <td>{{ $bd->assignedTo?->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $bd->status === 'closed' ? 'success' : ($bd->status === 'in_progress' ? 'primary' : ($bd->status === 'open' ? 'warning' : 'info')) }}">
                                {{ str_replace('_', ' ', ucfirst($bd->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('breakdowns.show', $bd) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No breakdown records</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($breakdowns->hasPages())
    <div class="card-footer">{{ $breakdowns->links() }}</div>
    @endif
</div>

@endsection
