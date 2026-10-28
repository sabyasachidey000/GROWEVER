@extends('layouts.app')

@section('title', 'QC Inspection Entry - MES')
@section('page-title', 'QC Inspection Entry')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div><h4 class="mb-1"><i class="bi bi-clipboard2-check me-2"></i>Machine QC Inspection</h4><p class="text-muted mb-0">Record one inspection against a machine and its assigned product.</p></div>
        <a href="{{ route('qc-inspections.index') }}" class="btn btn-outline-secondary"><i class="bi bi-list-ul me-1"></i>View Reports</a>
    </div>
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>@endif
    <form method="POST" action="{{ route('qc-inspections.store') }}" id="qc-inspection-form">
        @csrf
        <div class="card mb-4"><div class="card-header"><strong>Inspection Information</strong></div><div class="card-body row g-3">
            <div class="col-md-3"><label class="form-label">Inspection Date <span class="text-danger">*</span></label><input type="date" name="inspection_date" class="form-control @error('inspection_date') is-invalid @enderror" value="{{ old('inspection_date', now()->toDateString()) }}" required>@error('inspection_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label class="form-label">Inspection Time <span class="text-danger">*</span></label><input type="time" name="inspection_time" class="form-control @error('inspection_time') is-invalid @enderror" value="{{ old('inspection_time', now()->format('H:i')) }}" required>@error('inspection_time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label class="form-label">Shift <span class="text-danger">*</span></label><select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required><option value="">Select shift</option>@foreach($shifts as $shift)<option value="{{ $shift->id }}" @selected(old('shift_id', $currentShift?->id) == $shift->id)>{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>@endforeach</select>@error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label class="form-label">Inspection Type <span class="text-danger">*</span></label><select name="inspection_type" class="form-select" required>@foreach(['first_piece' => 'First Piece', 'in_process' => 'In Process', 'final' => 'Final', 'patrol' => 'Patrol', 'receiving' => 'Receiving'] as $value => $label)<option value="{{ $value }}" @selected(old('inspection_type', 'in_process') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Machine <span class="text-danger">*</span></label><select name="machine_id" id="machine_id" class="form-select @error('machine_id') is-invalid @enderror" required><option value="">Select machine</option>@foreach($machines as $machine)<option value="{{ $machine->id }}" @selected(old('machine_id') == $machine->id)>{{ $machine->machine_code }} — {{ $machine->name }}</option>@endforeach</select>@error('machine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Assigned Product <span class="text-danger">*</span></label><input type="hidden" name="product_id" id="product_id" value="{{ old('product_id') }}"><input type="text" id="product_display" class="form-control" readonly placeholder="Select a machine to load its assigned product"><div id="product_feedback" class="form-text">Product is resolved from the machine assignment when saved.</div></div>
        </div></div>

        <div class="card mb-4"><div class="card-header"><strong>Quality Parameters</strong></div><div class="card-body row g-3">
            @php($resultFields = ['thread_gauge_go' => 'Thread Plug Gauge — GO', 'thread_gauge_no_go' => 'Thread Plug Gauge — NO GO', 'emt_gauge_after_tapping' => 'EMT Gauge After Tapping', 'thread_flat' => 'Thread Flat', 'tap_mark' => 'Tap Mark', 'offset_value' => 'Offset', 'angularity' => 'Angularity'])
            @foreach($resultFields as $field => $label)<div class="col-md-4"><label class="form-label">{{ $label }}</label><select name="{{ $field }}" class="form-select @error($field) is-invalid @enderror"><option value="">Select result</option>@foreach(['Pass', 'Fail', 'AB', 'N/A'] as $value)<option value="{{ $value }}" @selected(old($field) === $value)>{{ $value }}</option>@endforeach</select>@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endforeach
            @foreach(['burr_on_face' => 'Burrs on Face', 'burr_on_thread' => 'Burrs on Thread'] as $field => $label)<div class="col-md-4"><label class="form-label">{{ $label }}</label><select name="{{ $field }}" class="form-select @error($field) is-invalid @enderror"><option value="">Select result</option>@foreach(['Present', 'Absent', 'AB', 'N/A'] as $value)<option value="{{ $value }}" @selected(old($field) === $value)>{{ $value }}</option>@endforeach</select>@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endforeach
            <div class="col-md-6"><label class="form-label">Concentricity / T.P.G. Gauge / Minor Diameter</label><input name="concentricity_minor_dia" class="form-control" value="{{ old('concentricity_minor_dia') }}" placeholder="Measurement or observation"></div>
            <div class="col-md-6"><label class="form-label">Visual Defects</label><input name="visual_defects" class="form-control" value="{{ old('visual_defects') }}" placeholder="Describe defects, if any"></div>
        </div></div>

        <div class="card mb-4"><div class="card-header"><strong>Inspection Quantity</strong></div><div class="card-body row g-3">
            @foreach(['sample_size' => 'Sample Quantity', 'passed_quantity' => 'Passed Quantity', 'failed_quantity' => 'Rejected Quantity'] as $field => $label)<div class="col-md-4"><label class="form-label">{{ $label }} <span class="text-danger">*</span></label><input type="number" min="0" name="{{ $field }}" id="{{ $field }}" class="form-control @error($field) is-invalid @enderror" value="{{ old($field, 0) }}" required>@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endforeach
            <div class="col-12"><div id="quantity-feedback" class="alert alert-info mb-0">Sample Quantity = Passed Quantity + Rejected Quantity</div></div>
        </div></div>

        <div class="card mb-4"><div class="card-header"><strong>Approval</strong></div><div class="card-body row g-3">
            @php($loggedInEmployee = auth()->user()?->employee)
            <div class="col-md-3"><label class="form-label">QC Person <span class="text-danger">*</span></label><input class="form-control" value="{{ $loggedInEmployee ? $loggedInEmployee->employee_code . ' — ' . $loggedInEmployee->name : 'No employee profile linked' }}" readonly>@error('inspector')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label class="form-label">Production Signature</label><input class="form-control" value="{{ $loggedInEmployee?->name ?? 'No employee profile linked' }}" readonly></div>
            <div class="col-md-3"><label class="form-label">Checked By</label><input class="form-control" value="{{ $loggedInEmployee?->name ?? 'No employee profile linked' }}" readonly></div>
            <div class="col-md-3"><label class="form-label">Result <span class="text-danger">*</span></label><select name="result" class="form-select" required>@foreach(['pass' => 'Pass', 'fail' => 'Fail', 'conditional' => 'Conditional'] as $value => $label)<option value="{{ $value }}" @selected(old('result', 'pass') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label">Remarks / RM Information</label><textarea name="remarks" rows="2" class="form-control">{{ old('remarks') }}</textarea></div>
        </div></div>
        <div class="d-flex justify-content-end gap-2 mb-4"><a href="{{ route('qc-inspections.index') }}" class="btn btn-outline-secondary">Cancel</a><button type="reset" class="btn btn-outline-warning">Reset</button><button type="submit" id="save-button" class="btn btn-primary">Save Inspection</button></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const machine = document.getElementById('machine_id'), productId = document.getElementById('product_id'), productDisplay = document.getElementById('product_display'), productFeedback = document.getElementById('product_feedback'), save = document.getElementById('save-button');
    const endpoint = @json(route('qc-inspections.machine-product', ['machine' => '__machine__']));
    function clearProduct(message) { productId.value = ''; productDisplay.value = ''; productDisplay.placeholder = message; productFeedback.textContent = message; productFeedback.className = 'form-text text-danger'; save.disabled = true; }
    async function loadProduct() { if (!machine.value) { clearProduct('Select a machine to load its assigned product.'); return; } save.disabled = true; productDisplay.value = ''; productDisplay.placeholder = 'Loading assigned product…'; productFeedback.textContent = 'Loading machine assignment…'; productFeedback.className = 'form-text text-muted'; try { const response = await fetch(endpoint.replace('__machine__', encodeURIComponent(machine.value)), {headers: {'Accept': 'application/json'}}), data = await response.json(); if (!response.ok || !data.product) { clearProduct(data.message || 'No active product is assigned to this machine.'); return; } productId.value = data.product.id; productDisplay.value = data.product.product_code + ' — ' + data.product.name + (data.product.unit_of_measure ? ' (' + data.product.unit_of_measure + ')' : ''); productFeedback.textContent = 'Product loaded from the current machine assignment.'; productFeedback.className = 'form-text text-success'; save.disabled = false; } catch (error) { clearProduct('Unable to load the machine product. Please try again.'); } }
    machine.addEventListener('change', loadProduct); if (machine.value) loadProduct();
    const sample = document.getElementById('sample_size'), passed = document.getElementById('passed_quantity'), failed = document.getElementById('failed_quantity'), feedback = document.getElementById('quantity-feedback');
    function quantitiesValid() { const s = Number(sample.value), p = Number(passed.value), f = Number(failed.value), valid = Number.isInteger(s) && Number.isInteger(p) && Number.isInteger(f) && s >= 0 && p >= 0 && f >= 0 && s === p + f; feedback.className = 'alert mb-0 ' + (valid ? 'alert-success' : 'alert-danger'); feedback.textContent = valid ? 'Quantity verified: ' + s + ' samples = ' + p + ' passed + ' + f + ' rejected (' + (s ? ((p / s) * 100).toFixed(1) : '0.0') + '% pass).' : 'Quantity mismatch: Sample Quantity must equal Passed Quantity + Rejected Quantity.'; return valid; }
    [sample, passed, failed].forEach(input => input.addEventListener('input', quantitiesValid)); document.getElementById('qc-inspection-form').addEventListener('submit', event => { if (!quantitiesValid() || !productId.value) { event.preventDefault(); feedback.scrollIntoView({behavior: 'smooth', block: 'center'}); } }); quantitiesValid();
});
</script>
@endpush
