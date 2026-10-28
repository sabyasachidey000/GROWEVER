@extends('layouts.app')

@section('title', 'Production Tracking - MES')
@section('page-title', 'Production Tracking')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Production Records</h5>
        <div class="d-flex gap-2">
            <form class="d-flex gap-2">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', today()) }}">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button class="btn btn-sm btn-outline-primary">Filter</button>
            </form>
            @can('create production')
               <!--  <a href="{{ route('production.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>New Entry
                </a> -->
            @endcan
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Machine</th>
                        <th>Operator</th>
                        
                        <th>Shift</th>
                        <th>Total Count</th>
                        <th>Status</th>
                        <th>Approval</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productionRuns as $run)
                        <tr>
                            <td>{{ $run->start_time->format('d M Y') }}</td>
                            <td>{{ $run->machine->name }}</td>
                            <td>{{ $run->operator->name }}</td>
                           
                            <td>{{ $run->shift->name }}</td>
                            <td class="fw-bold">{{ $run->weight ? number_format($run->weight, 2) : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $run->status === 'completed' ? 'success' : ($run->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                    {{ str_replace('_', ' ', ucfirst($run->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($run->approval)
                                    <span class="badge bg-{{ $run->approval->status === 'approved' ? 'success' : ($run->approval->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($run->approval->status) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not Submitted</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('production.show', $run) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if(!$run->approval?->is_locked)
                                        <!-- <a href="{{ route('production.edit', $run) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a> -->
                                        <form action="{{ route('production.submit-approval', $run) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-outline-success" title="Submit for Approval"><i class="bi bi-hand-thumbs-up"></i></button>
                                        </form>
                                    @endif
                                    @if($run->status !== 'completed')
                                        <form action="{{ route('production.complete', $run) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-outline-info" title="Complete"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No production records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $productionRuns->links() }}</div>
    </div>
</div>
@endsection
