@extends('layouts.app')

@section('title', 'Dashboard Akuntansi')

@push('styles')
<style>
:root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#2D3047; }

.page-hero {
    background: linear-gradient(135deg, var(--secondary) 0%, #1a1d2e 60%, #3d2b1f 100%);
    border-radius: 16px; padding: 1.6rem 2rem; margin-bottom: 1.8rem; color: #fff;
    position: relative; overflow: hidden;
}
.page-hero::before { content:''; position:absolute; top:-50px; right:-50px; width:160px; height:160px; border-radius:50%; background:rgba(255,107,53,.15); }
.page-hero h2 { font-size:1.5rem; font-weight:700; margin-bottom:.25rem; }
.page-hero p { color:rgba(255,255,255,.65); margin:0; font-size:.88rem; }
.page-hero .hero-actions { margin-top:1rem; display:flex; gap:.6rem; flex-wrap:wrap; }
.btn-hero-primary { background:var(--primary); color:#fff; border:none; padding:.45rem 1.1rem; border-radius:8px; font-weight:600; font-size:.84rem; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; text-decoration:none; }
.btn-hero-primary:hover { background:var(--primary-dark); color:#fff; text-decoration:none; }
.btn-hero-outline { background:rgba(255,255,255,.1); color:#fff; border:1.5px solid rgba(255,255,255,.25); padding:.45rem 1.1rem; border-radius:8px; font-weight:600; font-size:.84rem; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; text-decoration:none; }
.btn-hero-outline:hover { background:rgba(255,255,255,.2); color:#fff; text-decoration:none; }

/* KPI */
.kpi-card { background:#fff; border-radius:14px; padding:1.3rem 1.5rem; box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); transition:transform .2s, box-shadow .2s; height:100%; }
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 6px 24px rgba(0,0,0,.11); }
.kpi-card .kpi-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:.8rem; }
.kpi-card .kpi-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
.kpi-card .kpi-value { font-size:1.4rem; font-weight:800; color:var(--secondary); line-height:1.1; }
.kpi-card .kpi-label { font-size:.74rem; color:#999; text-transform:uppercase; letter-spacing:.4px; margin-top:.2rem; }
.kpi-card .kpi-sub { font-size:.77rem; color:#bbb; margin-top:.4rem; }

/* Quick link cards */
.quicklink-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:.8rem; }
@media(max-width:576px) { .quicklink-grid { grid-template-columns:repeat(2,1fr); } }
.quicklink-item {
    background:#fff; border-radius:12px; padding:1rem; text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,.06); border:1.5px solid rgba(0,0,0,.04);
    transition:all .2s; text-decoration:none; color:inherit; display:block;
}
.quicklink-item:hover { border-color:var(--primary); box-shadow:0 4px 18px rgba(255,107,53,.15); transform:translateY(-2px); text-decoration:none; color:inherit; }
.quicklink-item .ql-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; margin:0 auto .6rem; font-size:1.1rem; }
.quicklink-item .ql-label { font-size:.8rem; font-weight:600; color:var(--secondary); line-height:1.3; }

/* Cards */
.section-card { background:#fff; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); overflow:hidden; margin-bottom:1.5rem; }
.section-card-header { padding:1rem 1.4rem; border-bottom:1px solid #f5f5f5; display:flex; align-items:center; justify-content:space-between; }
.section-card-header h5 { font-size:.93rem; font-weight:700; color:var(--secondary); margin:0; display:flex; align-items:center; gap:.5rem; }
.section-card-header h5 i { color:var(--primary); }

/* Table */
.table-modern { margin:0; width:100%; }
.table-modern thead th { background:#f8f9fb; color:#666; font-size:.74rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; padding:.75rem 1.1rem; border:none; white-space:nowrap; }
.table-modern tbody td { padding:.8rem 1.1rem; border:none; border-bottom:1px solid #f5f5f5; font-size:.85rem; color:var(--secondary); vertical-align:middle; }
.table-modern tbody tr:last-child td { border-bottom:none; }
.table-modern tbody tr:hover td { background:#fafafa; }

/* Period badge */
.period-badge { background:rgba(255,107,53,.08); color:var(--primary); border:1px solid rgba(255,107,53,.2); border-radius:8px; padding:.4rem .9rem; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; }

/* Chart card */
.chart-card { background:#fff; border-radius:14px; padding:1.3rem 1.5rem; box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); }
.chart-card-title { font-size:.93rem; font-weight:700; color:var(--secondary); display:flex; align-items:center; gap:.5rem; margin-bottom:1.1rem; }
.chart-card-title i { color:var(--primary); }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HERO --}}
    <div class="page-hero">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2><i class="fas fa-landmark mr-2" style="color:var(--primary)"></i>Dashboard Akuntansi</h2>
                <p>Kelola keuangan, jurnal, dan laporan finansial bisnis distribusi parfum Anda.</p>
                @if($currentPeriod)
                <div class="period-badge mt-2">
                    <i class="fas fa-calendar-check"></i>
                    Periode Aktif: <strong>{{ $currentPeriod->name }}</strong>
                    &mdash; {{ $currentPeriod->start_date->format('d M Y') }} s/d {{ $currentPeriod->end_date->format('d M Y') }}
                </div>
                @endif
                <div class="hero-actions">
                    <a href="{{ route('accounting.journal.create') }}" class="btn-hero-primary">
                        <i class="fas fa-plus"></i> Jurnal Baru
                    </a>
                    <a href="{{ route('accounting.coa.index') }}" class="btn-hero-outline">
                        <i class="fas fa-book"></i> Kelola Akun
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="opacity:.1;font-size:7rem;line-height:1"><i class="fas fa-landmark"></i></div>
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(41,128,185,.1);color:#2980B9"><i class="fas fa-university"></i></div>
                    <span style="background:rgba(41,128,185,.08);color:#2980B9;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Aset</span>
                </div>
                <div class="kpi-value">Rp {{ number_format($totalAssets ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Aset</div>
                <div class="kpi-sub"><i class="fas fa-info-circle mr-1"></i>Aset lancar + tetap</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(231,76,60,.1);color:#E74C3C"><i class="fas fa-file-invoice"></i></div>
                    <span style="background:rgba(231,76,60,.08);color:#E74C3C;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Liabilitas</span>
                </div>
                <div class="kpi-value">Rp {{ number_format($totalLiabilities ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Kewajiban</div>
                <div class="kpi-sub"><i class="fas fa-info-circle mr-1"></i>Kewajiban lancar + jk panjang</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(142,68,173,.1);color:#8e44ad"><i class="fas fa-coins"></i></div>
                    <span style="background:rgba(142,68,173,.08);color:#8e44ad;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Ekuitas</span>
                </div>
                <div class="kpi-value">Rp {{ number_format(($totalAssets ?? 0) - ($totalLiabilities ?? 0), 0, ',', '.') }}</div>
                <div class="kpi-label">Modal / Ekuitas</div>
                <div class="kpi-sub"><i class="fas fa-info-circle mr-1"></i>Aset - Kewajiban</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(39,174,96,.1);color:#27AE60"><i class="fas fa-chart-line"></i></div>
                    <span style="background:rgba(39,174,96,.08);color:#27AE60;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Profit</span>
                </div>
                <div class="kpi-value" style="color:{{ ($netIncome ?? 0) >= 0 ? '#27AE60' : '#E74C3C' }}">
                    Rp {{ number_format($netIncome ?? 0, 0, ',', '.') }}
                </div>
                <div class="kpi-label">Laba Bersih</div>
                <div class="kpi-sub"><i class="fas fa-calendar mr-1"></i>Periode aktif</div>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- QUICK LINKS --}}
        <div class="col-lg-4 mb-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="fas fa-th"></i> Modul Akuntansi</h5>
                </div>
                <div class="p-3">
                    <div class="quicklink-grid">
                        <a href="{{ route('accounting.journal.index') }}" class="quicklink-item">
                            <div class="ql-icon" style="background:rgba(255,107,53,.1);color:var(--primary)"><i class="fas fa-book-open"></i></div>
                            <div class="ql-label">Jurnal</div>
                        </a>
                        <a href="{{ route('accounting.ledger.index') }}" class="quicklink-item">
                            <div class="ql-icon" style="background:rgba(41,128,185,.1);color:#2980B9"><i class="fas fa-book"></i></div>
                            <div class="ql-label">Buku Besar</div>
                        </a>
                        <a href="{{ route('accounting.trial-balance.index') }}" class="quicklink-item">
                            <div class="ql-icon" style="background:rgba(39,174,96,.1);color:#27AE60"><i class="fas fa-balance-scale"></i></div>
                            <div class="ql-label">Trial Balance</div>
                        </a>
                        <a href="{{ route('accounting.income-statement.index') }}" class="quicklink-item">
                            <div class="ql-icon" style="background:rgba(142,68,173,.1);color:#8e44ad"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="ql-label">Laba Rugi</div>
                        </a>
                        <a href="{{ route('accounting.balance-sheet.index') }}" class="quicklink-item">
                            <div class="ql-icon" style="background:rgba(243,156,18,.1);color:#F39C12"><i class="fas fa-columns"></i></div>
                            <div class="ql-label">Neraca</div>
                        </a>
                        <a href="{{ route('accounting.cash-flow.index') }}" class="quicklink-item">
                            <div class="ql-icon" style="background:rgba(231,76,60,.1);color:#E74C3C"><i class="fas fa-water"></i></div>
                            <div class="ql-label">Arus Kas</div>
                        </a>
                    </div>

                    {{-- Periods --}}
                    <div class="mt-3">
                        <div style="font-size:.78rem;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.6rem">
                            Periode Akuntansi
                        </div>
                        @forelse($periods as $p)
                        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #f5f5f5;font-size:.84rem">
                            <div>
                                <div style="font-weight:600;color:var(--secondary)">{{ $p->name }}</div>
                                <div style="font-size:.75rem;color:#aaa">{{ $p->start_date->format('d/m/Y') }} - {{ $p->end_date->format('d/m/Y') }}</div>
                            </div>
                            @if($p->is_closed)
                            <span style="background:rgba(108,117,125,.1);color:#6c757d;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Tutup</span>
                            @else
                            <span style="background:rgba(39,174,96,.1);color:#27AE60;font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Aktif</span>
                            @endif
                        </div>
                        @empty
                        <div class="text-center text-muted py-2" style="font-size:.84rem">Belum ada periode</div>
                        @endforelse
                    </div>

                    {{-- Stats row --}}
                    <div class="row mt-3">
                        <div class="col-4 text-center" style="border-right:1px solid #f0f0f0">
                            <div style="font-size:1.2rem;font-weight:800;color:var(--secondary)">{{ $coaCount }}</div>
                            <div style="font-size:.72rem;color:#aaa">Total Akun</div>
                        </div>
                        <div class="col-4 text-center" style="border-right:1px solid #f0f0f0">
                            <div style="font-size:1.2rem;font-weight:800;color:var(--secondary)">{{ $journalCount }}</div>
                            <div style="font-size:.72rem;color:#aaa">Jurnal</div>
                        </div>
                        <div class="col-4 text-center">
                            <div style="font-size:1.2rem;font-weight:800;color:#F39C12">{{ $unpostedCount }}</div>
                            <div style="font-size:.72rem;color:#aaa">Draft</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- REVENUE vs EXPENSE CHART --}}
        <div class="col-lg-5 mb-4">
            <div class="chart-card" style="height:calc(100% - 1.5rem)">
                <div class="chart-card-title">
                    <i class="fas fa-chart-bar"></i> Pendapatan vs Pengeluaran (6 Bulan)
                </div>
                <canvas id="incomeExpenseChart" height="260" style="max-height:260px"></canvas>
            </div>
        </div>

        {{-- RECENT JOURNAL ENTRIES --}}
        <div class="col-lg-3 mb-4">
            <div class="section-card" style="height:calc(100% - 1.5rem)">
                <div class="section-card-header">
                    <h5><i class="fas fa-history"></i> Jurnal Terbaru</h5>
                    <a href="{{ route('accounting.journal.index') }}" style="font-size:.78rem;color:var(--primary);font-weight:600">Lihat Semua</a>
                </div>
                <div class="p-0">
                    @forelse($recentJournals ?? [] as $j)
                    <a href="{{ route('accounting.journal.show', $j->id) }}" style="display:flex;align-items:center;gap:.8rem;padding:.85rem 1.1rem;border-bottom:1px solid #f5f5f5;text-decoration:none;transition:background .15s" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                        <div style="width:36px;height:36px;border-radius:9px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:.85rem;{{ $j->status==='posted' ? 'background:rgba(39,174,96,.1);color:#27AE60' : 'background:rgba(243,156,18,.1);color:#F39C12' }}">
                            <i class="fas fa-{{ $j->status==='posted' ? 'check' : 'clock' }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.84rem;font-weight:600;color:var(--secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $j->journal_number }}</div>
                            <div style="font-size:.75rem;color:#aaa">{{ $j->date->format('d M Y') }}</div>
                        </div>
                        <div style="text-align:right;flex-shrink:0">
                            <div style="font-size:.82rem;font-weight:700;color:var(--secondary)">Rp {{ number_format($j->total_debit, 0, ',', '.') }}</div>
                            @if($j->status==='posted')
                            <span style="background:rgba(39,174,96,.1);color:#27AE60;font-size:.7rem;font-weight:600;padding:.1rem .4rem;border-radius:20px">Posted</span>
                            @else
                            <span style="background:rgba(243,156,18,.1);color:#F39C12;font-size:.7rem;font-weight:600;padding:.1rem .4rem;border-radius:20px">Draft</span>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4" style="font-size:.84rem">
                        <i class="fas fa-book d-block mb-1" style="font-size:1.5rem;opacity:.3"></i>
                        Belum ada jurnal
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartData['labels'] ?? ['Jan','Feb','Mar','Apr','Mei','Jun']),
        datasets: [
            {
                label: 'Pendapatan',
                data: @json($chartData['income'] ?? [0,0,0,0,0,0]),
                backgroundColor: 'rgba(39,174,96,0.75)',
                borderRadius: 6,
                borderWidth: 0
            },
            {
                label: 'Pengeluaran',
                data: @json($chartData['expense'] ?? [0,0,0,0,0,0]),
                backgroundColor: 'rgba(231,76,60,0.65)',
                borderRadius: 6,
                borderWidth: 0
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position:'bottom', labels:{ boxWidth:12, font:{ size:11 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color:'rgba(0,0,0,.04)' },
                ticks: { font:{size:11}, callback: v => 'Rp '+(v/1000000).toFixed(0)+'jt' }
            },
            x: { grid:{ display:false }, ticks:{ font:{size:11} } }
        }
    }
});
</script>
@endpush
