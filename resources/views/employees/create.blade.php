@extends('layouts.app')

@section('title', 'Add Employee - MES')
@section('page-title', 'Add New Employee')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>New Employee Registration</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                    <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror"
                           value="{{ old('employee_code') }}" required>
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                           value="{{ old('mobile') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Registered RFID Card</label>
                    <select name="rfid_card_number" class="form-select @error('rfid_card_number') is-invalid @enderror">
                        <option value="">No RFID card assigned</option>
                        @foreach($rfidCards as $card)
                            @php($assignedTo = $rfidAssignments->get(strtoupper(trim($card))))
                            <option value="{{ $card }}" @disabled($assignedTo) @selected(old('rfid_card_number') === $card)>
                                {{ $card }}{{ $assignedTo ? ' — tagged to ' . $assignedTo : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Cards are loaded from <code>rfid_register</code>; cards tagged to another operator cannot be selected.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control @error('date_of_joining') is-invalid @enderror"
                           value="{{ old('date_of_joining') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Designation</label>
                    <select name="designation_id" class="form-select @error('designation_id') is-invalid @enderror">
                        <option value="">Select Designation</option>
                        @foreach($designations as $des)
                            <option value="{{ $des->id }}" {{ old('designation_id') == $des->id ? 'selected' : '' }}>
                                {{ $des->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Emergency Contact</label>
                    <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Emergency Phone</label>
                    <input type="text" name="emergency_phone" class="form-control" value="{{ old('emergency_phone') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Employee
                </button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
