<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #111111;
        }

        .page {
            width: 100%;
            border: 1.2px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .no-border {
            border: none !important;
        }

        /* =========================================
           HEADER
        ========================================= */

        .header-logo {
            width: 18%;
            text-align: center;
            vertical-align: middle;
            padding: 8px;
        }

        .header-logo img {
            width: 85px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .company-area {
            width: 57%;
            text-align: center;
            vertical-align: middle;
            padding: 7px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 7px;
            line-height: 11px;
        }

        .memo-title {
            margin-top: 7px;
            background: #e8e8e8;
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        /* =========================================
           DOCUMENT CONTROL
        ========================================= */

        .document-control {
            width: 25%;
            padding: 0;
        }

        .document-control table {
            width: 100%;
        }

        .document-control td {
            font-size: 7px;
            padding: 4px;
            height: 20px;
        }

        .doc-label {
            font-weight: bold;
        }

        .doc-value {
            text-align: right;
            font-weight: bold;
        }

        /* =========================================
           COMMON LABEL
        ========================================= */

        .label {
            font-weight: bold;
            font-size: 8.5px;
        }

        .value {
            font-size: 9px;
        }

        /* =========================================
           BASIC INFO
        ========================================= */

        .basic-info td {
            height: 27px;
        }

        /* =========================================
           MACHINE INFORMATION
        ========================================= */

        .machine-info td {
            height: 75px;
            vertical-align: top;
            padding: 7px;
        }

        .field-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 8px;
        }

        .field-value {
            font-size: 10px;
            font-weight: normal;
        }

        /* =========================================
           PROBLEM
        ========================================= */

        .problem-box {
            height: 85px;
            vertical-align: top;
            padding: 7px;
        }

        /* =========================================
           SECTION HEADER
        ========================================= */

        .section-header {
            background: #e7e7e7;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            padding: 6px;
            height: 27px;
        }

        /* =========================================
           BREAKDOWN / ACTION
        ========================================= */

        .breakdown-box,
        .action-box {
            height: 245px;
            vertical-align: top;
            padding: 8px;
        }

        .description {
            padding-top: 8px;
            font-size: 9px;
            line-height: 15px;
        }

        /* =========================================
           TIME SECTION
        ========================================= */

        .time-info td {
            height: 38px;
            font-size: 8.5px;
        }

        /* =========================================
           SIGNATURE
        ========================================= */

        .signature-area td {
            height: 85px;
            vertical-align: bottom;
            text-align: center;
            padding-bottom: 8px;
        }

        .signature-line {
            display: block;
            width: 75%;
            margin: 0 auto 5px auto;
            border-bottom: 1px solid #000;
            height: 18px;
        }

        .signature-title {
            font-weight: bold;
            font-size: 8px;
        }

        .signature-name {
            font-size: 8px;
            margin-top: 3px;
        }

        /* =========================================
           FOOTER
        ========================================= */

        .footer {
            font-size: 6.5px;
            color: #555;
            text-align: right;
            padding-top: 4px;
        }

    </style>
</head>

<body>

<div class="page">

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <table>
        <tr>

            {{-- LOGO --}}
            <td class="header-logo">

                <img
                    src="{{ public_path('images/mts.png') }}"
                    alt="GROW EVER"
                >

            </td>


            {{-- COMPANY --}}
            <td class="company-area">

                <div class="company-name">
                    GROW EVER GROUP
                </div>

                <div class="company-address">
                    {{ $memo['address'] }}
                </div>

                <div class="memo-title">
                    INTER OFFICE MEMO
                </div>

            </td>


            {{-- DOCUMENT CONTROL --}}
            <td class="document-control">

                <table>

                    <tr>
                        <td class="doc-label">
                            Format No:
                        </td>

                        <td class="doc-value">
                            {{ $memo['format_no'] }}
                        </td>
                    </tr>

                    <tr>
                        <td class="doc-label">
                            REV NO:
                        </td>

                        <td class="doc-value">
                            {{ $memo['rev_no'] }}
                        </td>
                    </tr>

                    <tr>
                        <td class="doc-label">
                            REV DATE:
                        </td>

                        <td class="doc-value">
                            {{ $memo['rev_date'] }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2"
                            style="text-align:center;font-weight:bold;background:#eeeeee;">
                            STEEL / COMEX
                        </td>
                    </tr>

                </table>

            </td>

        </tr>
    </table>


    {{-- =====================================================
         DATE / DEPARTMENT / SHIFT
    ====================================================== --}}

    <table class="basic-info">

        <tr>

            <td width="23%">

                <span class="label">
                    DATE:
                </span>

                <span class="value">
                    {{ $memo['date'] }}
                </span>

            </td>


            <td width="57%">

                <span class="label">
                    Dept:
                </span>

                <span class="value">
                    {{ $memo['department'] }}
                </span>

            </td>


            <td width="20%">

                <span class="label">
                    SHIFT:
                </span>

                <span class="value">
                    {{ $memo['shift'] }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         MACHINE INFORMATION
    ====================================================== --}}

    <table class="machine-info">

        <tr>

            <td width="65%">

                <div class="field-title">
                    Machine Name &amp; Number:
                </div>

                <div class="field-value">
                    {{ $memo['machine_name'] }}
                </div>

                <div class="field-value">
                    {{ $memo['machine_no'] }}
                </div>

            </td>


            <td width="35%">

                <div class="field-title">
                    TIME:
                </div>

                <div class="field-value">
                    {{ $memo['time'] }}
                </div>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         PROBLEM IDENTIFIED
    ====================================================== --}}

    <table>

        <tr>

            <td class="problem-box">

                <div class="field-title">
                    Problem Identified:
                </div>

                <div class="description">

                    {{ $memo['problem'] }}

                </div>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         BREAKDOWN / ACTION
    ====================================================== --}}

    <table>

        <tr>

            {{-- BREAKDOWN --}}
            <td width="50%" style="padding:0;">

                <div class="section-header">
                    Breakdown Reason
                </div>

                <div class="breakdown-box">

                    <div class="description">
                        {{ $memo['breakdown_reason'] }}
                    </div>

                </div>

            </td>


            {{-- ACTION --}}
            <td width="50%" style="padding:0;">

                <div class="section-header">
                    Action Taken
                </div>

                <div class="action-box">

                    <div class="description">
                        {{ $memo['action_taken'] }}
                    </div>

                </div>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         TIME INFORMATION
    ====================================================== --}}

    <table class="time-info">

        <tr>

            <td width="50%">

                <span class="label">
                    Repair Completion Time:
                </span>

                {{ $memo['repair_completion_time'] }}

            </td>


            <td width="50%">

                <span class="label">
                    Total Down Time:
                </span>

                {{ $memo['total_down_time'] }}

            </td>

        </tr>

    </table>


    {{-- =====================================================
         SIGNATURE
    ====================================================== --}}

    <table class="signature-area">

        <tr>

            <td width="50%">

                <span class="signature-line"></span>

                <div class="signature-title">
                    Attended By
                </div>

                <div class="signature-name">
                    {{ $memo['attended_by'] }}
                </div>

            </td>


            <td width="50%">

                <span class="signature-line"></span>

                <div class="signature-title">
                    Signature of Production Supervisor
                </div>

                <div class="signature-name">
                    {{ $memo['supervisor'] }}
                </div>

            </td>

        </tr>

    </table>

</div>


<div class="footer">
    Grow Ever Group &nbsp; | &nbsp; Inter Office Memo
</div>

</body>
</html>