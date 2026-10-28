@extends('layouts.app')

@section('title', 'Reports - MES')
@section('page-title', 'Reports')

@section('content')
<div class="row g-4">
    @php
        $reports = [
            
            [
                'route' => 'reports.component-daily-production',
                'icon' => 'bi-clipboard-data',
                'title' => 'Component Daily Production Report',
                'desc' => 'Print-ready daily machine, operator, product and rejection report',
            ],
            [
                'route' => 'reports.qc-inspection',
                'icon' => 'bi-clipboard2-check',
                'title' => 'In-Process QC Inspection Report',
                'desc' => '3-hour QC checkpoint report by machine, product and shift',
            ],
            /*[
                'route' => 'reports.shift-production',
                'icon' => 'bi-clock',
                'title' => 'Shift Production Report',
                'desc' => 'Production by shift for a specific date',
            ],
            [
                'route' => 'reports.machine-utilization',
                'icon' => 'bi-gear',
                'title' => 'Machine Utilization Report',
                'desc' => 'Machine utilization rates over a date range',
            ],
            [
                'route' => 'reports.breakdown',
                'icon' => 'bi-tools',
                'title' => 'Breakdown Report',
                'desc' => 'Machine breakdown history and downtime analysis',
            ],
            [
                'route' => 'reports.operator-performance',
                'icon' => 'bi-person-badge',
                'title' => 'Operator Performance Report',
                'desc' => 'Production output and rejection rates by operator',
            ],
            [
                'route' => 'reports.rejection-analysis',
                'icon' => 'bi-x-octagon',
                'title' => 'Rejection Analysis Report',
                'desc' => 'Rejection breakdown by reason, product, and machine',
            ],
            [
                'route' => 'reports.oee',
                'icon' => 'bi-bar-chart-line',
                'title' => 'OEE Report',
                'desc' => 'Overall Equipment Effectiveness metrics',
            ],
            [
                'route' => 'reports.maintenance',
                'icon' => 'bi-wrench-adjustable',
                'title' => 'Maintenance Report',
                'desc' => 'Maintenance costs and downtime by breakdown type',
            ],
            [
                'route' => 'reports.machine-energy-consumption',
                'icon' => 'bi-lightning-charge',
                'title' => 'Machine Energy Consumption',
                'desc' => 'Machine-wise energy consumption from energy readings',
            ],*/
        ];
    @endphp

    @foreach($reports as $report)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi {{ $report['icon'] }} fs-1 text-primary"></i>
                    </div>
                    <h6 class="card-title">{{ $report['title'] }}</h6>
                    <p class="card-text text-muted small">{{ $report['desc'] }}</p>
                    <a href="{{ route($report['route']) }}" class="btn btn-sm btn-outline-primary">
                        Generate Report
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
