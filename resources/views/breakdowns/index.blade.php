@extends('layouts.app')

@section('title', 'Breakdowns - MES')
@section('page-title', 'Machine Breakdowns')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Breakdown Records</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('breakdowns.history') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-bar-chart me-1"></i>History
            </a>
            @can('create breakdowns')
                <!--<a href="{{ route('breakdowns.create') }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Report Breakdown
                </a>-->
            @endcan
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Start Time</th>
                        <th>Duration</th>
                        <th>Reported By</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($breakdowns as $bd)
                        <tr>
                            <td><strong>{{ $bd->machine->name }}</strong></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $bd->breakdown_type)) }}</td>
                            <td>
                                <span class="badge bg-{{ $bd->severity === 'critical' ? 'danger' : ($bd->severity === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($bd->severity) }}
                                </span>
                            </td>
                            <td>{{ $bd->start_time->format('d M Y H:i') }}</td>
                            <td>{{ $bd->duration_minutes ? $bd->duration_minutes . ' min' : 'In Progress' }}</td>
                            <td>{{ $bd->reportedBy->name }}</td>
                            <td>{{ $bd->assignedTo?->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $bd->status === 'open' ? 'danger' : ($bd->status === 'in_progress' ? 'warning' : ($bd->status === 'resolved' ? 'info' : 'success')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $bd->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('breakdowns.show', $bd) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if($bd->status !== 'closed')
                                        <a href="{{ route('breakdowns.edit', $bd) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                        @can('close breakdowns')
                                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#closeModal{{ $bd->id }}">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        @endcan
                                    @endif
                                   {{-- PDF --}}
                                    <a target="_blank" href="{{ route('breakdown.memo.pdf', $bd->id) }}"
                                       class="btn btn-outline-danger"
                                       title="Download Memo PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No breakdowns found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $breakdowns->links() }}</div>
    </div>
</div>

<!-- Close Modals -->
@foreach($breakdowns as $bd)
    @if($bd->status !== 'closed')
        <div class="modal fade" id="closeModal{{ $bd->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('breakdowns.close', $bd) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Close Breakdown - {{ $bd->machine->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Root Cause</label>
                                <textarea name="root_cause" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Repair Action</label>
                                <textarea name="repair_action" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Close Breakdown</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection
