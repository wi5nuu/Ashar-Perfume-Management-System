<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth
    <title>APMS - @yield('title')</title>
    
    <!-- Favicon & PWA -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-512x512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    
    <style>
        :root {
            --primary-color: #FF6B35;
            --primary-light: #FF8B5C;
            --primary-dark: #E55A2B;
            --secondary-color: #2D3047;
            --light-color: #F8F9FA;
            --dark-color: #343A40;
        }
        
        html, body {
            max-width: 100vw;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            font-size: 14px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        .wrapper {
            overflow-x: hidden;
        }

        /* Removed manual fixed sidebar & navbar adjustments because AdminLTE handles it perfectly via layout classes */
        
        .navbar-apms {
            background: #ffffff !important;
            border-bottom: 1px solid #e9ecef !important;
            box-shadow: none !important;
        }

        .navbar-apms .nav-link {
            color: #495057 !important;
            transition: color 0.3s ease;
            background: none !important; /* Ensure no background */
        }

        .navbar-apms .nav-link:hover {
            color: var(--primary-color) !important;
            background: none !important; /* Ensure no background on hover */
        }

        /* Ensure dropdowns also don't have conflicting styles */
        .navbar-apms .dropdown-item {
            background: none !important;
        }

        .navbar-apms .dropdown-item:hover {
            background-color: #f8f9fa !important;
            color: var(--primary-color) !important;
        }
        
        .sidebar-apms {
            background-color: white;
            border-right: 1px solid #eaeaea;
        }
        /* Hide sidebar scrollbar */
        .sidebar-apms .sidebar::-webkit-scrollbar { width: 0; }
        .sidebar-apms .sidebar { scrollbar-width: none; -ms-overflow-style: none; }
        
        .sidebar-apms .nav-link {
            color: var(--dark-color);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .sidebar-apms .nav-link p {
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Sidebar minimalist hover style */
        .sidebar-apms .nav-sidebar > .nav-item > .nav-link {
            background: transparent !important;
            transition: color 0.3s ease;
        }
        
        .sidebar-apms .nav-sidebar > .nav-item > .nav-link:hover {
            background: transparent !important;
            color: var(--primary-color) !important;
        }
        
        .bg-orange { background-color: #FF6B35 !important; }
        .text-orange { color: #FF6B35 !important; }
        .bg-gradient-orange {
            background: linear-gradient(135deg, #FF6B35 0%, #e85a2a 100%) !important;
        }
        .border-left-orange { border-left: 3px solid #FF6B35 !important; }

        .sidebar-apms .nav-link.active {
            background-color: rgba(255, 107, 53, 0.1) !important;
            color: var(--primary-color) !important;
            font-weight: 600;
            border-right: 4px solid var(--primary-color);
            border-radius: 8px 0 0 8px !important;
        }
        
        .card-apms {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .card-apms:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary-apms {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary-apms:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .badge-premium {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
        }
        
        .badge-wholesale {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        /* --------------------------------------------------
           COMPACT LAYOUT â€” tighter spacing for all screens
        -------------------------------------------------- */
        .compact .card-body {
            padding: 0.75rem !important;
        }
        .compact .card-header {
            padding: 0.5rem 0.75rem !important;
        }
        .compact .card {
            margin-bottom: 0.5rem !important;
        }
        .compact .row {
            margin-left: -6px;
            margin-right: -6px;
        }
        .compact .row > [class*="col-"] {
            padding-left: 6px;
            padding-right: 6px;
        }
        .compact .info-box {
            margin-bottom: 0.5rem !important;
            min-height: 55px;
        }
        .compact .info-box-icon {
            width: 45px;
            font-size: 1rem;
            line-height: 55px;
        }
        .compact .info-box-content {
            padding: 5px 8px;
        }
        .compact .info-box-number {
            font-size: 0.95rem;
        }
        .compact .form-group {
            margin-bottom: 0.5rem;
        }
        .compact .mb-3 {
            margin-bottom: 0.5rem !important;
        }
        .compact .mb-4 {
            margin-bottom: 0.75rem !important;
        }
        .compact .pt-3, .compact .py-3 {
            padding-top: 0.5rem !important;
        }
        .compact .pb-3, .compact .py-3 {
            padding-bottom: 0.5rem !important;
        }

        /* =============================================
           MOBILE COMPACT LAYOUT (< 768px)
        ============================================= */
        @media (max-width: 767.98px) {

            /* Navbar */
            .main-header .navbar-nav .nav-link {
                padding: 0.3rem 0.5rem;
                font-size: 0.8rem;
            }
            .main-header.navbar {
                min-height: 44px;
            }

            /* Page Content Padding */
            .content-wrapper {
                padding: 0 !important;
            }
            .content {
                padding: 8px !important;
            }
            .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            /* Cards */
            .card {
                margin-bottom: 8px !important;
                border-radius: 8px !important;
            }
            .card-apms {
                margin-bottom: 8px;
            }
            .card-header {
                padding: 8px 12px !important;
            }
            .card-header .card-title {
                font-size: 0.85rem !important;
                margin-bottom: 0 !important;
            }
            .card-body {
                padding: 10px 12px !important;
            }

            /* Info Boxes (stat cards) */
            .info-box {
                min-height: 60px !important;
                padding: 0 !important;
                margin-bottom: 8px !important;
            }
            .info-box-icon {
                width: 50px !important;
                line-height: 60px !important;
                font-size: 1.2rem !important;
            }
            .info-box-content {
                padding: 6px 10px !important;
            }
            .info-box-text {
                font-size: 0.7rem !important;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }
            .info-box-number {
                font-size: 1rem !important;
                font-weight: 700 !important;
            }

            /* Small Boxes (AdminLTE stat boxes) */
            .small-box {
                margin-bottom: 8px !important;
            }
            .small-box h3 {
                font-size: 1.4rem !important;
            }
            .small-box p {
                font-size: 0.75rem !important;
            }
            .small-box .icon {
                font-size: 50px !important;
                top: 5px !important;
                right: 10px !important;
            }

            /* Headings */
            h1, .h1 { font-size: 1.3rem !important; }
            h2, .h2 { font-size: 1.15rem !important; }
            h3, .h3 { font-size: 1rem !important; }
            h4, .h4 { font-size: 0.9rem !important; }
            h5, .h5 { font-size: 0.85rem !important; }

            /* Tables - no nowrap to prevent overflow */
            .table th, .table td {
                font-size: 0.72rem !important;
                padding: 4px 6px !important;
                white-space: normal !important;
                word-break: break-word;
            }
            .table-responsive {
                border-radius: 6px;
                max-width: 100%;
                overflow-x: auto;
            }

            /* Buttons */
            .btn {
                font-size: 0.78rem !important;
                padding: 5px 10px !important;
            }
            .btn-block {
                padding: 8px 10px !important;
            }

            /* Form Controls */
            .form-control, .form-group label {
                font-size: 0.8rem !important;
            }
            .form-group {
                margin-bottom: 8px !important;
            }

            /* Row gutters */
            .row {
                margin-left: -4px !important;
                margin-right: -4px !important;
            }
            .row > [class*="col-"] {
                padding-left: 4px !important;
                padding-right: 4px !important;
            }

            /* Prevent any element from exceeding screen */
            img, iframe, video, embed, canvas {
                max-width: 100% !important;
            }

            /* Charts - smaller height */
            canvas {
                max-height: 180px !important;
                width: 100% !important;
            }

            /* Charts - smaller height */
            canvas {
                max-height: 180px !important;
                width: 100% !important;
            }

            /* Sidebar brand */
            .brand-text {
                font-size: 1.1rem !important;
            }

            /* Content header */
            .content-header {
                padding: 8px 12px !important;
            }
            .content-header h1 {
                font-size: 1.1rem !important;
            }
            .content-header .breadcrumb {
                font-size: 0.7rem !important;
                padding: 2px 0 !important;
                background: none;
            }

            /* Page-specific: Dashboard top stats */
            .stat-card {
                padding: 8px !important;
            }

            /* Badge */
            .badge {
                font-size: 0.65rem !important;
            }
        }

        /* Tablet (768px - 991px) - slightly compact */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .info-box-number {
                font-size: 1.2rem !important;
            }
            .small-box h3 {
                font-size: 1.8rem !important;
            }
            .card-body {
                padding: 12px !important;
            }
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Mobile Bottom Nav Styles */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-around;
            align-items: center;
            border-top: 1px solid #ddd;
            z-index: 1040;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .mobile-bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #888;
            text-decoration: none;
            flex: 1;
            height: 100%;
            transition: all 0.2s ease;
        }

        .mobile-bottom-nav .nav-item i {
            font-size: 1.2rem;
            margin-bottom: 2px;
        }

        .mobile-bottom-nav .nav-item span {
            font-size: 0.65rem;
            font-weight: 500;
        }

        .mobile-bottom-nav .nav-item.active {
            color: var(--primary-color);
        }

        .mobile-bottom-nav .nav-item.active i {
            transform: scale(1.1);
        }

        @media (max-width: 767.98px) {
            body {
                padding-bottom: 60px;
            }
            .main-footer {
                display: none;
            }

            /* Touch-friendly targets (mobile) */
            .btn, .nav-link, .dropdown-item, .form-control {
                min-height: 44px;
            }
            .btn-sm, .btn-group-sm .btn {
                min-height: 36px;
            }
            .btn-group-toggle .btn {
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Prevent modal overflow */
            .modal-dialog {
                margin: 0.5rem;
                max-height: calc(100vh - 1rem);
            }
            .modal-body {
                max-height: calc(100vh - 200px);
                overflow-y: auto;
            }

            /* Better select inputs on mobile (prevents iOS zoom) */
            select.form-control {
                font-size: 16px !important;
            }

            /* Ensure proper tap targets */
            .product-card {
                min-height: 120px;
            }
            .product-card .card-body {
                padding: 8px !important;
            }
        }

        /* Tablet touch optimization */
        @media (min-width: 768px) and (max-width: 1024px) {
            .btn {
                padding: 8px 16px !important;
            }
            .btn-lg {
                padding: 10px 24px !important;
            }
        }

        /* Smooth scrolling for the whole page */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed compact">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-apms">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    Dashboard
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto align-items-center">
            {{-- Comprehensive Notifications Dropdown --}}
            {{-- Data provided by AppServiceProvider ViewComposer (cached 60s) --}}
            <li class="nav-item dropdown mr-2">
                <a class="nav-link position-relative" data-toggle="dropdown" href="#" id="notifDropdownToggle">
                    <i class="fas fa-bell" style="font-size:1.15rem"></i>
                    @if($totalNotif > 0)
                    <span class="badge badge-danger position-absolute" id="notificationCount"
                          style="top:2px;right:2px;font-size:0.6rem;padding:2px 5px;min-width:18px;border-radius:8px">
                        {{ $totalNotif > 99 ? '99+' : $totalNotif }}
                    </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg p-0" id="notificationList"
                     style="width:380px;max-height:520px;overflow-y:auto;border-radius:12px">
                    {{-- Header summary --}}
                    <div class="px-3 py-2 d-flex justify-content-between align-items-center"
                         style="background:linear-gradient(135deg,#2c3e50,#3498db);color:#fff;border-radius:12px 12px 0 0">
                        <span class="font-weight-bold" style="font-size:0.85rem"><i class="fas fa-bell mr-1"></i> Pusat Notifikasi</span>
                        <span style="font-size:0.7rem;opacity:0.85">{{ $totalNotif }} belum dibaca</span>
                    </div>

                    {{-- Quick summary badges --}}
                    <div class="d-flex px-2 py-2" style="background:#f8f9fa;gap:4px;border-bottom:1px solid #eee">
                        @if($pendingGrosirCount > 0)
                        <a href="{{ route('wholesale.index', ['status' => 'pending']) }}"
                           class="badge badge-warning px-2 py-1" style="font-size:0.68rem;text-decoration:none">
                            <i class="fas fa-boxes mr-1"></i> {{ $pendingGrosirCount }} Pesanan
                        </a>
                        @endif
                        @if($loginTodayCount > 0)
                        <span class="badge badge-info px-2 py-1" style="font-size:0.68rem">
                            <i class="fas fa-sign-in-alt mr-1"></i> {{ $loginTodayCount }} Login
                        </span>
                        @endif
                        @if($auditTodayCount > 0)
                        <span class="badge badge-secondary px-2 py-1" style="font-size:0.68rem">
                            <i class="fas fa-history mr-1"></i> {{ $auditTodayCount }} Aktivitas
                        </span>
                        @endif
                        @if($pendingResetCount > 0)
                        <a href="{{ route('settings.password.reset-requests') }}"
                           class="badge badge-danger px-2 py-1" style="font-size:0.68rem;text-decoration:none">
                            <i class="fas fa-key mr-1"></i> {{ $pendingResetCount }} Reset
                        </a>
                        @endif
                        @if($activeSessions > 0)
                        <span class="badge badge-success px-2 py-1" style="font-size:0.68rem">
                            <i class="fas fa-users mr-1"></i> {{ $activeSessions }} Online
                        </span>
                        @endif
                    </div>

                    {{-- Section: Pesanan Grosir Pending --}}
                    @if($pendingGrosirCount > 0)
                    <div class="px-3 pt-2 pb-1" style="border-bottom:1px solid #f0f0f0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#e67e22">
                                <i class="fas fa-boxes mr-1"></i> Pesanan Grosir Pending
                            </span>
                            <span class="badge badge-warning" style="font-size:0.6rem">{{ $pendingGrosirCount }}</span>
                        </div>
                        @foreach($pendingGrosirOrders as $o)
                        <a href="{{ route('wholesale.show', $o->id) }}" class="d-flex align-items-center py-1 text-decoration-none"
                           style="border-bottom:1px solid #f8f8f8;font-size:0.78rem;color:#333">
                            <div class="mr-2" style="width:28px;height:28px;border-radius:6px;background:#fff3e0;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-box text-warning" style="font-size:0.7rem"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div class="font-weight-500 text-truncate">{{ $o->invoice_number }} — {{ $o->recipient_name }}</div>
                                <div style="font-size:0.65rem;color:#999">Rp {{ number_format($o->total_amount, 0, ',', '.') }}</div>
                            </div>
                            <span style="font-size:0.6rem;color:#aaa;white-space:nowrap">{{ $o->created_at->diffForHumans() }}</span>
                        </a>
                        @endforeach
                        <a href="{{ route('wholesale.index', ['status' => 'pending']) }}"
                           class="d-block text-center py-1 text-decoration-none" style="font-size:0.72rem;color:#3498db;font-weight:600">
                            Lihat semua ({{ $pendingGrosirCount }})
                        </a>
                    </div>
                    @endif

                    {{-- Section: Login Hari Ini --}}
                    @if($loginTodayCount > 0)
                    <div class="px-3 pt-2 pb-1" style="border-bottom:1px solid #f0f0f0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#2980b9">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login Hari Ini
                            </span>
                            <span class="badge badge-info" style="font-size:0.6rem">{{ $loginTodayCount }}</span>
                        </div>
                        @foreach($loginToday as $l)
                        <div class="d-flex align-items-center py-1" style="border-bottom:1px solid #fafafa;font-size:0.78rem;color:#333">
                            <div class="mr-2" style="width:28px;height:28px;border-radius:50%;background:#e8f4fd;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-user text-info" style="font-size:0.7rem"></i>
                            </div>
                            <div style="flex:1">
                                <span class="font-weight-500">{{ $l->name }}</span>
                                <span class="badge badge-light text-muted" style="font-size:0.6rem">{{ $l->role }}</span>
                            </div>
                            <span style="font-size:0.6rem;color:#aaa;white-space:nowrap">{{ \Carbon\Carbon::parse($l->created_at)->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Section: Audit Logs Terbaru --}}
                    @if($auditTodayCount > 0)
                    <div class="px-3 pt-2 pb-1" style="border-bottom:1px solid #f0f0f0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#7f8c8d">
                                <i class="fas fa-history mr-1"></i> Aktivitas Sistem
                            </span>
                            <span class="badge badge-secondary" style="font-size:0.6rem">{{ $auditTodayCount }}</span>
                        </div>
                        @foreach($auditToday as $a)
                        <div class="d-flex align-items-center py-1" style="border-bottom:1px solid #fafafa;font-size:0.78rem;color:#333">
                            <div class="mr-2" style="width:28px;height:28px;border-radius:6px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-pen text-muted" style="font-size:0.65rem"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div class="text-truncate">
                                    <span class="font-weight-500">{{ $a->user_name ?? 'System' }}</span>
                                    <span class="text-muted">{{ \Illuminate\Support\Str::replace('App\\Models\\', '', $a->target_model) }}</span>
                                </div>
                                <div style="font-size:0.65rem;color:#999;text-transform:capitalize">{{ $a->action }}</div>
                            </div>
                            <span style="font-size:0.6rem;color:#aaa;white-space:nowrap">{{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Section: Database Notifications --}}
                    @if($dbNotifCount > 0)
                    <div class="px-3 pt-2 pb-1" style="border-bottom:1px solid #f0f0f0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#e74c3c">
                                <i class="fas fa-bell mr-1"></i> Notifikasi
                            </span>
                            <span class="badge badge-danger" style="font-size:0.6rem">{{ $dbNotifCount }}</span>
                        </div>
                        @foreach($dbNotifs as $n)
                            @php $nd = json_decode($n->data, true); @endphp
                            <div class="d-flex align-items-center py-1" style="border-bottom:1px solid #fafafa;font-size:0.78rem;color:#333">
                                <div class="mr-2" style="width:28px;height:28px;border-radius:50%;background:#fce4ec;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="fas fa-info text-danger" style="font-size:0.65rem"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div class="text-truncate font-weight-500">{{ $nd['message'] ?? $nd['title'] ?? 'Notifikasi' }}</div>
                                    <div style="font-size:0.65rem;color:#999">{{ \Illuminate\Support\Str::after(basename($n->type), 'Notifications\\') }}</div>
                                </div>
                                <span style="font-size:0.6rem;color:#aaa;white-space:nowrap">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</span>
                            </div>
                        @endforeach
                            @can('owner')
                            <form method="POST" action="{{ route('owner.notifications.read-all') }}" class="d-block text-center py-1">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none" style="font-size:0.72rem;color:#3498db;font-weight:600">
                                    Tandai semua sudah dibaca
                                </button>
                            </form>
                            @endcan
                    </div>
                    @endif

                    {{-- Empty state --}}
                    @if($totalNotif === 0)
                    <div class="text-center py-4 px-3">
                        <div style="width:60px;height:60px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                            <i class="fas fa-check-circle text-success" style="font-size:1.5rem"></i>
                        </div>
                        <div class="font-weight-bold" style="font-size:0.9rem;color:#333">Semua aman!</div>
                        <div style="font-size:0.75rem;color:#999">Tidak ada notifikasi baru.</div>
                    </div>
                    @endif
                </div>
            </li>

            <!-- User Profile Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" style="padding: 0.5rem;">
                    <div class="d-none d-md-block text-right mr-2" style="line-height: 1;">
                        <span class="d-block font-weight-bold" style="font-size: 0.85rem;">{{ Auth::user()->name }}</span>
                        <span class="badge badge-light border text-primary" style="font-size: 0.65rem;">{{ strtoupper(Auth::user()->role) }}</span>
                    </div>
                    <i class="far fa-user-circle fa-lg"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 mt-2">
                    <a href="{{ route('settings.profile') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item" 
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-light-primary elevation-4 sidebar-apms">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link border-0 d-flex align-items-center py-3 px-3">
            <img src="{{ asset('favicon-512x512.png') }}" alt="APMS Logo" class="brand-image" style="max-height: 40px; width: auto; opacity: 1;">
            <span class="brand-text font-weight-bold ml-2" style="color: var(--primary-color); font-size: 1.4rem; letter-spacing: -0.5px;">APMS</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu">
                    @php $ownerPendingResetCount = $pendingResetCount ?? 0; @endphp
                    {{-- DASHBOARD --}}
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard', 'dashboard.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-th-large"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    {{-- TRANSAKSI --}}
                    @canany(['manage_transactions', 'transactions.view'])
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd;">Transaksi</li>
                    @endcanany
                    @can('manage_transactions')
                    <li class="nav-item">
                        <a href="{{ route('transactions.create') }}" class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Kasir</p>
                        </a>
                    </li>
                    @endcan
                    @can('transactions.view')
                    <li class="nav-item">
                        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                    @endcan

                    {{-- GROSIR --}}
                    @canany(['wholesale.view', 'wholesale.manage'])
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd;">Grosir</li>
                    @endcanany
                    @can('wholesale.manage')
                    <li class="nav-item">
                        <a href="{{ route('wholesale.create') }}" class="nav-link {{ request()->routeIs('wholesale.create') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Kasir Grosir</p>
                        </a>
                    </li>
                    @endcan
                    @can('wholesale.view')
                    <li class="nav-item">
                        <a href="{{ route('wholesale.index') }}" class="nav-link {{ request()->routeIs('wholesale.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes-packing"></i>
                            <p>Pesanan Grosir</p>
                            @if(($pendingGrosirCount ?? 0) > 0)
                            <span class="badge badge-warning right">{{ $pendingGrosirCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endcan
                    @can('wholesale.manage')
                    <li class="nav-item">
                        <a href="{{ route('wholesale.products.index') }}" class="nav-link {{ request()->routeIs('wholesale.products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Produk Grosir</p>
                        </a>
                    </li>
                    @endcan

                    {{-- PRODUK & INVENTARIS --}}
                    @canany(['products.view', 'inventory.view', 'stock_requests.view', 'goods_receipts.view', 'expenses.view'])
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd;">Produk & Inventaris</li>
                    @endcanany
                    @can('products.view')
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-spray-can"></i>
                            <p>Produk</p>
                        </a>
                    </li>
                    @endcan
                    @can('manage_products')
                    <li class="nav-item">
                        <a href="{{ route('bulk-price.index') }}" class="nav-link {{ request()->routeIs('bulk-price.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Update Harga Massal</p>
                        </a>
                    </li>
                    @endcan
                    @can('inventory.view')
                    <li class="nav-item">
                        <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index', 'inventory.adjust', 'inventory.audit', 'stock_audits.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Inventory</p>
                        </a>
                    </li>
                    @endcan
                    @can('inventory.manage')
                    @if(auth()->user()->role !== 'warehouse')
                    <li class="nav-item">
                        <a href="{{ route('purchase-orders.index') }}" class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Purchase Order</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('supplier-prices.index') }}" class="nav-link {{ request()->routeIs('supplier-prices.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Harga Supplier</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-undo"></i>
                            <p>Retur Penjualan</p>
                        </a>
                    </li>
                    @endif
                    @endcan
                    @can('inventory.view')
                    <li class="nav-item">
                        <a href="{{ route('inventory.expiry-alerts') }}" class="nav-link {{ request()->routeIs('inventory.expiry*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-times"></i>
                            <p>Peringatan Kadaluarsa</p>
                        </a>
                    </li>
                    @endcan
                    @can('goods_receipts.view')
                    <li class="nav-item">
                        <a href="{{ route('goods-receipts.index') }}" class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck-loading"></i>
                            <p>Penerimaan Barang</p>
                        </a>
                    </li>
                    @endcan
                    @can('inventory.view')
                    <li class="nav-item">
                        <a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>Gudang</p>
                        </a>
                    </li>
                    @endcan
                    @can('stock_requests.view')
                    <li class="nav-item">
                        <a href="{{ route('stock-requests.index') }}" class="nav-link {{ request()->routeIs('stock-requests.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Permintaan Stok</p>
                        </a>
                    </li>
                    @endcan
                    @can('expenses.view')
                    <li class="nav-item">
                        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-coins"></i>
                            <p>Biaya Toko</p>
                        </a>
                    </li>
                    @endcan

                    {{-- CRM & LAPORAN --}}
                    @canany(['manage_customers', 'manage_coupons', 'view_reports'])
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd;">CRM & Laporan</li>
                    @endcanany

                    @can('manage_customers')
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>Pelanggan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('debts.aging') }}" class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hourglass-half"></i>
                            <p>Hutang & Piutang</p>
                        </a>
                    </li>
                    @endcan
                    
                    @can('manage_coupons')
                    <li class="nav-item">
                        <a href="{{ route('coupons.index') }}" class="nav-link {{ request()->routeIs('coupons.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Kupon & Loyalty</p>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view_reports')
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('commissions.index') }}" class="nav-link {{ request()->routeIs('commissions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Komisi Karyawan</p>
                        </a>
                    </li>
                    @endcan
                    
                    @canany(['manage_employees', 'manage_settings'])
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd; margin-top: 10px;">Manajemen</li>
                    @endcanany

                    @can('manage_employees')
                    <li class="nav-item">
                        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Karyawan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Payroll</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.password.reset-requests') }}" class="nav-link {{ request()->routeIs('settings.password.reset-requests*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Reset Password</p>
                            @if($ownerPendingResetCount > 0)
                            <span class="badge badge-danger right">{{ $ownerPendingResetCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endcan
                    
                    @can('manage_settings')
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan</p>
                        </a>
                    </li>
                    @endcan

                    {{-- Owner Section --}}
                    @can('owner')
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd; margin-top: 10px;">Owner</li>
                    <li class="nav-item">
                        <a href="{{ route('owner.monitoring') }}" class="nav-link {{ request()->routeIs('owner.monitoring') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>Monitoring</p>
                            @if($ownerPendingResetCount > 0)
                            <span class="badge badge-danger right">{{ $ownerPendingResetCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('owner.ai-dashboard') }}" class="nav-link {{ request()->routeIs('owner.ai-dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-robot"></i>
                            <p>AI Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('owner.wholesale-customers') }}" class="nav-link {{ request()->routeIs('owner.wholesale-customers') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Pelanggan Grosir</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('owner.customer-accounts') }}" class="nav-link {{ request()->routeIs('owner.customer-accounts') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-address-card"></i>
                            <p>Data Akun Pelanggan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('owner.loyalty.index') }}" class="nav-link {{ request()->routeIs('owner.loyalty.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-star text-warning"></i>
                            <p>Loyalty Grosir</p>
                        </a>
                    </li>
                    @endcan

                    @can('audit.view')
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd; margin-top: 10px;">Keamanan</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.security.overview') }}" class="nav-link {{ request()->routeIs('admin.security.overview') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Dashboard Keamanan</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.security.audit-logs', 'admin.security.login-activities', 'admin.security.locked-accounts', 'admin.security.blocked-ips', 'admin.security.integrity') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.security.audit-logs', 'admin.security.login-activities', 'admin.security.locked-accounts', 'admin.security.blocked-ips', 'admin.security.integrity') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Monitoring<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview ml-3">
                            <li class="nav-item"><a href="{{ route('admin.security.audit-logs') }}" class="nav-link {{ request()->routeIs('admin.security.audit-logs') ? 'active' : '' }}"><i class="fas fa-circle nav-icon" style="font-size:8px;"></i><p>Log Audit</p></a></li>
                            <li class="nav-item"><a href="{{ route('admin.security.login-activities') }}" class="nav-link {{ request()->routeIs('admin.security.login-activities') ? 'active' : '' }}"><i class="fas fa-circle nav-icon" style="font-size:8px;"></i><p>Aktivitas Login</p></a></li>
                            <li class="nav-item"><a href="{{ route('admin.security.locked-accounts') }}" class="nav-link {{ request()->routeIs('admin.security.locked-accounts') ? 'active' : '' }}"><i class="fas fa-circle nav-icon" style="font-size:8px;"></i><p>Akun Terkunci</p></a></li>
                            <li class="nav-item"><a href="{{ route('admin.security.blocked-ips') }}" class="nav-link {{ request()->routeIs('admin.security.blocked-ips') ? 'active' : '' }}"><i class="fas fa-circle nav-icon" style="font-size:8px;"></i><p>IP Diblokir</p></a></li>
                            <li class="nav-item"><a href="{{ route('admin.security.integrity') }}" class="nav-link {{ request()->routeIs('admin.security.integrity') ? 'active' : '' }}"><i class="fas fa-circle nav-icon" style="font-size:8px;"></i><p>Integritas Data</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.security.two-factor') }}" class="nav-link {{ request()->routeIs('admin.security.two-factor*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-qrcode"></i>
                            <p>Autentikasi 2FA</p>
                        </a>
                    </li>
                    @endcan
                    @can('roles.manage')
                    <li class="nav-item">
                        <a href="{{ route('admin.rbac.index') }}" class="nav-link {{ request()->routeIs('admin.rbac.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-lock"></i>
                            <p>Role & Izin</p>
                        </a>
                    </li>
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd; margin-top: 10px;">Monitoring</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.monitoring.logs') }}" class="nav-link {{ request()->routeIs('admin.monitoring.logs') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Log Aplikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.monitoring.backup') }}" class="nav-link {{ request()->routeIs('admin.monitoring.backup*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Backup Database</p>
                        </a>
                    </li>
                    @endcan

                    @if(auth()->user()->isOwner())
                    <li class="nav-header text-uppercase font-weight-bold" style="font-size: 0.7rem; color: #adb5bd; margin-top: 10px;">Manajemen Cabang</li>
                    <li class="nav-item">
                        <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-store"></i>
                            <p>Dashboard Cabang</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content pt-3">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="#" style="color: var(--primary-color);">Ashar Parfum Management System</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav d-md-none">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard', 'dashboard.index') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
        @can('products.view')
        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="fas fa-spray-can"></i>
            <span>Produk</span>
        </a>
        @endcan
        @can('manage_transactions')
        <a href="{{ route('transactions.create') }}" class="nav-item {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i>
            <span>Kasir</span>
        </a>
        @endcan
        @can('transactions.view')
        <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Histori</span>
        </a>
        @endcan
        <a href="#" class="nav-item" data-widget="pushmenu">
            <i class="fas fa-ellipsis-h"></i>
            <span>Menu</span>
        </a>
    </div>
</div>

<!-- Scripts -->
{{-- Vite handles app.js now --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Global AJAX CSRF setup â€” ensures all jQuery AJAX requests send the X-CSRF-TOKEN header
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>

<script>
    // BUG-4: Global SweetAlert2 Toast Notification
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: @json(session('success')) });
    @endif

    @if(session('error'))
        Toast.fire({ icon: 'error', title: @json(session('error')) });
    @endif

    $(document).ready(function() {
        // BUG-3: Global Form Submit Loading State & Double Submit Prevention
        $('form').not('.no-loading').on('submit', function() {
            let btn = $(this).find('button[type="submit"]');
            if (btn.length && !btn.prop('disabled')) {
                btn.prop('disabled', true);
                let originalText = btn.html();
                btn.data('original-text', originalText);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            }
        });

        // BUG-5: Global Delete Confirmation with SweetAlert2
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let formId = $(this).data('form-id');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E55A2B',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                }
            });
        });

        // Restore submit buttons after validation redirect (page restored from bfcache)
        $(window).on('pageshow', function(e) {
            if (e.originalEvent.persisted) {
                $('button[type="submit"]').each(function() {
                    const $btn = $(this);
                    const original = $btn.data('original-text');
                    if (original) {
                        $btn.html(original).prop('disabled', false);
                    }
                });
            }
        });
    });
</script>

@stack('styles')
@stack('scripts')
<script>
    // Fix sidebar navigation â€” force navigation using multiple methods
    document.querySelectorAll('.sidebar .nav-link').forEach(function(el) {
        el.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            var hasTreeview = this.nextElementSibling && this.nextElementSibling.classList.contains('nav-treeview');
            if (href && href !== '#' && !hasTreeview) {
                e.preventDefault();
                e.stopPropagation();
                console.log('[Nav] Force ->', href);
                location.assign(href);
            }
        });
    });

    // Scroll sidebar to show the active nav link on page load
    (function() {
        var activeLink = document.querySelector('.sidebar .nav-link.active');
        if (activeLink) {
            var sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                var linkRect = activeLink.getBoundingClientRect();
                var sidebarRect = sidebar.getBoundingClientRect();
                var offsetTop = activeLink.offsetTop - sidebar.offsetTop;
                var scrollTarget = offsetTop - sidebarRect.height / 2 + linkRect.height / 2;
                sidebar.scrollTop = Math.max(0, scrollTarget);
            }
        }
    })();

    // Legacy mock notif system
    function addNotification(title, message) {
        const $count = $('#notificationCount');
        const count = (parseInt($count.text(), 10) || 0) + 1;
        $count.text(count).show();
        
        if (count === 1) {
            $('#notificationList').html('<span class="dropdown-item dropdown-header">Notifikasi Baru</span>');
        }
        
        const safeTitle  = $('<span>').text(title).html();
        const safeMessage = $('<span>').text(message).html();
        
        const html = `
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-info-circle mr-2"></i> ${safeTitle}
                <span class="float-right text-muted text-sm">${safeMessage}</span>
            </a>
        `;
        $('#notificationList').append(html);
    }
</script>

<!-- ====== APMS AI Copilot Chat Widget ====== -->
<style>
.chat-btn {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg,#2c7be5,#6b5ce7);
    color: #fff; border: none; font-size: 24px;
    box-shadow: 0 4px 20px rgba(44,123,229,.45);
    cursor: pointer; transition: all .25s;
    display: flex; align-items: center; justify-content: center;
}
.chat-btn:hover { transform: scale(1.08); box-shadow: 0 6px 28px rgba(44,123,229,.55); }
.chat-btn.active { transform: rotate(45deg); }
.chat-panel {
    position: fixed; bottom: 90px; right: 24px; z-index: 9998;
    width: 380px; max-height: 580px;
    background: #1a1e2b; border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.5);
    display: none; flex-direction: column; overflow: hidden;
    border: 1px solid rgba(255,255,255,.08);
}
.chat-panel.open { display: flex; }
.chat-header {
    background: linear-gradient(135deg,#2c7be5,#6b5ce7);
    padding: 14px 18px; color: #fff; display: flex;
    align-items: center; justify-content: space-between; gap:8px;
}
.chat-header h6 { margin:0; font-weight:700; font-size:14px; }
.chat-header small { opacity:.8; font-size:11px; display:block; }
.chat-header-close {
    background: rgba(255,255,255,.15); border:none; color:#fff;
    border-radius: 50%; width:32px; height:32px; cursor:pointer;
    display:flex; align-items:center; justify-content:center; font-size:16px;
}
.chat-chips {
    display: flex; flex-wrap: wrap; gap:4px; padding: 8px 12px 4px;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.chat-chip {
    display: inline-block;
    background: rgba(44,123,229,.15); color: #8ab4f8;
    border-radius: 16px; padding: 3px 10px; margin: 1px;
    font-size: 11px; cursor: pointer; white-space: nowrap;
    border: 1px solid rgba(44,123,229,.2); transition: all .15s;
    font-weight: 600;
}
.chat-chip:hover { background: rgba(44,123,229,.3); }
.chat-body {
    flex:1; overflow-y: auto; padding: 12px;
    display: flex; flex-direction: column; gap: 10px;
    min-height: 280px; max-height: 340px;
    background: #141824;
}
.chat-body::-webkit-scrollbar { width:4px; }
.chat-body::-webkit-scrollbar-track { background:transparent; }
.chat-body::-webkit-scrollbar-thumb { background:#2c3e50; border-radius:4px; }
.chat-msg {
    max-width: 88%; padding: 10px 14px; border-radius: 14px;
    font-size: 13px; line-height: 1.5; word-wrap: break-word;
    animation: fadeUp .2s ease;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
.chat-msg.user {
    align-self: flex-end; background: #2c7be5; color: #fff;
    border-bottom-right-radius: 4px;
}
.chat-msg.bot {
    align-self: flex-start; background: #1e2433; color: #cfd9e6;
    border-bottom-left-radius: 4px;
}
.chat-msg.bot a { color: #7cafff; text-decoration: underline; }
.chat-msg.bot a.btn { text-decoration: none; }
.chat-typing {
    align-self: flex-start; background: #1e2433; color: #8a9bb5;
    border-radius: 14px; padding: 10px 16px; font-size: 13px;
    display: none; gap:4px; align-items: center;
}
.chat-typing span { animation: blink 1.2s infinite; }
.chat-typing span:nth-child(2) { animation-delay:.2s; }
.chat-typing span:nth-child(3) { animation-delay:.4s; }
@keyframes blink { 0%,80% { opacity:.3; } 40% { opacity:1; } }
.chat-footer {
    display: flex; align-items: center; gap:8px;
    padding: 10px 12px; background: #1a1e2b;
    border-top: 1px solid rgba(255,255,255,.06);
}
.chat-footer textarea {
    flex:1; background: #242b3d; border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px; padding: 8px 12px; color: #e0e6f0;
    font-size: 13px; resize: none; height: 40px; outline: none;
    font-family: inherit;
}
.chat-footer textarea::placeholder { color: #6a7a94; }
.chat-footer button {
    background: #2c7be5; border: none; color: #fff;
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 16px; transition: background .15s;
    flex-shrink: 0;
}
.chat-footer button:disabled { opacity:.4; cursor:default; }
.chat-footer button:hover:not(:disabled) { background: #1a68d0; }
</style>

<button class="chat-btn" id="chatToggle" onclick="toggleChat()">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
</button>

<div class="chat-panel" id="chatPanel">
    <div class="chat-header">
        <div style="display:flex;align-items:center;gap:10px;">
            <img src="{{ asset('favicon-512x512.png') }}" alt="Logo" style="height:28px;width:auto;border-radius:6px;">
            <div>
                <h6>APMS Copilot</h6>
                <small>Asisten Digital Bisnis</small>
            </div>
        </div>
        <button class="chat-header-close" onclick="toggleChat()">âœ•</button>
    </div>
    <div class="chat-chips" id="chatChips">
        <span class="chat-chip" onclick="quickAsk('Berapa penjualan hari ini?')">Penjualan Hari Ini</span>
        <span class="chat-chip" onclick="quickAsk('Stok yang habis')">Stok Habis</span>
        <span class="chat-chip" onclick="quickAsk('Parfum terlaris apa saja?')">Parfum Terlaris</span>
        <span class="chat-chip" onclick="quickAsk('Berapa cabang kita?')">Info Cabang</span>
        <span class="chat-chip" onclick="quickAsk('Berapa jumlah pelanggan kita?')">Jumlah Pelanggan</span>
        <span class="chat-chip" onclick="quickAsk('Darimana saja pelanggan kita?')">Asal Pelanggan</span>
    </div>
    <div class="chat-body" id="chatBody"></div>
    <div class="chat-typing" id="chatTyping"><span>â—</span><span>â—</span><span>â—</span></div>
    <div class="chat-footer">
        <textarea id="chatInput" rows="1" placeholder="Ajukan pertanyaan..." onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChat()}"></textarea>
        <button id="chatSend" onclick="sendChat()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
    </div>
</div>

<script>
let chatOpen = false;
let chatHistory = [];

function toggleChat() {
    chatOpen = !chatOpen;
    document.getElementById('chatToggle').classList.toggle('active', chatOpen);
    document.getElementById('chatPanel').classList.toggle('open', chatOpen);
    if (chatOpen) {
        const body = document.getElementById('chatBody');
        if (body.children.length === 0) {
            addBotMsg('Selamat datang. Saya <strong>APMS Copilot</strong>, asisten digital sistem manajemen Ashar Parfum. Silakan ajukan pertanyaan seputar bisnis Anda.');
        }
        setTimeout(function() { document.getElementById('chatInput').focus(); }, 300);
    }
}

function addBotMsg(html) {
    const div = document.createElement('div');
    div.className = 'chat-msg bot';
    div.innerHTML = html;
    document.getElementById('chatBody').appendChild(div);
    scrollChat();
}

function addUserMsg(text) {
    const div = document.createElement('div');
    div.className = 'chat-msg user';
    div.textContent = text;
    document.getElementById('chatBody').appendChild(div);
    scrollChat();
}

function scrollChat() {
    const body = document.getElementById('chatBody');
    body.scrollTop = body.scrollHeight;
}

function setTyping(on) {
    document.getElementById('chatTyping').style.display = on ? 'flex' : 'none';
    scrollChat();
}

function sendChat() {
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;
    input.value = '';
    document.getElementById('chatSend').disabled = true;

    addUserMsg(text);
    setTyping(true);

    fetch('{{ route("ai.chat") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ message: text })
    })
    .then(function(r) {
        if (!r.ok) {
            return r.json().then(function(d) {
                throw new Error(d.reply || 'Terjadi kesalahan pada server.');
            }).catch(function() {
                throw new Error('Terjadi kesalahan pada server (kode ' + r.status + ').');
            });
        }
        return r.json();
    })
    .then(function(data) {
        setTyping(false);
        addBotMsg(data.reply || 'Maaf, tidak ada jawaban.');
    })
    .catch(function(err) {
        setTyping(false);
        addBotMsg(err.message || 'Gagal terhubung ke server. Silakan coba lagi.');
    })
    .finally(function() {
        document.getElementById('chatSend').disabled = false;
    });
}

function quickAsk(msg) {
    document.getElementById('chatInput').value = msg;
    sendChat();
}
</script>
</body>
</html>
