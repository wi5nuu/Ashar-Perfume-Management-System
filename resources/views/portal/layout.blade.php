<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Portal') - APMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root { --apms-primary: #FF6B35; }
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-portal { background: var(--apms-primary); }
        .navbar-portal .navbar-brand, .navbar-portal .nav-link { color: #fff !important; }
        .navbar-portal .nav-link:hover { opacity: 0.85; }
        .card-portal { border: none; border-radius: .5rem; box-shadow: 0 0 15px rgba(0,0,0,.05); }
        .btn-portal { background: var(--apms-primary); color: #fff; border: none; }
        .btn-portal:hover { background: #e55a2b; color: #fff; }
        .badge-status { font-size: .85rem; padding: .35em .65em; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-portal mb-4">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="{{ route('portal.dashboard', $token) }}">
                <i class="fas fa-store mr-2"></i> {{ $customer->name }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#portalNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="portalNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('portal.dashboard', $token) }}">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('portal.orders') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('portal.orders', $token) }}">
                            <i class="fas fa-box mr-1"></i> Pesanan
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('portal.statement') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('portal.statement', $token) }}">
                            <i class="fas fa-file-invoice mr-1"></i> Riwayat
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @yield('content')
    </div>

    <footer class="text-center text-muted py-4 mt-5">
        <small>&copy; {{ date('Y') }} Ashar Parfum. All rights reserved.</small>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
