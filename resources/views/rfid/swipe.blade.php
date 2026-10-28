@extends('layouts.app')
@section('title', 'RFID Swipe - MES')
@section('page-title', 'RFID Card Swipe')
@section('content')

{{-- Active Sessions with Force Logout --}}
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Active Sessions</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Machine</th><th>Operator</th><th>Shift</th><th>Login Time</th><th>Duration</th><th>Force Logout</th></tr></thead>
            <tbody>
                @forelse($activeSessions as $session)
                <tr>
                    <td><strong>{{ $session->machine->machine_code }}</strong> — {{ $session->machine->name }}</td>
                    <td>{{ $session->employee->name }} <small class="text-muted">({{ $session->employee->employee_code }})</small></td>
                    <td>{{ $session->shift?->name ?? '-' }}</td>
                    <td>{{ $session->login_time->format('H:i:s') }}</td>
                    <td>{{ $session->login_time->diffForHumans() }}</td>
                    <td>
                        <form action="{{ route('rfid.force-logout') }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Force logout {{ $session->employee->name }} from {{ $session->machine->name }}?')">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-left me-1"></i>Logout</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No active sessions</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Quick Swipe Form --}}
<div class="card mb-4">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Process Swipe</h5></div>
    <div class="card-body">
        <form action="{{ route('rfid.swipe.process') }}" method="POST" id="rfidForm">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine</label>
                    <select name="machine_id" class="form-select" required>
                        <option value="">Select Machine</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}">{{ $machine->machine_code }} — {{ $machine->name }}
                                @if($machine->currentOperator) — {{ $machine->currentOperator->employee->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">RFID Card Number</label>
                    <input type="text" name="rfid_card_number" class="form-control" id="rfidInput" required autofocus>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select" required>
                        <option value="swipe_in">Swipe In (Login)</option>
                        <option value="swipe_out">Swipe Out (Logout)</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Process Swipe</button>
                <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Swipe Logs --}}
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Swipe Logs</h5></div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead><tr><th>Time</th><th>Employee</th><th>Machine</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><small>{{ $log->created_at->format('d M Y, H:i:s') }}</small></td>
                    <td>{{ $log->employee?->name ?? $log->rfid_card_number }}</td>
                    <td>{{ $log->machine?->name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $log->action === 'swipe_in' ? 'success' : ($log->action === 'force_logout' ? 'danger' : 'secondary') }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No swipe logs</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-2 px-3">{{ $logs->links() }}</div>
    </div>
</div>

@endsection
