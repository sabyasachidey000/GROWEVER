@extends('layouts.app')
@section('title', 'RFID Management - MES')
@section('page-title', 'RFID Management')
@section('content')

{{-- RFID Card Assignment Form --}}
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Tag RFID Card</h5><a href="{{ route('rfid.punch-logs') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-clock-history me-1"></i>Punch Details</a></div>
    <div class="card-body">
        <form action="{{ route('rfid.assign') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine</label>
                    <select name="machine_id" class="form-select" required>
                        <option value="">Select Machine</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}">{{ $machine->machine_code }} — {{ $machine->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Operator</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Operator</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->rfid_card_number ? 'disabled' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                                @if($emp->rfid_card_number) — Already Tagged @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">RFID Card Number</label>
                    <input type="text" name="rfid_card_number" class="form-control" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Assign Card</button>
                <a href="{{ route('rfid.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Assigned RFID Cards View --}}
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-table me-2"></i>Assigned RFID Cards</h5></div>
    <div class="card-body p-0">
        <table class="table table-hover datatable mb-0">
            <thead><tr><th>Operator</th><th>Department</th><th>Designation</th><th>RFID Card</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($employees as $emp)
                    @if($emp->rfid_card_number)
                    <tr>
                        <td><strong>{{ $emp->name }}</strong> <small class="text-muted">({{ $emp->employee_code }})</small></td>
                        <td>{{ $emp->department?->name ?? '-' }}</td>
                        <td>{{ $emp->designation?->name ?? '-' }}</td>
                        <td><code>{{ $emp->rfid_card_number }}</code></td>
                        <td>
                            <form action="{{ route('rfid.unassign', $emp) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Untag RFID card from {{ $emp->name }}?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger" title="Untag"><i class="bi bi-x-circle"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endif
                @empty
                <tr><td colspan="5" class="text-center text-muted">No operators</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
