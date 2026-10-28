@extends('layouts.app')

@section('title', 'Machines - MES')
@section('page-title', 'Machine Management')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-cpu me-2"></i>Machines List</h5>
        @can('create machines')
            <a href="{{ route('machines.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Machine
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
                        <th>Type</th>
                        <th>Operation</th>
                        <th>Location</th>
                        <th>Capacity/hr</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $machine)
                        <tr>
                            <td><strong>{{ $machine->machine_code }}</strong></td>
                            <td>{{ $machine->name }}</td>
                            <td>{{ $machine->type }}</td>
                            <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $machine->operation_type)) }}</span></td>
                            <td>{{ $machine->location ?? '-' }}</td>
                            <td>{{ $machine->production_capacity_per_hour ?? '-' }}</td>
                            <td>
                                <span class="badge badge-status status-{{ $machine->status }}">
                                    {{ ucfirst($machine->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('machines.show', $machine) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#energyModal{{ $machine->id }}" title="Energy consumption">
                                        <i class="bi bi-lightning-charge"></i>
                                    </button>
                                    @can('edit machines')
                                        <a href="{{ route('machines.edit', $machine) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                    @endcan
                                    @if($machine->status !== 'breakdown')
                                        <form action="{{ route('machines.report-breakdown', $machine) }}" method="POST" class="d-inline" onsubmit="return confirm('Report a breakdown for {{ $machine->name }}?')">
                                            @csrf
                                            <button class="btn btn-outline-danger" title="Report breakdown"><i class="bi bi-exclamation-triangle"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No machines found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $machines->links() }}</div>
    </div>
</div>

@foreach($machines as $machine)
    @php
        $energy = $machineEnergySummaries[$machine->id] ?? null;
        $readings = $machineEnergyReadings[$machine->id] ?? collect();
        $chartData = $machineEnergyCharts[$machine->id] ?? [];
        $metricCards = $machineEnergyCards[$machine->id] ?? [];
        $fieldAnalysis = $machineEnergyFieldAnalysis[$machine->id] ?? collect();
        $alerts = $machineEnergyAlerts[$machine->id] ?? [];
    @endphp
    <div class="modal fade" id="energyModal{{ $machine->id }}" tabindex="-1" aria-labelledby="energyModalLabel{{ $machine->id }}" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-xl-down modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="energyModalLabel{{ $machine->id }}">
                        <i class="bi bi-lightning-charge me-2"></i>{{ $machine->machine_code }} - {{ $machine->name }} Energy Details Analysis
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($energy && $energy['note'])
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $energy['note'] }}
                        </div>
                    @endif

                    <div class="energy-monitor-head mb-4">
                        <div>
                            <div class="h5 mb-1">Live Energy Monitoring</div>
                            <div class="text-muted small">Last {{ $energy['reading_count'] ?? 0 }} readings from mapped energy meter data</div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge {{ ($energy['reading_count'] ?? 0) > 0 ? 'bg-success' : 'bg-secondary' }}">
                                {{ ($energy['reading_count'] ?? 0) > 0 ? 'Online' : 'No Data' }}
                            </span>
                            <a href="{{ route('reports.machine-energy-consumption') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i>Reports
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        @forelse($metricCards as $card)
                            <div class="col-md-4 col-lg-2">
                                <div class="energy-metric-tile">
                                    <div class="energy-metric-label">{{ $card['label'] }}</div>
                                    <div class="energy-metric-value">{{ $card['value'] }} <span>{{ $card['unit'] }}</span></div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-4">
                                <div class="energy-metric-tile">
                                    <div class="energy-metric-label">Consumption</div>
                                    <div class="energy-metric-value">{{ number_format($energy['consumption_kwh'] ?? 0, 3) }} <span>kWh</span></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="energy-metric-tile">
                                    <div class="energy-metric-label">Readings</div>
                                    <div class="energy-metric-value">{{ $energy['reading_count'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="energy-metric-tile">
                                    <div class="energy-metric-label">Period</div>
                                    <div class="energy-metric-value energy-metric-date">{{ $energy['start_date'] ?? '-' }} to {{ $energy['end_date'] ?? '-' }}</div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-lg-9">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="energy-chart-panel">
                                        <div class="energy-chart-title">Voltage (L1, L2, L3)</div>
                                        <div class="energy-chart-wrap"><canvas id="voltageChart{{ $machine->id }}"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="energy-chart-panel">
                                        <div class="energy-chart-title">Current Watt</div>
                                        <div class="energy-chart-wrap"><canvas id="wattChart{{ $machine->id }}"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="energy-chart-panel">
                                        <div class="energy-chart-title">Current (L1, L2, L3)</div>
                                        <div class="energy-chart-wrap"><canvas id="currentChart{{ $machine->id }}"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="energy-chart-panel">
                                        <div class="energy-chart-title">Power Factor (Avg)</div>
                                        <div class="energy-chart-wrap"><canvas id="pfChart{{ $machine->id }}"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="energy-chart-panel">
                                        <div class="energy-chart-title">Frequency</div>
                                        <div class="energy-chart-wrap"><canvas id="frequencyChart{{ $machine->id }}"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="energy-chart-panel">
                                        <div class="energy-chart-title">Energy Import</div>
                                        <div class="energy-chart-wrap"><canvas id="energyChart{{ $machine->id }}"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="energy-alert-panel h-100">
                                <div class="energy-alert-title"><i class="bi bi-bell-fill me-1"></i>Alerts</div>
                                <div class="energy-alert-body">
                                    @foreach($alerts as $alert)
                                        <div class="{{ $alert === 'No active alerts.' ? 'text-muted' : 'text-danger' }}">{{ $alert }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="text-muted small">Period</div>
                                <div class="fw-semibold">{{ $energy['start_date'] ?? '-' }} to {{ $energy['end_date'] ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="text-muted small">Consumption</div>
                                <div class="fs-4 fw-bold text-success">{{ number_format($energy['consumption_kwh'] ?? 0, 3) }} kWh</div>
                                <div class="text-muted small">{{ number_format($energy['consumption_wh'] ?? 0, 3) }} Wh</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="text-muted small">First Reading</div>
                                <div class="fw-semibold">{{ $energy['first_reading_at'] ? \Carbon\Carbon::parse($energy['first_reading_at'])->format('Y-m-d H:i') : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100 bg-white">
                                <div class="text-muted small">Last Reading</div>
                                <div class="fw-semibold">{{ $energy['last_reading_at'] ? \Carbon\Carbon::parse($energy['last_reading_at'])->format('Y-m-d H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">Recent Energy Readings</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Wh Import</th>
                                    <th>Watt Sum</th>
                                    <th>Current Sum</th>
                                    <th>Voltage Avg</th>
                                    <th>Frequency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($readings as $reading)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($reading->timestamp)->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $reading->wh_import !== null ? number_format((float) $reading->wh_import, 3) : '-' }}</td>
                                        <td>{{ $reading->watt_sum !== null ? number_format((float) $reading->watt_sum, 3) : '-' }}</td>
                                        <td>{{ $reading->current_sum !== null ? number_format((float) $reading->current_sum, 3) : '-' }}</td>
                                        <td>{{ $reading->voltage_avg !== null ? number_format((float) $reading->voltage_avg, 3) : '-' }}</td>
                                        <td>{{ $reading->frequency !== null ? number_format((float) $reading->frequency, 3) : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">No mapped energy readings found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h6 class="mt-4 mb-3">All Fields Analysis</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover align-middle energy-analysis-table">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Latest</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Average</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fieldAnalysis as $field)
                                    <tr>
                                        <td><code>{{ $field['field'] }}</code><div class="text-muted small">{{ $field['label'] }}</div></td>
                                        <td>{{ $field['latest'] }}</td>
                                        <td>{{ $field['min'] }}</td>
                                        <td>{{ $field['max'] }}</td>
                                        <td>{{ $field['avg'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">No mapped field data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('reports.machine-energy-consumption') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i>Open Report
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        <script type="application/json" id="energyChartData{{ $machine->id }}">@json($chartData)</script>
    </div>
@endforeach
@endsection

@push('styles')
<style>
    .energy-monitor-head {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .energy-metric-tile,
    .energy-chart-panel,
    .energy-alert-panel {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .energy-metric-tile {
        min-height: 96px;
        padding: 1rem;
        text-align: center;
    }

    .energy-metric-label {
        color: #111827;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .energy-metric-value {
        color: #0d6efd;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.25;
        margin-top: 0.5rem;
        overflow-wrap: anywhere;
    }

    .energy-metric-value span {
        font-size: 0.9rem;
    }

    .energy-metric-date {
        color: #111827;
        font-size: 0.95rem;
    }

    .energy-chart-title {
        background: linear-gradient(90deg, #0d6efd, #6610f2);
        border-radius: 8px 8px 0 0;
        color: #fff;
        font-weight: 700;
        padding: 0.75rem 1rem;
        text-align: center;
    }

    .energy-chart-wrap {
        height: 260px;
        padding: 1rem;
    }

    .energy-alert-panel {
        border-color: rgba(220, 53, 69, 0.3);
        overflow: hidden;
    }

    .energy-alert-title {
        background: #dc3545;
        color: #fff;
        font-weight: 700;
        padding: 0.75rem 1rem;
        text-align: center;
    }

    .energy-alert-body {
        background: #fff5f5;
        min-height: 220px;
        padding: 1rem;
        text-align: center;
    }

    .energy-analysis-table code {
        color: #0f172a;
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .energy-monitor-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const energyCharts = {};

    function buildEnergyLineChart(canvasId, labels, datasets, yTitle) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || energyCharts[canvasId]) {
            return;
        }

        const hasData = labels.length && datasets.some((dataset) => dataset.data.some((value) => value !== null));
        if (!hasData) {
            const parent = canvas.parentElement;
            parent.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">No chart data</div>';
            return;
        }

        energyCharts[canvasId] = new Chart(canvas, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                elements: { point: { radius: 2 }, line: { tension: 0.25, borderWidth: 2 } },
                plugins: { legend: { position: 'top', labels: { boxWidth: 18 } } },
                scales: {
                    x: { title: { display: true, text: 'Time' }, ticks: { maxRotation: 55, minRotation: 35 } },
                    y: { title: { display: true, text: yTitle } }
                }
            }
        });
    }

    function initEnergyCharts(machineId) {
        const dataEl = document.getElementById('energyChartData' + machineId);
        if (!dataEl) {
            return;
        }

        const data = JSON.parse(dataEl.textContent || '{}');
        const labels = data.labels || [];

        buildEnergyLineChart('voltageChart' + machineId, labels, [
            { label: 'Voltage L1', data: data.voltage?.l1 || [], borderColor: '#0d6efd', backgroundColor: 'transparent' },
            { label: 'Voltage L2', data: data.voltage?.l2 || [], borderColor: '#198754', backgroundColor: 'transparent' },
            { label: 'Voltage L3', data: data.voltage?.l3 || [], borderColor: '#dc3545', backgroundColor: 'transparent' }
        ], 'Voltage (V)');

        buildEnergyLineChart('wattChart' + machineId, labels, [
            { label: 'Watt L1', data: data.watt?.l1 || [], borderColor: '#0d6efd', backgroundColor: 'transparent' },
            { label: 'Watt L2', data: data.watt?.l2 || [], borderColor: '#dc3545', backgroundColor: 'transparent' },
            { label: 'Watt L3', data: data.watt?.l3 || [], borderColor: '#198754', backgroundColor: 'transparent' },
            { label: 'Watt Sum', data: data.watt?.sum || [], borderColor: '#6f42c1', backgroundColor: 'transparent' }
        ], 'Watt');

        buildEnergyLineChart('currentChart' + machineId, labels, [
            { label: 'Current L1', data: data.current?.l1 || [], borderColor: '#0d6efd', backgroundColor: 'transparent' },
            { label: 'Current L2', data: data.current?.l2 || [], borderColor: '#198754', backgroundColor: 'transparent' },
            { label: 'Current L3', data: data.current?.l3 || [], borderColor: '#dc3545', backgroundColor: 'transparent' },
            { label: 'Current Sum', data: data.current?.sum || [], borderColor: '#6f42c1', backgroundColor: 'transparent' }
        ], 'Current (A)');

        buildEnergyLineChart('pfChart' + machineId, labels, [
            { label: 'Average Power Factor', data: data.power_factor?.avg || [], borderColor: '#6f42c1', backgroundColor: 'transparent' },
            { label: 'PF L1', data: data.power_factor?.l1 || [], borderColor: '#0d6efd', backgroundColor: 'transparent' },
            { label: 'PF L2', data: data.power_factor?.l2 || [], borderColor: '#198754', backgroundColor: 'transparent' },
            { label: 'PF L3', data: data.power_factor?.l3 || [], borderColor: '#dc3545', backgroundColor: 'transparent' }
        ], 'Power Factor');

        buildEnergyLineChart('frequencyChart' + machineId, labels, [
            { label: 'Frequency (Hz)', data: data.frequency?.value || [], borderColor: '#198754', backgroundColor: 'transparent' }
        ], 'Hz');

        buildEnergyLineChart('energyChart' + machineId, labels, [
            { label: 'Wh Import', data: data.energy?.wh_import || [], borderColor: '#0d6efd', backgroundColor: 'transparent' },
            { label: 'VAh', data: data.energy?.vah || [], borderColor: '#6610f2', backgroundColor: 'transparent' }
        ], 'Energy');
    }

    document.querySelectorAll('[id^="energyModal"]').forEach(function(modal) {
        modal.addEventListener('shown.bs.modal', function() {
            initEnergyCharts(modal.id.replace('energyModal', ''));
        });
    });
</script>
@endpush
