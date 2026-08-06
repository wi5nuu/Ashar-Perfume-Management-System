@extends('portal.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
/* ── Stat boxes (mirip small-box AdminLTE tapi untuk portal) ── */
.portal-stat-box {
    position: relative;
    display: block;
    border-radius: 10px;
    padding: 18px 20px 14px;
    overflow: hidden;
    color: #fff;
    margin-bottom: 20px;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}
.portal-stat-box:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.16); color: #fff; text-decoration: none; }
.portal-stat-box .inner h3 { font-size: 1.9rem; font-weight: 700; margin: 0 0 4px; line-height: 1; }
.portal-stat-box .inner p  { font-size: 0.82rem; font-weight: 600; margin: 0; opacity: 0.88; text-transform: uppercase; letter-spacing: 0.04em; }
.portal-stat-box .icon     { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 3.2rem; opacity: 0.25; }
.psb-primary  { background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%); }
.psb-warning  { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.psb-danger   { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.psb-success  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.psb-info     { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

/* ── Card portal yang diperkuat ── */
.card-portal {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    margin-bottom: 20px;
}
.card-portal .card-header {
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-radius: 10px 10px 0 0 !important;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card-portal .card-header h5 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}
.card-portal .card-header h5 i { color: #FF6B35; margin-right: 6px; }

/* ── Tabel ── */
.table-portal th {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    background: #f8fafc;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0 !important;
    white-space: nowrap;
}
.table-portal td {
    font-size: 0.84rem;
    padding: 10px 14px;
    vertical-align: middle;
    color: #334155;
    border-top: 1px solid #f8fafc !important;
}
.table-portal tbody tr:hover { background: rgba(255,107,53,0.04); }

/* ── Debt info box ── */
.debt-box {
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
}
.debt-box.has-debt   { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); }
.debt-box.no-debt    { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); }
.debt-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.debt-box.has-debt .debt-icon { background: rgba(239,68,68,0.12); color: #dc2626; }
.debt-box.no-debt  .debt-icon { background: rgba(16,185,129,0.12); color: #059669; }
.debt-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 2px; }
.debt-value { font-size: 1.2rem; font-weight: 700; }
.debt-box.has-debt .debt-value { color: #dc2626; }
.debt-box.no-debt  .debt-value { color: #059669; }

@media (max-width: 575.98px) {
    .portal-stat-box .inner h3 { font-size: 1.5rem; }
    .portal-stat-box .icon { font-size: 2.4rem; }
}
</style>
@endpush

@section('content')

{{-- ===== GREETING BANNER ===== --}}
<div style="background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%); border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; position: relative; overflow: hidden;">
    <div style="position: relative; z-index: 1;">
        <h4 class="mb-1 text-white font-weight-bold" style="font-size: 1.05rem;">
            Selamat datang, {{ $customer->name }}!
        </h4>
        <p class="mb-0" style="color: rgba(255,255,255,0.78); font-size: 0.8rem;">
            <i class="fas fa-calendar-alt mr-1"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
            <span class="mx-2" style="opacity:0.4;">·</span>
            <i class="fas fa-store mr-1"></i> Portal Pelanggan Grosir
        </p>
    </div>
    <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.07); border-radius: 50%;"></div>
    <div style="position: absolute; right: 40px; bottom: -30px; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
</div>

{{-- ===== STAT BOXES ===== --}}
<div class="row">
    <div class="col-6 col-md-3">
        <a href="{{ route('portal.orders', $token) }}" class="portal-stat-box psb-primary">
            <div class="inner">
                <h3>{{ $totalOrders }}</h3>
                <p>Total Pesanan</p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('portal.orders', $token) }}" class="portal-stat-box psb-warning">
            <div class="inner">
                <h3>{{ $pendingOrders }}</h3>
                <p>Pesanan Proses</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </a>
    </div>
    @php
        $completedOrders = $recentOrders->where('status', 'completed')->count() ?? 0;
    @endphp
    <div class="col-6 col-md-3">
        <a href="{{ route('portal.orders', $token) }}" class="portal-stat-box psb-success">
            <div class="inner">
                <h3>{{ $completedOrders }}</h3>
                <p>Selesai</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('portal.statement', $token) }}" class="portal-stat-box {{ $remainingDebt > 0 ? 'psb-danger' : 'psb-info' }}">
            <div class="inner">
                <h3 style="font-size: 1.2rem;">Rp {{ number_format($remainingDebt, 0, ',', '.') }}</h3>
                <p>Sisa Hutang</p>
            </div>
            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
        </a>
    </div>
</div>

{{-- ===== DEBT INFO BOX ===== --}}
@if($remainingDebt > 0)
<div class="debt-box has-debt">
    <div class="debt-icon"><i class="fas fa-exclamation-triangle"></i></div>
    <div>
        <div class="debt-label">Tagihan Belum Lunas</div>
        <div class="debt-value">Rp {{ number_format($remainingDebt, 0, ',', '.') }}</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Silakan hubungi admin untuk informasi pembayaran.</div>
    </div>
</div>
@else
<div class="debt-box no-debt">
    <div class="debt-icon"><i class="fas fa-check-circle"></i></div>
    <div>
        <div class="debt-label">Status Pembayaran</div>
        <div class="debt-value">Semua Lunas</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Tidak ada tagihan yang perlu dibayar.</div>
    </div>
</div>
@endif

{{-- ===== RECENT ORDERS TABLE ===== --}}
<div class="card card-portal">
    <div class="card-header">
        <h5><i class="fas fa-history"></i> Pesanan Terbaru</h5>
        <a href="{{ route('portal.orders', $token) }}" class="btn btn-sm btn-portal" style="border-radius: 7px; font-size: 0.78rem; padding: 5px 14px;">
            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-portal mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <span class="font-weight-bold" style="color: #FF6B35;">
                                {{ $order->invoice_number }}
                            </span>
                        </td>
                        <td>
                            <span>{{ $order->created_at->format('d/m/Y') }}</span>
                            <div style="font-size: 0.72rem; color: #94a3b8;">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="font-weight-bold">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td>
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge badge-warning" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-clock mr-1"></i>Menunggu
                                    </span>
                                    @break
                                @case('processing')
                                    <span class="badge badge-info" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-cog mr-1"></i>Diproses
                                    </span>
                                    @break
                                @case('shipped')
                                    <span class="badge badge-primary" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-truck mr-1"></i>Dikirim
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="badge badge-success" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-check mr-1"></i>Selesai
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-times mr-1"></i>Dibatalkan
                                    </span>
                                    @break
                                @default
                                    <span class="badge badge-secondary" style="border-radius: 6px; font-size: 0.75rem;">
                                        {{ ucfirst($order->status) }}
                                    </span>
                            @endswitch
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-box-open d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            Belum ada pesanan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
