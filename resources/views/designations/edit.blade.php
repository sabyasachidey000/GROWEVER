@extends('layouts.app')
@section('title', 'Edit Designation - MES')
@section('page-title', 'Edit: ' . $designation->name)
@section('content')
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Designation</h5></div>
    <div class="card-body">
        <form action="{{ route('designations.update', $designation) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Designation Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $designation->code) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Designation Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $designation->name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select">
                        @foreach(['manager','supervisor','team_lead','senior','staff','junior','trainee'] as $l)
                            <option value="{{ $l }}" {{ old('level', $designation->level) === $l ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $l)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $designation->description) }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $designation->is_active) ? 'checked' : '' }} id="isActive">
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('designations.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
