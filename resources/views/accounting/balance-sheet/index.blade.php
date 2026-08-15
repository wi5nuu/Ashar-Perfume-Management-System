@extends('layouts.app')

@section('title', 'Neraca Keuangan')

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

/* Balance sheet layout */
.bs-card {
    background:#fff; border-radius:16px; box-shadow:0 2px 20px rgba(0,0,0,.08);
    border:1px solid rgba(0,0,0,.05); overflow:hidden; margin-bottom:1.5rem;
}
.bs-company-header {
    background: linear-gradient(135deg, var(--secondary) 0%, #1a1d2e 100%);
    padding: 1.6rem 2rem; color:#fff; text-align:center;
}
.bs-company-header h3 { font-size:1.2rem; font-weight:700; margin-bottom:.2rem; letter-spacing:.5px; }
.bs-company-header p { font-size:.82rem; color:rgba(255,255,255,.6); margin:0; }
.bs-company-header .report-title { font-size:1rem; font-weight:600; color:rgba(255,255,255,.9); margin-top:.4rem; }
.bs-company-header .report-date { font-size:.82rem; color:rgba(255,107,53,.9); font-weight:600; margin-top:.2rem; }

.bs-col-header {
    padding:.7rem 1.2rem;
    font-size:.8rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px;
    text-align:center;
}
.bs-col-assets      { background:rgba(41,128,185,.08);  color:#1a5e8a;  border-bottom:3px solid #2980B9; }
.bs-col-liabilities { background:rgba(231,76,60,.08);   color:#c0392b;  border-bottom:3px solid #E74C3C; }
.bs-col-equity      { background:rgba(39,174,96,.08);   color:#1a8a4a;  border-bottom:3px solid #27AE60; }

.bs-section-label {
    padding:.55rem 1.2rem;
    font-size:.74rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px;
    display:flex; align-items:center; gap:.4rem;
}
.bs-section-current-asset { background:rgba(41,128,185,.06); color:#1a5e8a; border-left:3px solid #2980B9; }
.bs-section-fixed-asset   { background:rgba(142,68,173,.06); color:#6c2f9c; border-left:3px solid #8e44ad; }
.bs-section-curr-liab     { background:rgba(231,76,60,.06);  color:#c0392b; border-left:3px solid #E74C3C; }
.bs-section-lt-liab       { background:rgba(243,156,18,.06); color:#c47a00; border-left:3px solid #F39C12; }
.bs-section-equity-lbl    { background:rgba(39,174,96,.06);  color:#1a8a4a; border-left:3px solid #27AE60; }

.bs-row td { padding:.6rem 1.2rem; font-size:.85rem; color:#555; border-bottom:1px solid #f9f9f9; vertical-align:middle; }
.bs-row:hover td { background:#fafafa; }
.bs-row .acct-amount { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }

.bs-subtotal td { padding:.7rem 1.2rem !important; font-size:.86rem !important; font-weight:700 !important; border-bottom:1px solid #eee !important; }
.bs-subtotal-assets      { background:rgba(41,128,185,.07) !important; color:#1a5e8a !important; }
.bs-subtotal-liabilities { background:rgba(231,76,60,.07)  !important; color:#c0392b  !important; }
.bs-subtotal-equity      { background:rgba(39,174,96,.07)  !important; color:#1a8a4a  !important; }

.bs-grand-total td { padding:.9rem 1.2rem !important; font-size:.95rem !important; font-weight:800 !important; }
.bs-grand-assets { background:linear-gradient(90deg, rgba(41,128,185,.15), rgba(41,128,185,.06)) !important; color:#1a5e8a !important; border-top:2px solid #2980B9 !important; border-bottom:3px double #2980B9 !important; }
.bs-grand-liab-equity { background:linear-gradient(90deg, rgba(231,76,60,.15), rgba(231,76,60,.06)) !important; color:#c0392b !important; border-top:2px solid #E74C3C !important; border-bottom:3px double #E74C3C !important; }

.balance-check {
    margin:0 1.5rem 1.5rem;
    padding:.9rem 1.3rem;
    border-radius:10px;
    font-size:.88rem; font-weight:700;
    display:flex; align-items:center; gap:.6rem;
}
.balance-ok      { background:rgba(39,174,96,.1);  color:#1a8a4a; border:1.5px solid rgba(39,174,96,.25); }
.balance-not-ok  { background:rgba(231,76,60,.1);  color:#c0392b; border:1.5px solid rgba(231,76,60,.25); }

.btn-export-pdf { background:#E74C3C; color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; text-decoration:none; }
.btn-export-pdf:hover { background:#c0392b; color:#fff; }
.btn-print { background:var(--secondary); color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-print:hover { background:#1a1d2e; color:#fff; }

@media print {
    .page-header-bar, .filter-bar, .sidebar, .navbar, .content-header, .main-footer { display:none !important; }
    .bs-card { box-shadow:none !important; border:none !important; }
    body { background:#fff !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <h4><i class="fas fa-columns"></i> Neraca (Balance Sheet)</h4>
        <div class="d-flex align-items-center" style="gap:.6rem">
            <a href="{{ route('accounting.index') }}" class="btn btn-filter-reset">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
            <a class="btn-export-pdf" href="{{ route('accounting.balance-sheet.index', array_merge(request()->query(), ['export'=>'pdf'])) }}">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('accounting.balance-sheet.index') }}">
            <div class="row align-items-end">
                <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Per Tanggal</div>
                    <input type="date" class="form-control" name="as_of" value="{{ $data['as_of'] }}">
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="filter-label">&nbsp;</div>
                    <div class="d-flex" style="gap:.5rem">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-sync-alt"></i> Perbarui
                        </button>
                        <a href="{{ route('accounting.balance-sheet.index') }}" class="btn btn-filter-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- BALANCE SHEET CARD --}}
    <div class="bs-card" id="bsPrintArea">

        {{-- Company Header --}}
        <div class="bs-company-header">
            <h3>ASHAR GROSIR PARFUME</h3>
            <p>Sistem Manajemen Distribusi Parfum</p>
            <div class="report-title">NERACA (BALANCE SHEET)</div>
            <div class="report-date">Per Tanggal: {{ \Carbon\Carbon::parse($data['as_of'])->format('d/m/Y') }}</div>
        </div>

        {{-- Balance check --}}
        <div class="balance-check {{ $data['is_balanced'] ? 'balance-ok' : 'balance-not-ok' }} mt-3">
            <i class="fas fa-{{ $data['is_balanced'] ? 'check-circle' : 'exclamation-triangle' }}"></i>
            @if($data['is_balanced'])
                Neraca <strong>Seimbang</strong> &mdash; Total Aset = Total Kewajiban + Ekuitas = Rp {{ number_format($data['total_assets'], 0, ',', '.') }}
            @else
                Neraca <strong>Tidak Seimbang</strong> &mdash; Selisih: Rp {{ number_format(abs($data['total_assets'] - $data['total_liability_equity']), 0, ',', '.') }}
            @endif
        </div>

        {{-- TWO COLUMN LAYOUT --}}
        <div class="row no-gutters">

            {{-- ====== ASET COLUMN ====== --}}
            <div class="col-md-6" style="border-right:2px solid #f0f0f0">

                <div class="bs-col-header bs-col-assets">
                    <i class="fas fa-university mr-1"></i> ASET
                </div>

                <table style="width:100%;border-collapse:collapse">
                    <tr><td colspan="2" class="bs-section-label bs-section-current-asset"><i class="fas fa-wallet"></i> Aset</td></tr>
                    @foreach($data['assets'] as $a)
                    <tr class="bs-row">
                        <td style="padding-left:2rem">{{ $a['code'] }} — {{ $a['name'] }}</td>
                        <td class="acct-amount">Rp {{ number_format($a['balance'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="bs-grand-total bs-grand-assets">
                        <td><i class="fas fa-equals mr-2"></i>TOTAL ASET</td>
                        <td class="acct-amount">Rp {{ number_format($data['total_assets'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            {{-- ====== KEWAJIBAN + EKUITAS COLUMN ====== --}}
            <div class="col-md-6">

                <div class="bs-col-header bs-col-liabilities">
                    <i class="fas fa-file-invoice mr-1"></i> KEWAJIBAN &amp; EKUITAS
                </div>

                <table style="width:100%;border-collapse:collapse">

                    <tr><td colspan="2" class="bs-section-label bs-section-curr-liab"><i class="fas fa-clock"></i> Kewajiban</td></tr>
                    @foreach($data['liabilities'] as $l)
                    <tr class="bs-row">
                        <td style="padding-left:2rem">{{ $l['code'] }} — {{ $l['name'] }}</td>
                        <td class="acct-amount">Rp {{ number_format($l['balance'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="bs-subtotal bs-subtotal-liabilities">
                        <td>Total Kewajiban</td>
                        <td class="acct-amount">Rp {{ number_format($data['total_liabilities'], 0, ',', '.') }}</td>
                    </tr>

                    <tr><td colspan="2" class="bs-section-label bs-section-equity-lbl"><i class="fas fa-coins"></i> Ekuitas / Modal</td></tr>
                    @foreach($data['equities'] as $e)
                    <tr class="bs-row">
                        <td style="padding-left:2rem">{{ $e['code'] }} — {{ $e['name'] }}</td>
                        <td class="acct-amount">Rp {{ number_format($e['balance'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="bs-row" style="font-weight:600;color:#2980B9">
                        <td style="padding-left:2rem"><i class="fas fa-chart-line mr-1"></i>Laba Berjalan</td>
                        <td class="acct-amount">Rp {{ number_format($data['net_income'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bs-subtotal bs-subtotal-equity">
                        <td>Total Ekuitas</td>
                        <td class="acct-amount">Rp {{ number_format($data['total_equity'], 0, ',', '.') }}</td>
                    </tr>

                    <tr class="bs-grand-total bs-grand-liab-equity">
                        <td><i class="fas fa-equals mr-2"></i>TOTAL KEWAJIBAN + EKUITAS</td>
                        <td class="acct-amount">Rp {{ number_format($data['total_liability_equity'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:1rem 1.5rem;font-size:.75rem;color:#aaa;border-top:1px solid #f0f0f0;text-align:right">
            Digenerate pada: {{ now()->format('d M Y, H:i') }} WIB &nbsp;|&nbsp; Semua nilai dalam Rupiah (Rp)
        </div>
    </div>

</div>
@endsection
