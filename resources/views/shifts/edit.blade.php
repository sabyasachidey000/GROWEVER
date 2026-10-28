@extends('layouts.app')
@section('title', 'Edit Shift - MES')
@section('page-title', 'Edit: ' . $shift->name)
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Shift</h5></div>
    <div class="card-body">
        <form action="{{ route('shifts.update', $shift) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Shift Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $shift->name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Shift Code</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $shift->code) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Night Shift</label>
                    <select name="is_night_shift" class="form-select">
                        <option value="0" {{ !$shift->is_night_shift ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $shift->is_night_shift ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $shift->start_time) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $shift->end_time) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Break Start</label>
                    <input type="time" name="break_start" class="form-control" value="{{ $shift->break_start }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Break End</label>
                    <input type="time" name="break_end" class="form-control" value="{{ $shift->break_end }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
