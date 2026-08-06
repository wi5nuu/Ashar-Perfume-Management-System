@extends('layouts.app')

@section('title', 'Jurnal Umum')

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

/* Stats row */
.stats-row { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
.stat-chip {
    background:#fff; border-radius:10px; padding:.7rem 1.1rem;
    box-shadow:0 2px 10px rgba(0,0,0,.06); border:1px solid rgba(0,0,0,.04);
    display:flex; align-items:center; gap:.7rem; flex:1; min-width:160px;
}
.stat-chip .chip-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
.stat-chip .chip-val { font-size:1.1rem; font-weight:800; color:var(--secondary); line-height:1.1; }
.stat-chip .chip-lbl { font-size:.72rem; color:#aaa; }

/* Table card */
.table-card { background:#fff; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); overflow:hidden; }
.table-card-header { padding:1rem 1.4rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f5f5f5; }
.table-card-header h5 { font-size:.93rem; font-weight:700; color:var(--secondary); margin:0; display:flex; align-items:center; gap:.5rem; }
.table-card-header h5 i { color:var(--primary); }

.table-modern { margin:0; width:100%; }
.table-modern thead th { background:#f8f9fb; color:#666; font-size:.74rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:.75rem 1.1rem; border:none; white-space:nowrap; }
.table-modern tbody td { padding:.82rem 1.1rem; border:none; border-bottom:1px solid #f5f5f5; font-size:.85rem; color:var(--secondary); vertical-align:middle; }
.table-modern tbody tr:last-child td { border-bottom:none; }
.table-modern tbody tr:hover td { background:#fafafa; }
.table-modern .text-right { text-align:right; }

/* Running balance column */
.running-balance { font-size:.82rem; font-weight:600; }
.running-balance.positive { color:#27AE60; }
.running-balance.negative { color:#E74C3C; }

/* Status badges */
.badge-posted { background:rgba(39,174,96,.1); color:#27AE60; font-size:.74rem; font-weight:700; padding:.25rem .65rem; border-radius:20px; }
.badge-draft  { background:rgba(243,156,18,.1); color:#F39C12; font-size:.74rem; font-weight:700; padding:.25rem .65rem; border-radius:20px; }

/* Action buttons */
.btn-view { background:rgba(41,128,185,.1); color:#2980B9; border:none; padding:.3rem .65rem; border-radius:7px; font-size:.8rem; transition:all .2s; }
.btn-view:hover { background:#2980B9; color:#fff; }
.btn-new-journal {
    background:var(--primary); color:#fff; border:none;
    padding:.5rem 1.2rem; border-radius:9px; font-weight:600; font-size:.87rem;
    display:inline-flex; align-items:center; gap:.4rem; transition:background .2s;
    text-decoration:none;
}
.btn-new-journal:hover { background:var(--primary-dark); color:#fff; text-decoration:none; }

/* Journal number pill */
.journal-num {
    font-family: 'Courier New', monospace;
    background:rgba(45,48,71,.06); color:var(--secondary);
    padding:.2rem .6rem; border-radius:6px; font-size:.82rem; font-weight:600;
}

/* Pagination override */
.pagination { margin:0; }
.page-link { border-radius:7px !important; margin:0 2px; border:1.5px solid #e8e8e8 !important; color:var(--secondary); font-size:.83rem; }
.page-item.active .page-link { background:var(--primary) !important; border-color:var(--primary) !important; color:#fff !important; }
.page-link:hover { border-color:var(--primary) !important; color:var(--primary) !important; background:#fff !important; }
</style>

@endpush

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <h4><i class="fas fa-book-open"></i> Jurnal Akuntansi</h4>
        <div class="d-flex align-items-center" style="gap:.6rem">
            <a href="{{ route('accounting.index') }}" class="btn btn-filter-reset">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>
            <a href="{{ route('accounting.journal.create') }}" class="btn-new-journal">
                <i class="fas fa-plus"></i> Buat Jurnal Baru
            </a>
        </div>
    </div>

    {{-- STATS CHIPS --}}
    <div class="stats-row">
        <div class="stat-chip">
            <div class="chip-icon" style="background:rgba(255,107,53,.1);color:var(--primary)"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="chip-val">{{ $journals->total() }}</div>
                <div class="chip-lbl">Total Jurnal</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="chip-icon" style="background:rgba(39,174,96,.1);color:#27AE60"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="chip-val">{{ $journals->where('status','posted')->count() }}</div>
                <div class="chip-lbl">Posted</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="chip-icon" style="background:rgba(243,156,18,.1);color:#F39C12"><i class="fas fa-clock"></i></div>
            <div>
                <div class="chip-val">{{ $journals->where('status','draft')->count() }}</div>
                <div class="chip-lbl">Draft</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="chip-icon" style="background:rgba(41,128,185,.1);color:#2980B9"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="chip-val">Rp {{ number_format($journals->sum('total_debit'), 0, ',', '.') }}</div>
                <div class="chip-lbl">Total Debit (halaman ini)</div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('accounting.journal.index') }}">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Status</div>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="draft"  {{ request('status')==='draft'  ? 'selected' : '' }}>Draft</option>
                        <option value="posted" {{ request('status')==='posted' ? 'selected' : '' }}>Posted</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Dari Tanggal</div>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Sampai Tanggal</div>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Cari Jurnal</div>
                    <input type="text" name="search" class="form-control" placeholder="No. atau deskripsi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="filter-label">&nbsp;</div>
                    <div class="d-flex" style="gap:.5rem">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('accounting.journal.index') }}" class="btn btn-filter-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="fas fa-table"></i> Daftar Jurnal</h5>
            <span style="font-size:.8rem;color:#aaa">
                Menampilkan {{ $journals->firstItem() ?? 0 }}–{{ $journals->lastItem() ?? 0 }} dari {{ $journals->total() }} jurnal
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>No. Jurnal</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Kredit</th>
                        <th class="text-right">Running Balance</th>
                        <th>Status</th>
                        <th style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = 0; @endphp
                    @forelse($journals as $j)
                    @php $runningBalance += $j->total_debit - $j->total_credit; @endphp
                    <tr>
                        <td>
                            <span class="journal-num">{{ $j->journal_number }}</span>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.85rem">{{ $j->date->format('d/m/Y') }}</div>
                            <div style="font-size:.74rem;color:#aaa">{{ $j->date->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <div style="font-size:.85rem;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $j->description }}
                            </div>
                        </td>
                        <td class="text-right">
                            <span style="font-weight:600;color:#27AE60">Rp {{ number_format($j->total_debit, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">
                            <span style="font-weight:600;color:#E74C3C">Rp {{ number_format($j->total_credit, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">
                            <span class="running-balance {{ $runningBalance >= 0 ? 'positive' : 'negative' }}">
                                {{ $runningBalance >= 0 ? '' : '-' }}Rp {{ number_format(abs($runningBalance), 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if($j->status === 'posted')
                            <span class="badge-posted"><i class="fas fa-check mr-1"></i>Posted</span>
                            @else
                            <span class="badge-draft"><i class="fas fa-clock mr-1"></i>Draft</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('accounting.journal.show', $j->id) }}" class="btn-view" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-book d-block mb-2" style="font-size:2rem;color:#ddd"></i>
                            <span style="color:#aaa;font-size:.88rem">Belum ada jurnal</span>
                            <br>
                            <a href="{{ route('accounting.journal.create') }}" class="btn-new-journal mt-2 d-inline-flex" style="font-size:.82rem">
                                <i class="fas fa-plus"></i> Buat Jurnal Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($journals->hasPages())
        <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid #f5f5f5">
            <div style="font-size:.82rem;color:#aaa">
                Halaman {{ $journals->currentPage() }} dari {{ $journals->lastPage() }}
            </div>
            {{ $journals->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
