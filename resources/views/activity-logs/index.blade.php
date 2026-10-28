@extends('layouts.app')

@section('title', 'Activity Logs - MES')
@section('page-title', 'Activity Logs')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>System Activity Logs</h5>
    </div>
    <div class="card-body">
        <form class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-outline-primary">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('d M Y H:i:s') }}</small></td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="badge bg-secondary">{{ $log->log_name ?? 'general' }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td><small>{{ $log->ip_address }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No activity logs found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
