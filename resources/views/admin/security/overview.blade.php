@extends('layouts.app')
@section('title', 'Keamanan Sistem - APMS')

@push('styles')
<style>
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
}
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.page-header-apms::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 140px; height: 140px;
    background: rgba(255,107,53,0.15);
    border-radius: 50%;
}
.breadcrumb-apms { background: transparent; padding: 0; margin: 0; }
.breadcrumb-apms .breadcrumb-item a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.82rem; }
.breadcrumb-apms .breadcrumb-item.active { color: rgba(255,255,255,0.9); font-size: 0.82rem; }
.breadcrumb-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

/* Threat Level */
.threat-banner {
    border-radius: 14px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    border: 1.5px solid;
}
.threat-low      { background: rgba(40,167,69,0.07);  border-color: rgba(40,167,69,0.25);  color: #155724; }
.threat-medium   { background: rgba(255,193,7,0.08);  border-color: rgba(255,193,7,0.3);   color: #856404; }
.threat-high     { background: rgba(253,126,20,0.08); border-color: rgba(253,126,20,0.3);  color: #7d3c00; }
.threat-critical { background: rgba(220,53,69,0.08);  border-color: rgba(220,53,69,0.3);   color: #721c24; }
.threat-indicator {
    width: 14px; height: 14px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    animation: pulse-dot 1.8s infinite;
}
.threat-low .threat-indicator      { background: #28a745; }
.threat-medium .threat-indicator   { background: #ffc107; }
.threat-high .threat-indicator     { background: #fd7e14; }
.threat-critical .threat-indicator { background: #dc3545; }
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0,0,0,0.15); }
    50% { box-shadow: 0 0 0 5px rgba(0,0,0,0); }
}

/* KPI Cards */
.kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border: 1px solid #f0f0f0;
    transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 24px rgba(0,0,0,0.11); }
.kpi-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 12px;
}
.kpi-icon.red    { background: rgba(220,53,69,0.1);   color: #dc3545; }
.kpi-icon.orange { background: rgba(255,107,53,0.1);  color: var(--primary); }
.kpi-icon.yellow { background: rgba(255,193,7,0.12);  color: #d4a00a; }
.kpi-icon.blue   { background: rgba(0,123,255,0.1);   color: #007bff; }
.kpi-icon.green  { background: rgba(40,167,69,0.1);   color: #28a745; }
.kpi-value { font-size: 1.7rem; font-weight: 700; color: var(--secondary); line-height: 1; }
.kpi-label { font-size: 0.77rem; color: #888; margin-top: 4px; }
.kpi-sub   { font-size: 0.72rem; margin-top: 6px; font-weight: 600; }

/* Cards */
.card-modern {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(0,0,0,0.07);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}
.card-header-modern {
    padding: 14px 20px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    display: flex; align-items: center; justify-content: space-between;
}
.table-modern { width: 100%; border-collapse: collapse; }
.table-modern thead th {
    background: #f8f9fc;
    padding: 10px 16px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 2px solid #eef0f4;
    white-space: nowrap;
}
.table-modern tbody td {
    padding: 11px 16px;
    font-size: 0.83rem;
    color: #333;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: middle;
}
.table-modern tbody tr:hover { background: #fafbff; }
.table-modern tbody tr:last-child td { border-bottom: none; }

/* Integrity Score */
.integrity-ring {
    position: relative;
    width: 80px; height: 80px;
    flex-shrink: 0;
}
.integrity-ring svg { transform: rotate(-90deg); }
.integrity-ring .ring-text {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    line-height: 1.1;
}

/* Quick Action Cards */
.quick-action-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eef0f4;
    padding: 16px;
    text-align: center;
    transition: all .18s;
    cursor: pointer;
    text-decoration: none;
    display: block;
    color: inherit;
}
.quick-action-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    text-decoration: none;
    color: inherit;
}
.quick-action-icon {
    width: 44px; height: 44px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    margin: 0 auto 10px;
}

.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    padding: 7px 16px; font-weight: 600; font-size: 0.83rem;
    transition: all .18s; display: inline-flex; align-items: center;
}
.btn-primary-apms:hover { background: linear-gradient(135deg, var(--primary-dark), #c94d22); color: #fff; transform: translateY(-1px); }

/* Event badges */
.evt-badge {
    display: inline-block; padding: 2px 9px; border-radius: 20px;
    font-size: 0.7rem; font-weight: 600;
}
.evt-login    { background: rgba(40,167,69,0.1);  color: #28a745; }
.evt-fail     { background: rgba(220,53,69,0.1);  color: #dc3545; }
.evt-block    { background: rgba(255,107,53,0.1); color: var(--primary); }
.evt-suspect  { background: rgba(255,193,7,0.12); color: #856404; }
.evt-unlock   { background: rgba(0,123,255,0.1);  color: #007bff; }
</style>

@endpush

@section('content')
<div class="container-fluid pt-3">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-apms">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Keamanan Sistem</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div>
                <h3 class="mb-1 font-weight-bold" style="font-size:1.45rem;">
                    <i class="fas fa-shield-alt mr-2" style="color:var(--primary);"></i>Keamanan Sistem
                </h3>
                <p class="mb-0" style="color:rgba(255,255,255,0.65); font-size:0.85rem;">
                    Monitoring keamanan enterprise real-time
                </p>
            </div>
            <div class="d-none d-md-flex align-items-center" style="gap:8px;">
                <form method="POST" action="{{ route('admin.security.cleanup-logs') }}" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-sm"
                        style="background:rgba(255,193,7,0.2); color:#ffc107; border-radius:8px; border:1px solid rgba(255,193,7,0.3); font-size:0.82rem; font-weight:600;"
                        onclick="confirmCleanup(this.form)">
                        <i class="fas fa-trash-alt mr-1"></i> Bersihkan Log
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert border-0 mb-4" style="background:rgba(40,167,69,0.1); color:#155724; border-radius:10px; border-left:4px solid #28a745 !important;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- Threat Level Banner --}}
    @php
        $score = $stats['integrity_score'];
        $threatLevel = $score >= 80 ? 'low' : ($score >= 60 ? 'medium' : ($score >= 40 ? 'high' : 'critical'));
        $threatLabels = ['low' => 'RENDAH', 'medium' => 'SEDANG', 'high' => 'TINGGI', 'critical' => 'KRITIS'];
        $threatDescs  = [
            'low'      => 'Sistem berjalan normal, tidak ada ancaman terdeteksi.',
            'medium'   => 'Beberapa aktivitas mencurigakan terdeteksi, pantau terus.',
            'high'     => 'Ancaman terdeteksi! Tindakan segera diperlukan.',
            'critical' => 'Sistem dalam kondisi kritis! Segera ambil tindakan pencegahan.',
        ];
    @endphp
    <div class="threat-banner threat-{{ $threatLevel }} mb-4">
        <div class="d-flex align-items-center">
            <span class="threat-indicator"></span>
            <div>
                <div class="font-weight-bold" style="font-size:0.9rem;">
                    THREAT LEVEL: {{ $threatLabels[$threatLevel] }}
                </div>
                <div style="font-size:0.8rem; opacity:.85; margin-top:2px;">{{ $threatDescs[$threatLevel] }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap:16px;">
            <div class="integrity-ring">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="33" fill="none" stroke="#e0e0e0" stroke-width="7"/>
                    <circle cx="40" cy="40" r="33" fill="none"
                        stroke="{{ $score >= 80 ? '#28a745' : ($score >= 60 ? '#ffc107' : ($score >= 40 ? '#fd7e14' : '#dc3545')) }}"
                        stroke-width="7"
                        stroke-dasharray="{{ round(2 * 3.14159 * 33 * $score / 100) }} {{ round(2 * 3.14159 * 33) }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="ring-text">
                    <div class="font-weight-bold" style="font-size:0.95rem;">{{ $score }}%</div>
                    <div style="font-size:0.6rem; opacity:.7;">Integrity</div>
                </div>
            </div>
            <div>
                <div style="font-size:0.78rem; font-weight:600;">Integrity Score</div>
                <div style="font-size:0.72rem; opacity:.75;">System Health Check</div>
            </div>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon red"><i class="fas fa-history"></i></div>
                <div class="kpi-value">{{ number_format($stats['total_audit_logs']) }}</div>
                <div class="kpi-label">Total Log Audit</div>
                <div class="kpi-sub text-info">{{ number_format($stats['today_audit_logs']) }} hari ini</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon yellow"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="kpi-value">{{ number_format($stats['suspicious_activities']) }}</div>
                <div class="kpi-label">Aktivitas Mencurigakan</div>
                <div class="kpi-sub text-warning">Perlu ditinjau</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="fas fa-ban"></i></div>
                <div class="kpi-value">{{ number_format($stats['blocked_ips']) }}</div>
                <div class="kpi-label">IP Diblokir</div>
                <div class="kpi-sub" style="color:var(--primary);">Aktif saat ini</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon green"><i class="fas fa-users"></i></div>
                <div class="kpi-value">{{ number_format($stats['active_users_today']) }}</div>
                <div class="kpi-label">Pengguna Aktif Hari Ini</div>
                <div class="kpi-sub text-success">Login berhasil</div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Security Events --}}
        <div class="col-lg-8 mb-4">
            <div class="card-modern">
                <div class="card-header-modern">
                    <span class="font-weight-bold" style="color:var(--secondary); font-size:0.9rem;">
                        <i class="fas fa-clock mr-2" style="color:var(--primary);"></i>Event Keamanan Terbaru
                    </span>
                    <a href="{{ route('admin.security.login-activities') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:7px; font-size:0.75rem;">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Event</th>
                                <th>IP Address</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div style="font-size:0.82rem; font-weight:600;">{{ $activity->created_at->format('H:i') }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ $activity->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    <div style="font-size:0.83rem; font-weight:600;">{{ $activity->user?->name ?? 'Guest' }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ $activity->user?->email ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($activity->is_suspicious)
                                        <span class="evt-badge evt-suspect"><i class="fas fa-exclamation-triangle mr-1"></i>Mencurigakan</span>
                                    @else
                                        <span class="evt-badge evt-login"><i class="fas fa-sign-in-alt mr-1"></i>Login</span>
                                    @endif
                                </td>
                                <td><code style="font-size:0.78rem; background:#f0f0f0; padding:2px 6px; border-radius:4px;">{{ $activity->ip_address }}</code></td>
                                <td>
                                    @if($activity->is_suspicious)
                                        <span class="evt-badge evt-suspect">Mencurigakan</span>
                                    @else
                                        <span class="evt-badge evt-login">Normal</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-shield-alt text-muted" style="font-size:2rem; opacity:.25;"></i>
                                    <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;">Tidak ada event keamanan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4 mb-4">
            {{-- Quick Actions --}}
            <div class="card-modern mb-4">
                <div class="card-header-modern">
                    <span class="font-weight-bold" style="color:var(--secondary); font-size:0.9rem;">
                        <i class="fas fa-bolt mr-2" style="color:var(--primary);"></i>Quick Actions
                    </span>
                </div>
                <div style="padding:14px;">
                    <div class="row" style="margin:-4px;">
                        <div class="col-6 p-1">
                            <a href="{{ route('admin.security.blocked-ips') }}" class="quick-action-card">
                                <div class="quick-action-icon" style="background:rgba(220,53,69,0.1); color:#dc3545;">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div style="font-size:0.78rem; font-weight:600; color:#333;">Blokir IP</div>
                            </a>
                        </div>
                        <div class="col-6 p-1">
                            <a href="{{ route('admin.security.login-activities') }}" class="quick-action-card">
                                <div class="quick-action-icon" style="background:rgba(0,123,255,0.1); color:#007bff;">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div style="font-size:0.78rem; font-weight:600; color:#333;">Login Log</div>
                            </a>
                        </div>
                        <div class="col-6 p-1">
                            <a href="{{ route('admin.security.integrity') }}" class="quick-action-card">
                                <div class="quick-action-icon" style="background:rgba(40,167,69,0.1); color:#28a745;">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div style="font-size:0.78rem; font-weight:600; color:#333;">Integritas</div>
                            </a>
                        </div>
                        <div class="col-6 p-1">
                            <a href="{{ route('admin.security.two-factor') }}" class="quick-action-card">
                                <div class="quick-action-icon" style="background:rgba(111,66,193,0.1); color:#6f42c1;">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div style="font-size:0.78rem; font-weight:600; color:#333;">2FA Settings</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Stats --}}
            <div class="card-modern">
                <div class="card-header-modern">
                    <span class="font-weight-bold" style="color:var(--secondary); font-size:0.9rem;">
                        <i class="fas fa-chart-pie mr-2" style="color:var(--primary);"></i>Ringkasan Keamanan
                    </span>
                </div>
                <div style="padding:16px;">
                    @php
                        $secItems = [
                            ['label' => 'Login Berhasil Hari Ini', 'value' => $stats['successful_logins_today'] ?? 0, 'color' => '#28a745', 'icon' => 'fa-check-circle'],
                            ['label' => 'Login Gagal 24 Jam', 'value' => $stats['failed_logins_today'] ?? 0, 'color' => '#dc3545', 'icon' => 'fa-times-circle'],
                            ['label' => 'Akun Terkunci', 'value' => $stats['locked_accounts'] ?? 0, 'color' => '#ffc107', 'icon' => 'fa-lock'],
                            ['label' => 'IP Diblokir', 'value' => $stats['blocked_ips'] ?? 0, 'color' => '#FF6B35', 'icon' => 'fa-ban'],
                        ];
                    @endphp
                    @foreach($secItems as $item)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div style="width:28px; height:28px; background:{{ $item['color'] }}18; border-radius:7px; display:flex; align-items:center; justify-content:center; margin-right:10px;">
                                <i class="fas {{ $item['icon'] }}" style="color:{{ $item['color'] }}; font-size:0.75rem;"></i>
                            </div>
                            <span style="font-size:0.82rem; color:#555;">{{ $item['label'] }}</span>
                        </div>
                        <span class="font-weight-bold" style="color:{{ $item['color'] }}; font-size:0.9rem;">{{ number_format($item['value']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function confirmCleanup(form) {
    if (confirm('Bersihkan log audit lebih dari 90 hari? Tindakan ini tidak dapat dibatalkan.')) {
        form.submit();
    }
}
</script>
@endpush
@endsection
