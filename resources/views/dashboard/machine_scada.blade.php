<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Machine SCADA</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
        }

        body {
            background: #f4f7fb;
        }

        .scada-page {
            width: 100%;
            min-height: 100vh;
            background: #f4f7fb;
            padding: 20px;
            box-sizing: border-box;
        }

        /* your existing SCADA CSS continues here */
    </style>

</head>

<body>

<div class="scada-page">

<style>
    /* =========================================================
   INDUSTRIAL BLUE SCADA THEME
========================================================= */

/* ============================================================
   MACHINE SCADA - COMPLETE CSS
   Dark Blue Industrial / Premium SCADA Theme
   ============================================================ */

:root {
    /* Main colors */
    --scada-bg: #071521;
    --scada-bg-2: #091c2b;
    --scada-panel: #0b1e2e;
    --scada-panel-2: #102b40;
    --scada-panel-3: #0d2639;

    /* Borders */
    --scada-border: #1b4662;
    --scada-border-light: #245a7b;

    /* Text */
    --text-primary: #e8f4fb;
    --text-secondary: #9bb5c7;
    --text-muted: #66879d;

    /* Blue */
    --blue: #1683c8;
    --blue-light: #29a9ff;
    --blue-dark: #0b5d91;
    --blue-soft: rgba(41, 169, 255, 0.12);

    /* Status */
    --running: #20d98a;
    --running-soft: rgba(32, 217, 138, 0.12);

    --idle: #ffc247;
    --idle-soft: rgba(255, 194, 71, 0.12);

    --stopped: #ff5361;
    --stopped-soft: rgba(255, 83, 97, 0.12);

    --breakdown: #ff8a27;
    --breakdown-soft: rgba(255, 138, 39, 0.12);

    /* Shadows */
    --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.25);
    --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.30);
    --shadow-lg: 0 14px 40px rgba(0, 0, 0, 0.38);

    /* Radius */
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 14px;

    /* Transition */
    --transition: 0.22s ease;
}


/* ============================================================
   GLOBAL
   ============================================================ */

* {
    box-sizing: border-box;
}

html {
    min-height: 100%;
    background: var(--scada-bg);
}

body {
    margin: 0;
    padding: 0;
    min-height: 100vh;

    background:
        radial-gradient(
            circle at 20% 0%,
            rgba(22, 131, 200, 0.08),
            transparent 35%
        ),
        radial-gradient(
            circle at 90% 20%,
            rgba(41, 169, 255, 0.05),
            transparent 30%
        ),
        linear-gradient(
            180deg,
            var(--scada-bg) 0%,
            #06121d 100%
        );

    color: var(--text-primary);

    font-family:
        "Segoe UI",
        Roboto,
        Arial,
        sans-serif;

    overflow-x: hidden;
}


/* ============================================================
   SCADA PAGE
   ============================================================ */

.scada-page {
    width: 100%;
    min-height: 100vh;

    padding: 20px;

    background:
        linear-gradient(
            180deg,
            rgba(7, 21, 33, 0.96),
            rgba(6, 18, 29, 0.98)
        );
}


/* ============================================================
   HEADER
   ============================================================ */

.scada-header {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: space-between;

    min-height: 82px;

    padding: 18px 22px;

    margin-bottom: 18px;

    background:
        linear-gradient(
            135deg,
            #102d44 0%,
            #0a1e30 100%
        );

    border: 1px solid var(--scada-border);

    border-radius: var(--radius-lg);

    box-shadow: var(--shadow-md);

    overflow: hidden;
}

.scada-header::before {
    content: "";

    position: absolute;

    left: 0;
    right: 0;
    top: 0;

    height: 2px;

    background:
        linear-gradient(
            90deg,
            transparent,
            var(--blue-light),
            transparent
        );

    opacity: 0.8;
}

.scada-header::after {
    content: "";

    position: absolute;

    width: 220px;
    height: 220px;

    right: -100px;
    top: -150px;

    border-radius: 50%;

    background: rgba(41, 169, 255, 0.05);

    pointer-events: none;
}


/* Header title */

.scada-title {
    position: relative;
    z-index: 1;
}

.scada-title h3 {
    margin: 0;

    display: flex;
    align-items: center;
    gap: 10px;

    color: var(--text-primary);

    font-size: 23px;
    font-weight: 700;

    letter-spacing: 0.3px;
}

.scada-title h3 i {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    color: var(--blue-light);

    font-size: 20px;

    background:
        rgba(41, 169, 255, 0.10);

    border:
        1px solid rgba(41, 169, 255, 0.25);

    border-radius: 9px;

    box-shadow:
        0 0 16px rgba(41, 169, 255, 0.08);
}

.scada-title p {
    margin: 5px 0 0 48px;

    color: var(--text-muted);

    font-size: 12px;

    letter-spacing: 0.5px;
}


/* ============================================================
   LIVE INDICATOR
   ============================================================ */

.live-indicator {
    position: relative;
    z-index: 2;

    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 8px 13px;

    background:
        rgba(32, 217, 138, 0.08);

    border:
        1px solid rgba(32, 217, 138, 0.25);

    border-radius: 30px;

    color: var(--running);

    font-size: 11px;
    font-weight: 700;

    letter-spacing: 1px;

    text-transform: uppercase;

    box-shadow:
        0 0 15px rgba(32, 217, 138, 0.05);
}

.live-dot {
    width: 8px;
    height: 8px;

    border-radius: 50%;

    background: var(--running);

    box-shadow:
        0 0 0 3px rgba(32, 217, 138, 0.10),
        0 0 10px rgba(32, 217, 138, 0.65);

    animation: livePulse 1.8s infinite ease-in-out;
}

@keyframes livePulse {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.55;
        transform: scale(0.82);
    }
}


/* ============================================================
   SUMMARY SECTION
   ============================================================ */

.scada-summary {
    display: grid;

    grid-template-columns:
        repeat(5, minmax(0, 1fr));

    gap: 12px;

    margin-bottom: 18px;
}


/* Summary card */

.summary-card {
    position: relative;

    display: flex;
    align-items: center;

    min-height: 82px;

    padding: 14px 16px;

    background:
        linear-gradient(
            145deg,
            #102b40,
            #0b2031
        );

    border:
        1px solid var(--scada-border);

    border-radius: var(--radius-md);

    box-shadow: var(--shadow-sm);

    overflow: hidden;

    transition:
        transform var(--transition),
        border-color var(--transition),
        box-shadow var(--transition);
}

.summary-card:hover {
    transform: translateY(-2px);

    border-color: var(--scada-border-light);

    box-shadow: var(--shadow-md);
}

.summary-card::before {
    content: "";

    position: absolute;

    left: 0;
    top: 0;
    bottom: 0;

    width: 3px;

    background: var(--blue);
}


/* Summary icon */

.summary-icon {
    display: flex;

    align-items: center;
    justify-content: center;

    flex: 0 0 44px;

    width: 44px;
    height: 44px;

    margin-right: 12px;

    border-radius: 10px;

    background:
        rgba(41, 169, 255, 0.08);

    border:
        1px solid rgba(41, 169, 255, 0.15);

    color: var(--blue-light);

    font-size: 18px;
}

.summary-card h2 {
    margin: 0;

    color: var(--text-primary);

    font-family:
        "Consolas",
        "Courier New",
        monospace;

    font-size: 25px;
    font-weight: 700;

    line-height: 1.1;

    letter-spacing: 1px;
}

.summary-card span {
    display: block;

    margin-top: 4px;

    color: var(--text-muted);

    font-size: 10px;
    font-weight: 600;

    letter-spacing: 0.8px;

    text-transform: uppercase;
}


/* Summary status colors */

.summary-running::before {
    background: var(--running);
}

.summary-running .summary-icon {
    color: var(--running);
    background: var(--running-soft);
    border-color: rgba(32, 217, 138, 0.18);
}

.summary-idle::before {
    background: var(--idle);
}

.summary-idle .summary-icon {
    color: var(--idle);
    background: var(--idle-soft);
    border-color: rgba(255, 194, 71, 0.18);
}

.summary-stopped::before {
    background: var(--stopped);
}

.summary-stopped .summary-icon {
    color: var(--stopped);
    background: var(--stopped-soft);
    border-color: rgba(255, 83, 97, 0.18);
}

.summary-breakdown::before {
    background: var(--breakdown);
}

.summary-breakdown .summary-icon {
    color: var(--breakdown);
    background: var(--breakdown-soft);
    border-color: rgba(255, 138, 39, 0.18);
}

.summary-total::before {
    background: var(--blue-light);
}

.summary-total .summary-icon {
    color: var(--blue-light);
}


/* ============================================================
   TOOLBAR
   ============================================================ */

.scada-toolbar {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 12px;

    margin-bottom: 16px;

    padding: 10px;

    background:
        linear-gradient(
            145deg,
            #0d2639,
            #091c2b
        );

    border:
        1px solid var(--scada-border);

    border-radius: var(--radius-md);

    box-shadow: var(--shadow-sm);
}


/* Search */

.scada-search {
    position: relative;

    flex: 1;

    max-width: 440px;
}

.scada-search i {
    position: absolute;

    left: 13px;
    top: 50%;

    transform: translateY(-50%);

    color: var(--text-muted);

    font-size: 14px;

    pointer-events: none;
}

.scada-search input,
.scada-toolbar select {
    height: 40px;

    color: var(--text-primary);

    background:
        #081a29;

    border:
        1px solid var(--scada-border);

    border-radius: 7px;

    outline: none;

    font-size: 13px;

    transition:
        border-color var(--transition),
        box-shadow var(--transition),
        background var(--transition);
}

.scada-search input {
    width: 100%;

    padding:
        0 14px 0 38px;
}

.scada-search input::placeholder {
    color: #55758a;
}

.scada-toolbar select {
    min-width: 150px;

    padding: 0 34px 0 12px;

    cursor: pointer;
}

.scada-search input:focus,
.scada-toolbar select:focus {
    background: #0a2030;

    border-color: var(--blue);

    box-shadow:
        0 0 0 3px rgba(41, 169, 255, 0.08);
}


/* Refresh */

.refresh-btn {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    height: 40px;

    padding: 0 15px;

    color: var(--text-primary);

    background:
        linear-gradient(
            145deg,
            #123550,
            #0c263b
        );

    border:
        1px solid var(--scada-border);

    border-radius: 7px;

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition:
        background var(--transition),
        border-color var(--transition),
        transform var(--transition);
}

.refresh-btn:hover {
    background:
        linear-gradient(
            145deg,
            #16405f,
            #0e3049
        );

    border-color: var(--blue);

    transform: translateY(-1px);
}

.refresh-btn:active {
    transform: translateY(0);
}

.refresh-btn i {
    color: var(--blue-light);
}


/* ============================================================
   MACHINE GRID
   ============================================================ */

/* ============================================================
   MACHINE GRID - 3 CARDS PER ROW
   ============================================================ */

.machine-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    width: 100%;
}


/* Large screen - 3 cards */
@media (min-width: 1201px) {
    .machine-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}


/* Tablet - 2 cards */
@media (min-width: 601px) and (max-width: 1200px) {
    .machine-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}


/* Mobile - 1 card */
@media (max-width: 600px) {
    .machine-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
}


/* ============================================================
   MACHINE CARD
   ============================================================ */

.machine-card {
    position: relative;

    display: flex;
    flex-direction: column;

    min-width: 0;

    background:
        linear-gradient(
            145deg,
            #102b40,
            #0b1e2e
        );

    border:
        1px solid var(--scada-border);

    border-radius: var(--radius-lg);

    box-shadow: var(--shadow-md);

    overflow: hidden;

    transition:
        transform var(--transition),
        border-color var(--transition),
        box-shadow var(--transition);
}

.machine-card:hover {
    transform: translateY(-3px);

    border-color:
        rgba(41, 169, 255, 0.42);

    box-shadow:
        var(--shadow-lg),
        0 0 24px rgba(22, 131, 200, 0.06);
}


/* Top status strip */

.machine-card::before {
    content: "";

    position: absolute;

    left: 0;
    right: 0;
    top: 0;

    height: 3px;

    background: var(--blue);

    z-index: 5;
}

.machine-card.running::before {
    background: var(--running);
}

.machine-card.idle::before {
    background: var(--idle);
}

.machine-card.stopped::before {
    background: var(--stopped);
}

.machine-card.breakdown::before {
    background: var(--breakdown);
}


/* ============================================================
   MACHINE TOP
   ============================================================ */

.machine-top {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding: 15px 16px 12px;

    border-bottom:
        1px solid rgba(36, 90, 123, 0.35);
}

.machine-name {
    min-width: 0;

    color: var(--text-primary);

    font-size: 16px;
    font-weight: 700;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* Machine image */

.machine-image {
    position: relative;

    flex: 0 0 66px;

    width: 66px;
    height: 66px;

    display: flex;

    align-items: center;
    justify-content: center;

    background:
        linear-gradient(
            145deg,
            #0b1b29,
            #07131e
        );

    border:
        1px solid var(--scada-border);

    border-radius: 10px;

    overflow: hidden;
}

.machine-image img {
    max-width: 100%;
    max-height: 100%;

    width: auto;
    height: auto;

    object-fit: contain;
}

.machine-code {
    margin-top: 4px;

    color: var(--blue-light);

    font-family:
        "Consolas",
        "Courier New",
        monospace;

    font-size: 11px;

    letter-spacing: 0.8px;
}

.machine-type {
    margin-top: 2px;

    color: var(--text-muted);

    font-size: 10px;

    text-transform: uppercase;

    letter-spacing: 0.5px;
}


/* ============================================================
   MACHINE STATUS
   ============================================================ */

.machine-status {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 6px 9px;

    border-radius: 20px;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.6px;

    text-transform: uppercase;

    white-space: nowrap;
}

.machine-status .status-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: currentColor;

    box-shadow:
        0 0 7px currentColor;
}


/* Running */

.status-running {
    color: var(--running);

    background:
        var(--running-soft);

    border:
        1px solid rgba(32, 217, 138, 0.18);
}

.status-running .status-dot {
    animation: statusBlink 1.5s infinite ease-in-out;
}


/* Idle */

.status-idle {
    color: var(--idle);

    background:
        var(--idle-soft);

    border:
        1px solid rgba(255, 194, 71, 0.18);
}


/* Stopped */

.status-stopped {
    color: var(--stopped);

    background:
        var(--stopped-soft);

    border:
        1px solid rgba(255, 83, 97, 0.18);
}


/* Breakdown */

.status-breakdown {
    color: var(--breakdown);

    background:
        var(--breakdown-soft);

    border:
        1px solid rgba(255, 138, 39, 0.18);
}

@keyframes statusBlink {
    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.45;
    }
}


/* ============================================================
   PRODUCTION COUNTER PANEL
   ============================================================ */

.production-counter-panel {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    margin: 12px;

    background:
        linear-gradient(
            145deg,
            #0d2639,
            #081a29
        );

    border:
        1px solid #1b4c68;

    border-radius: 12px;

    overflow: hidden;

    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.025),
        0 6px 18px rgba(0, 0, 0, 0.18);
}


/* Individual counter */

.production-counter,
.production-counter_tap,
.production-counter-tot {
    position: relative;

    min-width: 0;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    min-height: 105px;

    padding: 14px 8px;

    text-align: center;

    transition:
        background var(--transition),
        transform var(--transition);
}


/* Vertical separators */

.production-counter:not(:last-child)::after,
.production-counter_tap:not(:last-child)::after {
    content: "";

    position: absolute;

    right: 0;
    top: 18px;
    bottom: 18px;

    width: 1px;

    background:
        linear-gradient(
            180deg,
            transparent,
            rgba(36, 90, 123, 0.65),
            transparent
        );
}


/* Hover */

.production-counter:hover,
.production-counter_tap:hover,
.production-counter-tot:hover {
    background:
        rgba(41, 169, 255, 0.035);
}


/* ============================================================
   COUNTER LABEL
   ============================================================ */

.production-label {
    margin-bottom: 7px;

    color: var(--text-muted);

    font-size: 9px;
    font-weight: 700;

    letter-spacing: 0.8px;

    text-transform: uppercase;

    white-space: nowrap;
}


/* ============================================================
   COUNTER VALUES
   ============================================================ */

.production-value,
.production-value-tap,
.production-value-tot {
    display: block;

    width: 100%;

    color: var(--blue-light);

    font-family:
        "Consolas",
        "Courier New",
        monospace;

    font-size: 25px;
    font-weight: 700;

    line-height: 1.1;

    letter-spacing: 1px;

    font-variant-numeric:
        tabular-nums;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    text-shadow:
        0 0 6px rgba(41, 169, 255, 0.35),
        0 0 15px rgba(41, 169, 255, 0.10);

    transition:
        color 0.2s ease,
        text-shadow 0.2s ease,
        transform 0.2s ease;
}


/* Counter active animation */

.production-value.counter-active,
.production-value-tap.counter-active,
.production-value-tot.counter-active {
    color: #70c9ff;

    text-shadow:
        0 0 7px rgba(41, 169, 255, 0.85),
        0 0 20px rgba(41, 169, 255, 0.42);

    transform: scale(1.025);
}


/* ============================================================
   COUNTER UNIT
   ============================================================ */

.production-unit {
    margin-top: 5px;

    color: #55798e;

    font-size: 8px;
    font-weight: 600;

    letter-spacing: 1px;

    text-transform: uppercase;
}


/* ============================================================
   TOTAL PRODUCTION COUNTER
   ============================================================ */

.production-counter.total-counter,
.production-counter-tot {
    background:
        linear-gradient(
            145deg,
            rgba(22, 131, 200, 0.08),
            rgba(8, 26, 41, 0.10)
        );
}

.production-counter.total-counter::before,
.production-counter-tot::before {
    content: "";

    position: absolute;

    left: 0;
    right: 0;
    bottom: 0;

    height: 2px;

    background:
        linear-gradient(
            90deg,
            transparent,
            var(--blue),
            transparent
        );

    opacity: 0.7;
}

.production-counter.total-counter .production-value,
.production-counter-tot .production-value-tot {
    color: #55bfff;

    font-size: 28px;

    text-shadow:
        0 0 7px rgba(41, 169, 255, 0.45),
        0 0 18px rgba(41, 169, 255, 0.14);
}


/* ============================================================
   MACHINE METRICS
   ============================================================ */

.machine-metrics {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 8px;

    padding:
        0 12px 12px;
}

.metric {
    min-width: 0;

    padding: 9px 8px;

    background:
        rgba(7, 21, 33, 0.55);

    border:
        1px solid rgba(36, 90, 123, 0.32);

    border-radius: 7px;

    text-align: center;
}

.metric:first-child {
    border-color:
        rgba(41, 169, 255, 0.20);
}

.metric-label {
    display: block;

    margin-bottom: 4px;

    color: var(--text-muted);

    font-size: 8px;
    font-weight: 600;

    letter-spacing: 0.6px;

    text-transform: uppercase;
}

.metric-value {
    display: block;

    color: var(--text-secondary);

    font-family:
        "Consolas",
        "Courier New",
        monospace;

    font-size: 12px;
    font-weight: 700;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* ============================================================
   MACHINE STATE
   ============================================================ */

.machine-state {
    display: flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    margin:
        0 12px 12px;

    min-height: 34px;

    border-radius: 7px;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.7px;

    text-transform: uppercase;

    border: 1px solid transparent;
}

.machine-state i {
    font-size: 11px;
}


/* Running */

.machine-state.running {
    color: var(--running);

    background:
        var(--running-soft);

    border-color:
        rgba(32, 217, 138, 0.18);
}


/* Idle */

.machine-state.idle {
    color: var(--idle);

    background:
        var(--idle-soft);

    border-color:
        rgba(255, 194, 71, 0.18);
}


/* Stopped */

.machine-state.stopped {
    color: var(--stopped);

    background:
        var(--stopped-soft);

    border-color:
        rgba(255, 83, 97, 0.18);
}


/* Breakdown */

.machine-state.breakdown {
    color: var(--breakdown);

    background:
        var(--breakdown-soft);

    border-color:
        rgba(255, 138, 39, 0.18);
}


/* ============================================================
   MACHINE ACTION
   ============================================================ */

.machine-action {
    display: flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    width: calc(100% - 24px);

    min-height: 36px;

    margin:
        0 12px 14px;

    color: var(--text-secondary);

    background:
        #102b40;

    border:
        1px solid var(--scada-border);

    border-radius: 7px;

    font-size: 10px;
    font-weight: 600;

    text-decoration: none;

    cursor: pointer;

    transition:
        color var(--transition),
        background var(--transition),
        border-color var(--transition),
        transform var(--transition);
}

.machine-action:hover {
    color: #ffffff;

    background:
        #123c58;

    border-color:
        var(--blue);

    transform: translateY(-1px);
}

.machine-action i {
    color: var(--blue-light);
}


/* ============================================================
   FOOTER
   ============================================================ */

.scada-footer {
    margin-top: 18px;

    padding: 12px 5px;

    color: var(--text-muted);

    font-size: 10px;

    text-align: center;

    letter-spacing: 0.4px;

    border-top:
        1px solid rgba(36, 90, 123, 0.25);
}


/* ============================================================
   SCROLLBAR
   ============================================================ */

::-webkit-scrollbar {
    width: 7px;
    height: 7px;
}

::-webkit-scrollbar-track {
    background: #06131f;
}

::-webkit-scrollbar-thumb {
    background:
        #17445f;

    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background:
        #216789;
}


/* ============================================================
   SELECTION
   ============================================================ */

::selection {
    color: #ffffff;

    background:
        rgba(41, 169, 255, 0.30);
}


/* ============================================================
   RESPONSIVE - LARGE TABLET
   ============================================================ */

@media (max-width: 1200px) {

    .scada-summary {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

    .machine-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
    }
}


/* ============================================================
   RESPONSIVE - TABLET
   ============================================================ */

@media (max-width: 900px) {

    .scada-page {
        padding: 14px;
    }

    .scada-summary {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .scada-toolbar {
        flex-wrap: wrap;
    }

    .scada-search {
        max-width: none;

        flex: 1 1 100%;
    }

    .machine-grid {
        grid-template-columns:
            1fr;
    }
}


/* ============================================================
   RESPONSIVE - MOBILE
   ============================================================ */

@media (max-width: 600px) {

    .scada-page {
        padding: 10px;
    }

    .scada-header {
        min-height: auto;

        padding: 15px;

        align-items: flex-start;
    }

    .scada-title h3 {
        font-size: 18px;
    }

    .scada-title h3 i {
        width: 32px;
        height: 32px;

        font-size: 16px;
    }

    .scada-title p {
        margin-left: 42px;

        font-size: 10px;
    }

    .live-indicator {
        padding: 6px 9px;

        font-size: 9px;
    }

    .scada-summary {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 8px;
    }

    .summary-card {
        min-height: 70px;

        padding: 10px;
    }

    .summary-icon {
        flex-basis: 36px;

        width: 36px;
        height: 36px;

        margin-right: 8px;

        font-size: 15px;
    }

    .summary-card h2 {
        font-size: 20px;
    }

    .summary-card span {
        font-size: 8px;
    }

    .scada-toolbar {
        padding: 8px;

        gap: 8px;
    }

    .scada-toolbar select {
        flex: 1;

        min-width: 0;
    }

    .refresh-btn {
        padding: 0 11px;
    }

    .machine-grid {
        grid-template-columns:
            1fr;

        gap: 10px;
    }

    .production-counter-panel {
        margin: 10px;
    }

    .production-counter,
    .production-counter_tap,
    .production-counter-tot {
        min-height: 92px;

        padding: 11px 5px;
    }

    .production-value,
    .production-value-tap {
        font-size: 21px;
    }

    .production-counter.total-counter .production-value,
    .production-counter-tot .production-value-tot {
        font-size: 23px;
    }

    .production-label {
        font-size: 8px;
    }

    .production-unit {
        font-size: 7px;
    }

    .machine-metrics {
        gap: 5px;
    }

    .metric {
        padding: 7px 5px;
    }

    .metric-label {
        font-size: 7px;
    }

    .metric-value {
        font-size: 10px;
    }
}


/* ============================================================
   RESPONSIVE - VERY SMALL DEVICES
   ============================================================ */

@media (max-width: 400px) {

    .scada-header {
        display: block;
    }

    .live-indicator {
        margin-top: 12px;
    }

    .scada-summary {
        grid-template-columns:
            1fr 1fr;
    }

    .summary-card {
        display: block;

        text-align: center;
    }

    .summary-icon {
        margin:
            0 auto 7px;
    }

    .production-counter-panel {
        grid-template-columns:
            1fr;
    }

    .production-counter:not(:last-child)::after,
    .production-counter_tap:not(:last-child)::after {
        left: 15px;
        right: 15px;

        top: auto;
        bottom: 0;

        width: auto;
        height: 1px;

        background:
            linear-gradient(
                90deg,
                transparent,
                rgba(36, 90, 123, 0.65),
                transparent
            );
    }

    .production-counter,
    .production-counter_tap,
    .production-counter-tot {
        min-height: 82px;
    }

    .machine-top {
        padding: 12px;
    }
}


/* ============================================================
   PRINT
   ============================================================ */

@media print {

    body {
        background: #ffffff;
        color: #000000;
    }

    .scada-page {
        background: #ffffff;
    }

    .scada-toolbar,
    .refresh-btn {
        display: none !important;
    }

    .machine-card,
    .summary-card {
        box-shadow: none;

        break-inside: avoid;
    }
}
/* ============================================================
   OPERATOR PANEL
   ============================================================ */

.operator-panel {
    display: flex;
    align-items: center;

    min-height: 52px;

    margin: 10px 12px 0;
    padding: 8px 10px;

    background:
        linear-gradient(
            145deg,
            rgba(22, 131, 200, 0.10),
            rgba(7, 21, 33, 0.65)
        );

    border: 1px solid rgba(36, 90, 123, 0.55);

    border-radius: 8px;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.025);

    transition:
        border-color 0.2s ease,
        background 0.2s ease;
}

.operator-panel:hover {
    border-color: rgba(41, 169, 255, 0.45);

    background:
        linear-gradient(
            145deg,
            rgba(22, 131, 200, 0.14),
            rgba(7, 21, 33, 0.75)
        );
}


/* Operator icon */

.operator-icon {
    width: 34px;
    height: 34px;

    flex: 0 0 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-right: 10px;

    color: #29a9ff;

    background:
        rgba(41, 169, 255, 0.10);

    border:
        1px solid rgba(41, 169, 255, 0.22);

    border-radius: 7px;

    font-size: 15px;
}


/* Operator information */

.operator-info {
    min-width: 0;
    flex: 1;
}

.operator-label {
    margin-bottom: 2px;

    color: #66879d;

    font-size: 8px;
    font-weight: 700;

    letter-spacing: 1px;

    text-transform: uppercase;
}

.operator-name {
    color: #e8f4fb;

    font-size: 13px;
    font-weight: 600;

    line-height: 1.2;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* Active status */

.operator-status {
    display: inline-flex;
    align-items: center;

    gap: 5px;

    margin-left: 8px;

    padding: 4px 7px;

    color: #20d98a;

    background:
        rgba(32, 217, 138, 0.08);

    border:
        1px solid rgba(32, 217, 138, 0.18);

    border-radius: 12px;

    font-size: 7px;
    font-weight: 700;

    letter-spacing: 0.6px;
}

.operator-status-dot {
    width: 5px;
    height: 5px;

    border-radius: 50%;

    background: #20d98a;

    box-shadow:
        0 0 7px rgba(32, 217, 138, 0.8);
}


/* Mobile */

@media (max-width: 600px) {

    .operator-panel {
        margin: 8px 10px 0;
    }

    .operator-name {
        font-size: 12px;
    }

    .operator-status {
        padding: 3px 6px;
    }
}
.shift-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    background: #0b1e2e;
    border: 1px solid #1b4662;
    border-radius: 8px;
}

.shift-icon {
    font-size: 24px;
    color: #29a9ff;
}

.shift-label {
    font-size: 11px;
    color: #66879d;
    font-weight: 600;
}

.shift-name {
    color: #e8f4fb;
    font-size: 17px;
    font-weight: 700;
}

.shift-time {
    color: #9bb5c7;
    font-size: 12px;
}
.shift-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    background: #0b1e2e;
    border: 1px solid #1b4662;
    border-radius: 8px;
}

.shift-icon {
    font-size: 24px;
    color: #29a9ff;
}

.shift-label {
    font-size: 11px;
    color: #66879d;
    font-weight: 600;
}

.shift-name {
    color: #e8f4fb;
    font-size: 17px;
    font-weight: 700;
}

.shift-time {
    color: #9bb5c7;
    font-size: 12px;
}

.current-product-icon {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #20d98a;
    background: rgba(32, 217, 138, 0.10);
    border-radius: 7px;
    font-size: 17px;
}

/*----*/

.current-product-panel {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 6px 12px 0;
    padding: 8px 10px;
    min-width: 0;
}

.current-product-icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
}

.current-product-info {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.current-product-label {
    font-size: 8px;
    white-space: nowrap;
}

.current-product-name {
    color: #e8f4fb;
    font-size: 12px;
    font-weight: 700;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.production-live-badge {
    flex-shrink: 0;
    white-space: nowrap;
}
/*----*/


@media (max-width: 600px) {
    .current-product-panel {
        flex-wrap: wrap;
    }
}

/* Light industrial monitoring dashboard */
:root {
    --scada-bg: #f4f7fb;
    --scada-bg-2: #eef3f8;
    --scada-panel: #fff;
    --scada-panel-2: #f8fafc;
    --scada-panel-3: #f1f5f9;
    --scada-border: #e2e8f0;
    --scada-border-light: #cbd5e1;
    --text-primary: #172b4d;
    --text-secondary: #475569;
    --text-muted: #64748b;
    --blue: #1677e8;
    --blue-light: #1677e8;
    --blue-dark: #125fbd;
    --blue-soft: #eff6ff;
    --running: #16a34a;
    --running-soft: #ecfdf3;
    --idle: #d97706;
    --idle-soft: #fff7e6;
    --stopped: #dc2626;
    --stopped-soft: #fff1f2;
    --breakdown: #ea580c;
    --breakdown-soft: #fff7ed;
    --shadow-sm: 0 2px 8px rgba(23, 43, 77, .045);
    --shadow-md: 0 5px 16px rgba(23, 43, 77, .07);
    --shadow-lg: 0 9px 24px rgba(23, 43, 77, .1);
}

html,
body {
    min-height: 100%;
    height: auto;
    background: var(--scada-bg);
}

body {
    color: var(--text-primary);
    background: var(--scada-bg);
}

.scada-page {
    min-height: 100vh;
    padding: 22px;
    background: var(--scada-bg);
}

.scada-page > .scada-page {
    min-height: 0;
    padding: 0;
    background: transparent;
}

.scada-header {
    min-height: 86px;
    padding: 18px 22px;
    background: linear-gradient(120deg, #fff, #f8fbff);
    border-color: var(--scada-border);
    box-shadow: var(--shadow-sm);
}

.scada-header::before {
    background: linear-gradient(90deg, transparent, var(--blue), transparent);
}

.scada-header::after {
    background: rgba(22, 119, 232, .04);
}

.scada-title h3 {
    color: var(--text-primary);
    font-size: 22px;
}

.scada-title h3 i {
    color: var(--blue);
    background: #eff6ff;
    border-color: #dbeafe;
    box-shadow: none;
}

.scada-title p {
    color: var(--text-muted);
}

.live-indicator {
    color: #15803d;
    background: #f0fdf4;
    border-color: #bbf7d0;
    box-shadow: none;
}

.live-dot {
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.scada-summary {
    gap: 14px;
}

.summary-card {
    min-height: 90px;
    background: #fff;
    border-color: var(--scada-border);
    box-shadow: var(--shadow-sm);
}

.summary-card:hover {
    border-color: #cbdced;
    box-shadow: var(--shadow-md);
}

.summary-icon {
    background: #eff6ff;
    border-color: #dbeafe;
    color: var(--blue);
}

.summary-card h2 {
    color: var(--text-primary);
}

.summary-card span {
    color: var(--text-muted);
}

.summary-running .summary-icon { color: #15803d; background: #ecfdf3; border-color: #bbf7d0; }
.summary-idle .summary-icon { color: #b45309; background: #fff7e6; border-color: #fde68a; }
.summary-stopped .summary-icon { color: #b91c1c; background: #fff1f2; border-color: #fecdd3; }
.summary-breakdown .summary-icon { color: #c2410c; background: #fff7ed; border-color: #fed7aa; }
.summary-total .summary-icon { color: var(--blue); background: #eff6ff; border-color: #dbeafe; }

.scada-toolbar {
    padding: 12px;
    background: #fff;
    border-color: var(--scada-border);
    box-shadow: var(--shadow-sm);
}

.scada-search i {
    color: #8492a6;
}

.scada-search input,
.scada-toolbar select {
    color: var(--text-primary);
    background: #fff;
    border-color: #cbd5e1;
}

.scada-search input::placeholder {
    color: #8795a8;
}

.scada-search input:focus,
.scada-toolbar select:focus {
    color: var(--text-primary);
    background: #fff;
    border-color: #72aef0;
    box-shadow: 0 0 0 3px rgba(22, 119, 232, .12);
}

.refresh-btn {
    color: #fff;
    background: var(--blue);
    border-color: var(--blue);
}

.refresh-btn:hover {
    color: #fff;
    background: #125fbd;
    border-color: #125fbd;
}

.refresh-btn i {
    color: #fff;
}

.machine-card {
    background: #fff;
    border-color: var(--scada-border);
    box-shadow: var(--shadow-md);
}

.machine-card:hover {
    border-color: #b8d4f1;
    box-shadow: var(--shadow-lg);
}

.machine-top {
    border-bottom-color: var(--scada-border);
}

.machine-name,
.current-product-name,
.operator-name,
.shift-name {
    color: var(--text-primary);
}

.machine-image {
    background: #f6f8fb;
    border-color: var(--scada-border);
}

.machine-code {
    color: var(--blue);
}

.machine-type,
.shift-time,
.operator-label,
.current-product-label,
.metric-label,
.production-label,
.production-unit {
    color: var(--text-muted);
}

.machine-status.status-running,
.machine-state.running {
    color: #15803d;
    background: #ecfdf3;
    border-color: #bbf7d0;
}

.machine-status.status-idle,
.machine-state.idle {
    color: #a16207;
    background: #fff7e6;
    border-color: #fde68a;
}

.machine-status.status-stopped,
.machine-state.stopped {
    color: #b91c1c;
    background: #fff1f2;
    border-color: #fecdd3;
}

.machine-status.status-breakdown,
.machine-state.breakdown {
    color: #c2410c;
    background: #fff7ed;
    border-color: #fed7aa;
}

.shift-info,
.operator-panel,
.current-product-panel {
    background: #f8fafc;
    border-color: var(--scada-border);
    box-shadow: none;
}

.shift-info {
    flex-wrap: wrap;
}

.shift-icon,
.operator-icon {
    color: var(--blue);
}

.operator-panel:hover {
    background: #f1f7fd;
    border-color: #c6dcf4;
}

.operator-status {
    color: #15803d;
    background: #ecfdf3;
    border-color: #bbf7d0;
}

.current-product-icon {
    color: #15803d;
    background: #ecfdf3;
}

.production-live-badge {
    color: #15803d;
    background: #ecfdf3;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    padding: 5px 8px;
    font-size: 9px;
    font-weight: 700;
}

.production-live-badge .live-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    margin-right: 4px;
}

.production-counter-panel {
    background: #f8fafc;
    border-color: var(--scada-border);
    box-shadow: none;
}

.production-counter:not(:last-child)::after,
.production-counter_tap:not(:last-child)::after {
    background: var(--scada-border);
}

.production-counter:hover,
.production-counter_tap:hover,
.production-counter-tot:hover {
    background: #f1f7fd;
}

.production-value,
.production-value-tap,
.production-value-tot,
.production-counter.total-counter .production-value,
.production-counter-tot .production-value-tot {
    color: #125fbd;
    text-shadow: none;
}

.production-value.counter-active,
.production-value-tap.counter-active,
.production-value-tot.counter-active {
    color: #1677e8;
    text-shadow: none;
}

.production-counter.total-counter,
.production-counter-tot {
    background: #eff6ff;
}

.machine-metrics {
    padding-bottom: 14px;
}

.metric {
    background: #f8fafc;
    border-color: var(--scada-border);
}

.metric:first-child {
    border-color: #bfdbfe;
}

.metric-value {
    color: var(--text-secondary);
}

.machine-action {
    color: var(--blue);
    background: #f8fafc;
    border-color: var(--scada-border);
}

.machine-action:hover {
    color: #125fbd;
    background: #eff6ff;
    border-color: #bfdbfe;
}

.machine-action i {
    color: var(--blue);
}

.scada-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 16px 4px 4px;
    color: var(--text-muted);
    border-top-color: var(--scada-border);
    text-align: left;
}

.legend {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
}

.legend-item strong {
    color: var(--text-primary);
}

.legend-dot {
    width: 9px;
    height: 9px;
    flex: 0 0 9px;
    border-radius: 50%;
}

.legend-running { background: var(--running); }
.legend-idle { background: var(--idle); }
.legend-stopped { background: var(--stopped); }

.total-production {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    color: var(--text-muted);
    background: #fff;
    border: 1px solid var(--scada-border);
    border-radius: 9px;
    box-shadow: var(--shadow-sm);
}

.total-production > i {
    color: var(--blue);
    font-size: 20px;
}

.total-production small,
.total-production strong {
    display: block;
}

.total-production strong {
    color: var(--text-primary);
    font-size: 17px;
}

#operatorHistoryModal .modal-content {
    color: var(--text-primary);
    background: #fff;
    border: 1px solid var(--scada-border);
    border-radius: 12px;
}

#operatorHistoryModal .modal-header,
#operatorHistoryModal .modal-footer {
    border-color: var(--scada-border);
}

#operatorHistoryModal .modal-title,
#operatorHistoryModal .modal-body strong,
#operatorHistoryModal .modal-body span {
    color: var(--text-primary) !important;
}

::-webkit-scrollbar-track {
    background: #edf2f7;
}

::-webkit-scrollbar-thumb {
    background: #c3cfdd;
}

::-webkit-scrollbar-thumb:hover {
    background: #9aabbe;
}

::selection {
    color: #102a4c;
    background: rgba(22, 119, 232, .18);
}

@media (max-width: 700px) {
    .scada-page {
        padding: 12px;
    }

    .scada-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .total-production {
        align-self: flex-start;
    }
}
/* ============================================================
   GROW EVER LIGHT SCADA OVERRIDE
   Keeps the live SCADA structure and JavaScript hooks intact while
   presenting the monitor as a clear, enterprise control room.
   ============================================================ */
:root {
    --scada-app-bg: #f4f7fb;
    --scada-surface: #ffffff;
    --scada-ink: #172b4d;
    --scada-muted: #64748b;
    --scada-border: #e2eaf3;
    --scada-blue: #1677e8;
    --scada-green: #16a34a;
    --scada-amber: #d97706;
    --scada-red: #dc2626;
    --scada-orange: #ea580c;
}

body,
.scada-page {
    background: var(--scada-app-bg) !important;
    color: var(--scada-ink) !important;
    font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
}
.scada-page { padding: 24px; }

.scada-header {
    min-height: 96px;
    padding: 20px 24px;
    background: linear-gradient(120deg, #102a4c, #174b7a) !important;
    border: 0;
    border-radius: 14px;
    box-shadow: 0 10px 26px rgba(16, 42, 76, .18);
}
.scada-header::before { height: 3px; background: linear-gradient(90deg, #55b7ff, rgba(85,183,255,0)); }
.scada-header::after { background: rgba(255,255,255,.08); }
.scada-title h3 { color: #fff; font-size: 1.35rem; letter-spacing: -.01em; }
.scada-title h3 i { color: #b9e0ff; background: rgba(255,255,255,.11); border-color: rgba(255,255,255,.18); box-shadow: none; }
.scada-title p { color: #c6d8eb; font-size: .76rem; }
.live-indicator { color: #167c43; background: #e7f8ee; border-color: #bde7cb; box-shadow: none; }
.live-dot { background: currentColor; box-shadow: 0 0 0 3px rgba(22,163,74,.12); }

.scada-summary { gap: 14px; }
.summary-card {
    min-height: 88px;
    background: var(--scada-surface) !important;
    border: 1px solid var(--scada-border);
    border-radius: 11px;
    box-shadow: 0 4px 14px rgba(23,43,77,.045);
}
.summary-card:hover { border-color: #b7d5f5; box-shadow: 0 8px 18px rgba(23,43,77,.08); }
.summary-card h2 { color: var(--scada-ink); font-family: inherit; font-size: 1.7rem; letter-spacing: -.04em; }
.summary-card span { color: var(--scada-muted); font-size: .66rem; letter-spacing: .055em; }
.summary-icon { background: #f1f6fd; border-color: #dbeafe; color: var(--scada-blue); }
.summary-running::before { background: var(--scada-green); }.summary-running .summary-icon { background:#eaf8ef; border-color:#c9edd5; color:var(--scada-green); }
.summary-idle::before { background: #f59e0b; }.summary-idle .summary-icon { background:#fff6df; border-color:#fae4a7; color:var(--scada-amber); }
.summary-stopped::before { background: var(--scada-red); }.summary-stopped .summary-icon { background:#fff0f0; border-color:#fbd1d1; color:var(--scada-red); }
.summary-breakdown::before { background: var(--scada-orange); }.summary-breakdown .summary-icon { background:#fff1e8; border-color:#fed6bb; color:var(--scada-orange); }

.scada-toolbar {
    padding: 12px;
    background: var(--scada-surface) !important;
    border-color: var(--scada-border);
    border-radius: 11px;
    box-shadow: 0 3px 12px rgba(23,43,77,.04);
}
.scada-search input,
.scada-toolbar select {
    color: var(--scada-ink); background: #fff; border-color: #cfd9e6;
}
.scada-search input::placeholder { color: #94a3b8; }
.scada-search i { color: #7d8fa6; }
.scada-search input:focus,
.scada-toolbar select:focus { background: #fff; border-color: #70afea; box-shadow: 0 0 0 3px rgba(22,119,232,.12); }
.auto-refresh { color: var(--scada-muted); background:#f6f9fc; border-color:var(--scada-border); }
.refresh-btn { color:#fff; background:var(--scada-blue); border-color:var(--scada-blue); box-shadow:0 3px 8px rgba(22,119,232,.18); }
.refresh-btn:hover { background:#0f67cc; border-color:#0f67cc; }
.refresh-btn i { color:#fff; }

.machine-grid { gap: 16px; }
.machine-card {
    background: var(--scada-surface) !important;
    border-color: var(--scada-border);
    border-radius: 13px;
    box-shadow: 0 4px 15px rgba(23,43,77,.055);
}
.machine-card:hover { border-color:#a9cff4; box-shadow:0 10px 24px rgba(23,43,77,.10); }
.machine-card::before { background: var(--scada-blue); }
.machine-card.running::before { background:var(--scada-green); }.machine-card.idle::before { background:#f59e0b; }.machine-card.stopped::before { background:var(--scada-red); }.machine-card.breakdown::before { background:var(--scada-orange); }
.machine-top { border-bottom-color:var(--scada-border); }
.machine-name { display:flex; align-items:center; gap:11px; color:var(--scada-ink); }
.machine-image { flex:0 0 58px; width:58px; height:58px; background:#f3f7fc; border-color:#dbe6f1; }
.machine-code { color:var(--scada-blue); font-family:inherit; font-size:.78rem; font-weight:750; }
.machine-type { color:var(--scada-muted); font-size:.68rem; }
.machine-status { border-radius:999px; font-size:.63rem; letter-spacing:.04em; }
.status-running { color:#15803d; background:#eaf8ef; border-color:#c8ebd3; }.status-idle { color:#a16207; background:#fff6df; border-color:#fae4a7; }.status-stopped { color:#c2413a; background:#fff0f0; border-color:#fbd1d1; }.status-breakdown { color:#c2410c; background:#fff1e8; border-color:#fed6bb; }

.shift-info,
.current-product-panel,
.operator-panel { background:#f8fafc !important; border-color:var(--scada-border) !important; }
.shift-label,.current-product-label,.operator-label { color:var(--scada-muted) !important; }
.shift-name,.current-product-name,.operator-name { color:var(--scada-ink) !important; }
.shift-time,#shiftElapsed { color:var(--scada-blue) !important; }
.production-live-badge { color:#15803d !important; background:#eaf8ef !important; border-color:#c8ebd3 !important; }
.production-counter-panel { background:#f8fafc; border-color:var(--scada-border); box-shadow:none; }
.production-counter,.production-counter_tap,.production-counter-tot { background:transparent !important; }
.production-counter:not(:last-child)::after,.production-counter_tap:not(:last-child)::after { background:#dfe8f1; }
.production-label,.production-unit,.metric-label { color:var(--scada-muted); }
.production-value,.production-value-tap,.production-counter.total-counter .production-value,.production-counter-tot .production-value-tot { color:var(--scada-ink); font-family:inherit; text-shadow:none; }
.production-counter.total-counter,.production-counter-tot { background:#eff6ff !important; }
.machine-metrics { gap:9px; }
.metric { background:#f8fafc; border-color:var(--scada-border); }
.metric-value { color:var(--scada-ink); font-family:inherit; }
.machine-state { margin-bottom:14px; }
.machine-state.running { color:#15803d; background:#eaf8ef; border-color:#c8ebd3; }.machine-state.idle { color:#a16207; background:#fff6df; border-color:#fae4a7; }.machine-state.stopped { color:#c2413a; background:#fff0f0; border-color:#fbd1d1; }.machine-state.breakdown { color:#c2410c; background:#fff1e8; border-color:#fed6bb; }
.machine-state .machine-action { width:auto; min-height:26px; margin:0 0 0 4px; padding:0 8px; color:inherit; background:transparent; border-color:currentColor; }
.machine-action i { color:currentColor; }
.scada-footer { color:var(--scada-muted); border-top-color:var(--scada-border); }
.total-production { background:#fff !important; border-color:var(--scada-border) !important; box-shadow:0 3px 12px rgba(23,43,77,.04); }

#operatorHistoryModal .modal-content { background:#fff; border-color:var(--scada-border); box-shadow:0 16px 42px rgba(23,43,77,.18); }
#operatorHistoryModal .modal-header,#operatorHistoryModal .modal-footer { background:#fff; border-color:var(--scada-border); }
#operatorHistoryModal .modal-title,#operatorHistoryModal .modal-body strong,#operatorHistoryModal .modal-body span { color:var(--scada-ink) !important; }
#operatorHistoryModal .table { color:#172b4d; font-size:12px; }
#operatorHistoryModal .table th {
    color:#243b5a;
    background:#e8f1fc;
    border-color:#dbe6f2;
    font-size:11px;
    font-weight:700;
    vertical-align:middle;
}
#operatorHistoryModal .table td {
    color:#344965;
    border-color:#e8eef5;
    vertical-align:middle;
}
#operatorHistoryModal .table > :not(caption) > * > * { padding:8px 10px; }
#operatorHistoryModal .table-hover tbody tr:hover > * { background:#f0f7ff; }

@media (max-width: 900px) { .scada-page { padding:16px; } }
@media (max-width: 600px) {
    .scada-page { padding:12px; }
    .scada-header { padding:16px; }
    .scada-title p { margin-left:42px; }
    .scada-summary { gap:10px; }
    .machine-grid { gap:12px; }
}
</style>


<div class="scada-page">

    <!-- ================= HEADER ================= -->
    <div class="scada-header">

        <div class="scada-title">
            <h3>
                <i class="bi bi-cpu me-2"></i>
                Machine SCADA
            </h3>

            <p>
                Real-time monitoring and production status of all tapping machines
            </p>
        </div>

        <div class="live-indicator">
            <span class="live-dot"></span>
            Live Monitoring
        </div>

    </div>


    <!-- ================= SUMMARY ================= -->
    <div class="scada-summary">

        <!-- Running -->
        <div class="summary-card summary-running">

            <div class="summary-icon">
                <i class="bi bi-play-fill"></i>
            </div>

            <div>
                <h2 id="runningCount"></h2>
                <span>Running Machines</span>
            </div>

        </div>


        <!-- Idle -->
        <div class="summary-card summary-idle">

            <div class="summary-icon">
                <i class="bi bi-pause-fill"></i>
            </div>

            <div>
                <h2 id="idleCount"></h2>
                <span>Idle Machines</span>
            </div>

        </div>


        <!-- Stopped -->
        <div class="summary-card summary-stopped">

            <div class="summary-icon">
                <i class="bi bi-stop-fill"></i>
            </div>

            <div>
                <h2 id="stoppedCount"></h2>
                <span>Stopped Machines</span>
            </div>

        </div>
        <div class="summary-card summary-breakdown">

		    <div class="summary-icon">
		        <i class="bi bi-exclamation-triangle-fill"></i>
		    </div>

		    <div>
		        <h2 id="breakdownCount">0</h2>
		        <span>Breakdown</span>
		    </div>

		</div>

        <!-- Total -->
        <div class="summary-card summary-total">

            <div class="summary-icon">
                <i class="bi bi-gear-fill"></i>
            </div>

            <div>
                <h2 id="totalMachines"></h2>
                <span>Total Machines</span>
            </div>

        </div>

    </div>


    <!-- ================= TOOLBAR ================= -->
    <div class="scada-toolbar">

        <div class="scada-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="machineSearch"
                placeholder="Search by Machine ID..."
            >

        </div>


        <select id="statusFilter">

		    <option value="all">All Status</option>

		    <option value="running">
		        Running
		    </option>

		    <option value="idle">
		        Idle
		    </option>

		    <option value="stopped">
		        Stopped
		    </option>

		    <option value="breakdown">
		        Breakdown
		    </option>

		</select>


        <select id="lineFilter">

            <option value="all">All Lines</option>
            <option value="line1">Production Line 1</option>
            <option value="line2">Production Line 2</option>
            <option value="line3">Production Line 3</option>

        </select>


        <div class="auto-refresh">

            <span class="live-dot"></span>

            Auto Refresh

            <input
                type="checkbox"
                id="autoRefresh"
                checked
            >

        </div>


        <button
            type="button"
            class="refresh-btn"
            onclick="refreshSCADA()"
            title="Refresh"
        >
            <i class="bi bi-arrow-clockwise"></i>
        </button>

    </div>


    <!-- ================= MACHINE GRID ================= -->
    <div class="machine-grid" id="machineGrid">

	    @foreach($rec as $machine)

	       <div
			    class="machine-card stopped"
			    data-machine="{{ $machine->machine_code }}"
			    data-status="stopped"
			    data-line="line1"
			>

			    <div class="machine-top">

			        <div class="machine-name">

			            <div class="machine-image">
			                <img
			                    src="{{ asset('images/machine.png') }}"
			                    alt="{{ $machine->machine_code }}"
			                    onerror="this.style.display='none'"
			                >
			            </div>

			            <div>

			                <div class="machine-code">
			                    {{ $machine->machine_code }}
			                </div>

			                <div class="machine-type">
			                    {{ $machine->name }}
			                </div>

			            </div>

			        </div>

			        <span class="machine-status status-stopped">

			            <span class="status-dot"></span>

			            STOPPED

			        </span>

			    </div>
                <div class="shift-info">

                    <div class="shift-label">
                        CURRENT SHIFT
                    </div>
                
                    <div id="shiftName" class="shift-name">
                        Loading...
                    </div>
                
                    <div id="shiftTime" class="shift-time">
                        --:-- - --:--
                    </div>
                
                    <div id="shiftElapsed">
                        00:00:00
                    </div>
                
                </div>
              
                <!-- CURRENT PRODUCT -->
                <div class="current-product-panel">
                    <div class="current-product-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                
                    <div class="current-product-info">
                        <div class="current-product-label">
                            CURRENTLY MANUFACTURING
                        </div>
                
                        <div class="current-product-name">
                            {{ $machine->product_name ?? 'No Product Assigned' }}
                        </div>
                    </div>
                
                    <span class="production-live-badge">
                        <span class="live-dot"></span>
                        IN PRODUCTION
                    </span>
                </div>

                <!-- Operator -->
                <div class="operator-panel">

                    <div class="operator-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                
                    <div class="operator-info">
                
                        <div class="operator-label">
                            OPERATOR
                        </div>
                
                        <div class="operator-name">
                            {{ $machine->operator_name ?? 'Not Assigned' }}
                        </div>
                
                    </div>
                
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-info ms-2"
                        onclick="openOperatorHistory(@js($machine->machine_code))"
                        title="Operator Login History"
                    >
                        <i class="bi bi-clock-history"></i>
                    </button>
                
                </div>
			    <!-- PRODUCTION -->

                			   
                
                <!-- ================= PRODUCTION COUNTERS ================= -->
                
                <div class="production-counter-panel">
                
                    <!-- TODAY -->
                    <div class="production-counter">
                
                        <div class="production-label">
                            Total Tapped This Shift
                        </div>
                
                        <div class="production-value">
                            {{ number_format($machine->production ?? 0) }}
                        </div>
                
                        <div class="production-unit">
                            Times
                        </div>
                
                    </div>
                
                
                    <!-- PER TAP -->
                    <div class="production-counter_tap">
                
                        <div class="production-label">
                            Count Per Tap
                        </div>
                
                        <div class="production-value-tap">
                            {{ number_format($machine->cycle_count ?? 0) }}
                        </div>
                
                        <div class="production-unit">
                            Pieces
                        </div>
                
                    </div>
                
                
                    <!-- TOTAL -->
                    <div class="production-counter-tot total-counter">
                        <div class="production-label">
                            Total Production
                        </div>
                    
                        <div class="production-value-tot">
                             {{ number_format(
                                ($machine->production ?? 0) *
                                ($machine->cycle_count ?? 0)
                            ) }}
                        </div>
                    
                        <div class="production-unit">
                            Pieces
                        </div>
                    </div>
                
                </div>
                


			    <!-- METRICS -->

			    <div class="machine-metrics">

			        <div class="metric">

			            <span class="metric-label">
			                Cycle Count
			            </span>

			            <span class="metric-value">
			                {{ number_format($machine->production ?? 0) }}
			            </span>

			        </div>


			        <div class="metric">

			            <span class="metric-label">
			                Last Cycle
			            </span>

			            <span class="metric-value">
			                -
			            </span>

			        </div>

			    </div>


			    <!-- MACHINE STATE -->

			    <div class="machine-state stopped">

			        <i class="bi bi-stop-circle"></i>

			        Machine Stopped

			        <button
			            type="button"
			            class="machine-action"
			        >
			            <i class="bi bi-bar-chart-fill"></i>
			        </button>

			    </div>

			</div>

	    @endforeach
    <div class="modal fade"
         id="operatorHistoryModal"
         tabindex="-1"
         aria-hidden="true">
    
        <div class="modal-dialog modal-lg modal-dialog-centered">
    
            <div class="modal-content">
    
                <div class="modal-header">
    
                    <h5 class="modal-title" style="    color: #0085b7;">
                        <i class="bi bi-clock-history me-2"></i>
    
                        Operator Login History
    
                        <small
                            id="operatorHistoryMachine"
                            class="text-muted ms-2">
                        </small>
                    </h5>
    
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
    
                </div>
    
                <div class="modal-body">
    
                    <div class="mb-3" >
    
                        <strong style="    color: #0085b7;">
                            Operator:
                        </strong>
    
                        <span style="    color: #0085b7;" id="historyOperatorName">
                            -
                        </span>
    
                    </div>
    
                    <div class="table-responsive">
    
                        <table
                            class="table table-bordered table-sm"
                            id="operatorHistoryTable">
    
                            <thead>
    
                                <tr>
                                    <th>#</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
    
                            </thead>
    
                            <tbody id="operatorHistoryBody">
                            </tbody>
    
                        </table>
    
                    </div>
    
                    <div class="text-end">
    
                        <small
                            id="operatorHistoryTotal"
                            class="text-muted">
                            0 session(s)
                        </small>
    
                    </div>
    
                </div>
    
            </div>
    
        </div>
    
    </div>
	</div>


    <!-- ================= FOOTER ================= -->
    <div class="scada-footer">

        <div class="legend">

            <div class="legend-item">
                <span class="legend-dot legend-running"></span>
                <span>
                    <strong>Running</strong><br>
                    Machine is in operation
                </span>
            </div>


            <div class="legend-item">
                <span class="legend-dot legend-idle"></span>
                <span>
                    <strong>Idle</strong><br>
                    Machine is ready / waiting
                </span>
            </div>


            <div class="legend-item">
                <span class="legend-dot legend-stopped"></span>
                <span>
                    <strong>Stopped</strong><br>
                    Machine stopped / fault
                </span>
            </div>

        </div>


        <div class="total-production">

            <i class="bi bi-bar-chart-line-fill"></i>

            <div>

                <small>Total Production Today</small>

                <strong id="totalProduction">
                    13,655
                </strong>

            </div>

        </div>

    </div>

</div>

</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

$(document).ready(function () {

    /* =========================
       MACHINE SEARCH
    ========================= */

    $('#machineSearch').on('keyup', function () {

        filterMachines();

    });


    /* =========================
       STATUS FILTER
    ========================= */

    $('#statusFilter').on('change', function () {

        filterMachines();

    });


    /* =========================
       LINE FILTER
    ========================= */

    $('#lineFilter').on('change', function () {

        filterMachines();

    });


    /* =========================
       FILTER MACHINES
    ========================= */

    function filterMachines() {

        let search = $('#machineSearch')
            .val()
            .toLowerCase();

        let status = $('#statusFilter').val();

        let line = $('#lineFilter').val();


        $('.machine-card').each(function () {

            let machine =
                $(this)
                .data('machine')
                .toString()
                .toLowerCase();

            let machineStatus =
                $(this).data('status');

            let machineLine =
                $(this).data('line');


            let searchMatch =
                machine.includes(search);

            let statusMatch =
                status === 'all' ||
                machineStatus === status;

            let lineMatch =
                line === 'all' ||
                machineLine === line;


            if (
                searchMatch &&
                statusMatch &&
                lineMatch
            ) {

                $(this).show();

            } else {

                $(this).hide();

            }

        });

    }
     loadCurrentShift();

    
    loadCurrentShift();

    setInterval(loadCurrentShift, 60000);

    setInterval(updateShiftElapsed, 1000);

});
let currentShiftStart = null;
let currentShiftEnd = null;

function loadCurrentShift() {

    $.ajax({
        url: '/api/current-shift',
        type: 'GET',
        dataType: 'json',

        success: function(response) {

            if (response.status && response.shift) {

                let shift = response.shift;

                $('#shiftName').text(shift.name);

                $('#shiftTime').text(
                    formatTime(shift.start_time) +
                    ' - ' +
                    formatTime(shift.end_time)
                );

                currentShiftStart = shift.start_time;
                currentShiftEnd   = shift.end_time;

                updateShiftElapsed();
            }
        },

        error: function(xhr) {

            $('#shiftName').text('No Shift');
            $('#shiftTime').text('--:-- - --:--');
            $('#shiftElapsed').text('--:--:--');
        }
    });
}
function formatTime(time) {

    let parts = time.split(':');

    let date = new Date();

    date.setHours(
        parseInt(parts[0]),
        parseInt(parts[1]),
        0
    );

    return date.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

/* =========================
   REFRESH
========================= */

function refreshSCADA() {

    const btn =
        $('.refresh-btn i');

    btn.addClass('fa-spin');

    setTimeout(function () {

        btn.removeClass('fa-spin');

    }, 700);

}


/* =========================
   AUTO REFRESH
========================= */

let autoRefreshTimer = null;


function startAutoRefresh() {

    if (autoRefreshTimer) {

        clearInterval(autoRefreshTimer);

    }


    autoRefreshTimer = setInterval(function () {

        if ($('#autoRefresh').is(':checked')) {

            /*
             * Later replace this section
             * with AJAX API call.
             *
             * Example:
             **/
              $.get(
                  "{{ route('api.machine.scada') }}",
                  function(data) {
                      updateMachines(data);
                 }
             );
            

            console.log('SCADA data refreshed');

        }

    }, 5000);

}
/* =========================================================
   UPDATE SCADA MACHINES
========================================================= */

function updateMachines(response)
{
    if (!response || response.success === false) {
        console.error('Invalid SCADA response');
        return;
    }

    let machines = response.machines || [];

    let running = 0;
	let idle = 0;
	let stopped = 0;
	let breakdown = 0;
	let totalProduction = 0;
    
    /* =================================================
       OPERATOR HISTORY
    ================================================= */
    
    

    machines.forEach(function(machine)
    {
        let machineCode = machine.machine_code;
        

        let card = $('.machine-card[data-machine="' + machineCode + '"]');


        if (card.length === 0) {
            return;
        }


        let status = machine.status || 'stopped';

        let production =
            parseInt(machine.production_today || 0);
        let cycle_count_tap =
            parseInt(machine.cycle_count_tap || 0);
        let tot_cycle_count_tap =
            parseInt(machine.total_product_cycle || 0);
        let operatorName = machine.op_name || 'Not Assigned';

        let cycleCount =
            parseInt(machine.cycle_count || 0);
       // let operatorName = machine.op_name || 'Not Assigned';
        let operatorHistory = machine.operator_history || [];
    
            card.data(
                'operator-history',
                operatorHistory
            );
            
            card.data(
                'operator-name',
                operatorName
            );

        /* =================================================
           SUMMARY COUNT
        ================================================= */

        if (status === 'running') {

    		running++;

		}
		else if (status === 'idle') {

		    idle++;

		}
		else if (status === 'stopped') {

		    stopped++;

		}
		else if (status === 'breakdown') {

		    breakdown++;

		}

        totalProduction += production;


        /* =================================================
           MACHINE CARD STATUS
        ================================================= */

        card
            .removeClass(
			    'running idle stopped breakdown'
			)
			.addClass(status);


        card.attr('data-status', status);


        /* =================================================
           STATUS BADGE
        ================================================= */

        let statusBadge =
            card.find('.machine-status');

        statusBadge
            .removeClass(
			    'status-running status-idle status-stopped status-breakdown'
			)
            .addClass('status-' + status);


        statusBadge.html(
            '<span class="status-dot"></span>' +
            status.toUpperCase()
        );


        /* =================================================
           PRODUCTION
        ================================================= */

        animateCounter(
		    card.find('.production-value'),
		    production,
		    1000
		);
		animateCounter(
		    card.find('.production-value-tap'),
		    cycle_count_tap,
		    1000
		);
		animateCounter(
		    card.find('.production-value-tot'),
		    tot_cycle_count_tap,
		    1000
		);
		
		/* =========================
           OPERATOR NAME
        ========================= */
        
        card.find('.operator-name')
            .text(operatorName);


        /* =================================================
           CYCLE COUNT
        ================================================= */

        animateCounter(
		    card.find('.metric')
		        .eq(0)
		        .find('.metric-value'),
		    cycleCount,
		    800
		);


        /* =================================================
           SECOND METRIC
        ================================================= */

        let secondMetric = card
            .find('.metric')
            .eq(1);

        if (status === 'running') {

            secondMetric.find('.metric-label')
                .text('Cycle Time');

            secondMetric.find('.metric-value')
                .text(
                    machine.cycle_time
                        ? machine.cycle_time + ' sec'
                        : '-'
                );

        }
        else if (status === 'idle') {

            secondMetric.find('.metric-label')
                .text('Last Cycle');

            secondMetric.find('.metric-value')
                .text(
                    machine.last_cycle || '-'
                );

        }
        else {

            secondMetric.find('.metric-label')
                .text('Stopped Since');

            secondMetric.find('.metric-value')
                .text(
                    machine.stopped_since || '-'
                );
        }


        /* =================================================
           MACHINE STATE
        ================================================= */

        let state = card.find('.machine-state');

        state
            .removeClass('running idle stopped')
            .addClass(status);


        if (status === 'running') {

            state.html(`
                <i class="bi bi-arrow-repeat"></i>

                Tapping in Progress...

                <button class="machine-action">
                    <i class="bi bi-bar-chart-fill"></i>
                </button>
            `);

        }
        else if (status === 'idle') {

            state.html(`
                <i class="bi bi-pause-circle"></i>

                Machine Idle

                <button class="machine-action">
                    <i class="bi bi-bar-chart-fill"></i>
                </button>
            `);

        }
        else if (status === 'breakdown') {

		    let severity =
		        machine.breakdown_severity
		            ? machine.breakdown_severity.toUpperCase()
		            : 'FAULT';

		    state.html(`
		        <i class="bi bi-exclamation-triangle-fill"></i>

		        MACHINE BREAKDOWN

		        <span style="
		            margin-left:4px;
		            opacity:.75;
		            font-size:9px;
		        ">
		            [${severity}]
		        </span>

		        <button
		            class="machine-action"
		            title="Breakdown Details"
		        >
		            <i class="bi bi-wrench-adjustable"></i>
		        </button>
		    `);
		}
        else {

            state.html(`
                <i class="bi bi-stop-circle"></i>

                Machine Stopped

                <button class="machine-action">
                    <i class="bi bi-bar-chart-fill"></i>
                </button>
            `);
        }

    });


    /* =====================================================
       UPDATE SUMMARY
    ===================================================== */

    $('#runningCount').text(running);

	$('#idleCount').text(idle);

	$('#stoppedCount').text(stopped);

	$('#breakdownCount').text(breakdown);

    $('#totalMachines').text(
        machines.length
    );


    /* =====================================================
       UPDATE TOTAL PRODUCTION
    ===================================================== */

    $('#totalProduction').text(
        Number(totalProduction).toLocaleString()
    );


    /* =====================================================
       LAST UPDATED
    ===================================================== */

    console.log(
        'SCADA updated:',
        new Date().toLocaleTimeString()
    );
}
/* ============================================================
   OPEN OPERATOR HISTORY
============================================================ */

function openOperatorHistory(machineCode)
{
    const card = $('.machine-card[data-machine="' + machineCode + '"]');

    if (card.length === 0) {

        console.error(
            'Machine card not found:',
            machineCode
        );

        return;
    }

    // Get operator history from API
    const history =
        card.data('operator-history') || [];

    // Get current operator name
    let operatorName =
        card.data('operator-name') ||
        'Not Assigned';

    // Remove "(Logged In at ...)" from operator name
    operatorName = operatorName.replace(
        /\s*\(Logged In at.*\)$/i,
        ''
    );

    // Modal header
    $('#operatorHistoryMachine')
        .text('Machine: ' + machineCode);

    $('#historyOperatorName')
        .text(operatorName);

    // Table body
    const tbody =
        $('#operatorHistoryBody');

    tbody.empty();

    // No history
    if (!history || history.length === 0) {

        tbody.html(`
            <tr>
                <td
                    colspan="5"
                    class="text-center text-muted py-4"
                >
                    <i class="bi bi-clock-history"></i>
                    No operator history found for today.
                </td>
            </tr>
        `);

        $('#operatorHistoryTotal')
            .text('0 session(s)');

    } else {

        // History rows
        history.forEach(function(item, index) {

            const login =
                formatDateTime(item.login_time);

            const logout =
                item.logout_time
                    ? formatDateTime(item.logout_time)
                    : '-';

            const duration =
                calculateDuration(
                    item.login_time,
                    item.logout_time
                );

            const status =
                String(item.status || '')
                    .toLowerCase();

            const statusText =
                status === 'active'
                    ? 'ACTIVE'
                    : 'LOGGED OUT';

            const statusClass =
                status === 'active'
                    ? 'text-success'
                    : 'text-secondary';

            tbody.append(`
                <tr>

                    <td>
                        ${index + 1}
                    </td>

                    <td>
                        ${escapeHtml(login)}
                    </td>

                    <td>
                        ${escapeHtml(logout)}
                    </td>

                    <td>
                        ${escapeHtml(duration)}
                    </td>

                    <td>
                        <span class="${statusClass}">
                            <span class="me-1">●</span>
                            ${statusText}
                        </span>
                    </td>

                </tr>
            `);
        });

        $('#operatorHistoryTotal')
            .text(
                history.length +
                ' session(s)'
            );
    }

    // Open Bootstrap 5 modal
    const modalElement =
        document.getElementById(
            'operatorHistoryModal'
        );

    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );

    modal.show();
}

function formatDateTime(value) {
    if (!value) return '-';

    const date = new Date(String(value).replace(' ', 'T'));

    if (isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString('en-IN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
}


function calculateDuration(loginTime, logoutTime) {
    if (!loginTime) return '-';

    const start = new Date(String(loginTime).replace(' ', 'T'));

    const end = logoutTime
        ? new Date(String(logoutTime).replace(' ', 'T'))
        : new Date();

    if (isNaN(start.getTime()) || isNaN(end.getTime())) {
        return '-';
    }

    let seconds = Math.floor((end - start) / 1000);

    if (seconds < 0) return '-';

    const hours = Math.floor(seconds / 3600);

    seconds %= 3600;

    const minutes = Math.floor(seconds / 60);

    seconds %= 60;

    return String(hours).padStart(2, '0') + ':' +
           String(minutes).padStart(2, '0') + ':' +
           String(seconds).padStart(2, '0');
}


function escapeHtml(value) {
    return $('<div>').text(value ?? '').html();
}


function animateCounter(element, target, duration = 1000)
{
    target = parseInt(target) || 0;

    let current = parseInt(
        $(element).text().replace(/,/g, '')
    ) || 0;

    if (current === target) {
        return;
    }

    if (target < current) {
        $(element).text(target.toLocaleString());
        return;
    }

    $(element).addClass('counter-active');

    let difference = target - current;

    let stepTime = Math.max(
        20,
        Math.floor(duration / difference)
    );

    let timer = setInterval(function () {

        current++;

        $(element).text(
            current.toLocaleString()
        );

        if (current >= target) {

            clearInterval(timer);

            $(element)
                .text(target.toLocaleString())
                .removeClass('counter-active');
        }

    }, stepTime);
}
function updateShiftElapsed() {

    if (!currentShiftStart || !currentShiftEnd) {
        return;
    }

    let now = new Date();

    let startParts = currentShiftStart.split(':');

    let start = new Date();

    start.setHours(
        parseInt(startParts[0]),
        parseInt(startParts[1]),
        parseInt(startParts[2] || 0),
        0
    );

    /*
     * Night shift
     * Example: 20:00 -> 08:00
     *
     * If current time is after midnight,
     * shift started yesterday.
     */
    let endParts = currentShiftEnd.split(':');

    let end = new Date();

    end.setHours(
        parseInt(endParts[0]),
        parseInt(endParts[1]),
        parseInt(endParts[2] || 0),
        0
    );

    if (end <= start) {

        // Night shift
        if (now < end) {
            start.setDate(start.getDate() - 1);
        } else {
            end.setDate(end.getDate() + 1);
        }
    }

    let elapsed = now - start;

    if (elapsed < 0) {
        elapsed = 0;
    }

    let totalSeconds = Math.floor(elapsed / 1000);

    let hours = Math.floor(totalSeconds / 3600);

    let minutes = Math.floor(
        (totalSeconds % 3600) / 60
    );

    let seconds = totalSeconds % 60;

    $('#shiftElapsed').text(
        String(hours).padStart(2, '0') + ':' +
        String(minutes).padStart(2, '0') + ':' +
        String(seconds).padStart(2, '0')
    );
}


startAutoRefresh();

</script>

</body>
</html>
