@extends('layouts.app')

@section('title', 'Dashboard - MES')
@section('page-title', 'Dashboard')

@section('content')

<style>
    :root {
        --dash-ink: #0f172a;
        --dash-ink-light: #334155;
        --dash-muted: #64748b;
        --dash-border: #e2e8f0;
        --dash-surface: #ffffff;
        --dash-surface-2: #f8fafc;
        --dash-primary: #2563eb;
        --dash-primary-dark: #1d4ed8;
        --dash-primary-glow: rgba(37, 99, 235, 0.12);
        --dash-secondary: #64748b;
        --dash-success: #059669;
        --dash-success-glow: rgba(5, 150, 105, 0.10);
        --dash-warning: #d97706;
        --dash-warning-glow: rgba(217, 119, 6, 0.10);
        --dash-danger: #dc2626;
        --dash-danger-glow: rgba(220, 38, 38, 0.10);
        --dash-info: #0891b2;
        --dash-info-glow: rgba(8, 145, 178, 0.10);
        --dash-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 60%, #0891b2 100%);
        --dash-gradient-success: linear-gradient(135deg, #059669 0%, #10b981 100%);
        --dash-gradient-warning: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        --dash-gradient-danger: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        --dash-shadow-xs: 0 1px 3px rgba(15, 23, 42, 0.04);
        --dash-shadow-sm: 0 4px 12px rgba(15, 23, 42, 0.06);
        --dash-shadow-md: 0 8px 24px rgba(15, 23, 42, 0.08);
        --dash-shadow-lg: 0 16px 48px rgba(15, 23, 42, 0.10);
        --dash-radius: 20px;
        --dash-radius-sm: 12px;
        --dash-transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
    }

    * { box-sizing: border-box; }

    .dashboard-shell {
        color: var(--dash-ink);
        padding-bottom: 2rem;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .dashboard-shell::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background:
            radial-gradient(ellipse 900px 700px at 12% -10%, rgba(37, 99, 235, 0.05), transparent),
            radial-gradient(ellipse 700px 500px at 88% 110%, rgba(8, 145, 178, 0.04), transparent),
            radial-gradient(ellipse 600px 400px at 40% 120%, rgba(5, 150, 105, 0.03), transparent);
        z-index: -1;
        pointer-events: none;
    }

    .dashboard-shell::after {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.15), transparent);
        z-index: 0;
    }

    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--dash-success);
        margin-left: 1rem;
        padding: 0.35rem 0.85rem;
        background: rgba(5, 150, 105, 0.08);
        border: 1px solid rgba(5, 150, 105, 0.18);
        border-radius: 999px;
    }

    .live-dot {
        width: 7px;
        height: 7px;
        background: var(--dash-success);
        border-radius: 50%;
        animation: livePulse 2s ease-in-out infinite;
    }

    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.15); }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) both;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dashboard-heading {
        background-color: #0c2340;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: var(--dash-radius);
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        min-height: 150px;
        overflow: hidden;
        padding: 1.875rem 2.25rem;
        position: relative;
        box-shadow: var(--dash-shadow-lg);
        backdrop-filter: blur(20px);
    }

    .dashboard-heading:before {
        content: "";
        position: absolute;
        top: -70px;
        right: -50px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12), transparent 65%);
        pointer-events: none;
    }

    .dashboard-heading:after {
        content: "";
        position: absolute;
        bottom: -40px;
        left: -30px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08), transparent 65%);
        pointer-events: none;
    }

    .dashboard-heading-content {
        z-index: 2;
        position: relative;
    }

    .dashboard-heading h1 {
        font-size: clamp(1.75rem, 3vw, 2.5rem);
        font-weight: 800;
        letter-spacing: -0.05em;
        margin: 0 0 0.625rem;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    }

    .dashboard-heading p {
        color: rgba(255, 255, 255, 0.88);
        font-size: 1rem;
        margin: 0;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    .dashboard-stat-link {
        color: inherit;
        display: block;
        text-decoration: none;
    }

    .dashboard-shell .stat-card,
    .dashboard-shell .summary-stat,
    .dashboard-shell .dashboard-panel {
        background: var(--dash-surface) !important;
        border: 1px solid var(--dash-border) !important;
        border-radius: var(--dash-radius) !important;
        box-shadow: var(--dash-shadow-xs);
        color: var(--dash-ink);
        transition: var(--dash-transition);
        overflow: hidden;
        position: relative;
    }

    .dashboard-shell .stat-card::after,
    .dashboard-shell .dashboard-panel::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.35), transparent);
        opacity: 0;
        transition: var(--dash-transition);
    }

    .dashboard-shell .stat-card:hover::after,
    .dashboard-shell .dashboard-panel:hover::after { opacity: 1; }

    .dashboard-shell .stat-card:hover,
    .dashboard-shell .summary-stat:hover {
        box-shadow: var(--dash-shadow-md);
        transform: translateY(-4px);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .dashboard-shell .dashboard-panel:hover {
        box-shadow: var(--dash-shadow-md);
        transform: translateY(-2px);
        border-color: rgba(37, 99, 235, 0.15);
    }

    .dashboard-shell .stat-card {
        min-height: 136px;
        padding: 1.625rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .dashboard-shell .stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--dash-gradient);
        opacity: 0;
        transition: var(--dash-transition);
    }

    .dashboard-shell .stat-card:hover::before { opacity: 1; }
    .dashboard-shell .status-running .stat-card::before { background: var(--dash-gradient-success); }
    .dashboard-shell .status-breakdown .stat-card::before { background: var(--dash-gradient-danger); }
    .dashboard-shell .stat-operators .stat-card::before { background: var(--dash-gradient-warning); }

    .dashboard-shell .stat-value {
        color: var(--dash-ink);
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1.1;
        margin: 0;
        background: linear-gradient(135deg, #0f172a 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-shell .stat-label {
        color: var(--dash-muted);
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.5rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .dashboard-shell .stat-icon {
        align-items: center;
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transition: var(--dash-transition);
        position: relative;
        z-index: 2;
    }

    .dashboard-shell .stat-card:hover .stat-icon { transform: scale(1.08) rotate(6deg); }

    .dashboard-shell .status-running .stat-icon {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #059669;
    }

    .dashboard-shell .status-idle .stat-icon {
        background: linear-gradient(135deg, #fef3c7, #fde047);
        color: #d97706;
    }

    .dashboard-shell .status-breakdown .stat-icon {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
    }

    .dashboard-shell .stat-operators .stat-icon {
        background: linear-gradient(135deg, #fef3c7, #fde047);
        color: #d97706;
    }

    .dashboard-shell .summary-stat {
        height: 100%;
        min-height: 140px;
        display: flex;
        align-items: center;
        padding: 0;
    }

    .dashboard-shell .summary-stat .card-body {
        align-items: center;
        display: grid;
        grid-template-columns: 52px 1fr;
        column-gap: 1rem;
        padding: 1.625rem;
    }

    .dashboard-shell .summary-icon {
        align-items: center;
        border-radius: 14px;
        display: flex;
        font-size: 1.375rem;
        grid-row: span 2;
        height: 52px;
        justify-content: center;
        width: 52px;
        position: relative;
        z-index: 2;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .dashboard-shell .summary-icon::before {
        content: "";
        position: absolute;
        inset: -2px;
        border-radius: 16px;
        padding: 2px;
        background: var(--dash-gradient);
        mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
        opacity: 0.15;
        pointer-events: none;
    }

    .dashboard-shell .summary-production .summary-icon { background: #e0f2fe; color: #0284c7; }
    .dashboard-shell .summary-rejections .summary-icon { background: #fee2e2; color: #dc2626; }
    .dashboard-shell .summary-approvals .summary-icon { background: #fef3c7; color: #d97706; }
    .dashboard-shell .summary-breakdowns .summary-icon { background: #dcfce7; color: #059669; }

    .dashboard-shell .summary-stat h3 {
        font-size: 1.875rem;
        margin: 0 0 0.25rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, var(--dash-ink) 0%, #64748b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-shell .summary-stat p {
        color: var(--dash-muted) !important;
        font-size: 0.78rem;
        font-weight: 600;
        margin: 0;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .dashboard-shell .summary-stat:hover .summary-icon { transform: scale(1.05); }

    .dashboard-shell .dashboard-panel {
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: var(--dash-surface) !important;
        backdrop-filter: blur(10px);
    }

    .dashboard-shell .dashboard-panel .card-header {
        background: var(--dash-surface-2) !important;
        border-bottom: 1px solid var(--dash-border) !important;
        padding: 1.25rem 1.5rem;
        border-radius: 0 !important;
    }

    .dashboard-shell .dashboard-panel .card-header h5 {
        color: var(--dash-ink);
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
    }

    .dashboard-shell .dashboard-panel .card-header h5 i {
        color: var(--dash-primary);
        margin-right: 0.625rem;
        font-size: 1.1rem;
    }

    .dashboard-shell .dashboard-panel .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .dashboard-shell .chart-card-body {
        min-height: 320px;
        padding: 1.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .dashboard-shell .oee-card-body {
        min-height: 320px;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
    }

    .dashboard-shell .oee-chart-wrap {
        position: relative;
        width: 100%;
        max-width: 240px;
        aspect-ratio: 1;
        flex: 0 0 auto;
    }

    .dashboard-shell .oee-chart-wrap canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }

    .dashboard-shell .table {
        color: var(--dash-ink);
        margin-bottom: 0;
    }

    .dashboard-shell .table thead th {
        background: var(--dash-surface-2) !important;
        border-bottom: 2px solid var(--dash-border) !important;
        color: var(--dash-muted);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        padding: 1rem 1.25rem;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .dashboard-shell .table tbody td {
        border-color: var(--dash-border) !important;
        padding: 1.125rem 1.25rem;
        vertical-align: middle;
    }

    .dashboard-shell .table tbody tr {
        transition: var(--dash-transition);
        border-bottom: 1px solid var(--dash-border);
    }

    .dashboard-shell .table tbody tr:last-child { border-bottom: none; }
    .dashboard-shell .table tbody tr:hover { background: #f0f7ff !important; }

    .dashboard-shell .badge-status {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .dashboard-shell .badge-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.9;
    }

    .dashboard-shell .badge-status.status-running {
        background: #dcfce7;
        color: #059669;
    }

    .dashboard-shell .badge-status.status-idle {
        background: #fef3c7;
        color: #d97706;
    }

    .dashboard-shell .badge-status.status-breakdown {
        background: #fee2e2;
        color: #dc2626;
    }

    .dashboard-shell .dashboard-recent-list {
        border-radius: var(--dash-radius-sm);
        overflow: hidden;
    }

    .dashboard-shell .dashboard-recent-list .list-group-item {
        border-color: var(--dash-border) !important;
        padding: 1rem 1.25rem;
        background: transparent;
        transition: var(--dash-transition);
        border-left: 3px solid transparent;
    }

    .dashboard-shell .dashboard-recent-list .list-group-item:first-child { border-top: none; }
    .dashboard-shell .dashboard-recent-list .list-group-item:last-child { border-bottom: none; }

    .dashboard-shell .dashboard-recent-list .list-group-item:hover {
        background: #f0f7ff !important;
        border-left-color: var(--dash-primary);
        transform: translateX(4px);
    }

    .dashboard-shell .dashboard-recent-list .list-group-item strong {
        color: var(--dash-ink);
        font-weight: 700;
        font-size: 0.9rem;
    }

    .dashboard-shell .dashboard-recent-list .list-group-item small {
        color: var(--dash-muted);
        font-size: 0.8rem;
        font-weight: 500;
    }

    .dashboard-shell .text-muted { color: var(--dash-muted) !important; }

    .dashboard-shell .chart-empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
        color: var(--dash-muted);
    }

    .dashboard-shell .chart-empty-state i {
        font-size: 3.5rem;
        margin-bottom: 1.25rem;
        opacity: 0.15;
        display: block;
        color: var(--dash-primary);
    }

    .dashboard-shell .chart-empty-state span {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--dash-muted);
    }

    .dashboard-shell .oee-chart-centre {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 5;
    }

    .dashboard-shell .oee-chart-centre strong {
        display: block;
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 800;
        color: var(--dash-ink);
        line-height: 1;
        background: linear-gradient(135deg, var(--dash-primary), var(--dash-info));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-shell .oee-chart-centre span {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.68rem;
        color: var(--dash-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dashboard-shell .oee-indicators {
        margin-top: 1.25rem;
        width: 100%;
        max-width: 420px;
    }

    .dashboard-shell .oee-indicators .col-4 {
        text-align: center;
    }

    .dashboard-shell .oee-indicators i {
        font-size: 1.375rem;
        margin-bottom: 0.625rem;
        display: block;
        color: var(--dash-primary);
    }

    .dashboard-shell .oee-indicators small {
        display: block;
        color: var(--dash-muted);
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.375rem;
    }

    .dashboard-shell .oee-indicators .fw-bold {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--dash-ink);
    }

    .dashboard-shell .btn-outline-primary {
        border-color: var(--dash-primary);
        color: var(--dash-primary);
        font-weight: 600;
        font-size: 0.82rem;
        padding: 0.45rem 1rem;
        border-radius: 10px;
        transition: var(--dash-transition);
    }

    .dashboard-shell .btn-outline-primary:hover {
        background: var(--dash-gradient);
        color: #ffffff;
        border-color: var(--dash-primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .dashboard-shell .row.g-3 {
        margin-left: calc(-0.75rem / 2);
        margin-right: calc(-0.75rem / 2);
    }

    .dashboard-shell .row.g-3 > * {
        padding-left: calc(0.75rem / 2);
        padding-right: calc(0.75rem / 2);
    }

    .dashboard-shell .dashboard-trend-row > .dashboard-trend-column {
        flex: 0 0 100%;
        max-width: 100%;
    }

    @media (min-width: 992px) {
        .dashboard-shell .dashboard-trend-row > .dashboard-trend-production,
        .dashboard-shell .dashboard-trend-row > .dashboard-trend-oee {
            flex-basis: 35%;
            max-width: 35%;
        }

        .dashboard-shell .dashboard-trend-row > .dashboard-trend-breakdowns {
            flex-basis: 30%;
            max-width: 30%;
        }
    }

    @media (max-width: 991.98px) {
        .dashboard-shell .oee-indicators { max-width: 100%; width: 100%; }
    }

    @media (max-width: 767.98px) {
        .dashboard-heading {
            border-radius: 16px;
            min-height: 130px;
            padding: 1.5rem;
        }
        .dashboard-shell .chart-card-body,
        .dashboard-shell .oee-card-body {
            min-height: 280px;
        }
        .dashboard-shell .stat-card { min-height: 128px; }
        .dashboard-shell .stat-value { font-size: 1.875rem; }
        .dashboard-heading h1 { font-size: 1.625rem; }
    }

    @media (max-width: 575.98px) {
        .dashboard-heading {
            min-height: 120px;
            padding: 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .dashboard-shell .stat-value { font-size: 1.625rem; }
        .dashboard-shell .summary-stat h3 { font-size: 1.5rem; }
    }
</style>

<div class="dashboard-shell">
<div class="dashboard-heading">
    <div class="dashboard-heading-content">
        <h1>Dashboard</h1>
        <p>Real-time overview of production, machines and operations</p>
    </div>
    <div class="live-indicator">
        <span class="live-dot"></span>
        <span>Live data sync</span>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Running Machines -->
    <div class="col-xl-3 col-lg-3 col-md-6">
        <a href="{{ route('machine_scada') }}" class="dashboard-stat-link">
        <div class="card stat-card dashboard-stat status-running fade-in-up" style="animation-delay:0.05s">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $stats['running_machines'] ?? 0 }}</div>
                    <div class="stat-label">Active Machines</div>
                </div>
                <div class="stat-icon"><i class="bi bi-play-circle"></i></div>
            </div>
        </div>
        </a>
    </div>

    <!-- Idle Machines -->
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card stat-card dashboard-stat status-idle fade-in-up" style="animation-delay:0.1s">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $stats['idle_machines'] ?? 0 }}</div>
                    <div class="stat-label">Idle Machines</div>
                </div>
                <div class="stat-icon"><i class="bi bi-pause-circle"></i></div>
            </div>
        </div>
    </div>

    <!-- Breakdown Machines -->
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card stat-card dashboard-stat status-breakdown fade-in-up" style="animation-delay:0.15s">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $stats['breakdown_machines'] ?? 0 }}</div>
                    <div class="stat-label">Breakdown Machines</div>
                </div>
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    <!-- Operators Active -->
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card stat-card dashboard-stat stat-operators fade-in-up" style="animation-delay:0.2s">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $stats['operator_count'] ?? 0 }}</div>
                    <div class="stat-label">Active Operators</div>
                </div>
                <div class="stat-icon"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="row g-3 mb-3">
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card summary-stat summary-production fade-in-up" style="animation-delay:0.25s">
            <div class="card-body">
                <i class="bi bi-activity summary-icon" aria-hidden="true"></i>
                <h3 id="productionToday"
                    class="text-primary fw-bold">
                    {{ $stats['production_today'] ?? 0 }}
                </h3>
                <p class="text-muted mb-0">Total Tapped Today</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card summary-stat summary-rejections fade-in-up" style="animation-delay:0.3s">
            <div class="card-body">
                <i class="bi bi-x-circle summary-icon" aria-hidden="true"></i>
                <h3 id="rejectionsToday"
                    class="text-danger fw-bold">
                    {{ $stats['rejections_today'] ?? 0 }}
                </h3>
                <p class="text-muted mb-0">Rejections Today</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card summary-stat summary-approvals fade-in-up" style="animation-delay:0.35s">
            <div class="card-body">
                <i class="bi bi-hourglass-split summary-icon" aria-hidden="true"></i>
                <h3 id="pendingApprovals"
                    class="text-warning fw-bold">
                    {{ $stats['pending_approvals'] ?? 0 }}
                </h3>
                <p class="text-muted mb-0">Pending Approvals</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card summary-stat summary-breakdowns fade-in-up" style="animation-delay:0.4s">
            <div class="card-body">
                <i class="bi bi-tools summary-icon" aria-hidden="true"></i>
                <h3 id="openBreakdowns"
                    class="text-success fw-bold">
                    {{ $stats['open_breakdowns'] ?? 0 }}
                </h3>
                <p class="text-muted mb-0">Open Breakdowns</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 dashboard-trend-row">
    <!-- Production Chart -->
    <div class="col-12 dashboard-trend-column dashboard-trend-production">
        <div class="card dashboard-panel">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Production Trend (7 Days)</h5>
            </div>
            <div class="card-body chart-card-body position-relative">
                <canvas id="productionChart" height="120"></canvas>
                @if(collect($productionChart)->sum('production') == 0 && collect($productionChart)->sum('rejection') == 0)
                    <div class="chart-empty-state" aria-label="No production data available">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>No production data available</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- OEE Summary -->
    <div class="col-12 dashboard-trend-column dashboard-trend-oee">
        <div class="card dashboard-panel">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-speedometer me-2"></i>OEE Summary</h5>
            </div>
            <div class="card-body oee-card-body">
                <div class="oee-chart-wrap">
                    <canvas id="oeeChart" role="img" aria-label="Overall equipment effectiveness progress"></canvas>
                    <div class="oee-chart-centre">
                        <strong>{{ $oeeSummary['oee'] ?? 0 }}%</strong>
                        <span>Overall OEE</span>
                    </div>
                </div>
                <div class="row oee-indicators">
                    <div class="col-4">
                        <i class="bi bi-gear-wide-connected" aria-hidden="true"></i>
                        <small class="text-muted">Availability</small>
                        <div class="fw-bold text-primary">{{ $oeeSummary['availability'] ?? 0 }}%</div>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
                        <small class="text-muted">Performance</small>
                        <div class="fw-bold text-success">{{ $oeeSummary['performance'] ?? 0 }}%</div>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        <small class="text-muted">Quality</small>
                        <div class="fw-bold text-info">{{ $oeeSummary['quality'] ?? 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Breakdowns -->
    <div class="col-12 dashboard-trend-column dashboard-trend-breakdowns">
        <div class="card dashboard-panel">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Recent Breakdowns</h5>
                <a href="{{ route('breakdowns.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush dashboard-recent-list">
                    @forelse($recentBreakdowns as $breakdown)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $breakdown->machine->name }}</strong>
                                <span class="badge bg-{{ $breakdown->status === 'open' ? 'danger' : 'warning' }}">
                                    {{ $breakdown->status }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $breakdown->breakdown_type }} - {{ $breakdown->start_time->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted">No recent breakdowns</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <!-- Machine Status -->
    <div class="col-12">
        <div class="card dashboard-panel">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cpu me-2"></i>Machine Status</h5>
                <a href="{{ route('machines.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Machine</th>
                                <th>Status</th>
                                <th>Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($machineStatus as $machine)
                                <tr>
                                    <td>
                                        <strong>{{ $machine['machine_code'] }}</strong><br>
                                        <small class="text-muted">{{ $machine['name'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-status status-{{ $machine['status'] }}">
                                            {{ ucfirst($machine['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $machine['operator'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No machines registered</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    refreshDashboardStats();

    setInterval(function () {
        refreshDashboardStats();
    }, 5000);

});
    // Production Chart
    const productionCtx = document.getElementById('productionChart').getContext('2d');
    new Chart(productionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($productionChart, 'label')) !!},
            datasets: [
                {
                    label: 'Production',
                    data: {!! json_encode(array_column($productionChart, 'production')) !!},
                    backgroundColor: 'rgba(22, 119, 255, 0.82)',
                    borderColor: '#1677FF',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                    maxBarThickness: 26
                },
                {
                    label: 'Rejections',
                    data: {!! json_encode(array_column($productionChart, 'rejection')) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.78)',
                    borderColor: '#EF4444',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                    maxBarThickness: 26
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        color: '#52647b',
                        boxWidth: 9,
                        boxHeight: 9,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 14,
                        font: { size: 11, weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: '#102a4c',
                    padding: 10,
                    titleFont: { weight: '600' },
                    cornerRadius: 7
                }
            },
            scales: {
                x: {
                    ticks: { color: '#64748B', font: { size: 10 } },
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#64748B', font: { size: 10 }, padding: 8 },
                    grid: { color: 'rgba(203, 213, 225, 0.5)', drawTicks: false },
                    border: { display: false }
                }
            }
        }
    });

    // OEE progress chart
    const oeeScore = Math.min(Math.max(Number(@json($oeeSummary['oee'] ?? 0)) || 0, 0), 100);
    const oeeCtx = document.getElementById('oeeChart').getContext('2d');
    new Chart(oeeCtx, {
        type: 'doughnut',
        data: {
            labels: ['OEE', 'Remaining'],
            datasets: [{
                data: [oeeScore, 100 - oeeScore],
                backgroundColor: [
                    '#2563eb',
                    '#e2e8f0'
                ],
                borderWidth: 0,
                borderRadius: 8,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '82%',
            rotation: -90,
            spacing: 3,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.label}: ${context.raw.toFixed(1)}%`
                    }
                }
            }
        }
    });

    // Auto-refresh dashboard stats when a breakdown alert fires
    function refreshDashboardStats() {

        $.get('{{ route("api.dashboard.stats") }}', function(data) {

            // Machine Stats
            if (data.running_machines !== undefined) {
                $('.stat-card.status-running .stat-value')
                    .text(data.running_machines);
            }

            if (data.idle_machines !== undefined) {
                $('.stat-card.status-idle .stat-value')
                    .text(data.idle_machines);
            }

            if (data.breakdown_machines !== undefined) {
                $('.stat-card.status-breakdown .stat-value')
                    .text(data.breakdown_machines);
            }

            if (data.operator_count !== undefined) {
                $('.stat-operators .stat-value')
                    .text(data.operator_count);
            }


            // ==============================
            // SUMMARY CARDS - REAL TIME
            // ==============================

            if (data.production_today !== undefined) {
                $('#productionToday')
                    .text(data.production_today);
            }

            if (data.rejections_today !== undefined) {
                $('#rejectionsToday')
                    .text(data.rejections_today);
            }

            if (data.pending_approvals !== undefined) {
                $('#pendingApprovals')
                    .text(data.pending_approvals);
            }

            if (data.open_breakdowns !== undefined) {
                $('#openBreakdowns')
                    .text(data.open_breakdowns);
            }

        }).fail(function(xhr) {
            console.error(
                'Dashboard stats error:',
                xhr.responseText
            );
        });


        // ==============================
        // MACHINE STATUS
        // ==============================

        $.get('{{ route("api.dashboard.machines") }}', function(data) {

            if (!data || !data.length) {
                return;
            }

            var tbody = $('.table-hover tbody');

            if (!tbody.length) {
                return;
            }

            var html = '';

            data.forEach(function(m) {

                var utilColor =
                    m.utilization > 70
                        ? 'success'
                        : (m.utilization > 40 ? 'warning' : 'danger');

                html += `
                    <tr>

                        <td>
                            <strong>${m.machine_code}</strong>
                            <br>
                            <small class="text-muted">
                                ${m.name}
                            </small>
                        </td>

                        <td>
                            <span class="badge badge-status status-${m.status}">
                                ${m.status.charAt(0).toUpperCase() + m.status.slice(1)}
                            </span>
                        </td>

                        <td>
                            ${m.operator || '-'}
                        </td>

                        

                    </tr>
                `;
            });

            tbody.html(html);

        }).fail(function(xhr) {

            console.error(
                'Machine status error:',
                xhr.responseText
            );

        });
    }
</script>
@endpush
