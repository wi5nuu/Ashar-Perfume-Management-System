@extends('layouts.app')

@section('title', 'Laba Rugi')

@push('styles')
<style>
:root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#2D3047; }

.page-header-bar {
    background:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem;
}
.page-header-bar h4 { font-weight:700; color:var(--secondary); margin:0; font-size:1.15rem; display:flex; align-items:center; gap:.5rem; }
.page-header-bar h4 i { color:var(--primary); }

.filter-bar {
    background:#fff; border-radius:14px; padding:1.1rem 1.5rem; margin-bottom:1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.06); border:1px solid rgba(0,0,0,.04);
}
.filter-label { font-size:.74rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#888; margin-bottom:.3rem; }
.filter-bar .form-control { border-radius:8px; border:1.5px solid #e8e8e8; font-size:.85rem; padding:.42rem .75rem; }
.filter-bar .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.12); }
.btn-filter-apply { background:var(--primary); color:#fff; border:none; padding:.45rem 1.2rem; border-radius:8px; font-weight:600; font-size:.84rem; display:inline-flex; align-items:center; gap:.4rem; }
.btn-filter-apply:hover { background:var(--primary-dark); color:#fff; }
.btn-filter-reset { background:transparent; color:#888; border:1.5px solid #e8e8e8; padding:.45rem .9rem; border-radius:8px; font-size:.84rem; }

/* Statement layout */
.statement-card {
    background:#fff; border-radius:16px; box-shadow:0 2px 20px rgba(0,0,0,.08);
    border:1px solid rgba(0,0,0,.05); overflow:hidden; margin-bottom:1.5rem;
}
.statement-company-header {
    background: linear-gradient(135deg, var(--secondary) 0%, #1a1d2e 100%);
    padding: 1.6rem 2rem; color:#fff; text-align:center;
}
.statement-company-header h3 { font-size:1.2rem; font-weight:700; margin-bottom:.25rem; letter-spacing:.5px; }
.statement-company-header p { font-size:.82rem; color:rgba(255,255,255,.6); margin:0; }
.statement-company-header .report-title { font-size:1rem; font-weight:600; color:rgba(255,255,255,.9); margin-top:.5rem; }
.statement-company-header .report-date { font-size:.82rem; color:rgba(255,107,53,.9); font-weight:600; }

/* Section headers */
.stmt-section-header {
    padding:.65rem 1.5rem;
    font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.8px;
    display:flex; align-items:center; gap:.5rem;
}
.stmt-section-income  { background:rgba(39,174,96,.07);  color:#1a8a4a; border-left:4px solid #27AE60; }
.stmt-section-expense { background:rgba(231,76,60,.07);  color:#c0392b; border-left:4px solid #E74C3C; }
.stmt-section-cogs    { background:rgba(243,156,18,.07); color:#c47a00; border-left:4px solid #F39C12; }
.stmt-section-opex    { background:rgba(142,68,173,.07); color:#6c2f9c; border-left:4px solid #8e44ad; }
.stmt-section-other   { background:rgba(41,128,185,.07); color:#1a5e8a; border-left:4px solid #2980B9; }

/* Account rows */
.stmt-table { width:100%; border-collapse:collapse; }
.stmt-table .stmt-row td { padding:.65rem 1.5rem; font-size:.86rem; color:#555; border-bottom:1px solid #f8f8f8; }
.stmt-table .stmt-row:hover td { background:#fafafa; }
.stmt-table .stmt-row td:last-child { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }
.stmt-table .stmt-row td:nth-child(2) { text-align:right; color:#aaa; font-size:.82rem; }

/* Subtotal rows */
.stmt-subtotal td { padding:.7rem 1.5rem !important; font-size:.86rem !important; font-weight:700 !important; }
.subtotal-income  { background:rgba(39,174,96,.06) !important;  color:#1a8a4a !important; }
.subtotal-expense { background:rgba(231,76,60,.06) !important;  color:#c0392b !important; }
.subtotal-cogs    { background:rgba(243,156,18,.06) !important; color:#c47a00 !important; }
.subtotal-opex    { background:rgba(142,68,173,.06) !important; color:#6c2f9c !important; }
.subtotal-other   { background:rgba(41,128,185,.06) !important; color:#1a5e8a !important; }

/* Gross profit, operating profit, net income rows */
.stmt-gross-profit td {
    padding:.9rem 1.5rem !important; font-size:.92rem !important; font-weight:700 !important;
    background:rgba(39,174,96,.1) !important; color:#1a8a4a !important;
    border-top:2px solid rgba(39,174,96,.2) !important;
}
.stmt-operating-profit td {
    padding:.9rem 1.5rem !important; font-size:.92rem !important; font-weight:700 !important;
    background:rgba(142,68,173,.1) !important; color:#6c2f9c !important;
    border-top:2px solid rgba(142,68,173,.2) !important;
}
.stmt-net-income-positive td {
    padding:1.1rem 1.5rem !important; font-size:1.05rem !important; font-weight:800 !important;
    background:linear-gradient(90deg, rgba(39,174,96,.12), rgba(39,174,96,.06)) !important;
    color:#1a8a4a !important;
    border-top:3px solid rgba(39,174,96,.3) !important;
    border-bottom:3px double rgba(39,174,96,.3) !important;
}
.stmt-net-income-negative td {
    padding:1.1rem 1.5rem !important; font-size:1.05rem !important; font-weight:800 !important;
    background:linear-gradient(90deg, rgba(231,76,60,.12), rgba(231,76,60,.06)) !important;
    color:#c0392b !important;
    border-top:3px solid rgba(231,76,60,.3) !important;
    border-bottom:3px double rgba(231,76,60,.3) !important;
}

/* Export buttons */
.btn-export-pdf { background:#E74C3C; color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-export-pdf:hover { background:#c0392b; color:#fff; }
.btn-print { background:var(--secondary); color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-print:hover { background:#1a1d2e; color:#fff; }

@media print {
    .page-header-bar, .filter-bar, .sidebar, .navbar, .content-header, .main-footer { display:none !important; }
    .statement-card { box-shadow:none !important; border:none !important; }
    body { background:#fff !important; }
}
</style>

@push('styles')
<style>
:root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#2D3047; }

.page-header-bar {
    background:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem;
}
.page-header-bar h4 { font-weight:700; color:var(--secondary); margin:0; font-size:1.15rem; display:flex; align-items:center; gap:.5rem; }
.page-header-bar h4 i { color:var(--primary); }

.filter-bar {
    background:#fff; border-radius:14px; padding:1.1rem 1.5rem; margin-bottom:1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.06); border:1px solid rgba(0,0,0,.04);
}
.filter-label { font-size:.74rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#888; margin-bottom:.3rem; }
.filter-bar .form-control { border-radius:8px; border:1.5px solid #e8e8e8; font-size:.85rem; padding:.42rem .75rem; }
.filter-bar .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.12); }
.btn-filter-apply { background:var(--primary); color:#fff; border:none; padding:.45rem 1.2rem; border-radius:8px; font-weight:600; font-size:.84rem; display:inline-flex; align-items:center; gap:.4rem; }
.btn-filter-apply:hover { background:var(--primary-dark); color:#fff; }
.btn-filter-reset { background:transparent; color:#888; border:1.5px solid #e8e8e8; padding:.45rem .9rem; border-radius:8px; font-size:.84rem; }

/* Statement layout */
.statement-card {
    background:#fff; border-radius:16px; box-shadow:0 2px 20px rgba(0,0,0,.08);
    border:1px solid rgba(0,0,0,.05); overflow:hidden; margin-bottom:1.5rem;
}
.statement-company-header {
    background: linear-gradient(135deg, var(--secondary) 0%, #1a1d2e 100%);
    padding: 1.6rem 2rem; color:#fff; text-align:center;
}
.statement-company-header h3 { font-size:1.2rem; font-weight:700; margin-bottom:.25rem; letter-spacing:.5px; }
.statement-company-header p { font-size:.82rem; color:rgba(255,255,255,.6); margin:0; }
.statement-company-header .report-title { font-size:1rem; font-weight:600; color:rgba(255,255,255,.9); margin-top:.5rem; }
.statement-company-header .report-date { font-size:.82rem; color:rgba(255,107,53,.9); font-weight:600; }

/* Section headers */
.stmt-section-header {
    padding:.65rem 1.5rem;
    font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.8px;
    display:flex; align-items:center; gap:.5rem;
}
.stmt-section-income  { background:rgba(39,174,96,.07);  color:#1a8a4a; border-left:4px solid #27AE60; }
.stmt-section-expense { background:rgba(231,76,60,.07);  color:#c0392b; border-left:4px solid #E74C3C; }
.stmt-section-cogs    { background:rgba(243,156,18,.07); color:#c47a00; border-left:4px solid #F39C12; }
.stmt-section-opex    { background:rgba(142,68,173,.07); color:#6c2f9c; border-left:4px solid #8e44ad; }
.stmt-section-other   { background:rgba(41,128,185,.07); color:#1a5e8a; border-left:4px solid #2980B9; }

/* Account rows */
.stmt-table { width:100%; border-collapse:collapse; }
.stmt-table .stmt-row td { padding:.65rem 1.5rem; font-size:.86rem; color:#555; border-bottom:1px solid #f8f8f8; }
.stmt-table .stmt-row:hover td { background:#fafafa; }
.stmt-table .stmt-row td:last-child { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }
.stmt-table .stmt-row td:nth-child(2) { text-align:right; color:#aaa; font-size:.82rem; }

/* Subtotal rows */
.stmt-subtotal td { padding:.7rem 1.5rem !important; font-size:.86rem !important; font-weight:700 !important; }
.subtotal-income  { background:rgba(39,174,96,.06) !important;  color:#1a8a4a !important; }
.subtotal-expense { background:rgba(231,76,60,.06) !important;  color:#c0392b !important; }
.subtotal-cogs    { background:rgba(243,156,18,.06) !important; color:#c47a00 !important; }
.subtotal-opex    { background:rgba(142,68,173,.06) !important; color:#6c2f9c !important; }
.subtotal-other   { background:rgba(41,128,185,.06) !important; color:#1a5e8a !important; }

/* Gross profit, operating profit, net income rows */
.stmt-gross-profit td {
    padding:.9rem 1.5rem !important; font-size:.92rem !important; font-weight:700 !important;
    background:rgba(39,174,96,.1) !important; color:#1a8a4a !important;
    border-top:2px solid rgba(39,174,96,.2) !important;
}
.stmt-operating-profit td {
    padding:.9rem 1.5rem !important; font-size:.92rem !important; font-weight:700 !important;
    background:rgba(142,68,173,.1) !important; color:#6c2f9c !important;
    border-top:2px solid rgba(142,68,173,.2) !important;
}
.stmt-net-income-positive td {
    padding:1.1rem 1.5rem !important; font-size:1.05rem !important; font-weight:800 !important;
    background:linear-gradient(90deg, rgba(39,174,96,.12), rgba(39,174,96,.06)) !important;
    color:#1a8a4a !important;
    border-top:3px solid rgba(39,174,96,.3) !important;
    border-bottom:3px double rgba(39,174,96,.3) !important;
}
.stmt-net-income-negative td {
    padding:1.1rem 1.5rem !important; font-size:1.05rem !important; font-weight:800 !important;
    background:linear-gradient(90deg, rgba(231,76,60,.12), rgba(231,76,60,.06)) !important;
    color:#c0392b !important;
    border-top:3px solid rgba(231,76,60,.3) !important;
    border-bottom:3px double rgba(231,76,60,.3) !important;
}

/* Export buttons */
.btn-export-pdf { background:#E74C3C; color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-export-pdf:hover { background:#c0392b; color:#fff; }
.btn-print { background:var(--secondary); color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-print:hover { background:#1a1d2e; color:#fff; }

@media print {
    .page-header-bar, .filter-bar, .sidebar, .navbar, .content-header, .main-footer { display:none !important; }
    .statement-card { box-shadow:none !important; border:none !important; }
    body { background:#fff !important; }
}
</style>

@endpush

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <h4><i class="fas fa-file-invoice-dollar"></i> Laporan Laba Rugi</h4>
        <div class="d-flex align-items-center" style="gap:.6rem">
            <a href="{{ route('accounting.index') }}" class="btn btn-filter-reset">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
            <button class="btn-export-pdf" onclick="exportPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('accounting.income-statement.index') }}">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Periode</div>
                    <select class="form-control" name="period" id="periodSelect">
                        <option value="monthly"   {{ request('period','monthly')==='monthly'   ? 'selected' : '' }}>Bulanan</option>
                        <option value="quarterly" {{ request('period')==='quarterly' ? 'selected' : '' }}>Kuartalan</option>
                        <option value="yearly"    {{ request('period')==='yearly'    ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Dari</div>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate ?? date('Y-m-01') }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Sampai</div>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Tahun</div>
                    <select class="form-control" name="year">
                        @for($y = date('Y'); $y >= date('Y')-5; $y--)
                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="filter-label">&nbsp;</div>
                    <div class="d-flex" style="gap:.5rem">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-sync-alt"></i> Perbarui
                        </button>
                        <a href="{{ route('accounting.income-statement.index') }}" class="btn btn-filter-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- STATEMENT CARD --}}
    <div class="statement-card" id="statementPrintArea">

        {{-- Company Header --}}
        <div class="statement-company-header">
            <h3>ASHAR GROSIR PARFUME</h3>
            <p>Sistem Manajemen Distribusi Parfum</p>
            <div class="report-title">LAPORAN LABA RUGI</div>
            <div class="report-date">Per {{ date('d/m/Y', strtotime($endDate)) }}</div>
        </div>

        <table class="stmt-table">

            {{-- ========== PENDAPATAN ========== --}}
            <tr>
                <td colspan="3" class="stmt-section-header stmt-section-income">
                    <i class="fas fa-arrow-up"></i> PENDAPATAN USAHA
                </td>
            </tr>
            @foreach($income as $i)
            @if($i['balance'] != 0)
            <tr class="stmt-row">
                <td style="padding-left:2.5rem">{{ $i['code'] }} — {{ $i['name'] }}</td>
                <td></td>
                <td>Rp {{ number_format($i['balance'], 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="stmt-subtotal subtotal-income">
                <td><strong>Total Pendapatan Usaha</strong></td>
                <td></td>
                <td><strong>Rp {{ number_format($ti, 0, ',', '.') }}</strong></td>
            </tr>

            {{-- ========== HPP ========== --}}
            <tr>
                <td colspan="3" class="stmt-section-header stmt-section-cogs">
                    <i class="fas fa-boxes"></i> HARGA POKOK PENJUALAN (HPP)
                </td>
            </tr>
            @php
                $cogs = ($expense ?? collect())->filter(fn($e) => str_starts_with($e['code'] ?? '', '5'));
                $totalCogs = $cogs->sum('balance');
            @endphp
            @foreach($cogs as $e)
            @if($e['balance'] != 0)
            <tr class="stmt-row">
                <td style="padding-left:2.5rem">{{ $e['code'] }} — {{ $e['name'] }}</td>
                <td></td>
                <td>Rp {{ number_format($e['balance'], 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="stmt-subtotal subtotal-cogs">
                <td><strong>Total HPP</strong></td>
                <td></td>
                <td><strong>Rp {{ number_format($totalCogs, 0, ',', '.') }}</strong></td>
            </tr>

            {{-- ========== LABA KOTOR ========== --}}
            <tr class="stmt-gross-profit">
                <td><i class="fas fa-equals mr-2"></i>LABA KOTOR</td>
                <td></td>
                <td>Rp {{ number_format($ti - $totalCogs, 0, ',', '.') }}</td>
            </tr>

            {{-- ========== BEBAN OPERASIONAL ========== --}}
            <tr>
                <td colspan="3" class="stmt-section-header stmt-section-opex">
                    <i class="fas fa-cogs"></i> BEBAN OPERASIONAL
                </td>
            </tr>
            @php
                $opex = ($expense ?? collect())->filter(fn($e) => !str_starts_with($e['code'] ?? '', '5') && !str_starts_with($e['code'] ?? '', '8'));
                $totalOpex = $opex->sum('balance');
            @endphp
            @foreach($opex as $e)
            @if($e['balance'] != 0)
            <tr class="stmt-row">
                <td style="padding-left:2.5rem">{{ $e['code'] }} — {{ $e['name'] }}</td>
                <td></td>
                <td>Rp {{ number_format($e['balance'], 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="stmt-subtotal subtotal-opex">
                <td><strong>Total Beban Operasional</strong></td>
                <td></td>
                <td><strong>Rp {{ number_format($totalOpex, 0, ',', '.') }}</strong></td>
            </tr>

            {{-- ========== LABA OPERASIONAL ========== --}}
            <tr class="stmt-operating-profit">
                <td><i class="fas fa-equals mr-2"></i>LABA OPERASIONAL</td>
                <td></td>
                <td>Rp {{ number_format($ti - $totalCogs - $totalOpex, 0, ',', '.') }}</td>
            </tr>

            {{-- ========== PENDAPATAN / BEBAN LAIN ========== --}}
            @php
                $other = ($expense ?? collect())->filter(fn($e) => str_starts_with($e['code'] ?? '', '8'));
                $totalOther = $other->sum('balance');
            @endphp
            @if($other->count() > 0)
            <tr>
                <td colspan="3" class="stmt-section-header stmt-section-other">
                    <i class="fas fa-ellipsis-h"></i> PENDAPATAN / BEBAN LAIN-LAIN
                </td>
            </tr>
            @foreach($other as $e)
            @if($e['balance'] != 0)
            <tr class="stmt-row">
                <td style="padding-left:2.5rem">{{ $e['code'] }} — {{ $e['name'] }}</td>
                <td></td>
                <td>Rp {{ number_format($e['balance'], 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="stmt-subtotal subtotal-other">
                <td><strong>Total Lain-lain</strong></td>
                <td></td>
                <td><strong>Rp {{ number_format($totalOther, 0, ',', '.') }}</strong></td>
            </tr>
            @endif

            {{-- ========== LABA BERSIH ========== --}}
            @php $netIncome = $ti - $te; @endphp
            <tr class="{{ $netIncome >= 0 ? 'stmt-net-income-positive' : 'stmt-net-income-negative' }}">
                <td>
                    <i class="fas fa-{{ $netIncome >= 0 ? 'check-circle' : 'times-circle' }} mr-2"></i>
                    LABA / (RUGI) BERSIH
                </td>
                <td></td>
                <td>Rp {{ number_format($netIncome, 0, ',', '.') }}</td>
            </tr>

        </table>

        {{-- Footer note --}}
        <div style="padding:1rem 1.5rem;font-size:.75rem;color:#aaa;border-top:1px solid #f0f0f0;text-align:right">
            Digenerate pada: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function exportPDF() {
    Swal.fire({
        title: 'Mengexport PDF...',
        text: 'Memproses laporan laba rugi',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'pdf');
    window.open(url.toString(), '_blank');
    setTimeout(() => Swal.close(), 4000);
}
</script>
@endpush
