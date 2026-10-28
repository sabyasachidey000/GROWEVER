@extends('layouts.app')

@section('title', 'Edit Breakdown - MES')
@section('page-title', 'Edit Breakdown #' . $breakdown->id)

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Breakdown</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('breakdowns.update', $breakdown) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine <span class="text-danger">*</span></label>
                    <select name="machine_id" class="form-select" required>
                        @foreach($machines as $m)
                            <option value="{{ $m->id }}" {{ $breakdown->machine_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Assigned To</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $breakdown->assigned_to == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Breakdown Type <span class="text-danger">*</span></label>
                    <select name="breakdown_type" class="form-select" required>
                        @foreach(['mechanical','electrical','hydraulic','pneumatic','tool_failure','material_shortage','software','other'] as $type)
                            <option value="{{ $type }}" {{ $breakdown->breakdown_type === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Severity <span class="text-danger">*</span></label>
                    <select name="severity" class="form-select" required>
                        @foreach(['low','medium','high','critical'] as $s)
                            <option value="{{ $s }}" {{ $breakdown->severity === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['open','in_progress','resolved','closed'] as $s)
                            <option value="{{ $s }}" {{ $breakdown->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Repair Cost</label>
                    <input type="number" name="repair_cost" class="form-control" value="{{ $breakdown->repair_cost }}" step="0.01">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="2" required>{{ old('description', $breakdown->description) }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Root Cause</label>
                    <textarea name="root_cause" class="form-control" rows="2">{{ old('root_cause', $breakdown->root_cause) }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Repair Action</label>
                    <textarea name="repair_action" class="form-control" rows="2">{{ old('repair_action', $breakdown->repair_action) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('breakdowns.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
