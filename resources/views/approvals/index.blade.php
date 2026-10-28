@extends('layouts.app')

@section('title', 'Approvals - MES')
@section('page-title', 'Production Approvals')

@section('content')
<style>
.approval-page{--ap-blue:#2563eb;--ap-ink:#1e293b;--ap-muted:#64748b;--ap-border:#e2e8f0;background:#f5f7fb;color:var(--ap-ink);padding:4px 0 24px}
.approval-page .ap-banner,.approval-page .ap-card,.approval-page .ap-stat{background:#fff;border:1px solid var(--ap-border);border-radius:14px;box-shadow:0 3px 12px rgba(15,23,42,.035)}
.approval-page .ap-banner{padding:20px 22px;margin-bottom:18px;border-left:4px solid var(--ap-blue)}
.approval-page .ap-icon{width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;border-radius:12px;background:#eff6ff;color:#2563eb;font-size:21px;flex-shrink:0}
.approval-page .ap-title{font-size:20px;font-weight:800;color:#172033;margin:0 0 4px}
.approval-page .ap-muted{color:var(--ap-muted)!important}
.approval-page .ap-stat{padding:17px 18px;height:100%}
.approval-page .ap-stat-label{font-size:11px;text-transform:uppercase;letter-spacing:.55px;font-weight:800;color:#64748b}
.approval-page .ap-stat-number{font-size:27px;line-height:1.25;font-weight:800;color:#172033;margin-top:7px}
.approval-page .ap-card{overflow:hidden}
.approval-page .ap-card-head{padding:18px 20px;border-bottom:1px solid var(--ap-border);background:#fff}
.approval-page .ap-filter-panel{padding:15px 18px;background:#f8fafc;border-bottom:1px solid var(--ap-border)}
.approval-page .form-label{font-size:12px;font-weight:700;color:#fff;margin-bottom:5px}
.approval-page .form-control,.approval-page .form-select{border-color:#cbd5e1;border-radius:8px;min-height:38px;color:#1e293b;background-color:#fff}
.approval-page .form-control:focus,.approval-page .form-select:focus{border-color:#93c5fd;box-shadow:0 0 0 .2rem rgba(37,99,235,.12)}
.approval-page .ap-status-tabs{display:flex;gap:7px;flex-wrap:wrap}
.approval-page .ap-status-tab{border:1px solid #dbe3ed;background:#fff;color:#475569;border-radius:8px;padding:7px 12px;font-size:12px;font-weight:700}
.approval-page .ap-status-tab.active{background:#1d4ed8;color:#fff;border-color:#1d4ed8}
.approval-page .ap-table-wrap{overflow-x:auto}
.approval-page table.ap-table{width:100%;margin:0;border-collapse:separate;border-spacing:0;vertical-align:middle;background:#fff}
.approval-page .ap-table thead th{background:#f1f5f9;color:#475569;padding:13px 12px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.45px;white-space:nowrap;border-bottom:1px solid #dbe3ed}
.approval-page .ap-table tbody td{padding:15px 12px;color:#1e293b;border-bottom:1px solid #edf1f5;background:#fff;vertical-align:middle}
.approval-page .ap-table tbody tr:hover td{background:#f8fbff}
.approval-page .ap-shift-date{font-size:11px;color:#64748b;margin-bottom:6px}
.approval-page .ap-time{font-size:12px;font-weight:650;white-space:nowrap;line-height:1.7;color:#334155}
.approval-page .ap-duration{display:inline-block;margin-top:7px;background:#f1f5f9;color:#475569;border-radius:6px;padding:4px 8px;font-size:11px;font-weight:750;white-space:nowrap}
.approval-page .ap-entity{display:flex;align-items:center;min-width:130px}
.approval-page .ap-entity-icon{width:33px;height:33px;display:inline-flex;align-items:center;justify-content:center;border-radius:9px;margin-right:9px;flex-shrink:0}
.approval-page .ap-machine{background:#eff6ff;color:#2563eb}.approval-page .ap-operator{background:#f3e8ff;color:#7e22ce}.approval-page .ap-product{background:#ecfdf5;color:#047857}
.approval-page .ap-entity-name{font-size:12px;font-weight:750;color:#1e293b;overflow-wrap:anywhere}
.approval-page .ap-entity-sub{font-size:10px;color:#64748b;margin-top:3px}
.approval-page .ap-qty{display:inline-block;min-width:78px;text-align:center;padding:8px 9px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;color:#166534;font-weight:800;font-size:12px;font-variant-numeric:tabular-nums}
.approval-page .ap-status{display:inline-block;border-radius:999px;padding:5px 9px;font-size:10px;font-weight:800;white-space:nowrap}
.approval-page .ap-pending{background:#fff7ed;color:#9a3412}.approval-page .ap-approved{background:#dcfce7;color:#166534}.approval-page .ap-rejected{background:#fee2e2;color:#991b1b}.approval-page .ap-other{background:#f1f5f9;color:#475569}
.approval-page .ap-actions{display:flex;flex-direction:column;align-items:stretch;gap:5px;min-width:88px}
.approval-page .ap-actions .btn{font-size:11px;font-weight:750;border-radius:7px;white-space:nowrap}
.approval-page .ap-view{background:#eff6ff;color:#1d4ed8;border:1px solid #dbeafe}
.approval-page .ap-approve{background:#15803d;color:#fff;border:1px solid #15803d}
.approval-page .ap-approve:hover{background:#166534;color:#fff}
.approval-page .ap-reject{background:#fff;color:#b91c1c;border:1px solid #fecaca}
.approval-page .ap-reject:hover{background:#fef2f2;color:#991b1b}
.approval-page .ap-empty{padding:42px 15px!important;text-align:center;color:#64748b}
.approval-page .ap-footer{padding:14px 18px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.approval-page .pagination{margin:0}.approval-page .pagination .page-link{color:#334155;border-color:#e2e8f0}.approval-page .pagination .active .page-link{background:#2563eb;border-color:#2563eb;color:#fff}
.approval-page .ap-hidden{display:none!important}
@media(max-width:768px){.approval-page .ap-banner{padding:15px}.approval-page .ap-title{font-size:18px}.approval-page .ap-stat-number{font-size:23px}.approval-page .ap-card-head{padding:15px}.approval-page .ap-filter-panel{padding:12px}.approval-page .ap-table thead th,.approval-page .ap-table tbody td{padding:12px 10px}}

/* =====================================
   MES Industrial Blue Theme
===================================== */

.approval-page {
    background: #e8eef5 !important;
    color: #172b45;
    padding: 16px;
    border-radius: 12px;
}

/* Main page header */
.approval-page .approval-header {
    background: linear-gradient(135deg, #163a5f, #245b85) !important;
    border: 1px solid #2f6289;
    border-left: 5px solid #60a5fa;
    color: #ffffff !important;
    box-shadow: 0 5px 18px rgba(15, 42, 68, .16);
}

.approval-page .approval-header h4,
.approval-page .approval-header p {
    color: #ffffff !important;
}

.approval-page .approval-header .header-icon {
    background: rgba(255, 255, 255, .14) !important;
    color: #bfdbfe !important;
}

/* Summary cards */
.approval-page .stat-card {
    background: #ffffff !important;
    border: 1px solid #cfdae6;
    border-top: 3px solid #2f6b9a;
    box-shadow: 0 4px 12px rgba(20, 48, 76, .07);
}

.approval-page .stat-label {
    color: #52677d !important;
}

.approval-page .stat-value {
    color: #163a5f !important;
}

/* Production records panel */
.approval-page .approval-card {
    background: #ffffff !important;
    border: 1px solid #cfdae6;
    box-shadow: 0 6px 22px rgba(20, 48, 76, .09);
}

.approval-page .approval-card-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #cfdae6;
}

/* Table heading */
.approval-page .table thead th {
    background: #dce7f1 !important;
    color: #193b5a !important;
    border-bottom: 2px solid #b8cbdc !important;
}

/* Table body */
.approval-page .table tbody td {
    background: #ffffff !important;
    color: #243b53 !important;
    border-bottom: 1px solid #e1e8f0 !important;
}

.approval-page .table tbody tr:hover > td {
    background: #edf5fc !important;
}

/* Machine icon */
.approval-page .machine-icon {
    background: #e2effb !important;
    color: #1d5c91 !important;
}

/* Operator icon */
.approval-page .operator-icon {
    background: #e8eaf6 !important;
    color: #3949ab !important;
}

/* Product icon */
.approval-page .product-icon {
    background: #e1f2ed !important;
    color: #16735c !important;
}

/* Production quantity */
.approval-page .production-count {
    background: #e5f5ec !important;
    border-color: #b7e4c7 !important;
    color: #17653b !important;
}

/* Primary action */
.approval-page .btn-approve {
    background: #167347 !important;
    border-color: #167347 !important;
    color: #ffffff !important;
}

.approval-page .btn-approve:hover {
    background: #105a37 !important;
}

/* Secondary action */
.approval-page .btn-view {
    background: #e5effa !important;
    border-color: #c7dcef !important;
    color: #1e5280 !important;
}

/* Shift duration */
.approval-page .duration-badge {
    background: #e8eef5 !important;
    color: #38536d !important;
}

/* Search and filter controls */
.approval-page .form-control,
.approval-page .form-select {
    border: 1px solid #b9cadb;
    border-radius: 8px;
}

.approval-page .form-control:focus,
.approval-page .form-select:focus {
    border-color: #3976a5;
    box-shadow: 0 0 0 .2rem rgba(57, 118, 165, .15);
}

/* Pagination */
.approval-page .pagination .active > .page-link {
    background: #245b85 !important;
    border-color: #245b85 !important;
}

/* Mobile spacing */
@media (max-width: 768px) {
    .approval-page {
        padding: 8px;
    }
}

/* =====================================
   MES INDUSTRIAL BLUE — AP THEME
   Compatible with existing .ap-* classes
===================================== */

.approval-page {
    background: #e8eef5 !important;
    color: #172b45;
    padding: 16px;
    border-radius: 12px;
}

/* Header banner */
.approval-page .ap-banner {
    background: linear-gradient(135deg, #163a5f, #245b85) !important;
    border: 1px solid #2f6289;
    border-left: 5px solid #60a5fa;
    box-shadow: 0 5px 18px rgba(15, 42, 68, .14);
}

.approval-page .ap-banner .ap-title {
    color: #ffffff !important;
}

.approval-page .ap-banner .ap-muted {
    color: #dbeafe !important;
}

.approval-page .ap-banner .ap-icon {
    background: rgba(255, 255, 255, .14) !important;
    color: #bfdbfe !important;
}

/* Summary cards */
.approval-page .ap-stat {
    background: #ffffff !important;
    border: 1px solid #cfdae6;
    border-top: 3px solid #2f6b9a;
    box-shadow: 0 4px 12px rgba(20, 48, 76, .06);
}

.approval-page .ap-stat-label {
    color: #52677d !important;
}

.approval-page .ap-stat-number {
    color: #163a5f !important;
}

/* Main card */
.approval-page .ap-card {
    background: #ffffff !important;
    border: 1px solid #cfdae6;
    box-shadow: 0 6px 22px rgba(20, 48, 76, .08);
}

.approval-page .ap-card-head {
    background: #f8fafc !important;
    border-bottom: 1px solid #cfdae6;
}

/* Filters */
.approval-page .ap-filter-panel {
    background: #edf3f8 !important;
    border-bottom: 1px solid #cfdae6;
}

.approval-page .form-label {
    color: #fff !important;
}

.approval-page .form-control,
.approval-page .form-select {
    background-color: #ffffff !important;
    color: #243b53 !important;
    border-color: #b9cadb !important;
}

.approval-page .form-control:focus,
.approval-page .form-select:focus {
    border-color: #3976a5 !important;
    box-shadow: 0 0 0 .2rem rgba(57, 118, 165, .15) !important;
}

/* Status filter buttons */
.approval-page .ap-status-tab {
    background: #ffffff;
    color: #38536d;
    border-color: #cbd8e5;
}

.approval-page .ap-status-tab:hover {
    background: #edf5fc;
    border-color: #8fb5d5;
}

.approval-page .ap-status-tab.active {
    background: #245b85 !important;
    color: #ffffff !important;
    border-color: #245b85 !important;
}

/* Table header */
.approval-page .ap-table thead th {
    background: #dce7f1 !important;
    color: #193b5a !important;
    border-bottom: 2px solid #b8cbdc !important;
}

/* Table body */
.approval-page .ap-table tbody td {
    background: #ffffff !important;
    color: #243b53 !important;
    border-bottom: 1px solid #e1e8f0 !important;
}

.approval-page .ap-table tbody tr:hover td {
    background: #edf5fc !important;
}

/* Entity icons */
.approval-page .ap-machine {
    background: #e2effb !important;
    color: #1d5c91 !important;
}

.approval-page .ap-operator {
    background: #e8eaf6 !important;
    color: #3949ab !important;
}

.approval-page .ap-product {
    background: #e1f2ed !important;
    color: #16735c !important;
}

/* Shift details */
.approval-page .ap-shift-date {
    color: #647b91 !important;
}

.approval-page .ap-time {
    color: #29445e !important;
}

.approval-page .ap-duration {
    background: #e8eef5 !important;
    color: #38536d !important;
}

/* Production quantity */
.approval-page .ap-qty {
    background: #e5f5ec !important;
    border-color: #b7e4c7 !important;
    color: #17653b !important;
}

/* Status badges */
.approval-page .ap-pending {
    background: #fff2d6 !important;
    color: #8a4b08 !important;
}

.approval-page .ap-approved {
    background: #dcfce7 !important;
    color: #166534 !important;
}

.approval-page .ap-rejected {
    background: #fee2e2 !important;
    color: #991b1b !important;
}

.approval-page .ap-other {
    background: #e8eef5 !important;
    color: #38536d !important;
}

/* Action buttons */
.approval-page .ap-view {
    background: #e5effa !important;
    border-color: #c7dcef !important;
    color: #1e5280 !important;
}

.approval-page .ap-view:hover {
    background: #d3e5f5 !important;
}

.approval-page .ap-approve {
    background: #167347 !important;
    border-color: #167347 !important;
    color: #ffffff !important;
}

.approval-page .ap-approve:hover {
    background: #105a37 !important;
    color: #ffffff !important;
}

.approval-page .ap-reject {
    background: #ffffff !important;
    border-color: #e7b9b9 !important;
    color: #b42323 !important;
}

.approval-page .ap-reject:hover {
    background: #fff1f1 !important;
    border-color: #dc8888 !important;
}

/* Footer and pagination */
.approval-page .ap-footer {
    background: #f8fafc;
    border-top: 1px solid #cfdae6;
}

.approval-page .pagination .page-link {
    color: #245b85;
    border-color: #cfdae6;
}

.approval-page .pagination .active .page-link {
    background: #245b85 !important;
    border-color: #245b85 !important;
    color: #ffffff !important;
}

@media (max-width: 768px) {
    .approval-page {
        padding: 8px;
    }
}

.ap-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding: 12px 16px;
    background: #fff;
}

.ap-muted {
    color: #64748b;
    font-size: 12px;
}


/* Pagination list */


/* Reduce oversized arrows / SVG icons */


/* Mobile responsive */
@media (max-width: 576px) {
    .ap-footer {
        justify-content: center;
    }

    
}

/* Approval footer */
.ap-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 18px;
    border-top: 1px solid #e2e8f0;
    background: #fff;
}

.ap-footer .ap-muted {
    color: #64748b;
    font-size: 12px;
}

/* Pagination alignment */
.ap-footer nav {
    display: flex;
    justify-content: flex-end;
}

.ap-footer nav > div {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
}

/* Remove oversized SVG arrows */
.ap-footer nav svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
}

/* Compact pagination links */
.ap-footer nav a,
.ap-footer nav span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    min-height: 32px;
    padding: 6px 10px;
    font-size: 12px !important;
    line-height: 1.2;
    border-radius: 5px;
    text-decoration: none;
}

/* Keep pagination horizontal */
.ap-footer nav > div > span,
.ap-footer nav > div > a {
    flex-direction: row;
}

/* Mobile */
@media (max-width: 576px) {
    .ap-footer {
        justify-content: center;
    }

    .ap-footer nav {
        max-width: 100%;
        overflow-x: auto;
    }
}

.ap-footer nav a svg,
.ap-footer nav button svg,
.ap-footer nav span svg {
    width: 14px !important;
    height: 14px !important;
    flex-shrink: 0;
    display: inline-block;
}
/* White pagination background */
.ap-footer,
.ap-footer nav,
.ap-footer nav > div {
    background: #ffffff !important;
}

/* Pagination buttons */
.ap-footer nav a,
.ap-footer nav span {
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 5px;
    box-shadow: none !important;
}

/* Active page */
.ap-footer nav span[aria-current="page"] {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}

/* Hover */
.ap-footer nav a:hover {
    background: #eff6ff !important;
    color: #2563eb !important;
}

/* Small arrows */
.ap-footer nav svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
}

/* Disabled Previous / Next */
.ap-footer nav span[aria-disabled="true"] {
    background: #ffffff !important;
    color: #94a3b8 !important;
}
/* Approval footer */
.ap-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
}

/* Record count */
.ap-footer .ap-muted {
    color: #64748b;
    font-size: 12px;
}

/* Laravel pagination wrapper */
.ap-footer .pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    margin: 0;
    padding: 0;
    list-style: none;
}

/* Hide duplicate Tailwind summary and controls */
.ap-footer nav > div:first-child {
    display: none !important;
}

/* Compact page buttons */
.ap-footer .pagination .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 6px 10px;
    background: #fff !important;
    color: #334155 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    font-size: 12px;
    text-decoration: none;
    box-shadow: none !important;
}

/* Active page */
.ap-footer .pagination .page-item.active .page-link {
    background: #2563eb !important;
    color: #fff !important;
    border-color: #2563eb !important;
}

/* Hover */
.ap-footer .pagination .page-link:hover {
    background: #eff6ff !important;
    color: #2563eb !important;
}

/* Arrow size */
.ap-footer nav svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
}

/* Responsive */
@media (max-width: 576px) {
    .ap-footer {
        justify-content: center;
    }
}
.dark-dashboard .bg-white {
    background-color: #fff !important;
}





</style>

<div class="approval-page">
    <div class="ap-banner">
        <div class="d-flex align-items-center gap-3">
            <div class="ap-icon"><i class="bi bi-clipboard2-check"></i></div>
            <div class="flex-grow-1">
                <h4 class="ap-title">Production Approvals</h4>
                <div class="small ap-muted">Review shift output and approve verified production records.</div>
            </div>
            <span class="badge rounded-pill bg-light text-primary border px-3 py-2 d-none d-md-inline-flex"><i class="bi bi-shield-check me-1"></i> Approval Center</span>
        </div>
    </div>

    @php
        $pendingCount = $pendingRuns->total();
        $approvedCount = $approvedRuns->total();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="ap-stat"><div class="d-flex justify-content-between align-items-start"><div><div class="ap-stat-label">Total records</div><div class="ap-stat-number">{{ $pendingCount + $approvedCount }}</div></div><div class="ap-icon"><i class="bi bi-files"></i></div></div><div class="small ap-muted mt-2">All production runs</div></div></div>
        <div class="col-6 col-xl-3"><div class="ap-stat"><div class="d-flex justify-content-between align-items-start"><div><div class="ap-stat-label">Pending Approval</div><div class="ap-stat-number" style="color:#b45309">{{ $pendingCount }}</div></div><div class="ap-icon" style="background:#fff7ed;color:#c2410c"><i class="bi bi-hourglass-split"></i></div></div><div class="small ap-muted mt-2">In progress · awaiting review</div></div></div>
        <div class="col-6 col-xl-3"><div class="ap-stat"><div class="d-flex justify-content-between align-items-start"><div><div class="ap-stat-label">Approved</div><div class="ap-stat-number" style="color:#15803d">{{ $approvedCount }}</div></div><div class="ap-icon" style="background:#dcfce7;color:#15803d"><i class="bi bi-check-circle"></i></div></div><div class="small ap-muted mt-2">Completed production</div></div></div>
        <div class="col-6 col-xl-3"><div class="ap-stat"><div class="d-flex justify-content-between align-items-start"><div><div class="ap-stat-label">Rejected</div><div class="ap-stat-number" style="color:#b91c1c">0</div></div><div class="ap-icon" style="background:#fee2e2;color:#b91c1c"><i class="bi bi-x-circle"></i></div></div><div class="small ap-muted mt-2">Rejected runs</div></div></div>
    </div>

    {{-- Pending Production Approvals (in_progress) --}}
    <div class="ap-card mb-4">
        <div class="ap-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><h5 class="fw-bold mb-1" style="color:#172033"><i class="bi bi-hourglass-split text-warning me-2"></i>Pending Production Approvals</h5><div class="small ap-muted">In-progress production runs awaiting your approval.</div></div>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-2">{{ $pendingRuns->total() }} records</span>
        </div>

        <div class="ap-filter-panel">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4"><label for="apPendingSearch" class="form-label">Search records</label><input id="apPendingSearch" type="search" class="form-control" placeholder="Machine, operator, product, submitted by..."></div>
                <div class="col-6 col-md-2"><label for="apPendingFrom" class="form-label">From date</label><input id="apPendingFrom" type="date" class="form-control"></div>
                <div class="col-6 col-md-2"><label for="apPendingTo" class="form-label">To date</label><input id="apPendingTo" type="date" class="form-control"></div>
                <div class="col-12 col-md-2"><button type="button" id="apPendingReset" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset filters</button></div>
            </div>
            <div class="small ap-muted mt-2" id="apPendingResultText" aria-live="polite">Showing {{ $pendingRuns->count() }} records on this page.</div>
        </div>

        <div class="ap-table-wrap">
            <table class="ap-table">
                <thead><tr><th>Shift / Time</th><th>Machine</th><th>Operator</th><th>Product</th><th class="text-center">Production</th><th>Submitted by</th><th>Status / Actions</th></tr></thead>
                <tbody id="apPendingTableBody">
                @forelse($pendingRuns as $run)
                    @php
                        $approval = $run->approval;
                        $shiftName = $run->shift->name ?? ('Shift ' . ($run->shift_id ?? '-'));
                        $startAt = $run->start_time ? \Illuminate\Support\Carbon::parse($run->start_time) : null;
                        $endAt = $run->end_time ? \Illuminate\Support\Carbon::parse($run->end_time) : null;
                        $machineName = $run->machine->name ?? '-';
                        $operatorName = $run->operator->name ?? '-';
                        $productName = $run->product->name ?? '-';
                        $productweight = $run->product->weight ?? '-';
                        $submitterName = $approval ? ($approval->submittedBy->name ?? '-') : '-';
                        $statusValue = $approval ? strtolower($approval->status ?? 'pending') : 'pending';
                    @endphp
                    <tr data-status="{{ $statusValue }}" data-date="{{ $startAt ? $startAt->format('Y-m-d') : '' }}" data-shift="{{ $shiftName }}" data-search="{{ strtolower($machineName.' '.$operatorName.' '.$productName.' '.$submitterName.' '.$shiftName.' '.$statusValue) }}">
                        <td style="min-width:190px">
                            <div class="ap-shift-date"><i class="bi bi-calendar3 me-1"></i>{{ $startAt ? $startAt->format('d M Y') : '-' }} <span class="ms-1">· {{ $shiftName }}</span></div>
                            <div class="ap-time"><i class="bi bi-play-circle text-success me-1"></i>{{ $startAt ? $startAt->format('d M Y, h:i A') : '-' }}</div>
                            <div class="ap-time"><i class="bi bi-flag text-danger me-1"></i>{{ $endAt ? $endAt->format('d M Y, h:i A') : '-' }}</div>
                            <span class="ap-duration"><i class="bi bi-hourglass me-1"></i>{{ $run->runtime_minutes ?? 0 }} min</span>
                        </td>
                        <td><div class="ap-entity"><span class="ap-entity-icon ap-machine"><i class="bi bi-gear-wide-connected"></i></span><div><div class="ap-entity-name">{{ $machineName }}</div><div class="ap-entity-sub">Machine</div></div></div></td>
                        <td><div class="ap-entity"><span class="ap-entity-icon ap-operator"><i class="bi bi-person-badge"></i></span><div><div class="ap-entity-name">{{ $operatorName }}</div><div class="ap-entity-sub">Operator</div></div></div></td>
                        <td><div class="ap-entity"><span class="ap-entity-icon ap-product"><i class="bi bi-box-seam"></i></span><div class="ap-entity-name">{{ $productName }} </div></div></td>
                        <td class="text-center"><span class="ap-qty">{{ $run && $run->weight !== null ? number_format($run->weight* $productweight, 2) : '-' }} KG </span> Count:- {{ $run && $run->weight !== null ? number_format($run->weight, 2) : '-' }}</td>
                        <td><span class="small fw-semibold" style="color:#475569"><i class="bi bi-person-check me-1 text-secondary"></i>{{ $submitterName }}</span></td>
                        <td><div class="ap-actions">
                            <span class="ap-status ap-pending">Pending</span>
                            @if($approval)
                                <a href="{{ route('approvals.show', $approval) }}" class="btn btn-sm ap-view"><i class="bi bi-eye me-1"></i>Details</a>
                            @endif
                            @can('approve production')
                                <button type="button" class="btn btn-sm ap-approve" data-bs-toggle="modal" data-bs-target="#approveModal{{ $run->id }}"><i class="bi bi-check2-circle me-1"></i>Approve</button>
                            @endcan
                        </div></td>
                    </tr>

                    @if($approval)
                    <div class="modal fade" id="approveModal{{ $run->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form action="{{ route('approvals.approve', $approval) }}" method="POST" class="w-100">@csrf<div class="modal-content border-0 rounded-4 overflow-hidden"><div class="modal-header text-white" style="background:#166534"><div><h5 class="modal-title fw-bold"><i class="bi bi-check-circle me-2"></i>Approve production</h5><small>Review the record before confirming.</small></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body p-4"><div class="rounded-3 p-3 mb-3" style="background:#f0fdf4"><div class="fw-bold" style="color: #000;">{{ $machineName }}</div><div class="small text-secondary mt-1">Operator: {{ $operatorName }}</div><div class="small text-secondary">Shift: {{ $shiftName }}</div><div class="small text-secondary">Production: {{ $run && $run->weight !== null ? number_format($run->weight, 2) : '-' }}</div></div>
                    <div class="mb-3">
                    <label class="form-label fw-bold"
                           for="totalAactualProduction{{ $run->id }}">
                        Total Product Count <span class="text-danger">*</span>
                    </label>
                
                    <input
                        type="number"
                        id="totalAactualProduction{{ $run->id }}"
                        name="total_actual_count"
                        class="form-control"
                        min="0"
                        step="1"
                        required value="{{ round($run->weight) }}"
                    >
                </div>
                    <div class="mb-3">
                    <label class="form-label fw-bold"
                           for="totalReject{{ $run->id }}">
                        Total Reject Count <span class="text-danger">*</span>
                    </label>
                
                    <input
                        type="number"
                        id="totalReject{{ $run->id }}"
                        name="total_reject"
                        class="form-control"
                        min="0"
                        step="1"
                        required
                    >
                </div>
<label class="form-label" for="comments{{ $run->id }}">Comments <span class="fw-normal text-secondary">(optional)</span></label><textarea id="comments{{ $run->id }}" name="comments" class="form-control" rows="3" placeholder="Add approval comments..."></textarea></div><div class="modal-footer bg-light"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success"><i class="bi bi-lock me-1"></i>Approve &amp; Lock</button></div></div></form></div></div>
                    @endif
                @empty
                    <tr><td colspan="7" class="ap-empty"><div class="ap-icon mx-auto mb-3"><i class="bi bi-clipboard2-check"></i></div><h6 class="fw-bold" style="color:#1e293b">No pending production approvals</h6><p class="small mb-0">All in-progress runs have been reviewed.</p></td></tr>
                @endforelse
                <tr id="apPendingNoMatches" class="ap-hidden"><td colspan="7" class="ap-empty"><i class="bi bi-search fs-3 d-block mb-2"></i><strong>No matching records</strong><div class="small mt-1">Try changing the search text or filters.</div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="ap-footer border-top"><div class="small ap-muted" id="apPendingFooterCount">Showing {{ $pendingRuns->count() }} records on this page · {{ $pendingRuns->total() }} total</div><div>{{ $pendingRuns->links() }}</div></div>
    </div>

    {{-- Approved Production (completed) --}}
    <div class="ap-card">
        <div class="ap-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div><h5 class="fw-bold mb-1" style="color:#172033"><i class="bi bi-check-circle text-success me-2"></i>Approved Production</h5><div class="small ap-muted">Completed production runs that have been approved.</div></div>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-2">{{ $approvedRuns->total() }} records</span>
        </div>

        <div class="ap-filter-panel">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4"><label for="apApprovedSearch" class="form-label">Search records</label><input id="apApprovedSearch" type="search" class="form-control" placeholder="Machine, operator, product, submitted by..."></div>
                <div class="col-6 col-md-2"><label for="apApprovedFrom" class="form-label">From date</label><input id="apApprovedFrom" type="date" class="form-control"></div>
                <div class="col-6 col-md-2"><label for="apApprovedTo" class="form-label">To date</label><input id="apApprovedTo" type="date" class="form-control"></div>
                <div class="col-12 col-md-2"><button type="button" id="apApprovedReset" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset filters</button></div>
            </div>
            <div class="small ap-muted mt-2" id="apApprovedResultText" aria-live="polite">Showing {{ $approvedRuns->count() }} records on this page.</div>
        </div>

        <div class="ap-table-wrap">
            <table class="ap-table">
                <thead><tr><th>Shift / Time</th><th>Machine</th><th>Operator</th><th>Product</th><th class="text-center">Production</th><th>Submitted by</th><th>Status / Actions</th></tr></thead>
                <tbody id="apApprovedTableBody">
                @forelse($approvedRuns as $run)
                    @php
                        $approval = $run->approval;
                        $shiftName = $run->shift->name ?? ('Shift ' . ($run->shift_id ?? '-'));
                        $startAt = $run->start_time ? \Illuminate\Support\Carbon::parse($run->start_time) : null;
                        $endAt = $run->end_time ? \Illuminate\Support\Carbon::parse($run->end_time) : null;
                        $machineName = $run->machine->name ?? '-';
                        $operatorName = $run->operator->name ?? '-';
                        $productName = $run->product->name ?? '-';
                        $productweight = $run->product->weight ?? '-';
                        $submitterName = $approval ? ($approval->submittedBy->name ?? '-') : '-';
                        $statusValue = $approval ? strtolower($approval->status ?? 'approved') : 'approved';
                    @endphp
                    <tr data-status="{{ $statusValue }}" data-date="{{ $startAt ? $startAt->format('Y-m-d') : '' }}" data-shift="{{ $shiftName }}" data-search="{{ strtolower($machineName.' '.$operatorName.' '.$productName.' '.$submitterName.' '.$shiftName.' '.$statusValue) }}">
                        <td style="min-width:190px">
                            <div class="ap-shift-date"><i class="bi bi-calendar3 me-1"></i>{{ $startAt ? $startAt->format('d M Y') : '-' }} <span class="ms-1">· {{ $shiftName }}</span></div>
                            <div class="ap-time"><i class="bi bi-play-circle text-success me-1"></i>{{ $startAt ? $startAt->format('d M Y, h:i A') : '-' }}</div>
                            <div class="ap-time"><i class="bi bi-flag text-danger me-1"></i>{{ $endAt ? $endAt->format('d M Y, h:i A') : '-' }}</div>
                            <span class="ap-duration"><i class="bi bi-hourglass me-1"></i>{{ $run->runtime_minutes ?? 0 }} min</span>
                        </td>
                        <td><div class="ap-entity"><span class="ap-entity-icon ap-machine"><i class="bi bi-gear-wide-connected"></i></span><div><div class="ap-entity-name">{{ $machineName }}</div><div class="ap-entity-sub">Machine</div></div></div></td>
                        <td><div class="ap-entity"><span class="ap-entity-icon ap-operator"><i class="bi bi-person-badge"></i></span><div><div class="ap-entity-name">{{ $operatorName }}</div><div class="ap-entity-sub">Operator</div></div></div></td>
                        <td><div class="ap-entity"><span class="ap-entity-icon ap-product"><i class="bi bi-box-seam"></i></span><div class="ap-entity-name">{{ $productName }} </div></div></td>
                        <td class="text-center"><span class="ap-qty">{{ $run && $run->weight !== null ? number_format($run->weight* $productweight, 2) : '-' }} KG </span> Count:- {{ $run && $run->weight !== null ? number_format($run->weight, 2) : '-' }}</td>
                        <td><span class="small fw-semibold" style="color:#475569"><i class="bi bi-person-check me-1 text-secondary"></i>{{ $submitterName }}</span></td>
                        <td><div class="ap-actions">
                            <span class="ap-status ap-approved">Approved</span>
                            @if($approval)
                                <a href="{{ route('approvals.show', $approval) }}" class="btn btn-sm ap-view"><i class="bi bi-eye me-1"></i>Details</a>
                            @endif
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="ap-empty"><div class="ap-icon mx-auto mb-3"><i class="bi bi-clipboard2-check"></i></div><h6 class="fw-bold" style="color:#1e293b">No approved production</h6><p class="small mb-0">Completed runs will appear here once approved.</p></td></tr>
                @endforelse
                <tr id="apApprovedNoMatches" class="ap-hidden"><td colspan="7" class="ap-empty"><i class="bi bi-search fs-3 d-block mb-2"></i><strong>No matching records</strong><div class="small mt-1">Try changing the search text or filters.</div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="ap-footer border-top"><div class="small ap-muted" id="apApprovedFooterCount">Showing {{ $approvedRuns->count() }} records on this page · {{ $approvedRuns->total() }} total</div><div>{{ $approvedRuns->links() }}</div></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('.approval-page');
    if (!root) return;

    function initSection(tableBodyId, searchId, fromId, toId, resetId, noMatchesId, resultId, footerId, totalCount) {
        const tableBody = root.querySelector('#' + tableBodyId);
        if (!tableBody) return;
        const rows = Array.from(tableBody.querySelectorAll('tr[data-status]'));
        const search = root.querySelector('#' + searchId);
        const from = root.querySelector('#' + fromId);
        const to = root.querySelector('#' + toId);
        const reset = root.querySelector('#' + resetId);
        const noMatches = root.querySelector('#' + noMatchesId);
        const resultText = root.querySelector('#' + resultId);
        const footerCount = root.querySelector('#' + footerId);

        function applyFilters() {
            const term = (search ? search.value || '' : '').trim().toLowerCase();
            const fromDate = from ? from.value || '' : '';
            const toDate = to ? to.value || '' : '';
            let visible = 0;

            rows.forEach(function (row) {
                const matchesText = !term || (row.dataset.search || '').includes(term);
                const rowDate = row.dataset.date || '';
                const matchesFrom = !fromDate || (rowDate && rowDate >= fromDate);
                const matchesTo = !toDate || (rowDate && rowDate <= toDate);
                const show = matchesText && matchesFrom && matchesTo;
                row.classList.toggle('ap-hidden', !show);
                if (show) visible++;
            });

            if (noMatches) noMatches.classList.toggle('ap-hidden', visible !== 0 || rows.length === 0);
            if (resultText) resultText.textContent = 'Showing ' + visible + ' of ' + rows.length + ' records on this page.';
            if (footerCount) footerCount.textContent = 'Showing ' + visible + ' filtered records on this page · ' + totalCount + ' total records across pages.';
        }

        if (search) search.addEventListener('input', applyFilters);
        if (from) from.addEventListener('change', applyFilters);
        if (to) to.addEventListener('change', applyFilters);
        if (reset) reset.addEventListener('click', function () {
            if (search) search.value = '';
            if (from) from.value = '';
            if (to) to.value = '';
            applyFilters();
        });
        applyFilters();
    }

    initSection('apPendingTableBody', 'apPendingSearch', 'apPendingFrom', 'apPendingTo', 'apPendingReset', 'apPendingNoMatches', 'apPendingResultText', 'apPendingFooterCount', @json($pendingRuns->total()));
    initSection('apApprovedTableBody', 'apApprovedSearch', 'apApprovedFrom', 'apApprovedTo', 'apApprovedReset', 'apApprovedNoMatches', 'apApprovedResultText', 'apApprovedFooterCount', @json($approvedRuns->total()));
});
</script>
@endsection
