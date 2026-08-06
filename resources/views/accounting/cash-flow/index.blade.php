@extends('layouts.app')

@section('title', 'Arus Kas')

@push('styles')
<style>
    :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
    .page-header-apms { background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
    .page-header-apms h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 4px; color: #fff; }
    .page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; font-size: 0.8rem; }
    .page-header-apms .breadcrumb-item a { color: rgba(255,255,255,0.7); text-decoration: none; }
    .page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,0.5); }
    .page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }
    .filter-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 8px rgba(45,48,71,0.05); padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .filter-card .form-control { border-radius: 8px; border: 1.5px solid #e4e8f0; font-size: 0.85rem; color: var(--secondary); }
    .filter-card .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .kpi-card { background: #fff; border-radius: 14px; padding: 1.25rem 1.5rem; box-shadow: 0 2px 12px rgba(45,48,71,0.07); border: 1px solid #f0f2f8; display: flex; align-items: center; gap: 1rem; height: 100%; transition: transform 0.2s; }
    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .kpi-icon.green  { background: linear-gradient(135deg,rgba(67,160,71,0.15),rgba(56,142,60,0.08)); color: #388e3c; }
    .kpi-icon.orange { background: linear-gradient(135deg,rgba(255,107,53,0.15),rgba(229,90,43,0.08)); color: var(--primary); }
    .kpi-icon.blue   { background: linear-gradient(135deg,rgba(33,150,243,0.15),rgba(30,136,229,0.08)); color: #1976d2; }
    .kpi-icon.purple { background: linear-gradient(135deg,rgba(126,87,194,0.15),rgba(103,58,183,0.08)); color: #7b1fa2; }
    .kpi-value { font-size: 1.3rem; font-weight: 800; color: var(--secondary); line-height: 1; margin-bottom: 3px; }
    .kpi-label { font-size: 0.72rem; color: #8892a4; font-weight: 500; text-transform: uppercase; letter-spacing: 0.4px; }
    .cf-section { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); overflow: hidden; margin-bottom: 1.25rem; }
    .cf-section-header { padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f0f2f8; }
    .cf-section-header h5 { font-size: 0.92rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
    .cf-section-header .subtotal-val { font-size: 1rem; font-weight: 800; }
    .cf-table { width: 100%; font-size: 0.85rem; color: var(--secondary); }
    .cf-table tr { border-bottom: 1px solid #f5f6fb; }
    .cf-table tr:last-child { border-bottom: none; }
    .cf-table tr:hover { background: #fafbff; }
    .cf-table td { padding: 0.75rem 1.5rem; vertical-align: middle; }
    .cf-table .item-label { color: #667; }
    .cf-table .item-value { text-align: right; font-weight: 600; white-space: nowrap; }
    .cf-table .subtotal-row td { background: #f8f9ff; font-weight: 700; border-top: 2px solid #eef0f8; color: var(--secondary); }
    .amount-positive { color: #2e7d32; }
    .amount-negative { color: #c62828; }
    .net-change-card { border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .net-change-card.positive { background: linear-gradient(135deg,#e8f5e9,#f1f8e9); border: 1px solid #a5d6a7; }
    .net-change-card.negative { background: linear-gradient(135deg,#fce4ec,#fff3f3); border: 1px solid #ef9a9a; }
    .net-change-card.neutral  { background: linear-gradient(135deg,#f5f6fb,#fff); border: 1px solid #eef0f8; }
    .balance-strip { display: flex; gap: 1rem; flex-wrap: wrap; }
    .balance-item { flex: 1; min-width: 160px; background: #fff; border-radius: 10px; padding: 0.9rem 1.2rem; border: 1px solid #eef0f8; box-shadow: 0 1px 6px rgba(45,48,71,0.05); }
    .balance-item .b-label { font-size: 0.72rem; color: #8892a4; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
    .balance-item .b-value { font-size: 1.1rem; font-weight: 800; color: var(--secondary); }
    .period-chip { display: inline-flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.15); border-radius: 6px; padding: 3px 10px; font-size: 0.78rem; color: rgba(255,255,255,0.9); }
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; border: 1.5px solid; transition: all 0.15s; text-decoration: none; cursor: pointer; }
    .btn-export.pdf { border-color: #ef5350; color: #ef5350; background: rgba(239,83,80,0.05); }
    .btn-export.pdf:hover { background: #ef5350; color: #fff; text-decoration: none; }
    .btn-export.print { border-color: var(--secondary); color: var(--secondary); background: rgba(45,48,71,0.04); }
    .btn-export.print:hover { background: var(--secondary); color: #fff; text-decoration: none; }
    .chart-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .chart-card h5 { font-size: 0.92rem; font-weight: 700; color: var(--secondary); margin-bottom: 1rem; }
    .unavailable-note { display: flex; align-items: center; gap: 8px; padding: 0.75rem 1.5rem; background: #f8f9ff; color: #8892a4; font-size: 0.82rem; font-style: italic; }
</style>

@push('styles')
<style>
    :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
    .page-header-apms { background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
    .page-header-apms h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 4px; color: #fff; }
    .page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; font-size: 0.8rem; }
    .page-header-apms .breadcrumb-item a { color: rgba(255,255,255,0.7); text-decoration: none; }
    .page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,0.5); }
    .page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }
    .filter-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 8px rgba(45,48,71,0.05); padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .filter-card .form-control { border-radius: 8px; border: 1.5px solid #e4e8f0; font-size: 0.85rem; color: var(--secondary); }
    .filter-card .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .kpi-card { background: #fff; border-radius: 14px; padding: 1.25rem 1.5rem; box-shadow: 0 2px 12px rgba(45,48,71,0.07); border: 1px solid #f0f2f8; display: flex; align-items: center; gap: 1rem; height: 100%; transition: transform 0.2s; }
    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .kpi-icon.green  { background: linear-gradient(135deg,rgba(67,160,71,0.15),rgba(56,142,60,0.08)); color: #388e3c; }
    .kpi-icon.orange { background: linear-gradient(135deg,rgba(255,107,53,0.15),rgba(229,90,43,0.08)); color: var(--primary); }
    .kpi-icon.blue   { background: linear-gradient(135deg,rgba(33,150,243,0.15),rgba(30,136,229,0.08)); color: #1976d2; }
    .kpi-icon.purple { background: linear-gradient(135deg,rgba(126,87,194,0.15),rgba(103,58,183,0.08)); color: #7b1fa2; }
    .kpi-value { font-size: 1.3rem; font-weight: 800; color: var(--secondary); line-height: 1; margin-bottom: 3px; }
    .kpi-label { font-size: 0.72rem; color: #8892a4; font-weight: 500; text-transform: uppercase; letter-spacing: 0.4px; }
    .cf-section { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); overflow: hidden; margin-bottom: 1.25rem; }
    .cf-section-header { padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f0f2f8; }
    .cf-section-header h5 { font-size: 0.92rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
    .cf-section-header .subtotal-val { font-size: 1rem; font-weight: 800; }
    .cf-table { width: 100%; font-size: 0.85rem; color: var(--secondary); }
    .cf-table tr { border-bottom: 1px solid #f5f6fb; }
    .cf-table tr:last-child { border-bottom: none; }
    .cf-table tr:hover { background: #fafbff; }
    .cf-table td { padding: 0.75rem 1.5rem; vertical-align: middle; }
    .cf-table .item-label { color: #667; }
    .cf-table .item-value { text-align: right; font-weight: 600; white-space: nowrap; }
    .cf-table .subtotal-row td { background: #f8f9ff; font-weight: 700; border-top: 2px solid #eef0f8; color: var(--secondary); }
    .amount-positive { color: #2e7d32; }
    .amount-negative { color: #c62828; }
    .net-change-card { border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .net-change-card.positive { background: linear-gradient(135deg,#e8f5e9,#f1f8e9); border: 1px solid #a5d6a7; }
    .net-change-card.negative { background: linear-gradient(135deg,#fce4ec,#fff3f3); border: 1px solid #ef9a9a; }
    .net-change-card.neutral  { background: linear-gradient(135deg,#f5f6fb,#fff); border: 1px solid #eef0f8; }
    .balance-strip { display: flex; gap: 1rem; flex-wrap: wrap; }
    .balance-item { flex: 1; min-width: 160px; background: #fff; border-radius: 10px; padding: 0.9rem 1.2rem; border: 1px solid #eef0f8; box-shadow: 0 1px 6px rgba(45,48,71,0.05); }
    .balance-item .b-label { font-size: 0.72rem; color: #8892a4; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
    .balance-item .b-value { font-size: 1.1rem; font-weight: 800; color: var(--secondary); }
    .period-chip { display: inline-flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.15); border-radius: 6px; padding: 3px 10px; font-size: 0.78rem; color: rgba(255,255,255,0.9); }
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; border: 1.5px solid; transition: all 0.15s; text-decoration: none; cursor: pointer; }
    .btn-export.pdf { border-color: #ef5350; color: #ef5350; background: rgba(239,83,80,0.05); }
    .btn-export.pdf:hover { background: #ef5350; color: #fff; text-decoration: none; }
    .btn-export.print { border-color: var(--secondary); color: var(--secondary); background: rgba(45,48,71,0.04); }
    .btn-export.print:hover { background: var(--secondary); color: #fff; text-decoration: none; }
    .chart-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; }
    .chart-card h5 { font-size: 0.92rem; font-weight: 700; color: var(--secondary); margin-bottom: 1rem; }
    .unavailable-note { display: flex; align-items: center; gap: 8px; padding: 0.75rem 1.5rem; background: #f8f9ff; color: #8892a4; font-size: 0.82rem; font-style: italic; }
</style>

@endpush

@section('content')
<div class="container-fluid pb-4">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <div>
            <h1><i class="fas fa-water mr-2" style="color:var(--primary)"></i>Arus Kas (Cash Flow)</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Akuntansi</a></li>
                    <li class="breadcrumb-item active">Arus Kas</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap:0.5rem">
            <span class="period-chip"><i class="fas fa-calendar-alt"></i>{{ date('d M Y', strtotime($startDate)) }} &ndash; {{ date('d M Y', strtotime($endDate)) }}</span>
            <button onclick="window.print()" class="btn-export print"><i class="fas fa-print"></i> Cetak</button>
            <a href="{{ route('accounting.cash-flow.index', array_merge(request()->query(), ['export'=>'pdf'])) }}" class="btn-export pdf"><i class="fas fa-file-pdf"></i> PDF</a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row align-items-end" style="row-gap:0.75rem">
                <div class="col-md-3">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn w-100" style="background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.85rem;height:calc(1.5em + 0.75rem + 2px)">
                        <i class="fas fa-sync-alt mr-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Summary --}}
    @php
        $operatingNet = $cashIn - $cashOut;
        $totalNet     = $operatingNet; // investing & financing not yet available
    @endphp
    <div class="row mb-4" style="row-gap:1rem">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon green"><i class="fas fa-arrow-down"></i></div>
                <div>
                    <div class="kpi-value" style="font-size:1.05rem">Rp {{ number_format($cashIn, 0, ',', '.') }}</div>
                    <div class="kpi-label">Total Penerimaan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="fas fa-arrow-up"></i></div>
                <div>
                    <div class="kpi-value" style="font-size:1.05rem">Rp {{ number_format($cashOut, 0, ',', '.') }}</div>
                    <div class="kpi-label">Total Pengeluaran</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon {{ $operatingNet >= 0 ? 'green' : 'orange' }}"><i class="fas fa-exchange-alt"></i></div>
                <div>
                    <div class="kpi-value {{ $operatingNet >= 0 ? 'amount-positive' : 'amount-negative' }}" style="font-size:1.05rem">
                        {{ $operatingNet >= 0 ? '' : '-' }}Rp {{ number_format(abs($operatingNet), 0, ',', '.') }}
                    </div>
                    <div class="kpi-label">Kas Bersih Operasi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="kpi-value" style="font-size:0.9rem;color:#8892a4">�</div>
                    <div class="kpi-label">Saldo Kas Akhir</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 1: Operating --}}
    <div class="cf-section">
        <div class="cf-section-header">
            <h5><span style="width:10px;height:10px;border-radius:50%;background:#43a047;display:inline-block"></span>Arus Kas dari Aktivitas Operasi</h5>
            <div class="subtotal-val {{ $operatingNet >= 0 ? 'amount-positive' : 'amount-negative' }}">
                {{ $operatingNet >= 0 ? '+' : '' }}Rp {{ number_format($operatingNet, 0, ',', '.') }}
            </div>
        </div>
        <table class="cf-table">
            <tr>
                <td class="item-label"><i class="fas fa-plus-circle text-success mr-2" style="font-size:0.78rem"></i>Penerimaan Kas dari Penjualan</td>
                <td class="item-value amount-positive">Rp {{ number_format($cashIn, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="item-label"><i class="fas fa-minus-circle text-danger mr-2" style="font-size:0.78rem"></i>Pembayaran Kas untuk Beban Operasional</td>
                <td class="item-value amount-negative">(Rp {{ number_format($cashOut, 0, ',', '.') }})</td>
            </tr>
            <tr class="subtotal-row">
                <td>Kas Bersih dari Aktivitas Operasi</td>
                <td class="item-value {{ $operatingNet >= 0 ? 'amount-positive' : 'amount-negative' }}">
                    {{ $operatingNet >= 0 ? '' : '-' }}Rp {{ number_format(abs($operatingNet), 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Section 2: Investing --}}
    <div class="cf-section">
        <div class="cf-section-header">
            <h5><span style="width:10px;height:10px;border-radius:50%;background:#f9a825;display:inline-block"></span>Arus Kas dari Aktivitas Investasi</h5>
            <div class="subtotal-val" style="color:#8892a4">Rp 0</div>
        </div>
        <div class="unavailable-note">
            <i class="fas fa-info-circle" style="color:#b0b8c9"></i>
            Data aktivitas investasi belum tersedia. Akan ditampilkan setelah jurnal investasi dicatat.
        </div>
        <table class="cf-table">
            <tr class="subtotal-row">
                <td>Kas Bersih dari Aktivitas Investasi</td>
                <td class="item-value" style="color:#8892a4">Rp 0</td>
            </tr>
        </table>
    </div>

    {{-- Section 3: Financing --}}
    <div class="cf-section">
        <div class="cf-section-header">
            <h5><span style="width:10px;height:10px;border-radius:50%;background:#1976d2;display:inline-block"></span>Arus Kas dari Aktivitas Pendanaan</h5>
            <div class="subtotal-val" style="color:#8892a4">Rp 0</div>
        </div>
        <div class="unavailable-note">
            <i class="fas fa-info-circle" style="color:#b0b8c9"></i>
            Data aktivitas pendanaan belum tersedia. Akan ditampilkan setelah jurnal pendanaan dicatat.
        </div>
        <table class="cf-table">
            <tr class="subtotal-row">
                <td>Kas Bersih dari Aktivitas Pendanaan</td>
                <td class="item-value" style="color:#8892a4">Rp 0</td>
            </tr>
        </table>
    </div>

    {{-- Net Change & Balances --}}
    <div class="net-change-card {{ $totalNet > 0 ? 'positive' : ($totalNet < 0 ? 'negative' : 'neutral') }}">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#8892a4;margin-bottom:4px">
                    Kenaikan / Penurunan Bersih Kas
                </div>
                <div style="font-size:1.75rem;font-weight:800;color:{{ $totalNet >= 0 ? '#2e7d32' : '#c62828' }}">
                    {{ $totalNet >= 0 ? '+' : '-' }}Rp {{ number_format(abs($totalNet), 0, ',', '.') }}
                </div>
                <div style="font-size:0.78rem;color:#8892a4;margin-top:4px">
                    Periode: {{ date('d M Y', strtotime($startDate)) }} &ndash; {{ date('d M Y', strtotime($endDate)) }}
                </div>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="balance-strip">
                    <div class="balance-item">
                        <div class="b-label">Saldo Kas Awal</div>
                        <div class="b-value" style="color:#8892a4;font-size:0.9rem">�</div>
                    </div>
                    <div class="balance-item">
                        <div class="b-label">Saldo Kas Akhir</div>
                        <div class="b-value" style="color:#8892a4;font-size:0.9rem">�</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="chart-card">
        <h5><i class="fas fa-chart-bar mr-2" style="color:var(--primary)"></i>Grafik Arus Kas Operasi</h5>
        <canvas id="cashFlowChart" height="80"></canvas>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('cashFlowChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            datasets: [
                {
                    label: 'Penerimaan',
                    data: [0,0,0,0,0,0,0,0,0,0,0,{{ $cashIn }}],
                    backgroundColor: 'rgba(67,160,71,0.7)',
                    borderColor: '#43a047',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Pengeluaran',
                    data: [0,0,0,0,0,0,0,0,0,0,0,{{ $cashOut }}],
                    backgroundColor: 'rgba(255,107,53,0.7)',
                    borderColor: '#FF6B35',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return 'Rp ' + (v/1000000).toFixed(1) + 'jt'; }
                    },
                    grid: { color: '#f0f2f8' }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
@endsection