@extends('layouts.app')

@section('title', 'New Production Entry - MES')
@section('page-title', 'New Production Entry')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Record Production</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('production.store') }}" method="POST">
            @csrf
            <input type="hidden" name="operator_session_id" id="sessionInput" value="{{ old('operator_session_id') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Operator <span class="text-danger">*</span></label>
                    <select name="operator_id" class="form-select @error('operator_id') is-invalid @enderror" id="operatorSelect" required>
                        <option value="">Select Operator</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>
                                {{ $op->employee_code }} - {{ $op->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Machine <span class="text-danger">*</span></label>
                    <input type="hidden" name="machine_id" id="machineHidden" value="{{ old('machine_id') }}">
                    <input type="text" id="machineDisplay" class="form-control" readonly placeholder="Select operator to load assigned machine" value="">
                    @error('machine_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" id="productSelect" required>
                        <option value="">Select Operator First</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->product_code }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                    <select name="shift_id" class="form-select" required>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" step="0.01">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="stopped">Stopped</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Production Entry</button>
                <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('operatorSelect').addEventListener('change', function() {
    var operatorId = this.value;
    var machineDisplay = document.getElementById('machineDisplay');
    var machineHidden = document.getElementById('machineHidden');
    var productSelect = document.getElementById('productSelect');
    var sessionInput = document.getElementById('sessionInput');
    var shiftSelect = document.querySelector('select[name="shift_id"]');

    // Reset
    machineDisplay.value = '';
    machineHidden.value = '';
    sessionInput.value = '';
    productSelect.innerHTML = '<option value="">Select Product</option>';

    if (!operatorId) {
        machineDisplay.placeholder = 'Select operator to load assigned machine';
        return;
    }

    fetch('{{ route('api.production.operator-data', '__id__') }}'.replace('__id__', operatorId))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.machine) {
                machineDisplay.value = data.machine.machine_code + ' - ' + data.machine.name;
                machineHidden.value = data.machine.id;
                sessionInput.value = data.session_id;

                // Auto-select shift if available
                if (data.shift_id && shiftSelect) {
                    var options = shiftSelect.querySelectorAll('option');
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == data.shift_id) {
                            options[i].selected = true;
                            break;
                        }
                    }
                }

                // Populate products
                if (data.products && data.products.length > 0) {
                    data.products.forEach(function(p) {
                        var opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.product_code + ' - ' + p.name;
                        productSelect.appendChild(opt);
                    });
                } else {
                    var opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'No products tagged to this machine';
                    opt.disabled = true;
                    productSelect.appendChild(opt);
                }
            } else {
                machineDisplay.value = '';
                machineDisplay.placeholder = data.message || 'No active session';
            }
        })
        .catch(function(err) {
            console.error('Error fetching operator data:', err);
            machineDisplay.placeholder = 'Error loading machine assignment';
        });
});
</script>
@endpush
