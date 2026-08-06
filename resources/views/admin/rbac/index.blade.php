@extends('layouts.app')
@section('title', 'Manajemen Roles & Permissions - APMS')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-user-shield mr-2"></i>Manajemen Roles &amp; Permissions</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.7);font-size:.88rem;">
                        Kelola hak akses pengguna berdasarkan peran dalam sistem
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Roles &amp; Permissions</li>
                    </ol>
                </div>
                <a href="{{ route('admin.rbac.users.all') }}" class="btn btn-primary-apms d-none d-md-flex align-items-center">
                    <i class="fas fa-users mr-2"></i> Semua Pengguna
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-3">
    <x-alert />

    {{-- KPI Row --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="kpi-value">{{ $roles->count() }}</div>
                    <div class="kpi-label">Total Roles</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-users"></i></div>
                <div>
                    <div class="kpi-value">{{ $roles->sum(fn($r) => $r->users_count ?? 0) }}</div>
                    <div class="kpi-label">Total Pengguna</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-key"></i></div>
                <div>
                    <div class="kpi-value">{{ $roles->sum(fn($r) => $r->permissions_count ?? 0) }}</div>
                    <div class="kpi-label">Total Permissions</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon gold"><i class="fas fa-crown"></i></div>
                <div>
                    <div class="kpi-value">{{ $adminPusatCount ?? 0 }}</div>
                    <div class="kpi-label">Super Admins</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Role Cards Grid --}}
    @php
        $adminPusatRole  = $roles->firstWhere('slug', 'admin_pusat');
        $adminCabangRole = $roles->firstWhere('slug', 'admin');
        $roleConfig = [
            'owner'       => ['color' => 'gold',   'icon' => 'fa-crown',        'label' => 'Owner',        'desc' => 'Akses penuh ke seluruh sistem', 'badge' => 'Sistem'],
            'admin_pusat' => ['color' => 'purple',  'icon' => 'fa-building',     'label' => 'Admin Pusat',  'desc' => 'Mengelola seluruh cabang & operasional', 'badge' => 'Pusat'],
            'admin'       => ['color' => 'blue',    'icon' => 'fa-user-tie',     'label' => 'Admin Cabang', 'desc' => 'Mengelola operasional cabang', 'badge' => 'Cabang'],
            'kasir'       => ['color' => 'green',   'icon' => 'fa-cash-register','label' => 'Kasir',        'desc' => 'Proses transaksi & pembayaran', 'badge' => 'Operasional'],
            'gudang'      => ['color' => 'teal',    'icon' => 'fa-warehouse',    'label' => 'Gudang',       'desc' => 'Kelola stok & inventori produk', 'badge' => 'Operasional'],
            'supervisor'  => ['color' => 'orange',  'icon' => 'fa-user-check',   'label' => 'Supervisor',   'desc' => 'Pengawasan & laporan cabang', 'badge' => 'Pengawas'],
        ];
    @endphp

    <div class="row">
        @foreach($roles as $role)
        @php
            $cfg = $roleConfig[$role->slug] ?? ['color' => 'orange', 'icon' => 'fa-user', 'label' => $role->name, 'desc' => $role->description ?? 'Peran pengguna', 'badge' => 'Custom'];
            $userCount = $role->users_count ?? 0;
            if ($role->slug === 'admin_pusat') $userCount = $adminPusatCount ?? $userCount;
        @endphp
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="role-card role-{{ $cfg['color'] }}">
                <div class="role-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-avatar mr-3">
                            <i class="fas {{ $cfg['icon'] }}"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1" style="color:var(--secondary); font-size:0.95rem;">{{ $cfg['label'] }}</h6>
                            <span class="badge-role badge-role-{{ $cfg['color'] }}">{{ $cfg['badge'] }}</span>
                        </div>
                    </div>
                    @if($role->is_system ?? false)
                    <span class="badge badge-secondary" style="font-size:0.68rem; border-radius:6px;">System</span>
                    @endif
                </div>
                <div class="role-card-body">
                    <p class="text-muted mb-3" style="font-size:0.82rem;">{{ $cfg['desc'] }}</p>
                    <div class="role-stat-row">
                        <div class="role-stat-box">
                            <div class="stat-num">{{ $userCount }}</div>
                            <div class="stat-lbl"><i class="fas fa-user mr-1"></i>Pengguna</div>
                        </div>
                        <div class="role-stat-box">
                            <div class="stat-num">{{ $role->permissions_count ?? 0 }}</div>
                            <div class="stat-lbl"><i class="fas fa-key mr-1"></i>Izin</div>
                        </div>
                    </div>
                </div>
                <div class="role-card-footer">
                    <div class="d-flex gap-2" style="gap:8px;">
                        <a href="{{ route('admin.rbac.show', $role) }}" class="btn btn-primary-apms btn-sm flex-fill text-center">
                            <i class="fas fa-key mr-1"></i> Edit Permissions
                        </a>
                        <a href="{{ route('admin.rbac.users', $role) }}" class="btn btn-outline-secondary btn-sm flex-fill text-center" style="border-radius:8px; font-size:0.83rem; font-weight:600;">
                            <i class="fas fa-users mr-1"></i> Pengguna
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
