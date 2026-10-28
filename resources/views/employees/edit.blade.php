@extends('layouts.app')

@section('title', 'Edit Employee - MES')
@section('page-title', 'Edit Employee: ' . $employee->name)

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Employee</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.update', $employee) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                    <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror"
                           value="{{ old('employee_code', $employee->employee_code) }}" required>
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $employee->name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $employee->email) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control"
                           value="{{ old('mobile', $employee->mobile) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Registered RFID Card</label>
                    @php($selectedRfid = old('rfid_card_number', $employee->rfid_card_number))
                    <select name="rfid_card_number" class="form-select">
                        <option value="">No RFID card assigned</option>
                        @if($selectedRfid && !$rfidCards->contains(strtoupper(trim($selectedRfid))))
                            <option value="{{ $selectedRfid }}" selected>{{ $selectedRfid }}</option>
                        @endif
                        @foreach($rfidCards as $card)
                            @php($assignedTo = $rfidAssignments->get(strtoupper(trim($card))))
                            <option value="{{ $card }}" @disabled($assignedTo) @selected($selectedRfid === $card)>
                                {{ $card }}{{ $assignedTo ? ' — tagged to ' . $assignedTo : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Choose an available card registered in <code>rfid_register</code>.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">Select</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Designation</label>
                    <select name="designation_id" class="form-select">
                        <option value="">Select</option>
                        @foreach($designations as $des)
                            <option value="{{ $des->id }}" {{ $employee->designation_id == $des->id ? 'selected' : '' }}>
                                {{ $des->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ $employee->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
