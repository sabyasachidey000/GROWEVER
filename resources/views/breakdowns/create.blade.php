@extends('layouts.app')

@section('title', 'Report Breakdown - MES')
@section('page-title', 'Report Machine Breakdown')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Report New Breakdown</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('breakdowns.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine <span class="text-danger">*</span></label>
                    <select name="machine_id" class="form-select @error('machine_id') is-invalid @enderror" required>
                        <option value="">Select Machine</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->machine_code }} - {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Reported By <span class="text-danger">*</span></label>
                    <select name="reported_by" class="form-select @error('reported_by') is-invalid @enderror" required>
                        <option value="">Select</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('reported_by') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->employee_code }} - {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Assigned To</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Breakdown Type <span class="text-danger">*</span></label>
                    <select name="breakdown_type" class="form-select" required>
                        @foreach(['mechanical','electrical','hydraulic','pneumatic','tool_failure','material_shortage','software','other'] as $type)
                            <option value="{{ $type }}" {{ old('breakdown_type') === $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Severity <span class="text-danger">*</span></label>
                    <select name="severity" class="form-select" required>
                        <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger"><i class="bi bi-check-lg me-1"></i>Report Breakdown</button>
                <a href="{{ route('breakdowns.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
