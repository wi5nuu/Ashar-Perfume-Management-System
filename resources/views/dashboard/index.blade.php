@extends('layouts.app')

@section('title', 'Dashboard')

@php
$user = auth()->user();
$hour = (int) now()->format('H');
$greeting = $hour < 10 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
@endphp

@push('styles')
<style>
/* KPI small-box equal height */
.kpi-row .small-box {
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.kpi-row .small-box .inner {
    padding-bottom: 0;
}
.kpi-row .small-box .inner h3 {
    font-size: 1rem !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
    margin-bottom: 4px;
}
.kpi-row .small-box .inner p {
    font-size: 0.78rem;
    margin-bottom: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-row .small-box > .icon {
    top: 8px;
    right: 8px;
}
.kpi-row .small-box > .icon i {
    font-size: 3rem;
    line-height: 1;
}

/* Greeting banner */
.dash-greeting {
    background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%);
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
}
.dash-greeting::after {
    content: '';
    position: absolute;
    right: -20px; top: -20px;
    width: 120px; height: 120px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    pointer-events: none;
}
.dash-greeting::before {
    content: '';
    position: absolute;
    right: 40px; bottom: -30px;
    width: 80px; height: 80px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
    pointer-events: none;
}

/* Stock alert items */
.stock-item {
    display: flex;
    align-items: center;
    padding: 10px 18px;
    border-bottom: 1px solid #f8fafc;
    gap: 12px;
    transition: background 0.15s;
}
.stock-item:last-child { border-bottom: none; }
.stock-item:hover { background: #fafafa; }
.stock-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(245,158,11,0.1);
    display: flex; align-items: center; justify-content: center;
    color: #f59e0b;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.stock-name { font-size: 0.82rem; font-weight: 600; color: #334155; }
.stock-qty { font-size: 0.72rem; color: #ef4444; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ===== GREETING BANNER ===== --}}
    <div class="dash-greeting">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px; position:relative; z-index:1;">
            <div>
                <h4 class="mb-1 font-weight-bold text-white" style="font-size:1.1rem;">{{ $greeting }}, {{ $user->name }}!</h4>
                <p class="mb-0" style="color:rgba(255,255,255,0.85); font-size:0.8rem;">
                    <i class="fas fa-store mr-1"></i> {{ $user->branch?->name ?? 'Pusat' }}
                    <span class="mx-1" style="opacity:0.5;">·</span>
                    {{ now()->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
            @if(!$user->isOwner())
            <div class="d-flex align-items-center" style="gap:8px;">
                @if($activeShift)
                    <span class="badge" style="background:rgba(255,255,255,0.2); color:#fff; padding:6px 14px; border-radius:8px; font-size:0.78rem; font-weight:600;">
                        <i class="fas fa-circle mr-1" style="color:#4ade80; font-size:0.5rem;"></i> Shift Aktif
                    </span>
                @else
                    <span class="badge" style="background:rgba(0,0,0,0.15); color:rgba(255,255,255,0.85); padding:6px 14px; border-radius:8px; font-size:0.78rem;">
                        <i class="fas fa-circle mr-1" style="color:rgba(255,255,255,0.4); font-size:0.5rem;"></i> Shift Tutup
                    </span>
                    @can('manage_transactions')
                    <a href="{{ route('shifts.index') }}" class="btn btn-sm" style="background:#fff; color:#FF6B35; border-radius:8px; font-size:0.78rem; font-weight:600; padding:6px 14px;">
                        <i class="fas fa-play mr-1"></i> Buka Shift
                    </a>
                    @endcan
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ===== KPI STAT BOXES ===== --}}
    @can('transactions.view')
    <div class="row kpi-row">
        <div class="col-6 col-lg-2">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3 style="font-size:1.2rem;">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    <p>Eceran Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <a href="{{ route('transactions.index') }}" class="small-box-footer">
                    Lihat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3 style="font-size:1.2rem;">Rp {{ number_format($wholesaleSalesToday, 0, ',', '.') }}</h3>
                    <p>Grosir Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <a href="{{ route('wholesale.index') }}" class="small-box-footer">
                    Lihat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ $todayTransactions ?? 0 }}</h3>
                    <p>Total Transaksi</p>
                </div>
                <div class="icon"><i class="fas fa-cash-register"></i></div>
                <a href="{{ route('transactions.index') }}" class="small-box-footer">
                    Lihat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $totalCustomers ?? 0 }}</h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('customers.index') }}" class="small-box-footer">
                    Lihat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h3>{{ $lowStockProductsCount ?? 0 }}</h3>
                    <p>Stok Menipis</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('inventory.index') }}" class="small-box-footer">
                    Lihat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="small-box bg-gradient-teal">
                <div class="inner">
                    <h3 style="font-size:1.1rem;">Rp {{ number_format($monthSales, 0, ',', '.') }}</h3>
                    <p>Penjualan Bulan Ini</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <a href="{{ route('reports.sales') }}" class="small-box-footer">
                    Lihat <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    @endcan

    @if(auth()->user()->can('reports.view'))
    {{-- Full Dashboard for roles with reports --}}

    {{-- Period Comparison + Payment Distribution --}}
    <div class="row mb-3">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card card-apms h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt mr-1 text-primary"></i> Perbandingan Periode
                    </h3>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-sm btn-outline-primary active" style="border-radius:6px 0 0 6px; font-size:0.75rem;">
                            <input type="radio" name="comp_mode" value="mom" checked> MoM
                        </label>
                        <label class="btn btn-sm btn-outline-primary" style="border-radius:0 6px 6px 0; font-size:0.75rem;">
                            <input type="radio" name="comp_mode" value="yoy"> YoY
                        </label>
                    </div>
                </div>
                <div class="card-body py-3 px-3" id="comparison-body">
                    <div class="text-center text-muted py-4" id="comparison-loading">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...
                    </div>
                    <div id="comparison-content" style="display:none;">
                        <div id="comparison-cards" class="row"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-apms h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-1 text-info"></i> Distribusi Pembayaran
                    </h3>
                </div>
                <div class="card-body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-center" style="gap:16px;">
                        <canvas id="paymentChart" height="90" style="max-height:90px;max-width:90px;flex-shrink:0;"></canvas>
                        <div style="flex:1;min-width:0;">
                            <canvas id="paymentBarChart" height="90" style="width:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Chart + Recent Transactions --}}
    <div class="row">
        <div class="col-lg-8 mb-3 mb-lg-0">

            {{-- Sales Chart --}}
            <div class="card card-apms mb-3 chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1 text-success"></i> Grafik Penjualan {{ date('Y') }}
                    </h3>
                    <button type="button" class="btn btn-sm btn-light" data-card-widget="collapse" style="border-radius:8px; font-size:0.75rem; padding:4px 10px;">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div style="position:relative; height:200px;">
                        <canvas id="salesChart" style="width:100%; max-height:200px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions Table --}}
            @can('transactions.view')
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-receipt mr-1 text-warning"></i> Transaksi Terbaru
                    </h3>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px; font-size:0.75rem; padding:5px 12px;">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.8rem;">
                            <thead class="thead bg-light">
                                <tr>
                                     <th style="min-width:130px; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8;">Invoice</th>
                                     <th style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8;">Pelanggan</th>
                                     <th style="white-space:nowrap; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8;">Total</th>
                                     <th style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8;">Metode</th>
                                     <th style="max-width:110px; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8;">Kasir</th>
                                     <th style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td style="padding:8px 12px;">
                                        <a href="{{ route('transactions.show', $transaction->id) }}" class="font-weight-600 d-inline-block text-truncate" style="color:#FF6B35; font-size:0.78rem; max-width:140px;" title="{{ $transaction->invoice_number }}">
                                            {{ $transaction->invoice_number }}
                                        </a>
                                    </td>
                                    <td style="padding:8px 12px; font-size:0.8rem; color:#334155;">{{ $transaction->customer->name ?? 'Umum' }}</td>
                                    <td style="padding:8px 12px; font-size:0.8rem; font-weight:600; white-space:nowrap; color:#1e293b;">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td style="padding:8px 12px;">
                                        <span class="badge" style="background:#f1f5f9; color:#475569; border-radius:6px; font-size:0.65rem;">
                                            {{ strtoupper($transaction->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="text-truncate" style="padding:8px 12px; max-width:110px; font-size:0.78rem; color:#64748b;" title="{{ $transaction->user?->name ?? '-' }}">{{ $transaction->user?->name ?? '-' }}</td>
                                    <td style="padding:8px 12px;">
                                        @if($transaction->paid_amount >= $transaction->total_amount)
                                            <span class="badge badge-success" style="border-radius:6px; font-size:0.65rem;">Lunas</span>
                                        @else
                                            <span class="badge badge-warning" style="border-radius:6px; font-size:0.65rem;">Hutang</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox d-block mb-1" style="font-size:1.5rem; opacity:0.3;"></i>
                                        <small>Belum ada transaksi hari ini</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        {{-- Right sidebar: Stock Alerts + Top Products --}}
        <div class="col-lg-4">
            @can('inventory.view')
            <div class="card card-apms mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Peringatan Stok
                    </h3>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-danger" style="border-radius:8px; font-size:0.72rem; padding:4px 10px;">
                        Lihat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($lowStockAlerts as $alert)
                    <div class="stock-item">
                        <div class="stock-icon" style="background:rgba(245,158,11,0.1); color:#d97706;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="stock-name text-truncate">{{ $alert->name }}</div>
                            <div class="stock-qty">Stok: {{ number_format($alert->bulk_stock_ml, 0) }}ml (min: {{ $alert->minimum_stock }}ml)</div>
                        </div>
                        <span class="badge badge-warning" style="border-radius:6px; flex-shrink:0;">{{ number_format($alert->bulk_stock_ml, 0) }}ml</span>
                    </div>
                    @empty
                    @endforelse
                    @forelse($expiringAlerts as $alert)
                    <div class="stock-item">
                        <div class="stock-icon" style="background:rgba(239,68,68,0.1); color:#dc2626;">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="stock-name text-truncate">{{ $alert->product?->name ?? 'Produk dihapus' }}</div>
                            <div class="stock-qty">
                                Exp: {{ $alert->expiration_date ? \Carbon\Carbon::parse($alert->expiration_date)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        @if($alert->expiration_date)
                        <span class="badge badge-danger" style="border-radius:6px; flex-shrink:0; font-size:0.62rem;">
                            {{ \Carbon\Carbon::parse($alert->expiration_date)->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                    @empty
                    @if($lowStockAlerts->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle d-block mb-1" style="font-size:1.4rem; color:#10b981;"></i>
                        <small>Semua stok aman</small>
                    </div>
                    @endif
                    @endforelse
                </div>
            </div>
            @endcan

            @can('reports.view')
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-fire mr-1 text-warning"></i> Produk Terlaris
                    </h3>
                </div>
                <div class="card-body p-0">
                    @forelse($topProducts as $index => $product)
                    <div class="stock-item">
                        <div class="kpi-icon" style="width:32px; height:32px; border-radius:8px; font-size:0.8rem; font-weight:700;
                            background:{{ ['rgba(255,107,53,0.1)','rgba(59,130,246,0.1)','rgba(16,185,129,0.1)','rgba(245,158,11,0.1)','rgba(139,92,246,0.1)'][$index % 5] }};
                            color:{{ ['#FF6B35','#2563eb','#059669','#d97706','#7c3aed'][$index % 5] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            {{ $index + 1 }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="stock-name text-truncate">{{ $product->name }}</div>
                            <div style="font-size:0.7rem; color:#94a3b8;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                        </div>
                        <span class="badge" style="background:rgba(139,92,246,0.1); color:#7c3aed; border-radius:6px; flex-shrink:0; font-size:0.7rem;">
                            {{ $product->total_sold }} pcs
                        </span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box-open d-block mb-1" style="font-size:1.4rem; opacity:0.3;"></i>
                        <small>Belum ada data</small>
                    </div>
                    @endforelse
                </div>
            </div>
            @endcan
            @can('expenses.view')
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-wallet mr-1 text-success"></i> Ringkasan Keuangan
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="row text-center" style="gap:0;">
                        <div class="col-4">
                            <div style="font-size:0.8rem; font-weight:700; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Rp {{ number_format($periodSales, 0, ',', '.') }}</div>
                            <div style="font-size:0.65rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Revenue</div>
                        </div>
                        <div class="col-4" style="border-left:1px solid #f1f5f9; border-right:1px solid #f1f5f9;">
                            <div style="font-size:0.8rem; font-weight:700; color:#ef4444; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Rp {{ number_format($periodExpenses, 0, ',', '.') }}</div>
                            <div style="font-size:0.68rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Expense</div>
                        </div>
                        <div class="col-4">
                            <div style="font-size:0.8rem; font-weight:700; color:#10b981; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Rp {{ number_format($profit, 0, ',', '.') }}</div>
                            <div style="font-size:0.65rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Laba</div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
    @else
    {{-- Compact Dashboard (roles without reports.view) --}}
    <div class="row">
        <div class="col-lg-7">
            @can('transactions.view')
            <div class="card card-apms mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-receipt mr-1 text-warning"></i> Transaksi Terbaru
                    </h3>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px; font-size:0.72rem; padding:4px 10px;">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentTransactions->take(5) as $t)
                    <a href="{{ route('transactions.show', $t->id) }}" class="stock-item" style="display:flex; text-decoration:none; color:inherit;">
                        <div class="stock-icon" style="background:rgba(255,107,53,0.08); color:#FF6B35;">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="stock-name text-truncate" style="color:#FF6B35;">{{ $t->invoice_number }}</div>
                            <div style="font-size:0.7rem; color:#94a3b8;">{{ $t->customer?->name ?? 'Umum' }} · {{ $t->created_at?->format('H:i') ?? '-' }}</div>
                        </div>
                        <div class="text-right" style="flex-shrink:0;">
                            <div style="font-size:0.82rem; font-weight:700; color:#1e293b;">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</div>
                            @if($t->paid_amount >= $t->total_amount)
                                <span class="badge badge-success" style="border-radius:6px; font-size:0.62rem;">Lunas</span>
                            @else
                                <span class="badge badge-warning" style="border-radius:6px; font-size:0.62rem;">Hutang</span>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox d-block mb-1" style="font-size:1.5rem; opacity:0.3;"></i>
                        <small>Belum ada transaksi hari ini</small>
                    </div>
                    @endforelse
                </div>
            </div>
            @endcan

            @can('expenses.view')
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-coins mr-1 text-danger"></i> Biaya Terbaru
                    </h3>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-danger" style="border-radius:8px; font-size:0.72rem; padding:4px 10px;">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentExpenses->take(5) as $e)
                    <div class="stock-item">
                        <div class="stock-icon" style="background:rgba(239,68,68,0.08); color:#ef4444;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="stock-name text-truncate">{{ $e->category->name ?? 'Umum' }}</div>
                            <div style="font-size:0.7rem; color:#94a3b8;">{{ $e->description ?? ($e->date ? $e->date->format('d/m/Y') : '-') }}</div>
                        </div>
                        <div style="font-size:0.82rem; font-weight:700; color:#ef4444; flex-shrink:0;">
                            Rp {{ number_format($e->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle d-block mb-1" style="font-size:1.5rem; color:#10b981; opacity:0.7;"></i>
                        <small>Belum ada biaya hari ini</small>
                    </div>
                    @endforelse
                </div>
            </div>
            @endcan
        </div>

        <div class="col-lg-5">
            @can('stock_requests.view')
            <div class="card card-apms mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list mr-1 text-info"></i> Permintaan Stok
                    </h3>
                    @can('stock_requests.create')
                    <a href="{{ route('stock-requests.create') }}" class="btn btn-sm btn-outline-info" style="border-radius:8px; font-size:0.72rem; padding:4px 10px;">
                        <i class="fas fa-plus mr-1"></i> Baru
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @php
                        $srStatuses = ['pending', 'approved', 'shipped', 'received'];
                        $srBg = ['pending'=>'rgba(245,158,11,0.1)','approved'=>'rgba(59,130,246,0.1)','shipped'=>'rgba(255,107,53,0.1)','received'=>'rgba(16,185,129,0.1)'];
                        $srColor = ['pending'=>'#d97706','approved'=>'#2563eb','shipped'=>'#FF6B35','received'=>'#059669'];
                        $srIcons = ['pending'=>'fa-clock','approved'=>'fa-check','shipped'=>'fa-truck','received'=>'fa-box-open'];
                        $srLabels = ['pending'=>'Pending','approved'=>'Disetujui','shipped'=>'Dikirim','received'=>'Diterima'];
                        $srColors = ['pending'=>'warning','approved'=>'info','shipped'=>'primary','received'=>'success'];
                    @endphp
                    <div class="row mb-3" style="margin:0 -4px;">
                        @foreach($srStatuses as $s)
                        <div class="col-3" style="padding:0 4px;">
                            <div style="background:{{ $srBg[$s] }}; border-radius:10px; padding:10px 6px; text-align:center;">
                                <i class="fas {{ $srIcons[$s] }}" style="color:{{ $srColor[$s] }}; font-size:0.9rem;"></i>
                                <div style="font-weight:700; font-size:0.95rem; color:#1e293b;">{{ $stockRequestStats[$s] ?? 0 }}</div>
                                <div style="font-size:0.6rem; color:#64748b; font-weight:600;">{{ $srLabels[$s] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @forelse($recentStockRequests->take(4) as $sr)
                    <a href="{{ route('stock-requests.show', $sr) }}" class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #f8fafc; text-decoration:none; color:inherit;">
                        <div>
                            <div style="font-size:0.8rem; font-weight:600; color:#334155;">{{ $sr->request_number }}</div>
                            <div style="font-size:0.7rem; color:#94a3b8;">{{ $sr->requester->name ?? '-' }}</div>
                        </div>
                        <span class="badge badge-{{ $srColors[$sr->status] ?? 'secondary' }}" style="border-radius:6px;">
                            {{ $srLabels[$sr->status] ?? ucfirst($sr->status) }}
                        </span>
                    </a>
                    @empty
                    <div class="text-center text-muted py-3">
                        <small>Belum ada permintaan stok</small>
                    </div>
                    @endforelse
                </div>
            </div>
            @endcan

            @canany(['transactions.create', 'stock_requests.create', 'expenses.manage'])
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-1 text-warning"></i> Aksi Cepat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row" style="margin:0 -6px;">
                        @can('transactions.create')
                        <div class="col-6" style="padding:0 6px; margin-bottom:10px;">
                            <a href="{{ route('transactions.create') }}" class="d-flex flex-column align-items-center justify-content-center py-3" style="background:rgba(255,107,53,0.08); border-radius:12px; text-decoration:none; color:#FF6B35; border:1px solid rgba(255,107,53,0.15); transition:all 0.2s;">
                                <i class="fas fa-cash-register" style="font-size:1.3rem; margin-bottom:5px;"></i>
                                <small style="font-weight:600; font-size:0.75rem;">Kasir</small>
                            </a>
                        </div>
                        @endcan
                        @can('stock_requests.create')
                        <div class="col-6" style="padding:0 6px; margin-bottom:10px;">
                            <a href="{{ route('stock-requests.create') }}" class="d-flex flex-column align-items-center justify-content-center py-3" style="background:rgba(59,130,246,0.08); border-radius:12px; text-decoration:none; color:#2563eb; border:1px solid rgba(59,130,246,0.15); transition:all 0.2s;">
                                <i class="fas fa-clipboard-list" style="font-size:1.3rem; margin-bottom:5px;"></i>
                                <small style="font-weight:600; font-size:0.75rem;">Minta Stok</small>
                            </a>
                        </div>
                        @endcan
                        @can('expenses.manage')
                        <div class="col-6" style="padding:0 6px; margin-bottom:10px;">
                            <a href="{{ route('expenses.create') }}" class="d-flex flex-column align-items-center justify-content-center py-3" style="background:rgba(239,68,68,0.08); border-radius:12px; text-decoration:none; color:#dc2626; border:1px solid rgba(239,68,68,0.15); transition:all 0.2s;">
                                <i class="fas fa-coins" style="font-size:1.3rem; margin-bottom:5px;"></i>
                                <small style="font-weight:600; font-size:0.75rem;">Catat Biaya</small>
                            </a>
                        </div>
                        @endcan
                        @can('products.view')
                        <div class="col-6" style="padding:0 6px; margin-bottom:10px;">
                            <a href="{{ route('products.index') }}" class="d-flex flex-column align-items-center justify-content-center py-3" style="background:rgba(16,185,129,0.08); border-radius:12px; text-decoration:none; color:#059669; border:1px solid rgba(16,185,129,0.15); transition:all 0.2s;">
                                <i class="fas fa-spray-can" style="font-size:1.3rem; margin-bottom:5px;"></i>
                                <small style="font-weight:600; font-size:0.75rem;">Produk</small>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany
        </div>
    </div>
    @endcan

    @can('reports.view')
    @if(count($smartInsights) > 0)
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb mr-1 text-warning"></i> Ringkasan & Rekomendasi
                    </h3>
                </div>
                <div class="card-body py-3 px-3">
                    <div class="row">
                        @foreach($smartInsights as $insight)
                        <div class="col-md-3 col-6 mb-3">
                            <div class="d-flex align-items-start" style="gap:10px;">
                                <div style="width:34px; height:34px; border-radius:10px; background:rgba(59,130,246,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="fas {{ $insight['icon'] ?? 'fa-lightbulb' }} {{ $insight['color'] ?? 'text-primary' }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:0.82rem; font-weight:700; color:#1e293b;">{{ $insight['title'] ?? '' }}</div>
                                    <div style="font-size:0.73rem; color:#64748b; line-height:1.4; margin-top:2px;">{{ $insight['text'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endcan
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Setup CSRF token for all AJAX requests to Laravel web routes
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    @can('reports.view')
    if ($('#salesChart').length) {
    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
    if (!salesChartCanvas) return;
    var salesChartData = {
        labels: @json(collect($salesData)->pluck('month')),
        datasets: [{
            label: 'Penjualan',
            backgroundColor: 'rgba(255, 107, 53, 0.2)',
            borderColor: 'rgba(255, 107, 53, 1)',
            pointBackgroundColor: 'rgba(255, 107, 53, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(255, 107, 53, 1)',
            data: @json(collect($salesData)->pluck('sales'))
        }]
    };
    new Chart(salesChartCanvas, {
        type: 'line',
        data: salesChartData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    grid: { display: true },
                    ticks: {
                        callback: function(value) { return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
                    }
                }
            }
        }
    });
    }

    @php
        $pmLabels = ['cash' => 'CASH', 'qris' => 'QRIS', 'transfer' => 'TRANSFER', 'ewallet' => 'E-WALLET', 'debit_card' => 'DEBIT', 'credit_card' => 'KREDIT'];
        $pmColors = ['cash' => '#FF6B35', 'qris' => '#3498db', 'transfer' => '#2ecc71', 'ewallet' => '#e74c3c', 'debit_card' => '#9b59b6', 'credit_card' => '#f39c12'];
        $pmData = $paymentData ?? [];
        $pmKeys = array_keys($pmData);
        $pmChartLabels = array_map(fn($k) => $pmLabels[$k] ?? ucfirst($k), $pmKeys);
        $pmChartColors = array_map(fn($k) => $pmColors[$k] ?? '#6c757d', $pmKeys);
        $pmChartValues = array_values($pmData);
    @endphp
    if ($('#paymentChart').length) {
        var pmCtx = $('#paymentChart').get(0).getContext('2d');
        new Chart(pmCtx, {
            type: 'doughnut',
            data: {
                labels: @json($pmChartLabels),
                datasets: [{
                    data: @json($pmChartValues),
                    backgroundColor: @json($pmChartColors)
                }]
            },
            options: { maintainAspectRatio: false, responsive: true, cutout: '70%', plugins: { legend: { display: false } } }
        });
        if ($('#paymentBarChart').length) {
        var pmBarCtx = $('#paymentBarChart').get(0).getContext('2d');
        new Chart(pmBarCtx, {
            type: 'bar',
            data: {
                labels: @json($pmChartLabels),
                datasets: [{
                    data: @json($pmChartValues),
                    backgroundColor: @json($pmChartColors),
                    borderRadius: 3
                }]
            },
            options: {
                indexAxis: 'y', maintainAspectRatio: false, responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { display: false } },
                    y: { grid: { display: false }, ticks: { font: { size: 8 } } }
                }
            }
        });
        }
    }

    var comparisonRequest;
    var compIconColors = {
        revenue:      { icon: 'fa-money-bill-wave', bg: 'rgba(16,185,129,0.1)',  color: '#059669' },
        transactions: { icon: 'fa-receipt',          bg: 'rgba(59,130,246,0.1)',  color: '#2563eb' },
        profit:       { icon: 'fa-chart-line',        bg: 'rgba(139,92,246,0.1)', color: '#7c3aed' },
        avg_basket:   { icon: 'fa-shopping-cart',     bg: 'rgba(245,158,11,0.1)', color: '#d97706' }
    };
    function loadComparison(mode) {
        if (comparisonRequest) comparisonRequest.abort();
        $('#comparison-loading').show();
        $('#comparison-content').hide();
        comparisonRequest = $.getJSON('/api/dashboard/comparison', { mode: mode }, function(data) {
            var container = document.getElementById('comparison-cards');
            container.innerHTML = '';
            $.each(data.kpis, function(key, kpi) {
                var d = parseFloat(kpi.delta);
                var ic = compIconColors[key] || { icon: 'fa-chart-bar', bg: 'rgba(100,116,139,0.1)', color: '#475569' };
                var isUp = d > 0, isDown = d < 0;
                var trendBg    = isUp ? 'rgba(16,185,129,0.1)'  : (isDown ? 'rgba(239,68,68,0.1)'  : 'rgba(100,116,139,0.1)');
                var trendColor = isUp ? '#059669'               : (isDown ? '#dc2626'               : '#64748b');
                var trendIcon  = isUp ? 'fa-arrow-up'           : (isDown ? 'fa-arrow-down'          : 'fa-minus');
                var deltaText  = (d >= 0 ? '+' : '') + d + '%';

                // Build card via DOM — never concat API strings into innerHTML
                var col = document.createElement('div');
                col.className = 'col-6 col-md-3 mb-3';

                var card = document.createElement('div');
                card.style.cssText = 'background:#fff;border-radius:12px;padding:14px 16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);height:100%;';

                // Icon + label row
                var header = document.createElement('div');
                header.style.cssText = 'display:flex;align-items:center;gap:10px;margin-bottom:10px;';
                var iconWrap = document.createElement('div');
                iconWrap.style.cssText = 'width:36px;height:36px;border-radius:10px;background:' + ic.bg + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;';
                var icon = document.createElement('i');
                icon.className = 'fas ' + ic.icon;
                icon.style.cssText = 'color:' + ic.color + ';font-size:0.9rem;';
                iconWrap.appendChild(icon);
                var labelEl = document.createElement('div');
                labelEl.style.cssText = 'font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;';
                labelEl.textContent = kpi.label || '';
                header.appendChild(iconWrap);
                header.appendChild(labelEl);
                card.appendChild(header);

                // Current value
                var currentEl = document.createElement('div');
                currentEl.style.cssText = 'font-size:0.88rem;font-weight:700;color:#1e293b;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
                currentEl.textContent = kpi.current || '';
                card.appendChild(currentEl);

                // Previous + trend row
                var footer = document.createElement('div');
                footer.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:6px;';
                var prevWrap = document.createElement('div');
                prevWrap.style.cssText = 'font-size:0.7rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
                var histIcon = document.createElement('i');
                histIcon.className = 'fas fa-history';
                histIcon.style.marginRight = '3px';
                prevWrap.appendChild(histIcon);
                prevWrap.appendChild(document.createTextNode(kpi.previous || ''));
                var trendEl = document.createElement('span');
                trendEl.style.cssText = 'background:' + trendBg + ';color:' + trendColor + ';border-radius:6px;padding:2px 8px;font-size:0.68rem;font-weight:700;white-space:nowrap;flex-shrink:0;';
                var trendIconEl = document.createElement('i');
                trendIconEl.className = 'fas ' + trendIcon;
                trendIconEl.style.cssText = 'margin-right:2px;font-size:0.6rem;';
                trendEl.appendChild(trendIconEl);
                trendEl.appendChild(document.createTextNode(deltaText));
                footer.appendChild(prevWrap);
                footer.appendChild(trendEl);
                card.appendChild(footer);

                col.appendChild(card);
                container.appendChild(col);
            });
            $('#comparison-loading').hide();
            $('#comparison-content').show();
        }).fail(function() {
            $('#comparison-loading').hide();
            $('#comparison-content').html(
                '<div class="text-center text-danger py-3"><i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat perbandingan</div>'
            ).show();
        });
    }
    loadComparison('mom');
    $('[name="comp_mode"]').on('change', function() { loadComparison($(this).val()); });
    $('.btn-group-toggle label').on('click', function() { $(this).find('input').prop('checked', true).trigger('change'); });

    // Auto-refresh comparison every 60 seconds
    setInterval(function() {
        var activeMode = $('[name="comp_mode"]:checked').val() || 'mom';
        loadComparison(activeMode);
    }, 60000);
    @endcan
});
</script>
@endpush