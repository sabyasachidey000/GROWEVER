@extends('layouts.app')

@section('title', 'Report - MES')
@section('page-title', 'Report: ' . str_replace('_', ' ', ucfirst($filename)))

@section('content')
@if(request()->routeIs('reports.machine-energy-consumption'))
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.machine-energy-consumption') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->subDays(30)->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <button type="submit" name="format" value="excel" class="btn btn-success">
                        <i class="bi bi-file-excel me-1"></i>Export Excel
                    </button>
                    <button type="submit" name="format" value="pdf" class="btn btn-danger">
                        <i class="bi bi-file-pdf me-1"></i>Export PDF
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-grid me-1"></i>Reports
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>{{ str_replace('_', ' ', ucfirst($filename)) }}</h5>
        @unless(request()->routeIs('reports.machine-energy-consumption'))
        <div class="d-flex gap-2">
            <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['format' => 'pdf'])) }}" class="btn btn-sm btn-danger">
                <i class="bi bi-file-pdf me-1"></i>Export PDF
            </a>
            <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-sm btn-success">
                <i class="bi bi-file-excel me-1"></i>Export Excel
            </a>
        </div>
        @endunless
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="reportTable">
                <thead>
                    <tr>
                        @if($data->isNotEmpty())
                            @foreach(array_keys((array) $data->first()) as $column)
                                <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            @foreach((array) $row as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var $table = $('#reportTable');
        var $thead = $table.find('thead th');
        if ($thead.length === 0) {
            return; // No columns, skip DataTable initialization
        }
        $table.DataTable({
            "dom": 'Bfrtip',
            "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
            "pageLength": 25
        });
    });
</script>
@endpush
