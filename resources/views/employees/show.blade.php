@extends('layouts.app')

@section('title', 'Employee Profile - MES')
@section('page-title', $employee->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <h5>{{ $employee->name }}</h5>
                <p class="text-muted mb-1">{{ $employee->employee_code }}</p>
                <p class="text-muted mb-1">{{ $employee->designation?->name ?? 'No Designation' }}</p>
                <p class="text-muted mb-3">{{ $employee->department?->name ?? 'No Department' }}</p>
                <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'warning' }}">
                    {{ ucfirst($employee->status) }}
                </span>
                <hr>
                <div class="text-start">
                    <p class="mb-1"><i class="bi bi-envelope me-2"></i>{{ $employee->email ?? 'N/A' }}</p>
                    <p class="mb-1"><i class="bi bi-phone me-2"></i>{{ $employee->mobile ?? 'N/A' }}</p>
                    <p class="mb-1"><i class="bi bi-person-bounding-box me-2"></i>
                        @if($employee->face_descriptor)
                            <span class="text-success">Face enrolled</span>
                        @else
                            <span class="text-muted">Face not enrolled</span>
                        @endif
                    </p>
                    <p class="mb-1"><i class="bi bi-calendar me-2"></i>{{ $employee->date_of_joining?->format('d M Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Production History</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Date</th><th>Machine</th><th>Product</th><th>Weight (kg)</th></tr>
                    </thead>
                    <tbody>
                        @forelse($employee->productionRuns()->latest()->limit(10)->get() as $run)
                            <tr>
                                <td>{{ $run->start_time->format('d M Y') }}</td>
                                <td>{{ $run->machine->name }}</td>
                                <td>{{ $run->product->name }}</td>
                                <td>{{ $run->weight ? number_format($run->weight, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No production records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
