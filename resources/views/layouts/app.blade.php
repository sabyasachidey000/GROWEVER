<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MTS - Manufacturing Tracking System')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard-dark.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mes-enterprise.css') }}" rel="stylesheet">

    <style>
    :root {
        --sidebar-width: 248px;

        /* ===== MTS Industrial Theme ===== */
        --primary-color: #0B1728;
        --secondary-color: #10243A;
        --accent-color: #1683FF;

        --page-bg: #07111F;
        --card-bg: #0F2138;
        --card-header: #163451;
        --border-color: #1D3553;

        --text-primary: #E8F0F8;
        --text-secondary: #8FA6BC;

        --success-color: #22C55E;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --maintenance-color: #F97316;
        --offline-color: #64748B;
    }

    /* =========================
       BODY
    ========================= */

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--page-bg);
        color: var(--text-primary);
    }

    /* =========================
       SIDEBAR
    ========================= */

    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;

        background: linear-gradient(
            180deg,
            #081525 0%,
            #0B1B2D 55%,
            #0E243B 100%
        );

        color: #fff;
        overflow-y: auto;
        z-index: 1000;
        transition: all 0.3s;

        border-right: 1px solid rgba(255,255,255,0.06);
        box-shadow: 5px 0 25px rgba(0,0,0,0.25);
    }

    /* =========================
       SIDEBAR BRAND
    ========================= */

    .sidebar-brand {
        padding: 1rem 1rem 1.2rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        text-align: center;
    }

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

    .sidebar-brand h4,
    .sidebar-brand h6 {
        margin: 0;
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .sidebar-brand small {
        display: block;
        margin-top: 3px;

        font-size: 0.62rem;
        letter-spacing: 1.2px;

        color: #8FA6BC;
        opacity: 1;
    }

    /* =========================
       SIDEBAR MENU
    ========================= */

    .sidebar .nav-link {
        color: rgba(232,240,248,0.78);

        padding: 0.72rem 1.25rem;

        border-left: 3px solid transparent;

        transition:
            background 0.2s ease,
            color 0.2s ease,
            border 0.2s ease;

        font-size: 0.9rem;
    }

    .sidebar .nav-link:hover {
        color: #fff;

        background: rgba(22,131,255,0.10);

        border-left-color: rgba(22,131,255,0.5);
    }

    .sidebar .nav-link.active {
        color: #fff;

        background: linear-gradient(
            90deg,
            rgba(22,131,255,0.25),
            rgba(22,131,255,0.08)
        );

        border-left-color: var(--accent-color);

        box-shadow: inset 0 0 20px rgba(22,131,255,0.04);
    }

    .sidebar .nav-link i {
        width: 24px;
        margin-right: 8px;

        color: #8FA6BC;

        transition: color 0.2s ease;
    }

    .sidebar .nav-link:hover i,
    .sidebar .nav-link.active i {
        color: #1683FF;
    }

    /* =========================
       SECTION TITLE
    ========================= */

    .sidebar .nav-section {
        padding: 0.7rem 1.25rem 0.35rem;

        font-size: 0.68rem;
        text-transform: uppercase;

        letter-spacing: 1.4px;

        color: #6F8AA3;

        opacity: 1;

        margin-top: 0.8rem;
    }

    /* =========================
       MAIN CONTENT
    ========================= */

    .main-content {
        margin-left: var(--sidebar-width);
        min-height: 100vh;

        background: var(--page-bg);
    }

    /* =========================
       TOPBAR
    ========================= */

    .topbar {
        background: #091827;

        border-bottom: 1px solid var(--border-color);

        box-shadow: 0 2px 15px rgba(0,0,0,0.20);

        padding: 0.75rem 1.5rem;

        color: var(--text-primary);
    }

    .topbar h5 {
        color: #fff;
        font-weight: 600;
    }

    /* =========================
       CONTENT
    ========================= */

    .content-area {
        padding: 1.5rem;
    }

    /* =========================
       CARDS
    ========================= */

    .card {
        border: 1px solid var(--border-color);

        background-color: var(--card-bg);

        box-shadow:
            0 4px 15px rgba(0,0,0,0.18);

        border-radius: 10px;

        color: var(--text-primary);
    }

    /* =========================
       STAT CARDS
    ========================= */

    .stat-card {
        padding: 1.5rem;

        border-radius: 10px;

        color: #fff;

        border: 1px solid rgba(255,255,255,0.08);

        box-shadow:
            0 5px 18px rgba(0,0,0,0.20);
    }

    .stat-card .stat-icon {
        font-size: 2.5rem;
        opacity: 0.75;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-card .stat-label {
        font-size: -0.125rem;
        opacity: 0.85;
    }

    /* =========================
       MACHINE STATUS
    ========================= */

    .status-running {
        background-color: var(--success-color);
        color: #fff;
    }

    .status-idle {
        background-color: var(--warning-color);
        color: #111827;
    }

    .status-breakdown {
        background-color: var(--danger-color);
        color: #fff;
    }

    .status-maintenance {
        background-color: var(--maintenance-color);
        color: #fff;
    }

    .status-offline {
        background-color: var(--offline-color);
        color: #fff;
    }

    /* =========================
       TABLE
    ========================= */

    .table {
        color: var(--text-primary);
        border-color: var(--border-color);
    }

    .table th {
        font-weight: 600;

        background-color: var(--card-header);

        color: #DCE8F3;

        border-color: var(--border-color);
    }

    .table td {
        background-color: var(--card-bg);
        color: var(--text-secondary);

        border-color: var(--border-color);
    }

    .table-hover tbody tr:hover td {
        background-color: #142B45;
        color: #fff;
    }

    /* =========================
       BADGE
    ========================= */

    .badge-status {
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-size: 0.75rem;
    }

    /* =========================
       FORM CONTROLS
    ========================= */

    .form-control,
    .form-select {
        background-color: #0B1B2D;
        border-color: var(--border-color);

        color: #E8F0F8;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #0B1B2D;

        color: #fff;

        border-color: var(--accent-color);

        box-shadow:
            0 0 0 0.2rem rgba(22,131,255,0.15);
    }

    .form-control::placeholder {
        color: #60758A;
    }

    /* =========================
       BUTTON
    ========================= */

    .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }

    .btn-primary:hover {
        background-color: #0875E1;
        border-color: #0875E1;
    }

    /* =========================
       SUB MENU
    ========================= */

    .sidebar-submenu {
        padding-left: 1rem;
    }

    .sidebar-submenu .nav-link {
        padding: 0.5rem 1.5rem;
        font-size: 0.88rem;
    }

    /* =========================
       SCROLLBAR
    ========================= */

    .sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #081525;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #29445F;
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #3C6286;
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 768px) {

        .sidebar {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar.show {
            margin-left: 0;
        }

        .main-content {
            margin-left: 0;
        }

        .content-area {
            padding: 1rem;
        }
    }
</style>

    @stack('styles')
</head>
<body class="dark-dashboard">
    @php($hideSidebar = request()->routeIs('operator.*'))

    <!-- Sidebar -->
    @unless($hideSidebar)
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand text-center">
    <img src="{{ asset('images/mts.png') }}"
         alt="GROW EVER"
         class="sidebar-logo">
    <!-- <div class="sidebar-brand-title">ELECTROMAF</div> -->
    <small>MANUFACTURING EXECUTION SYSTEM</small>
</div>

        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('dashboard'))
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                @endif
            </li>

            <li class="nav-section">Management</li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('departments'))
                <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                    <i class="bi bi-building"></i> Departments
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('product_types'))
                <a class="nav-link {{ request()->routeIs('product-types.*') ? 'active' : '' }}" href="{{ route('product-types.index') }}">
                    <i class="bi bi-tag"></i> Product Types
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('products'))
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('designations'))
                <a class="nav-link {{ request()->routeIs('designations.*') ? 'active' : '' }}" href="{{ route('designations.index') }}">
                    <i class="bi bi-briefcase"></i> Designations
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('shifts'))
                <a class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}" href="{{ route('shifts.index') }}">
                    <i class="bi bi-clock"></i> Shifts
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('employees'))
                <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                    <i class="bi bi-people"></i> Employees
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('rfid'))
                <a class="nav-link {{ request()->routeIs('rfid.index') ? 'active' : '' }}" href="{{ route('rfid.index') }}">
                    <i class="bi bi-person-badge"></i> RFID Management
                </a>
                @endif
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rfid.swipe') ? 'active' : '' }}" href="{{ route('rfid.swipe') }}">
                    <i class="bi bi-credit-card"></i> RFID Swipe
                </a>
                <a class="nav-link {{ request()->routeIs('rfid.punch-logs') ? 'active' : '' }}" href="{{ route('rfid.punch-logs') }}">
                    <i class="bi bi-clock-history"></i> RFID Punch Logs
                </a>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('face-recognition.*') ? 'active' : '' }}" href="{{ route('face-recognition.attendance') }}">
                    <i class="bi bi-camera-video"></i> Face Recognition
                </a>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('operator.*') ? 'active' : '' }}" href="{{ route('operator.login') }}">
                    <i class="bi bi-person-video3"></i> Operator Login
                </a>
            </li> -->

            <li class="nav-section">Machines</li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('machines'))
                <a class="nav-link {{ request()->routeIs('machines.*') ? 'active' : '' }}" href="{{ route('machines.index') }}">
                    <i class="bi bi-cpu"></i> Machines
                </a>
                @endif
            </li>

            <li class="nav-section">Production</li>
            <!--<li class="nav-item">
                @if(auth()->user()->hasMenuPermission('production'))
                <a class="nav-link {{ request()->routeIs('production.*') ? 'active' : '' }}" href="{{ route('production.index') }}">
                    <i class="bi bi-activity"></i> Production Tracking
                </a>
                @endif
            </li>-->
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('breakdowns'))
                <a class="nav-link {{ request()->routeIs('breakdowns.*') ? 'active' : '' }}" href="{{ route('breakdowns.index') }}">
                    <i class="bi bi-tools"></i> Breakdowns
                </a>
                @endif
            </li>
           <!-- <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('rejections'))
                <a class="nav-link {{ request()->routeIs('rejections.*') ? 'active' : '' }}" href="{{ route('rejections.index') }}">
                    <i class="bi bi-x-octagon"></i> Rejections
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('rejection_analysis'))
                <a class="nav-link {{ request()->routeIs('rejections.analysis') ? 'active' : '' }}" href="{{ route('rejections.analysis') }}">
                    <i class="bi bi-graph-down"></i> Rejection Analysis
                </a>
                @endif
            </li>-->

            <li class="nav-section">Quality & OEE</li>
           <!-- <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('quality'))
                <a class="nav-link {{ request()->routeIs('quality.*') ? 'active' : '' }}" href="{{ route('quality.index') }}">
                    <i class="bi bi-check-circle"></i> Quality Inspection
                </a>
                @endif
            </li>-->
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('quality'))
                <a class="nav-link {{ request()->routeIs('qc-inspections.*') ? 'active' : '' }}" href="{{ route('qc-inspections.index') }}">
                    <i class="bi bi-clipboard2-check"></i> QC Inspection
                </a>
                @endif
            </li>
          <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('approvals'))
                <a class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}" href="{{ route('approvals.index') }}">
                    <i class="bi bi-hand-thumbs-up"></i> Approvals
                </a>
                @endif
                @if(auth()->user()->hasMenuPermission('pending_approvals'))
                <a class="nav-link {{ request()->routeIs('pending_approvals.*') ? 'active' : '' }}" href="{{ route('pending_approvals.index') }}">
                    <i class="bi bi-clock"></i> Pending Production Approvals
                </a>
                @endif
                @if(auth()->user()->hasMenuPermission('approved_approvals'))
                <a class="nav-link {{ request()->routeIs('approved_approvals.*') ? 'active' : '' }}" href="{{ route('approved_approvals.index') }}">
                    <i class="bi bi-check-circle"></i> Approved Production
                </a>
                @endif
            </li>
            <!--<li class="nav-item">
                @if(auth()->user()->hasMenuPermission('oee'))
                <a class="nav-link {{ request()->routeIs('oee.*') ? 'active' : '' }}" href="{{ route('oee.index') }}">
                    <i class="bi bi-bar-chart-line"></i> OEE
                </a>
                @endif
            </li>-->

            <li class="nav-section">Administration</li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('users'))
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-person-gear"></i> Users
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('reports'))
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reports
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('activity_logs'))
                <a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}">
                    <i class="bi bi-journal-text"></i> Activity Logs
                </a>
                @endif
            </li>
            <li class="nav-item">
                @if(auth()->user()->hasMenuPermission('notifications'))
                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell"></i> Notifications
                </a>
                @endif
            </li>
        </ul>
    </nav>
    @endunless

    <!-- Main Content -->
    <div class="main-content" @if($hideSidebar) style="margin-left: 0;" @endif>
        <!-- Top Bar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="topbar-title">
                @unless($hideSidebar)
                <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                @endunless
                <h5 class="mb-0 d-inline-block ms-2">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="topbar-actions d-flex align-items-center gap-3">
                <div class="mes-header-clock" id="mesHeaderClock" aria-label="Current date and time in India Standard Time" aria-live="off">
                    <span class="mes-header-clock-item">
                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                        <time id="mesCurrentDate"></time>
                    </span>
                    <span class="mes-header-clock-item">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        <time id="mesCurrentTime"></time>
                    </span>
                </div>
                @auth
                <div class="dropdown">
                    <a href="#" class="topbar-notification text-dark position-relative" data-bs-toggle="dropdown" id="notificationDropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display:none;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div id="notificationList" style="max-height: 300px; overflow-y: auto;"></div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary">View All</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="topbar-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="topbar-user-name ms-2 text-dark">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </div>

        <!-- Breakdown Alert Toast -->
        <div class="toast-container position-fixed top-0 end-0 p-3" id="alertToastContainer" style="z-index: 1100;"></div>

        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        (function() {
            var clock = document.getElementById('mesHeaderClock');
            var dateDisplay = document.getElementById('mesCurrentDate');
            var timeDisplay = document.getElementById('mesCurrentTime');

            if (!clock || !dateDisplay || !timeDisplay) return;

            var dateFormatter = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Kolkata',
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            var timeFormatter = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Kolkata',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hourCycle: 'h23'
            });

            function updateMesHeaderClock() {
                if (!clock.isConnected) {
                    window.clearInterval(clockInterval);
                    return;
                }

                var now = new Date();
                dateDisplay.textContent = dateFormatter.format(now);
                timeDisplay.textContent = timeFormatter.format(now);
            }

            updateMesHeaderClock();
            var clockInterval = window.setInterval(updateMesHeaderClock, 1000);
            window.addEventListener('pagehide', function() {
                window.clearInterval(clockInterval);
            }, { once: true });
        })();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Sidebar Toggle
        $('#sidebarToggle').click(function() {
            $('#sidebar').toggleClass('show');
        });

        // Initialize DataTables - validate column counts first
        $(document).ready(function() {
            $('.datatable').each(function() {
                var $table = $(this);
                var $thead = $table.find('thead th');
                var $firstBodyRow = $table.find('tbody tr').first();

                // Count actual columns: sum of colspan in first body row (or td count if no colspan)
                var bodyCols = 0;
                $firstBodyRow.find('td').each(function() {
                    bodyCols += parseInt($(this).attr('colspan')) || 1;
                });

                // Skip if column counts don't match (avoids "Incorrect column count" error)
                if ($thead.length > 0 && bodyCols > 0 && $thead.length !== bodyCols) {
                    console.warn('DataTable skipped: thead has ' + $thead.length + ' cols, tbody has ' + bodyCols);
                    return;
                }

                // Skip if no meaningful data (just an empty colspan row)
                if ($thead.length === 0 || ($thead.length === 1 && $firstBodyRow.find('td').first().attr('colspan'))) {
                    return;
                }

                $table.DataTable({
                    "order": [[0, 'desc']],
                    "pageLength": 25,
                    "responsive": true
                });
            });
        });

        // Notification polling — badge count every 30s
        @auth
        let lastAlertId = 0;

        function fetchNotifications() {
            $.get('{{ route("api.notifications.count") }}', function(data) {
                var badge = $('#notificationCount');
                if (data.count > 0) {
                    badge.text(data.count).show();
                } else {
                    badge.hide();
                }
            });
        }

        // Fetch recent breakdown alerts every 5 seconds
        function fetchBreakdownAlerts() {
            $.get('{{ route("api.notifications.recent-alerts") }}', function(data) {
                if (!data || data.length === 0) return;

                // Only show alerts we haven't already displayed this session
                data.forEach(function(alert) {
                    if (alert.id > lastAlertId) {
                        lastAlertId = alert.id;
                        showBreakdownAlert(alert);
                    }
                });
            });
        }

        // Play an alert sound using the Web Audio API (no external file needed)
        function playAlertSound() {
            try {
                var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                // First tone — higher pitch
                var osc1 = audioCtx.createOscillator();
                var gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.value = 880;
                gain1.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                osc1.connect(gain1).connect(audioCtx.destination);
                osc1.start(audioCtx.currentTime);
                osc1.stop(audioCtx.currentTime + 0.3);
                // Second tone — lower pitch, plays shortly after
                var osc2 = audioCtx.createOscillator();
                var gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.value = 660;
                gain2.gain.setValueAtTime(0.3, audioCtx.currentTime + 0.35);
                gain2.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.65);
                osc2.connect(gain2).connect(audioCtx.destination);
                osc2.start(audioCtx.currentTime + 0.35);
                osc2.stop(audioCtx.currentTime + 0.65);
            } catch (e) {
                // AudioContext not supported or blocked — silent fail
            }
        }

        // Show a Bootstrap toast for a breakdown alert
        function showBreakdownAlert(alert) {
            playAlertSound();

            var icon = 'bi-exclamation-triangle-fill';
            var bgClass = 'bg-danger';
            var textClass = 'text-white';

            if (alert.type === 'quality_alert') {
                icon = 'bi-shield-exclamation';
            } else if (alert.type === 'production_target') {
                icon = 'bi-graph-down-arrow';
                bgClass = 'bg-warning';
                textClass = 'text-dark';
            }

            var toastId = 'toast-' + alert.id;
            var html = `
                <div id="${toastId}" class="toast align-items-center ${bgClass} ${textClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="10000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong><i class="bi ${icon} me-1"></i>${escapeHtml(alert.title)}</strong><br>
                            <small>${escapeHtml(alert.message)}</small>
                            <br><small class="opacity-75">${alert.time}</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;

            $('#alertToastContainer').append(html);
            var toastEl = document.getElementById(toastId);
            var toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Flash the notification badge
            var badge = $('#notificationCount');
            badge.addClass('animate__animated animate__headShake').text(function(i, txt) {
                return parseInt(txt || 0) + 1;
            }).show();
            setTimeout(function() { badge.removeClass('animate__animated animate__headShake'); }, 2000);

            // Refresh dashboard stats if on dashboard page
            if (typeof refreshDashboardStats === 'function') {
                refreshDashboardStats();
            }
        }

        // Simple HTML escape to prevent XSS in toast content
        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        // Poll every 30 seconds for badge count, every 5 seconds for breakdown alerts
        setInterval(fetchNotifications, 30000);
        setInterval(fetchBreakdownAlerts, 5000);
        fetchNotifications();
        fetchBreakdownAlerts();
        @endauth
    </script>

    @stack('scripts')
</body>
</html>
