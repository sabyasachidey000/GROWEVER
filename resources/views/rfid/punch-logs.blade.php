@extends('layouts.app')

@section('title', 'RFID Punch Details - MES')
@section('page-title', 'RFID Punch Details')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div><h5 class="mb-1"><i class="bi bi-clock-history me-2"></i>Employee RFID Punch Details</h5><small class="text-muted">Paired login and logout records from mapped RFID devices.</small></div>
        <a href="{{ route('rfid.index') }}" class="btn btn-outline-primary btn-sm">RFID Management</a>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label">Date</label><input type="date" name="date" value="{{ request('date') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Employee</label><select name="employee_id" class="form-select"><option value="">All employees</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->name }} ({{ $employee->employee_code }})</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">Machine</label><select name="machine_id" class="form-select"><option value="">All machines</option>@foreach($machines as $machine)<option value="{{ $machine->id }}" @selected((string) request('machine_id') === (string) $machine->id)>{{ $machine->machine_code }} — {{ $machine->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="out" @selected(request('status') === 'out')>Out</option></select></div>
            <div class="col-md-2"><label class="form-label">Search</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, code, card"></div>
            <div class="col-12 d-flex gap-2"><button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button><a href="{{ route('rfid.punch-logs') }}" class="btn btn-outline-secondary btn-sm">Reset</a></div>
        </form>
    </div>
    <div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead><tr><th>Employee</th><th>Code</th><th>RFID Card</th><th>Machine</th><th>Device</th><th>Login</th><th>Logout</th><th>Status</th></tr></thead><tbody>
        @forelse($punches as $punch)
            <tr><td>{{ $punch->employee?->name }}</td><td><code>{{ $punch->employee?->employee_code }}</code></td><td><code>{{ $punch->rfid_card_number }}</code></td><td>{{ $punch->machine?->machine_code }} <small class="text-muted">{{ $punch->machine?->name }}</small></td><td>{{ $punch->device_id }}</td><td>{{ $punch->punch_time->format('d M Y, h:i:s A') }}</td><td>{{ $punch->logoutPunch?->punch_time?->format('d M Y, h:i:s A') ?? '—' }}</td><td><span class="badge bg-{{ $punch->logoutPunch ? 'secondary' : 'success' }}">{{ $punch->logoutPunch ? 'OUT' : 'ACTIVE' }}</span></td></tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No RFID punch sessions match these filters.</td></tr>
        @endforelse
    </tbody></table></div>
    <div class="card-body">{{ $punches->links() }}</div>
</div>
@endsection
