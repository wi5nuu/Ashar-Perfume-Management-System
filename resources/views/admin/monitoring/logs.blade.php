@extends('layouts.app')
@section('title', 'System Logs - APMS')

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4268 100%);
    border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff;
    position: relative; overflow: hidden;
}
.page-header-apms::before {
    content: ''; position: absolute; top: -40px; right: -40px;
    width: 160px; height: 160px; background: rgba(255,107,53,.12); border-radius: 50%;
}
.page-header-apms .breadcrumb { background: transparent; padding: 0; margin: 0; }
.page-header-apms .breadcrumb-item,
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.65); font-size: .82rem; }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.4); }
.kpi-card {
    border: none; border-radius: 14px; padding: 1.1rem 1.25rem;
    background: #fff; box-shadow: 0 2px 12px rgba(45,48,71,.07);
    transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(45,48,71,.12); }
.kpi-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.kpi-value { font-size: 1.6rem; font-weight: 700; line-height: 1.1; color: var(--secondary); }
.kpi-label { font-size: .73rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #8a8fa8; margin-top: .2rem; }
.filter-bar { background: #fff; border-radius: 12px; padding: 1rem 1.25rem; box-shadow: 0 2px 10px rgba(45,48,71,.06); margin-bottom: 1.25rem; }
.card-apms { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(45,48,71,.07); }
.table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
.table-modern thead th {
    background: #f8f9fc; color: #5a5f7d; font-size: .73rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    padding: .8rem 1rem; border-bottom: 2px solid #eef0f7; border-top: none; white-space: nowrap;
}
.table-modern tbody tr:hover td { background: #fff9f6; }
.table-modern tbody td { padding: .75rem 1rem; border-bottom: 1px solid #f2f3f8; vertical-align: middle; font-size: .85rem; }
.table-modern tbody tr:last-child td { border-bottom: none; }
.log-row-error   { border-left: 3px solid #ef4444 !important; }
.log-row-warning { border-left: 3px solid #f59e0b !important; }
.log-row-info    { border-left: 3px solid #3b82f6 !important; }
.log-row-debug   { border-left: 3px solid #94a3b8 !important; }
.badge-log { padding: .3em .7em; border-radius: 6px; font-size: .72rem; font-weight: 700; letter-spacing: .03em; font-family: monospace; }
.badge-error    { background: #fef0f0; color: #c0392b; }
.badge-critical { background: #fef0f0; color: #7f1d1d; }
.badge-warning  { background: #fff8e6; color: #b45309; }
.badge-info     { background: #e8f0fe; color: #1e40af; }
.badge-debug    { background: #f3f4f6; color: #6b7280; }
.badge-notice   { background: #eff6ff; color: #2563eb; }
.log-message { max-width: 480px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: monospace; font-size: .8rem; color: #3d4268; }
.auto-refresh-badge { background: rgba(16,185,129,.12); color: #059669; padding: .25em .7em; border-radius: 20px; font-size: .72rem; font-weight: 600; }
.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    font-weight: 600; font-size: .85rem; padding: .5rem 1.1rem;
    transition: all .2s; box-shadow: 0 3px 10px rgba(255,107,53,.25);
}
.btn-primary-apms:hover { transform: translateY(-1px); color: #fff; }
</style>

@endpush

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-apms d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item active">System Logs</li>
                </ol>
            </nav>
            <h4 class="mb-0 mt-1 font-weight-bold">
                System Logs
                <span class="auto-refresh-badge ml-2" id="refreshBadge">
                    <i class="fas fa-circle mr-1" style="font-size:.5rem"></i> Live
                </span>
            </h4>
            <small style="opacity:.75">Monitor error, warning, dan info dari aplikasi secara real-time</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-outline-light font-weight-600" id="toggleRefresh" onclick="toggleAutoRefresh()">
                <i class="fas fa-pause mr-1"></i> Pause
            </button>
            @can('admin.monitoring')
            <a href="{{ route('admin.monitoring.logs', ['clear' => 1]) }}"
               class="btn btn-sm btn-outline-light font-weight-600"
               onclick="return confirm('Hapus semua log? Tindakan ini tidak dapat dibatalkan.')">
                <i class="fas fa-trash mr-1"></i> Hapus Log
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible rounded-lg mb-3">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(239,68,68,.1)">
                    <i class="fas fa-times-circle" style="color:#ef4444"></i>
                </div>
                <div>
                    <div class="kpi-value" style="color:#ef4444">{{ $stats['errors'] ?? 0 }}</div>
                    <div class="kpi-label">Error</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(245,158,11,.1)">
                    <i class="fas fa-exclamation-triangle" style="color:#f59e0b"></i>
                </div>
                <div>
                    <div class="kpi-value" style="color:#f59e0b">{{ $stats['warnings'] ?? 0 }}</div>
                    <div class="kpi-label">Warning</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(59,130,246,.1)">
                    <i class="fas fa-info-circle" style="color:#3b82f6"></i>
                </div>
                <div>
                    <div class="kpi-value" style="color:#3b82f6">{{ $stats['info'] ?? 0 }}</div>
                    <div class="kpi-label">Info</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(255,107,53,.1)">
                    <i class="fas fa-percentage" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="kpi-value">{{ $stats['error_rate'] ?? 0 }}%</div>
                    <div class="kpi-label">Error Rate</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.monitoring.logs') }}" id="filterForm">
            <div class="row align-items-end g-2">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Level</label>
                    <select name="level" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Semua Level</option>
                        @foreach($levels as $l)
                        <option value="{{ $l }}" @if(($level ?? '') === $l) selected @endif>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <label class="mb-1" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Cari Pesan</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari pesan error, class, stack trace..."
                               value="{{ $search ?? '' }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary-apms">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <label class="mb-1 d-block" style="font-size:.75rem;font-weight:600;color:transparent">.</label>
                    <div class="d-flex gap-2">
                        @if(($level ?? '') || ($search ?? ''))
                        <a href="{{ route('admin.monitoring.logs') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i> Reset
                        </a>
                        @endif
                        <span class="ml-auto text-muted" style="font-size:.78rem;align-self:center">
                            Total: <strong>{{ $logs['total'] ?? 0 }}</strong> entri
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Log Table --}}
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width:160px">Waktu</th>
                            <th style="width:100px">Level</th>
                            <th>Pesan</th>
                            <th class="d-none d-lg-table-cell" style="width:120px">Channel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs['items'] as $log)
                        @php
                            $lv = strtolower($log['level'] ?? 'debug');
                            $rowClass = match(true) {
                                in_array($lv, ['error','critical','alert','emergency']) => 'log-row-error',
                                $lv === 'warning' => 'log-row-warning',
                                $lv === 'info' => 'log-row-info',
                                default => 'log-row-debug',
                            };
                            $badgeClass = match(true) {
                                in_array($lv, ['critical','alert','emergency']) => 'badge-critical',
                                $lv === 'error' => 'badge-error',
                                $lv === 'warning' => 'badge-warning',
                                $lv === 'notice' => 'badge-notice',
                                $lv === 'info' => 'badge-info',
                                default => 'badge-debug',
                            };
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td style="white-space:nowrap;font-size:.78rem;font-family:monospace;color:#64748b">
                                {{ $log['timestamp'] ?? '-' }}
                            </td>
                            <td>
                                <span class="badge-log {{ $badgeClass }}">{{ strtoupper($log['level'] ?? 'DEBUG') }}</span>
                            </td>
                            <td>
                                <div class="log-message" title="{{ $log['message'] ?? '' }}">
                                    {{ $log['message'] ?? '-' }}
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell" style="font-size:.75rem;color:#94a3b8;font-family:monospace">
                                {{ $log['file'] ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle" style="font-size:2.5rem;color:#10b981"></i>
                                    <p class="mt-3 mb-0 font-weight-600" style="color:#3d4268">Tidak ada log ditemukan</p>
                                    <small class="text-muted">Sistem berjalan normal</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(($logs['last_page'] ?? 1) > 1)
            <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f2f3f8">
                <small class="text-muted">
                    Menampilkan {{ count($logs['items']) }} dari {{ $logs['total'] }} entri
                </small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        @for($i = 1; $i <= $logs['last_page']; $i++)
                        <li class="page-item {{ ($logs['page'] ?? 1) == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ route('admin.monitoring.logs', array_filter(['page' => $i, 'level' => $level ?? '', 'search' => $search ?? ''])) }}">{{ $i }}</a>
                        </li>
                        @endfor
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
var autoRefreshInterval = null;
var isRefreshing = true;

function toggleAutoRefresh() {
    if (isRefreshing) {
        clearInterval(autoRefreshInterval);
        isRefreshing = false;
        document.getElementById('toggleRefresh').innerHTML = '<i class="fas fa-play mr-1"></i> Resume';
        document.getElementById('refreshBadge').style.opacity = '.4';
    } else {
        startAutoRefresh();
        isRefreshing = true;
        document.getElementById('toggleRefresh').innerHTML = '<i class="fas fa-pause mr-1"></i> Pause';
        document.getElementById('refreshBadge').style.opacity = '1';
    }
}

function startAutoRefresh() {
    autoRefreshInterval = setInterval(function() {
        location.reload();
    }, 30000);
}

// Start auto-refresh on page load
startAutoRefresh();
</script>
@endpush
@endsection
