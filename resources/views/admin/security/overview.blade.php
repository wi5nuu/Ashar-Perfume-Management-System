@extends('layouts.app')
@section('title', 'Keamanan Sistem - APMS')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark"><i class="fas fa-shield-alt mr-2 text-primary"></i>Keamanan Sistem</h3>
            <p class="text-muted">Monitoring keamanan enterprise secara real-time</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="mr-2">Integrity Score:</span>
            <div class="progress" style="width:100px;height:20px;">
                <div class="progress-bar {{ $stats['integrity_score'] >= 80 ? 'bg-success' : ($stats['integrity_score'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                     style="width: {{ $stats['integrity_score'] }}%">
                    {{ $stats['integrity_score'] }}%
                </div>
            </div>
            <form method="POST" action="{{ route('admin.security.cleanup-logs') }}" class="d-inline ml-2">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Bersihkan log > 90 hari?')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row">
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 border-left border-primary border-3" style="border-left:4px solid #007bff!important;">
                <div class="card-body text-center py-3">
                    <i class="fas fa-history fa-2x text-primary mb-1"></i>
                    <h5 class="font-weight-bold mb-0">{{ number_format($stats['total_audit_logs']) }}</h5>
                    <small class="text-muted">Total Log Audit</small>
                    <small class="d-block text-info">{{ number_format($stats['today_audit_logs']) }} hari ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 border-left border-warning border-3" style="border-left:4px solid #ffc107!important;">
                <div class="card-body text-center py-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-1"></i>
                    <h5 class="font-weight-bold mb-0">{{ number_format($stats['suspicious_logins']) }}</h5>
                    <small class="text-muted">Login Mencurigakan</small>
                    <small class="d-block text-warning">{{ $stats['active_today'] }} aktif hari ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 border-left border-danger border-3" style="border-left:4px solid #dc3545!important;">
                <div class="card-body text-center py-3">
                    <i class="fas fa-lock fa-2x text-danger mb-1"></i>
                    <h5 class="font-weight-bold mb-0">{{ number_format($stats['locked_accounts']) }}</h5>
                    <small class="text-muted">Akun Terkunci</small>
                    <small class="d-block text-danger">{{ $stats['blocked_ips'] }} IP diblokir</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100 border-left border-success border-3" style="border-left:4px solid #28a745!important;">
                <div class="card-body text-center py-3">
                    <i class="fas fa-mobile-alt fa-2x text-success mb-1"></i>
                    <h5 class="font-weight-bold mb-0">{{ number_format($stats['two_factor_enabled']) }}</h5>
                    <small class="text-muted">2FA Aktif</small>
                    <small class="d-block text-success">/ {{ $stats['total_users'] }} pengguna</small>
                </div>
            </div>
        </div>
    </div>

    @if($stats['expired_passwords'] > 0)
    <div class="alert alert-warning">
        <i class="fas fa-clock mr-1"></i> {{ $stats['expired_passwords'] }} pengguna memiliki kata sandi kedaluwarsa (&gt;90 hari).
        <a href="{{ route('admin.security.locked-accounts') }}" class="alert-link">Lihat detail</a>
    </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                    <h6 class="font-weight-bold">Log Audit</h6>
                    <p class="text-muted small">Semua perubahan data dalam sistem</p>
                    <a href="{{ route('admin.security.audit-logs') }}" class="btn btn-outline-primary btn-sm px-3">Lihat</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-sign-in-alt fa-3x text-info mb-3"></i>
                    <h6 class="font-weight-bold">Aktivitas Login</h6>
                    <p class="text-muted small">Semua percobaan login pengguna</p>
                    <a href="{{ route('admin.security.login-activities') }}" class="btn btn-outline-info btn-sm px-3">Lihat</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-lock fa-3x text-danger mb-3"></i>
                    <h6 class="font-weight-bold">Akun Terkunci</h6>
                    <p class="text-muted small">Akun yang diblokir karena mencurigakan</p>
                    <a href="{{ route('admin.security.locked-accounts') }}" class="btn btn-outline-danger btn-sm px-3">Lihat</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-ban fa-3x text-secondary mb-3"></i>
                    <h6 class="font-weight-bold">IP Diblokir</h6>
                    <p class="text-muted small">Alamat IP yang diblokir otomatis</p>
                    <a href="{{ route('admin.security.blocked-ips') }}" class="btn btn-outline-secondary btn-sm px-3">Lihat</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                    <h6 class="font-weight-bold">Integritas Data</h6>
                    <p class="text-muted small">Pemeriksaan integritas database</p>
                    <a href="{{ route('admin.security.integrity') }}" class="btn btn-outline-success btn-sm px-3">Cek</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <i class="fas fa-qrcode fa-3x text-purple mb-3" style="color:#6f42c1;"></i>
                    <h6 class="font-weight-bold">2FA Settings</h6>
                    <p class="text-muted small">Autentikasi dua faktor</p>
                    <a href="{{ route('admin.security.two-factor') }}" class="btn btn-outline-purple btn-sm px-3" style="border-color:#6f42c1;color:#6f42c1;">Atur</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
