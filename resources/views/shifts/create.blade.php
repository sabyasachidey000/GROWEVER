@extends('layouts.app')
@section('title', 'Add Shift - MES')
@section('page-title', 'Create New Shift')
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-clock me-2"></i>New Shift</h5></div>
    <div class="card-body">
        <form action="{{ route('shifts.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Shift Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Shift Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Night Shift</label>
                    <select name="is_night_shift" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Break Start</label>
                    <input type="time" name="break_start" class="form-control" value="{{ old('break_start') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Break End</label>
                    <input type="time" name="break_end" class="form-control" value="{{ old('break_end') }}">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Shift</button>
                <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
