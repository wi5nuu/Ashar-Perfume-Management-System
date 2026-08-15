@extends('layouts.app')

@section('title', 'Neraca Saldo')

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
    .table-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); overflow: hidden; }
    .table-card .table { margin: 0; font-size: 0.85rem; color: var(--secondary); }
    .table-card .table thead th { background: #f8f9ff; border-bottom: 2px solid #eef0f8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #8892a4; padding: 0.85rem 1rem; border-top: none; }
    .table-card .table tbody tr { border-bottom: 1px solid #f5f6fb; transition: background 0.15s; }
    .table-card .table tbody tr:last-child { border-bottom: none; }
    .table-card .table tbody tr:hover { background: #fafbff; }
    .table-card .table tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-top: none; }
    .table-card .table tfoot tr { background: #f8f9ff; }
    .table-card .table tfoot td { padding: 0.9rem 1rem; border-top: 2px solid #eef0f8; font-weight: 800; font-size: 0.9rem; color: var(--secondary); }
    .amount-debit  { color: #1565c0; font-weight: 600; }
    .amount-credit { color: #2e7d32; font-weight: 600; }
    .balance-ok    { color: #2e7d32; }
    .balance-err   { color: #c62828; }
    .acct-code-badge { font-size: 0.72rem; font-weight: 700; background: #f0f2f8; color: #667; padding: 2px 7px; border-radius: 5px; font-family: monospace; }
    .balance-status-bar { border-radius: 10px; padding: 0.7rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; font-size: 0.85rem; font-weight: 600; }
    .balance-status-bar.ok  { background: #e8f5e9; border: 1px solid #a5d6a7; color: #2e7d32; }
    .balance-status-bar.err { background: #fce4ec; border: 1px solid #ef9a9a; color: #c62828; }
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; border: 1.5px solid; transition: all 0.15s; text-decoration: none; cursor: pointer; }
    .btn-export.pdf { border-color: #ef5350; color: #ef5350; background: rgba(239,83,80,0.05); }
    .btn-export.pdf:hover { background: #ef5350; color: #fff; text-decoration: none; }
    .btn-export.csv { border-color: #388e3c; color: #388e3c; background: rgba(56,142,60,0.05); }
    .btn-export.csv:hover { background: #388e3c; color: #fff; text-decoration: none; }
    .btn-export.print { border-color: var(--secondary); color: var(--secondary); background: rgba(45,48,71,0.04); }
    .btn-export.print:hover { background: var(--secondary); color: #fff; text-decoration: none; }
    .period-chip { display: inline-flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.15); border-radius: 6px; padding: 3px 10px; font-size: 0.78rem; color: rgba(255,255,255,0.9); }
</style>
@endpush

@section('content')
<div class="container-fluid pb-4">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <div>
            <h1><i class="fas fa-balance-scale mr-2" style="color:var(--primary)"></i>Neraca Saldo</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounting.index') }}">Akuntansi</a></li>
                    <li class="breadcrumb-item active">Neraca Saldo</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap:0.5rem">
            <span class="period-chip"><i class="fas fa-calendar-alt"></i>Per {{ \Carbon\Carbon::parse($data['to'])->format('d M Y') }}</span>
            <button onclick="window.print()" class="btn-export print"><i class="fas fa-print"></i> Cetak</button>
            <a href="{{ route('accounting.trial-balance.index', array_merge(request()->query(), ['export'=>'pdf'])) }}" class="btn-export pdf"><i class="fas fa-file-pdf"></i> PDF</a>
            <a href="{{ route('accounting.trial-balance.index', array_merge(request()->query(), ['export'=>'csv'])) }}" class="btn-export csv"><i class="fas fa-file-csv"></i> CSV</a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row align-items-end" style="row-gap:0.75rem">
                <div class="col-md-4">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" value="{{ $data['from'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" value="{{ $data['to'] }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn w-100" style="background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.85rem;height:calc(1.5em + 0.75rem + 2px)">
                        <i class="fas fa-sync-alt mr-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Balance Status --}}
    <div class="balance-status-bar {{ $data['is_balanced'] ? 'ok' : 'err' }}">
        @if($data['is_balanced'])
            <i class="fas fa-check-circle fa-lg"></i>
            <span>Neraca seimbang &mdash; Total Debit = Total Kredit = Rp {{ number_format($data['total_debit'], 0, ',', '.') }}</span>
        @else
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <span>Neraca tidak seimbang! Selisih: Rp {{ number_format(abs($data['total_debit'] - $data['total_credit']), 0, ',', '.') }}</span>
        @endif
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:120px">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-right" style="color:#1565c0;width:180px">Debit</th>
                        <th class="text-right" style="color:#2e7d32;width:180px">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['rows'] as $acc)
                    <tr>
                        <td><span class="acct-code-badge">{{ $acc['code'] }}</span></td>
                        <td style="font-weight:500;color:var(--secondary)">{{ $acc['name'] }}</td>
                        <td class="text-right">
                            @if($acc['debit'] > 0)
                                <span class="amount-debit">Rp {{ number_format($acc['debit'], 0, ',', '.') }}</span>
                            @else
                                <span style="color:#e0e0e0">&mdash;</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($acc['credit'] > 0)
                                <span class="amount-credit">Rp {{ number_format($acc['credit'], 0, ',', '.') }}</span>
                            @else
                                <span style="color:#e0e0e0">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5" style="color:#8892a4;font-size:0.85rem">
                            <i class="fas fa-inbox fa-2x d-block mb-2" style="color:#c0c8d8"></i>Belum ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.4px">TOTAL</td>
                        <td class="text-right">
                            <span class="amount-debit" style="font-size:0.95rem">Rp {{ number_format($data['total_debit'], 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">
                            <span class="amount-credit" style="font-size:0.95rem">Rp {{ number_format($data['total_credit'], 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @if(!$data['is_balanced'])
                    <tr style="background:#fce4ec">
                        <td colspan="2" class="text-right" style="color:#c62828;font-size:0.78rem">SELISIH (TIDAK BALANCE)</td>
                        <td colspan="2" class="text-right" style="color:#c62828">
                            Rp {{ number_format(abs($data['total_debit'] - $data['total_credit']), 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
