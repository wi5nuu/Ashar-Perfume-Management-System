@extends('layouts.app')
@section('title', 'Laporan Penjualan Harian')

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4268 100%);
    border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff;
    position: relative; overflow: hidden;
}
.page-header-apms::before { content:''; position:absolute; top:-40px; right:-40px; width:160px; height:160px; background:rgba(255,107,53,.12); border-radius:50%; }
.page-header-apms .breadcrumb { background:transparent; padding:0; margin:0; }
.page-header-apms .breadcrumb-item,
.page-header-apms .breadcrumb-item a { color:rgba(255,255,255,.65); font-size:.82rem; }
.page-header-apms .breadcrumb-item.active { color:rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); }
.kpi-card { border:none; border-radius:14px; padding:1.25rem 1.5rem; background:#fff; box-shadow:0 2px 12px rgba(45,48,71,.07); transition:transform .18s,box-shadow .18s; position:relative; overflow:hidden; }
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 6px 24px rgba(45,48,71,.12); }
.kpi-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.kpi-value { font-size:1.5rem; font-weight:700; line-height:1.1; color:var(--secondary); }
.kpi-label { font-size:.73rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#8a8fa8; margin-top:.2rem; }
.card-apms { border:none; border-radius:16px; box-shadow:0 2px 12px rgba(45,48,71,.07); }
.filter-bar { background:#fff; border-radius:12px; padding:1rem 1.25rem; box-shadow:0 2px 10px rgba(45,48,71,.06); margin-bottom:1.25rem; }
.table-modern { border-collapse:separate; border-spacing:0; width:100%; }
.table-modern thead th { background:#f8f9fc; color:#5a5f7d; font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:.8rem 1rem; border-bottom:2px solid #eef0f7; border-top:none; white-space:nowrap; }
.table-modern tbody tr:hover td { background:#fff9f6; }
.table-modern tbody td { padding:.8rem 1rem; border-bottom:1px solid #f2f3f8; vertical-align:middle; color:#3d4268; font-size:.87rem; }
.table-modern tbody tr:last-child td { border-bottom:none; }
.btn-primary-apms { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); border:none; color:#fff; border-radius:8px; font-weight:600; font-size:.88rem; padding:.5rem 1.25rem; transition:all .2s; box-shadow:0 3px 10px rgba(255,107,53,.25); }
.btn-primary-apms:hover { transform:translateY(-1px); color:#fff; }
.rank-badge { width:28px; height:28px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:700; }
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
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active">Penjualan Harian</li>
                </ol>
            </nav>
            <h4 class="mb-0 mt-1 font-weight-bold">Laporan Penjualan Harian</h4>
            <small style="opacity:.75">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
               class="btn btn-sm btn-outline-light font-weight-600">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="filter-bar mb-3">
        <form method="GET" action="{{ route('reports.daily-sales') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div>
                <label class="mb-1 d-block" style="font-size:.75rem;font-weight:600;color:#5a5f7d;text-transform:uppercase;letter-spacing:.05em">Pilih Tanggal</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" style="min-width:160px">
            </div>
            <div>
                <label class="mb-1 d-block" style="font-size:.75rem;font-weight:600;color:transparent">.</label>
                <button type="submit" class="btn-primary-apms btn btn-sm">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
            </div>
            <div class="ml-auto d-flex gap-2">
                <a href="{{ route('reports.daily-sales', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('reports.daily-sales', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(255,107,53,.1)">
                    <i class="fas fa-chart-line" style="color:var(--primary)"></i>
                </div>
                <div>
                    <div class="kpi-value" style="font-size:1.1rem">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                    <div class="kpi-label">Total Penjualan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(16,185,129,.1)">
                    <i class="fas fa-receipt" style="color:#10b981"></i>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalTransactions }}</div>
                    <div class="kpi-label">Transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(59,130,246,.1)">
                    <i class="fas fa-store" style="color:#3b82f6"></i>
                </div>
                <div>
                    <div class="kpi-value" style="font-size:1rem">Rp {{ number_format($retailSales, 0, ',', '.') }}</div>
                    <div class="kpi-label">Eceran ({{ $retailCount }})</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(245,158,11,.1)">
                    <i class="fas fa-boxes" style="color:#f59e0b"></i>
                </div>
                <div>
                    <div class="kpi-value" style="font-size:1rem">Rp {{ number_format($wholesaleSales, 0, ',', '.') }}</div>
                    <div class="kpi-label">Grosir ({{ $wholesaleCount }})</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    @if($topProducts->count())
    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #f2f3f8">
                <h6 class="mb-0 font-weight-700" style="color:var(--secondary)">
                    <i class="fas fa-fire mr-2" style="color:var(--primary)"></i>Produk Terlaris Hari Ini
                </h6>
                <span class="text-muted" style="font-size:.78rem">Top {{ $topProducts->count() }} produk</span>
            </div>
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Produk</th>
                            <th class="text-right">Qty Terjual</th>
                            <th class="text-right">Total Penjualan</th>
                            <th class="d-none d-md-table-cell" style="width:160px">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $i => $p)
                        @php
                            $pct = $totalSales > 0 ? round($p->total / $totalSales * 100, 1) : 0;
                            $rankColors = ['#f59e0b','#94a3b8','#cd7f32'];
                            $rankBg = ['rgba(245,158,11,.12)','rgba(148,163,184,.12)','rgba(205,127,50,.12)'];
                        @endphp
                        <tr>
                            <td>
                                @if($i < 3)
                                <span class="rank-badge" style="background:{{ $rankBg[$i] }};color:{{ $rankColors[$i] }}">
                                    {{ $i + 1 }}
                                </span>
                                @else
                                <span style="color:#94a3b8;font-size:.85rem;font-weight:600;padding-left:6px">{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:.88rem">{{ $p->product->name ?? '-' }}</div>
                                <div style="font-size:.75rem;color:#8a8fa8">{{ $p->product->sku ?? '' }}</div>
                            </td>
                            <td class="text-right" style="font-weight:700;color:var(--secondary)">
                                {{ number_format($p->qty, 0, ',', '.') }}
                            </td>
                            <td class="text-right" style="font-weight:700;color:#1a7a45">
                                Rp {{ number_format($p->total, 0, ',', '.') }}
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex:1;background:#f1f5f9;border-radius:99px;height:6px;overflow:hidden">
                                        <div style="width:{{ $pct }}%;height:100%;background:var(--primary);border-radius:99px"></div>
                                    </div>
                                    <span style="font-size:.75rem;font-weight:600;color:var(--primary);min-width:36px">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card card-apms">
        <div class="text-center py-5">
            <i class="fas fa-chart-bar" style="font-size:2.5rem;color:#d1d5e0"></i>
            <p class="mt-3 mb-0 font-weight-600" style="color:#3d4268">Tidak ada transaksi</p>
            <small class="text-muted">Belum ada penjualan pada tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</small>
        </div>
    </div>
    @endif

</div>
@endsection
