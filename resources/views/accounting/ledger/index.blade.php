@extends('layouts.app')

@section('title', 'Buku Besar')

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
    .acct-header-card { background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%); border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 8px rgba(45,48,71,0.05); padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
    .acct-code { font-size: 0.75rem; font-weight: 700; color: #8892a4; text-transform: uppercase; letter-spacing: 0.5px; }
    .acct-name { font-size: 1.25rem; font-weight: 800; color: var(--secondary); margin: 2px 0; }
    .acct-balance-label { font-size: 0.72rem; color: #8892a4; text-transform: uppercase; letter-spacing: 0.4px; }
    .acct-balance-val { font-size: 1.1rem; font-weight: 700; color: var(--secondary); }
    .table-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.07); overflow: hidden; }
    .table-card .table { margin: 0; font-size: 0.85rem; color: var(--secondary); }
    .table-card .table thead th { background: #f8f9ff; border-bottom: 2px solid #eef0f8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #8892a4; padding: 0.85rem 1rem; border-top: none; white-space: nowrap; }
    .table-card .table tbody tr { border-bottom: 1px solid #f5f6fb; transition: background 0.15s; }
    .table-card .table tbody tr:last-child { border-bottom: none; }
    .table-card .table tbody tr:hover { background: #fafbff; }
    .table-card .table tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-top: none; }
    .table-card .table tfoot td { padding: 0.85rem 1rem; font-weight: 700; background: #f8f9ff; border-top: 2px solid #eef0f8; color: var(--secondary); font-size: 0.88rem; }
    .amount-debit  { color: #1565c0; font-weight: 600; }
    .amount-credit { color: #2e7d32; font-weight: 600; }
    .amount-balance-pos { color: #1565c0; font-weight: 700; }
    .amount-balance-neg { color: #c62828; font-weight: 700; }
    .journal-num { font-size: 0.78rem; color: #7b1fa2; font-weight: 600; background: #f3e5f5; padding: 2px 7px; border-radius: 4px; }
    .empty-state { padding: 3.5rem 1rem; text-align: center; }
    .empty-state .empty-icon { width: 64px; height: 64px; background: #f5f6fb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: #c0c8d8; }
    .empty-state h6 { font-weight: 600; color: var(--secondary); margin-bottom: 4px; }
    .empty-state p { font-size: 0.83rem; color: #8892a4; margin: 0; }
    .btn-export { display: inline-flex; align-items: center; gap: 6px; padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; border: 1.5px solid; transition: all 0.15s; text-decoration: none; cursor: pointer; }
    .btn-export.pdf { border-color: #ef5350; color: #ef5350; background: rgba(239,83,80,0.05); }
    .btn-export.pdf:hover { background: #ef5350; color: #fff; text-decoration: none; }
    .btn-export.print { border-color: var(--secondary); color: var(--secondary); background: rgba(45,48,71,0.04); }
    .btn-export.print:hover { background: var(--secondary); color: #fff; text-decoration: none; }
</style>

@endpush

@section('content')
<div class="container-fluid pb-4">

    {{-- Page Header --}}
    <div class="page-header-apms">
        <div>
            <h1><i class="fas fa-book mr-2" style="color:var(--primary)"></i>Buku Besar</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Akuntansi</a></li>
                    <li class="breadcrumb-item active">Buku Besar</li>
                </ol>
            </nav>
        </div>
        @if($accountId)
        <div class="d-flex" style="gap:0.5rem">
            <button onclick="window.print()" class="btn-export print">
                <i class="fas fa-print"></i> Cetak
            </button>
            <a href="{{ route('accounting.ledger.index', array_merge(request()->query(), ['export'=>'pdf'])) }}" class="btn-export pdf">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
        @endif
    </div>

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row align-items-end" style="row-gap:0.75rem">
                <div class="col-md-5">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Pilih Akun</label>
                    <select name="account_id" class="form-control" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} &mdash; {{ $acc->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-3 d-flex" style="gap:0.5rem">
                    <button type="submit" class="btn flex-fill" style="background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.85rem;">
                        <i class="fas fa-search mr-1"></i>Tampilkan
                    </button>
                    @if($accountId)
                    <a href="{{ route('accounting.ledger.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;padding:0 0.75rem" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($accountId && $data)
    {{-- Account Header --}}
    <div class="acct-header-card">
        <div>
            <div class="acct-code">{{ $data['account']->code }}</div>
            <div class="acct-name">{{ $data['account']->name }}</div>
            <div style="font-size:0.78rem;color:#8892a4;margin-top:2px">
                Tipe: {{ \App\Models\ChartOfAccount::TYPES[$data['account']->type] ?? $data['account']->type }}
                &bull; Normal Balance: <span style="font-weight:600;color:var(--secondary)">{{ strtoupper($data['account']->normal_balance ?? '-') }}</span>
            </div>
        </div>
        <div class="text-right">
            <div class="acct-balance-label">Saldo Akhir</div>
            <div class="acct-balance-val {{ $data['closing_balance'] >= 0 ? 'amount-balance-pos' : 'amount-balance-neg' }}">
                Rp {{ number_format(abs($data['closing_balance']), 0, ',', '.') }}
                <span style="font-size:0.72rem;font-weight:600">{{ $data['closing_balance'] >= 0 ? 'Dr' : 'Cr' }}</span>
            </div>
            <div style="font-size:0.72rem;color:#8892a4;margin-top:2px">
                {{ \Carbon\Carbon::parse($data['from'])->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($data['to'])->format('d M Y') }}
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Jurnal</th>
                        <th>Keterangan</th>
                        <th class="text-right" style="color:#1565c0">Debit</th>
                        <th class="text-right" style="color:#2e7d32">Kredit</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background:#f5f6fb">
                        <td colspan="3" style="font-size:0.78rem;font-weight:700;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Saldo Awal</td>
                        <td class="text-right" colspan="2"></td>
                        <td class="text-right">
                            <span class="{{ $data['opening_balance'] >= 0 ? 'amount-balance-pos' : 'amount-balance-neg' }}">
                                Rp {{ number_format(abs($data['opening_balance']), 0, ',', '.') }}
                                <span style="font-size:0.65rem">{{ $data['opening_balance'] >= 0 ? 'Dr' : 'Cr' }}</span>
                            </span>
                        </td>
                    </tr>
                    @forelse($data['rows'] as $d)
                    <tr>
                        <td style="font-weight:500;font-size:0.84rem">{{ \Carbon\Carbon::parse($d['date'])->format('d M Y') }}</td>
                        <td>
                            <span class="journal-num">{{ $d['journal_number'] }}</span>
                        </td>
                        <td style="max-width:280px">
                            <div style="font-size:0.84rem;color:var(--secondary)">{{ $d['description'] }}</div>
                            @if($d['memo'])
                            <div style="font-size:0.72rem;color:#b0b8c9">{{ $d['memo'] }}</div>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($d['debit'] > 0)
                                <span class="amount-debit">Rp {{ number_format($d['debit'], 0, ',', '.') }}</span>
                            @else
                                <span style="color:#e0e0e0">&mdash;</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($d['credit'] > 0)
                                <span class="amount-credit">Rp {{ number_format($d['credit'], 0, ',', '.') }}</span>
                            @else
                                <span style="color:#e0e0e0">&mdash;</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <span class="{{ $d['running_balance'] >= 0 ? 'amount-balance-pos' : 'amount-balance-neg' }}">
                                Rp {{ number_format(abs($d['running_balance']), 0, ',', '.') }}
                                <span style="font-size:0.65rem">{{ $d['running_balance'] >= 0 ? 'Dr' : 'Cr' }}</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-book"></i></div>
                                <h6>Tidak Ada Transaksi</h6>
                                <p>Tidak ada transaksi pada periode yang dipilih.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right" style="font-size:0.8rem;letter-spacing:0.3px;text-transform:uppercase">Saldo Akhir</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right">
                            <span class="{{ $data['closing_balance'] >= 0 ? 'amount-balance-pos' : 'amount-balance-neg' }}" style="font-size:0.95rem">
                                Rp {{ number_format(abs($data['closing_balance']), 0, ',', '.') }}
                                <span style="font-size:0.7rem">{{ $data['closing_balance'] >= 0 ? 'Dr' : 'Cr' }}</span>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="table-card">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-book-open"></i></div>
            <h6>Pilih Akun untuk Menampilkan Buku Besar</h6>
            <p>Gunakan filter di atas untuk memilih akun dan periode yang ingin ditampilkan.</p>
        </div>
    </div>
    @endif

</div>
@endsection