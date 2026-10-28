<table class="report-header-table">
    <colgroup>
        <col style="width: 18%">
        <col style="width: 59%">
        <col style="width: 23%">
    </colgroup>
    <tbody>
        <tr>
            <td rowspan="3" class="report-logo-cell">
               <img src="{{ $logoPath }}" alt="Grow Ever Logo">
            </td>

            <td class="report-company-name">
                GROW EVER COMEX PVT. LTD.
            </td>

            <td class="report-meta-cell">FORMAT NO: GEG/PROD/02</td>
        </tr>
        <tr>
            <td class="report-address">
                Maheshpur, Malanchaberia, Routa, Birshibpur,
                Uluberia, Howrah-711316
            </td>
            <td class="report-meta-cell">REV NO: 00</td>
        </tr>
        <tr>
            <td class="report-title">
                DAILY PRODUCTION REPORT FOR COMPONENT
            </td>
            <td class="report-meta-cell">REV DATE: 01/09/25</td>
        </tr>
    </tbody>
</table>
<table class="report-info-strip">
    <tr>
        <td>
            PRODUCTION DATE:
            <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
        </td>
        <td>
            SHIFT:
            <strong>
                {{ $shiftId
                    ? optional($shifts->firstWhere('id', $shiftId))->name
                    : 'All Shifts' }}
            </strong>
        </td>
        <td>
            DEPARTMENT: COMPONENT PRODUCTION
        </td>
    </tr>
</table>
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
    @endforelse</tbody></table></div>
    
    <div class="signature-area">
    <div>Signature of Supervisor</div>
    <div>Production In-Charge</div>
    <div>Authorised Signature</div>
</div>
</div>
<style>

@page {
    size: A4 landscape;
    margin: 7mm;
}

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
    font-family: DejaVu Sans, sans-serif;
    font-size: 9px;
    color: #1f2937;
    background: #fff;
}

.report-paper {
    width: 100%;
    margin: 0;
    padding: 0;
    border: 0;
    background: #fff;
    color: #1f2937;
}

.report-paper > .card-body {
    padding: 0;
}

/* Company header */
.report-header-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-bottom: 8px;
}

.report-header-table td {
    border: 1px solid #94a3b8;
    padding: 6px;
    vertical-align: middle;
}

.report-logo-cell {
    text-align: center;
    background: #fff;
}

.report-logo-cell img {
    display: block;
    width: 125px;
    max-width: 100%;
    max-height: 65px;
    margin: auto;
}

.report-company-name {
    background: #17365d;
    color: #fff;
    text-align: center;
    font-size: 19px;
    font-weight: bold;
}

.report-address {
    text-align: center;
    font-size: 9px;
    font-weight: bold;
    color: #334155;
}

.report-title {
    text-align: center;
    background: #e8eff7;
    color: #17365d;
    font-size: 11px;
    font-weight: bold;
    padding: 7px !important;
}

.report-meta-cell {
    font-size: 8px;
    font-weight: bold;
    text-align: left;
    color: #334155;
}

/* Date and shift */
.report-info-strip {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.report-info-strip td {
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    padding: 7px;
    font-size: 9px;
}

/* Production table */
.table-responsive {
    width: 100%;
    overflow: visible;
}

.production-report-table {
    width: 100% !important;
    table-layout: fixed;
    border-collapse: collapse !important;
    font-size: 9px;
    margin: 0;
}

.production-report-table th,
.production-report-table td {
    border: 1px solid #94a3b8 !important;
    padding: 5px 3px !important;
    color: #1f2937 !important;
    text-align: center;
    vertical-align: middle !important;
    overflow-wrap: break-word;
    word-wrap: break-word;
    line-height: 1.3;
}

.production-report-table thead th {
    background: #1f4e78 !important;
    color: #fff !important;
    font-size: 9px;
    font-weight: bold;
}

.production-report-table tbody tr:nth-child(even) td {
    background: #f1f5f9;
}

.production-report-table tbody td small {
    font-size: 8px;
    color: #475569;
}

/* Signatures */
.signature-area {
    width: 100%;
    display: table;
    table-layout: fixed;
    margin-top: 30px;
    text-align: center;
    font-size: 9px;
    font-weight: bold;
}

.signature-area > div {
    display: table-cell;
    width: 33.33%;
    padding: 35px 8px 5px;
    vertical-align: bottom;
}

/* Print and PDF */
@media print {
    html,
    body {
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .no-print,
    .sidebar,
    .topbar,
    .navbar {
        display: none !important;
    }

    .main-content,
    .report-sheet,
    .report-paper,
    .report-paper > .card-body {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    .report-header-table {
        page-break-inside: avoid;
    }

    .production-report-table {
        width: 100% !important;
        table-layout: fixed;
    }

    .production-report-table thead {
        display: table-header-group;
    }

    .production-report-table tr {
        page-break-inside: avoid;
    }

    .signature-area {
        page-break-inside: avoid;
    }
}

</style>