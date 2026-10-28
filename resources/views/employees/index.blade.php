@extends('layouts.app')

@section('title', 'Employees - MES')
@section('page-title', 'Employee Management')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Employees List</h5>
        @can('create employees')
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Employee
            </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Mobile</th>
                        <th>Face</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td><strong>{{ $employee->employee_code }}</strong></td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->department?->name ?? '-' }}</td>
                            <td>{{ $employee->designation?->name ?? '-' }}</td>
                            <td>{{ $employee->mobile ?? '-' }}</td>
                            <td>
                                @if($employee->face_descriptor)
                                    <span class="badge bg-success"><i class="bi bi-person-bounding-box"></i> Enrolled</span>
                                @else
                                    <span class="badge bg-secondary">Not Enrolled</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('edit employees')
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete employees')
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No employees found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $employees->links() }}</div>
    </div>
</div>
@endsection
