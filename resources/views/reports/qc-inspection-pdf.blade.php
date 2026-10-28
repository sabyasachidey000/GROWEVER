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
                            <img src="{{ $logoPath }}" alt="Grow Ever Logo">
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
                                    @for($j = 0; $j < 15; $j++)
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
.qc-header-table,
.qc-info-table,
.qc-report-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin: 0;
}

.qc-header-table td {
    border: 1px solid #cbd5e1;
    padding: 5px;
    vertical-align: middle;
}

.qc-logo-cell {
    text-align: center;
    background: #fff;
}

.qc-logo-cell img {
    display: block;
    width: 115px;
    max-width: 100%;
    max-height: 58px;
    margin: auto;
}

.qc-company-cell {
    background: #17365d;
    color: #fff;
    text-align: center;
}

.qc-company-cell h3 {
    margin: 0;
    color: #fff;
    font-size: 19px;
    font-weight: bold;
    letter-spacing: 1px;
}

.qc-address {
    text-align: center;
    font-size: 8px;
    font-weight: bold;
    color: #334155;
}

.qc-subtitle {
    text-align: center;
    font-size: 9px;
    font-weight: bold;
    color: #17365d;
}

.qc-meta-cell {
    padding: 5px;
    text-align: left;
    font-size: 8px;
    font-weight: bold;
    color: #334155;
}

/* Date / shift strip */
.qc-info-table {
    margin-top: 7px;
    margin-bottom: 7px;
}

.qc-info-table td {
    padding: 7px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    font-size: 10px;
    vertical-align: middle;
}

.qc-info-table strong {
    color: #17365d;
}

.qc-category {
    text-align: center;
    font-weight: bold;
    color: #17365d;
    letter-spacing: .5px;
}

/* Inspection table */
.qc-table-wrap {
    width: 100%;
    overflow: visible;
}

.qc-report-table {
    font-size: 7px;
}

.qc-report-table th,
.qc-report-table td {
    border: 1px solid #94a3b8;
    padding: 4px 2px;
    text-align: center;
    vertical-align: middle;
    line-height: 1.3;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.qc-report-table thead th {
    background: #1f4e78;
    color: #fff;
    font-size: 7px;
    font-weight: bold;
}

.qc-report-table tbody td {
    color: #1f2937;
    background: #fff;
}

.qc-report-table tbody tr:nth-child(even) td {
    background: #f1f5f9;
}

.qc-report-table tbody tr {
    page-break-inside: avoid;
}

/* Signature section */
.qc-signatures {
    width: 100%;
    margin-top: 18px;
    display: table;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 8px 0;
}

.qc-signatures > div {
    display: table-cell;
    width: 33.33%;
    height: 65px;
    padding: 8px;
    border: 1px solid #94a3b8;
    border-top: 3px solid #1f4e78;
    vertical-align: top;
    font-size: 9px;
    font-weight: bold;
    color: #17365d;
}

.signature-space {
    height: 38px;
}

/* PDF-safe print rules */
@media print {
    html,
    body {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .report-paper,
    .report-paper > .card-body {
        width: 100%;
        margin: 0;
        padding: 0;
        border: 0;
        box-shadow: none;
    }

    .qc-table-wrap {
        overflow: visible;
    }

    .qc-report-table {
        width: 100%;
        table-layout: fixed;
    }

    .qc-report-table thead {
        display: table-header-group;
    }

    .qc-report-table tr {
        page-break-inside: avoid;
    }

    .qc-signatures {
        page-break-inside: avoid;
    }
}

</style>