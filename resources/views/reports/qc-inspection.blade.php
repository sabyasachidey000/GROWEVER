
@extends('layouts.app')

@section('title', 'In-Process QC Inspection Report - MES')
@section('page-title', 'In-Process QC Inspection Report')

@section('content')
<div class="container-fluid py-3 report-sheet">

    {{-- Date and shift filter --}}
    <div class="card mb-3 no-print filter-card">
        <div class="card-body">
            <form class="row g-3 align-items-end"
                  method="GET"
                  action="{{ route('reports.qc-inspection') }}">

                <div class="col-md-3">
                    <label class="form-label">Inspection Date</label>
                    <input type="date"
                           class="form-control"
                           name="date"
                           value="{{ $date }}"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Shift</label>
                    <select class="form-select" name="shift_id">
                        <option value="">All shifts</option>

                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}"
                                @selected((string) $shiftId === (string) $shift->id)>
                                {{ $shift->name }}
                                ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                                –{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-auto d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search me-1"></i>Generate
                    </button>

                    <button class="btn btn-outline-secondary"
                            type="button"
                            onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                   
                    <a href="{{ route('reports.qc-inspection.pdf', [
                        'date' => $date,
                        'shift_id' => $shiftId
                    ]) }}"
                       class="btn btn-danger"
                       target="_blank">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>

                </div>
            </form>
        </div>
    </div>

    {{-- Printable report --}}
    <div class="card report-paper">
        <div class="card-body p-2">

            {{-- Company header --}}
            <table class="qc-header-table">
                <colgroup>
                    <col style="width:16%">
                    <col style="width:65%">
                    <col style="width:19%">
                </colgroup>

                <tbody>
                    <tr>
                        <td rowspan="3" class="qc-logo-cell">
                            <img src="{{ asset('images/mts.png') }}"
                                 alt="Grow Ever Logo">
                        </td>

                        <td class="qc-company-cell">
                            <h3>GROW EVER GROUP</h3>
                        </td>

                        <td class="qc-meta-cell">
                            FORMAT NO: GEG/QC
                        </td>
                    </tr>

                    <tr>
                        <td class="qc-address">
                            8/1A, SHREE MANJARI BUILDING, SIR WILLIAM
                            JONES SARANI, 5TH FLOOR, KOLKATA-700071
                        </td>

                        <td class="qc-meta-cell">REV NO: 00</td>
                    </tr>

                    <tr>
                        <td class="qc-subtitle">
                            IN-PROCESS QUALITY INSPECTION REPORT FOR TAPPING
                        </td>

                        <td class="qc-meta-cell">REV DATE: 01/03/25</td>
                    </tr>
                </tbody>
            </table>

            {{-- Date, shift and category --}}
            <table class="qc-info-table">
                <colgroup>
                    <col style="width:28%">
                    <col style="width:48%">
                    <col style="width:24%">
                </colgroup>

                <tbody>
                    <tr>
                        <td>
                            DATE:
                            <strong>
                                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                            </strong>
                        </td>

                        <td>
                            SHIFT:
                            <strong>
                                @if($shiftId)
                                    {{ optional($shifts->firstWhere('id', $shiftId))->name ?? 'Selected shift' }}
                                @else
                                    All Shifts
                                @endif
                            </strong>
                        </td>

                        <td class="qc-category">STEEL / COMEX</td>
                    </tr>
                </tbody>
            </table>

            {{-- QC measurements --}}
            <div class="qc-table-wrap">
                <table class="qc-report-table">
                    <colgroup>
    <col style="width:4%">
    <col style="width:5%">
    <col style="width:6%">
    <col style="width:4%">
    <col style="width:4%">
    <col style="width:9%">
    <col style="width:7%">
    <col style="width:6%">
    <col style="width:6%">
    <col style="width:6%">
    <col style="width:6%">
    <col style="width:6%">
    <col style="width:6%">
    <col style="width:8%">
    <col style="width:7%">
</colgroup>

                    <thead>
                        <tr>
                            <th rowspan="2">TIME</th>
                            <th rowspan="2">M/C NO.</th>
                            <th rowspan="2">PRODUCT</th>
                            <th colspan="2">THREAD PLUG GAUGE</th>

                            <th rowspan="2">
                                CONCENTRICITY / EMT &amp; T.P.G.
                                GAUGE / MINOR DIA
                            </th>

                            <th>EMT GAUGE AFTER TAPPING</th>
                            <th>BURS ON FACE</th>
                            <th>BURS ON THREAD</th>
                            <th>THREAD FLAT</th>
                            <th>TAP MARK</th>
                            <th>OFFSET</th>
                            <th>ANGULARITY</th>
                            <th>VISUAL DEFECTS FOUND ANY</th>
                            <th rowspan="2">REMARKS / RM INFO</th>
                            
                        </tr>

                        <tr>
                            <th>GO</th>
                            <th>NO GO</th>
                            <th>GO</th>
                            <th>PRESENT / ABSENT</th>
                            <th>PRESENT / ABSENT</th>
                            <th>PRESENT / ABSENT</th>
                            <th>PRESENT / ABSENT</th>
                            <th>PRESENT / ABSENT</th>
                            <th>PRESENT / ABSENT</th>
                            <th>PRESENT / ABSENT</th>
                            
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($inspections as $inspection)
                            @php
                                $measurements = $inspection->measurements ?? [];
                                $checkNumber = '—';

                                if ($inspection->shift && $inspection->inspection_time) {
                                    $start = \Carbon\Carbon::parse(
                                        $inspection->shift->start_time
                                    );

                                    $checkedAt = \Carbon\Carbon::parse(
                                        $inspection->inspection_time
                                    );

                                    if ($checkedAt->lt($start)) {
                                        $checkedAt->addDay();
                                    }

                                    $checkNumber = (int) floor(
                                        $start->diffInMinutes($checkedAt) / 180
                                    ) + 1;
                                }
                            @endphp

                            <tr>
                                <td>
                                    {{ $inspection->inspection_time
                                        ? \Carbon\Carbon::parse($inspection->inspection_time)->format('H:i')
                                        : '—' }}
                                </td>

                                <td>{{ $inspection->machine?->machine_code ?? '—' }}</td>

                                <td>{{ $inspection->product?->product_code ?? '—' }}</td>

                                <td>{{ $measurements['thread_gauge_go'] ?? '—' }}</td>

                                <td>{{ $measurements['thread_gauge_no_go'] ?? '—' }}</td>

                                <td>{{ $measurements['concentricity_minor_dia'] ?? '—' }}</td>

                                <td>{{ $measurements['emt_gauge_after_tapping'] ?? '—' }}</td>

                                <td>{{ $measurements['burr_on_face'] ?? '—' }}</td>

                                <td>{{ $measurements['burr_on_thread'] ?? '—' }}</td>

                                <td>{{ $measurements['thread_flat'] ?? '—' }}</td>

                                <td>{{ $measurements['tap_mark'] ?? '—' }}</td>

                                <td>{{ $measurements['offset_value'] ?? '—' }}</td>

                                <td>{{ $measurements['angularity'] ?? '—' }}</td>

                                <td>{{ $measurements['visual_defects'] ?? '—' }}</td>

                                <td>{{ $inspection->remarks ?: '—' }}</td>

                               
                            </tr>
                        @empty
                            @for($i = 0; $i < 24; $i++)
                                <tr>
                                    @for($j = 0; $j < 18; $j++)
                                        <td>&nbsp;</td>
                                    @endfor
                                </tr>
                            @endfor
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Signature boxes --}}
            <div class="qc-signatures">
                <div>
                    <strong>QC PERSON</strong>
                    <div class="signature-space"></div>
                </div>

                <div>
                    <strong>PROD. SIGNATURE:</strong>
                    <div class="signature-space"></div>
                </div>

                <div>
                    <strong>CHECKED BY:</strong>
                    <div class="signature-space"></div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .report-sheet {
        color: #111;
    }

    .report-paper {
        background: #fff !important;
        color: #111 !important;
        border: 1px solid #222 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .qc-header-table,
    .qc-info-table,
    .qc-report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        color: #111;
        margin: 0;
    }

    .qc-header-table td,
    .qc-info-table td,
    .qc-report-table th,
    .qc-report-table td {
        border: 1px solid #333;
    }

    .qc-header-table td {
        padding: 4px;
        text-align: center;
    }

    .qc-logo-cell img {
        display: block;
        width: 100%;
        max-width: 125px;
        max-height: 70px;
        margin: auto;
        object-fit: contain;
    }

    .qc-company-cell h3 {
        margin: 0;
        font-size: 20px;
        line-height: 1.3;
        font-weight: 800;
        color: #000;
    }

    .qc-address {
        font-size: 9px;
        font-weight: 600;
        line-height: 1.3;
    }

    .qc-subtitle {
        font-size: 9px;
        font-weight: 700;
    }

    .qc-meta-cell {
        padding: 5px !important;
        text-align: left !important;
        font-size: 9px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .qc-info-table td {
        padding: 8px;
        font-size: 11px;
        vertical-align: middle;
    }

    .qc-category {
        font-weight: 800;
        text-align: center;
    }

    .qc-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .qc-report-table {
        font-size: 8px;
    }

    .qc-report-table th,
    .qc-report-table td {
        padding: 4px 2px;
        text-align: center;
        vertical-align: middle;
        line-height: 1.35;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .qc-report-table thead th {
        background: #eaf1f8;
        font-size: 8px;
        font-weight: 700;
    }

    .qc-report-table thead tr:first-child th {
        height: 58px;
    }

    .qc-report-table thead tr:nth-child(2) th {
        height: 32px;
    }

    .qc-report-table tbody td {
        min-height: 24px;
    }
    .qc-signatures {
    display: grid;
    grid-template-columns: 1fr 1.3fr 1.3fr;
    gap: 20px;
    margin-top: 25px;
}

.qc-signatures > div {
    min-height: 65px;
    padding: 6px;
    border: 1px solid #333;
    font-size: 9px;
}

.signature-space {
    min-height: 38px;
}

@media print {
    .report-paper > .card-body {
        display: flex !important;
        flex-direction: column !important;
        min-height: 185mm !important;
    }

    .qc-signatures {
        margin-top: auto !important;
        padding-top: 15px;
        break-inside: avoid;
    }
}
    
    

    @page {
        size: A4 landscape;
        margin: 6mm;
    }

    @media print {
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
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
        .report-paper > .card-body {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .qc-table-wrap {
            overflow: visible !important;
        }

        .qc-header-table,
        .qc-info-table,
        .qc-report-table {
            width: 100% !important;
        }

        .qc-report-table {
            table-layout: fixed !important;
            font-size: 6.5px !important;
        }

        .qc-report-table th,
        .qc-report-table td {
            font-size: 6.5px !important;
            line-height: 1.25 !important;
            padding: 3px 1px !important;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }

        .qc-report-table thead th {
            height: auto !important;
            padding: 4px 1px !important;
        }

        .qc-header-table,
        .qc-info-table,
        .qc-report-table tr,
        
        

        .qc-logo-cell img {
            max-height: 55px;
        }


    }

    @media screen and (max-width: 768px) {
        .qc-table-wrap {
            overflow-x: auto;
        }

        .qc-report-table {
            min-width: 1200px;
        }
    }
    @media print {
    /* General report font */
    .report-paper {
        font-size: 12px !important;
    }

    /* Company name and report heading */
    .report-paper h3,
    .report-paper h4 {
        font-size: 18px !important;
    }

    /* Table font */
    .report-paper table {
        font-size: 11px !important;
    }

    .report-paper table th,
    .report-paper table td {
        font-size: 11px !important;
        padding: 5px 3px !important;
        line-height: 1.3 !important;
    }

    /* Date and shift information */
    .report-paper .report-info {
        font-size: 12px !important;
    }

    /* Signature section */
    .qc-signatures {
        font-size: 12px !important;
        margin-top: auto !important;
    }
}
</style>
@endpush
