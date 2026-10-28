@extends('layouts.app')

@section('title', 'Component Daily Production Report - MES')
@section('page-title', 'Component Daily Production Report')

@section('content')
<div class="container-fluid py-3 report-sheet">
    <div class="card mb-3 no-print"><div class="card-body"><form class="row g-3 align-items-end" method="GET" action="{{ route('reports.component-daily-production') }}"><div class="col-md-3"><label class="form-label">Production Date</label><input type="date" class="form-control" name="date" value="{{ $date }}"></div><div class="col-md-4"><label class="form-label">Shift</label><select class="form-select" name="shift_id"><option value="">All shifts</option>@foreach($shifts as $shift)<option value="{{ $shift->id }}" @selected((string) $shiftId === (string) $shift->id)>{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>@endforeach</select></div><div class="col-md-auto">
    <button class="btn btn-primary" type="submit">Generate</button>

    <button class="btn btn-outline-secondary"
            type="button"
            onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Print
    </button>

    <a href="{{ route('reports.daily.production.pdf', [
        'date' => $date,
        'shift_id' => $shiftId
    ]) }}"
       class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
    </a>
</div></form></div></div>
    <div class="card report-paper"><div class="card-body p-3">
<div class="report-header">
    <div class="report-logo">
        <img
            src="{{ asset('images/mts.png') }}"
            alt="Grow Ever Logo"
            class="sidebar-logo"
        >
    </div>
   
    <div class="report-company-info">
        <h4 style="color:#000;">GROW EVER COMEX PVT. LTD.</h4>
        <div>
            Maheshpur, Malanchaberia, Routa, Birshibpur,
            Uluberia, Howrah-711316
        </div>
        <div class="report-title">
            DAILY PRODUCTION REPORT FOR COMPONENT
        </div>
    </div>

    <div class="report-meta">
        <div>FORMAT NO: GEG/PROD/02</div>
        <div>REV NO: 00</div>
        <div>REV DATE: 01/09/25</div>
    </div>
</div>
<div class="d-flex justify-content-between mb-3"><strong>Date: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong><strong>{{ $shiftId ? 'Selected shift' : 'All shifts' }}</strong></div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm production-report-table">

    <colgroup>
        <col style="width:10%">  {{-- M/C Name & No. --}}
        <col style="width:15%">  {{-- Name of Operator --}}
        <col style="width:10%">  {{-- Product --}}
        <col style="width:7%">  {{-- Shift --}}
        <col style="width:10%">  {{-- Production (Kg) --}}
        <col style="width:10%">  {{-- Production (Kg) --}}
        <col style="width:10%">  {{-- Total --}}
        <col style="width:10%">  {{-- Rejection --}}
        <col style="width:18%">  {{-- Breakdown / Remarks --}}
    </colgroup>
@php 
    $tot_pro_a="";
    @endphp
    <thead>
        <tr>
            <th>M/C Name &amp;<br> No.</th>
            <th>Name of Operator</th>
            <th>Product</th>
            <th>Shift</th>
            <th>Performed <br>Production <br>(PCS)</th>
            <th>Performed <br>Production <br>(Kg)</th>
            <th>Total</th>
            <th>Rejection</th>
            <th>Breakdown / Remarks</th>
        </tr>
    </thead><tbody>@forelse($productionRuns as $run)   
    @php 
    $tot_pro=$run->total_actual_count==''?$run->weight:$run->total_actual_count;
    $tot_pro_a=$run->total_actual_count==''?'*':'';
    @endphp
    <tr><td>{{ $run->machine?->machine_code ?? '—' }}<br><small>{{ $run->machine?->name }}</small></td>
    <td>{{ $run->operator?->name ?? '—' }}</td>
    <td>{{ $run->product?->product_code ?? '—' }}<br><small>{{ $run->product?->name }}</small></td>
    <td>{{ $run->shift?->code ?? '—' }}</td>
    <td>{{ $run->weight !== null ? number_format((float)  $tot_pro, 2) : '—' }} {{ $tot_pro_a }}</td>
    <td>{{ $run->weight !== null ? number_format((float)  $tot_pro*$run->product?->weight, 4) : '—' }} KG</td>
    </td><td>{{ $run->weight !== null ? number_format((float)  $tot_pro, 2) : '—' }} </td>
    <td>{{ (int) ($run->rejection_quantity ?? 0) }} </td>
    <td>{{ $run->remarks ?: '—' }}</td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center py-4">No production runs found for this selection.</td></tr>
    @endforelse
    </tbody>
    </table>
    </div>
    <div class="row mt-5 text-center"><div class="col">Signature of Supervisor</div><div class="col">Production In-Charge</div><div class="col">Authorised Signature</div></div></div></div>
   @if($tot_pro_a=='*')
    <p>* The report has not been verified by the approvers.</p>
   @endif
</div>
@endsection

@push('styles')

<style>
.sidebar-logo {
        width: 125px;
        height: 82px;
        object-fit: contain;

        background: #fff;
        border-radius: 12px;
        padding: 6px;

        margin-bottom: 8px;

        box-shadow: 0 5px 20px rgba(0,0,0,0.25);
    }
    .report-sheet {
        background: #fff;
        color: #111;
    }

    .report-paper {
        background: #fff !important;
        color: #111 !important;
        border: 1px solid #222 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .production-report-table {
        width: 100% !important;
        table-layout: fixed;
        border-collapse: collapse !important;
        font-size: 10px;
        margin-bottom: 0;
    }

    .production-report-table th,
    .production-report-table td {
        border: 1px solid #222 !important;
        padding: 5px 4px !important;
        color: #111 !important;
        vertical-align: middle !important;
        overflow-wrap: anywhere;
    }

    .production-report-table thead th {
        height: 48px;
        background: #eaf1f8 !important;
        text-align: center;
        font-size: 10px;
        font-weight: 700;
    }

    .production-report-table tbody td {
        height: 25px;
    }

    .report-heading {
        border: 1px solid #222;
        text-align: center;
        padding: 5px;
    }

    .report-company {
        font-size: 21px;
        font-weight: 800;
        margin: 0;
    }

    .report-title {
        font-size: 16px;
        font-weight: 800;
        background: #eaf1f8;
        padding: 5px;
    }

    .report-meta {
        font-size: 10px;
        font-weight: 700;
        text-align: left;
        padding: 7px !important;
    }

    .report-info {
        width: 100%;
        border-collapse: collapse;
    }

    .report-info td {
        border: 1px solid #222;
        padding: 10px 7px;
        font-size: 12px;
        font-weight: 700;
    }

    .signature-area {
        display: flex;
        justify-content: space-between;
        gap: 30px;
        margin: 45px 15px 10px;
        text-align: center;
        font-size: 10px;
        font-weight: 700;
    }

    .signature-area > div {
        width: 33.333%;
        border-top: 1px solid #222;
        padding-top: 7px;
    }

    @page {
        size: A4 landscape;
        margin: 7mm;
    }

    @media print {
        html, body {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sidebar,
        .topbar,
        .navbar,
        .no-print {
            display: none !important;
        }

        .main-content,
        .report-sheet,
        .report-paper,
        .report-paper .card-body {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .production-report-table {
            font-size: 9px !important;
        }

        .production-report-table thead {
            display: table-header-group;
        }

        .production-report-table tr {
            break-inside: avoid;
        }

        .signature-area {
            break-inside: avoid;
            margin-top: 35px;
        }
    }
    .report-header {
    display: grid;
    grid-template-columns: 20% 57% 23%;
    border: 1px solid #222;
    align-items: stretch;
    text-align: center;
}

.report-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #222;
    padding: 8px;
}

.report-logo img {
    max-width: 100%;
    width: 150px;
    max-height: 75px;
    object-fit: contain;
}

.report-company-info h4 {
    margin: 8px 0 4px;
    font-size: 20px;
    font-weight: 800;
}

.report-company-info > div:not(.report-title) {
    padding: 5px;
    font-size: 11px;
}

.report-title {
    padding: 7px;
    background: #eaf1f8;
    border-top: 1px solid #222;
    font-weight: 800;
}

.report-meta {
    display: flex;
    flex-direction: column;
    text-align: left;
    font-size: 10px;
    font-weight: bold;
}

.report-meta div {
    flex: 1;
    display: flex;
    align-items: center;
    padding: 5px;
    border-bottom: 1px solid #222;
}

.report-meta div:last-child {
    border-bottom: 0;
}

@media print {
    .report-header {
        break-inside: avoid;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .report-logo img {
        max-height: 65px;
    }
}
</style>

@endpush
