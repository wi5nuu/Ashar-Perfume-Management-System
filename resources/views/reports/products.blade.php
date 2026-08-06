@extends('layouts.app')

@section('title', 'Laporan Produk Terlaris')

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
    background:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.06); border:1px solid rgba(0,0,0,.05);
}
.filter-label { font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#888; margin-bottom:.35rem; }
.filter-bar .form-control { border-radius:8px; border:1.5px solid #e8e8e8; font-size:.86rem; padding:.45rem .8rem; transition:border-color .2s; }
.filter-bar .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.12); }
.btn-filter-apply { background:var(--primary); color:#fff; border:none; padding:.48rem 1.3rem; border-radius:8px; font-weight:600; font-size:.86rem; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-filter-apply:hover { background:var(--primary-dark); color:#fff; }
.btn-filter-reset { background:transparent; color:#888; border:1.5px solid #e8e8e8; padding:.48rem 1rem; border-radius:8px; font-size:.86rem; transition:all .2s; }
.btn-filter-reset:hover { border-color:var(--primary); color:var(--primary); }

/* Ranking cards */
.rank-card {
    background:#fff; border-radius:14px; padding:1.2rem 1.4rem; margin-bottom:.8rem;
    box-shadow:0 2px 10px rgba(0,0,0,.06); border:1px solid rgba(0,0,0,.04);
    display:flex; align-items:center; gap:1rem; transition:transform .2s, box-shadow .2s;
}
.rank-card:hover { transform:translateY(-2px); box-shadow:0 5px 20px rgba(0,0,0,.1); }
.rank-badge {
    width:38px; height:38px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:.95rem;
}
.rank-1 { background:linear-gradient(135deg,#FFD700,#FFA500); color:#fff; }
.rank-2 { background:linear-gradient(135deg,#C0C0C0,#A0A0A0); color:#fff; }
.rank-3 { background:linear-gradient(135deg,#CD7F32,#A0522D); color:#fff; }
.rank-other { background:rgba(45,48,71,.08); color:var(--secondary); }
.rank-info { flex:1; min-width:0; }
.rank-name { font-weight:700; font-size:.9rem; color:var(--secondary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rank-cat  { font-size:.76rem; color:#aaa; margin-top:.15rem; }
.rank-progress { margin-top:.5rem; height:5px; background:#f0f0f0; border-radius:3px; overflow:hidden; }
.rank-progress-bar { height:100%; background:linear-gradient(90deg,var(--primary),var(--primary-dark)); border-radius:3px; transition:width .8s ease; }
.rank-stats { text-align:right; flex-shrink:0; }
.rank-revenue { font-weight:700; font-size:.9rem; color:var(--secondary); }
.rank-qty { font-size:.75rem; color:#aaa; margin-top:.15rem; }

/* Chart card */
.chart-card {
    background:#fff; border-radius:14px; padding:1.4rem 1.6rem;
    box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); margin-bottom:1.5rem;
}
.chart-card-title { font-size:.95rem; font-weight:700; color:var(--secondary); display:flex; align-items:center; gap:.5rem; margin-bottom:1.2rem; }
.chart-card-title i { color:var(--primary); }

/* Table */
.table-card { background:#fff; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,.07); border:1px solid rgba(0,0,0,.04); overflow:hidden; margin-bottom:1.5rem; }
.table-card-header { padding:1.1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f5f5f5; }
.table-card-header h5 { font-size:.95rem; font-weight:700; color:var(--secondary); margin:0; display:flex; align-items:center; gap:.5rem; }
.table-card-header h5 i { color:var(--primary); }
.table-modern { margin:0; width:100%; }
.table-modern thead th { background:#f8f9fb; color:#666; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; padding:.8rem 1.2rem; border:none; white-space:nowrap; }
.table-modern tbody td { padding:.85rem 1.2rem; border:none; border-bottom:1px solid #f5f5f5; font-size:.86rem; color:var(--secondary); vertical-align:middle; }
.table-modern tbody tr:last-child td { border-bottom:none; }
.table-modern tbody tr:hover td { background:#fafafa; }

/* Slow moving section */
.slow-card {
    background:linear-gradient(135deg,#fff8f5 0%,#fff 100%);
    border:1.5px solid rgba(231,76,60,.15);
    border-radius:14px; padding:1.4rem 1.6rem;
    box-shadow:0 2px 14px rgba(0,0,0,.06); margin-bottom:1.5rem;
}
.slow-badge { background:rgba(231,76,60,.1); color:#E74C3C; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:20px; }

.btn-export-pdf { background:#E74C3C; color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-export-pdf:hover { background:#c0392b; color:#fff; }
.btn-export-excel { background:#27AE60; color:#fff; border:none; padding:.42rem 1rem; border-radius:8px; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
.btn-export-excel:hover { background:#1e8449; color:#fff; }
</style>

@endpush

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <h4><i class="fas fa-star"></i> Laporan Produk Terlaris</h4>
        <div class="d-flex align-items-center" style="gap:.6rem">
            <a href="{{ route('reports.index') }}" class="btn btn-filter-reset">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button class="btn-export-pdf" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button class="btn-export-excel" onclick="exportReport('excel')">
                <i class="fas fa-file-excel"></i> Excel
            </button>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form action="{{ route('reports.sales') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Tanggal Mulai</div>
                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Tanggal Akhir</div>
                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Kategori</div>
                    <select class="form-control" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Tipe Produk</div>
                    <select class="form-control" name="product_type">
                        <option value="">Semua Tipe</option>
                        <option value="perfume"   {{ request('product_type')==='perfume'   ? 'selected' : '' }}>Parfum</option>
                        <option value="oud"       {{ request('product_type')==='oud'       ? 'selected' : '' }}>Oud</option>
                        <option value="body_mist" {{ request('product_type')==='body_mist' ? 'selected' : '' }}>Body Mist</option>
                        <option value="other"     {{ request('product_type')==='other'     ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="filter-label">&nbsp;</div>
                    <div class="d-flex" style="gap:.5rem">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i> Terapkan
                        </button>
                        <a href="{{ route('reports.sales') }}" class="btn btn-filter-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row">

        {{-- TOP 10 RANKING CARDS --}}
        <div class="col-lg-5 mb-4">
            <div class="chart-card">
                <div class="chart-card-title">
                    <i class="fas fa-trophy"></i> Top 10 Produk Terlaris
                </div>
                @php $maxRevenue = $topProducts->max('total_revenue') ?? 1; @endphp
                @forelse($topProducts->take(10) as $idx => $product)
                <div class="rank-card">
                    <div class="rank-badge {{ $idx==0 ? 'rank-1' : ($idx==1 ? 'rank-2' : ($idx==2 ? 'rank-3' : 'rank-other')) }}">
                        {{ $idx + 1 }}
                    </div>
                    <div class="rank-info">
                        <div class="rank-name">{{ $product->product_name ?? $product->name }}</div>
                        <div class="rank-cat">{{ $product->category_name ?? 'Parfum' }}</div>
                        <div class="rank-progress">
                            <div class="rank-progress-bar" style="width:{{ $maxRevenue > 0 ? ($product->total_revenue/$maxRevenue*100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="rank-stats">
                        <div class="rank-revenue">Rp {{ number_format($product->total_revenue ?? 0, 0, ',', '.') }}</div>
                        <div class="rank-qty">{{ number_format($product->total_qty ?? 0) }} unit</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-box-open d-block mb-2" style="font-size:2rem;opacity:.3"></i>
                    Tidak ada data produk
                </div>
                @endforelse
            </div>
        </div>

        {{-- HORIZONTAL BAR CHART --}}
        <div class="col-lg-7 mb-4">
            <div class="chart-card" style="height:calc(100% - 1.5rem)">
                <div class="chart-card-title">
                    <i class="fas fa-chart-bar"></i> Perbandingan Revenue Produk
                </div>
                <canvas id="productBarChart" height="320" style="max-height:320px"></canvas>
            </div>
        </div>

    </div>

    {{-- DETAIL TABLE --}}
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="fas fa-list-ol"></i> Tabel Ranking Produk Lengkap</h5>
            <span class="badge" style="background:rgba(255,107,53,.1);color:var(--primary);font-size:.8rem;padding:.35rem .75rem;border-radius:20px">
                {{ $topProducts->count() }} produk
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width:50px">Rank</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th class="text-right">Qty Terjual</th>
                        <th class="text-right">Revenue</th>
                        <th class="text-right">% Kontribusi</th>
                        <th class="text-right">Harga Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalRevenue = $topProducts->sum('total_revenue'); @endphp
                    @forelse($topProducts as $idx => $product)
                    <tr>
                        <td>
                            <span class="rank-badge d-inline-flex {{ $idx==0 ? 'rank-1' : ($idx==1 ? 'rank-2' : ($idx==2 ? 'rank-3' : 'rank-other')) }}"
                                style="width:30px;height:30px;font-size:.8rem;border-radius:8px">
                                {{ $idx + 1 }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600">{{ $product->product_name ?? $product->name }}</div>
                            <div style="font-size:.76rem;color:#aaa">SKU: {{ $product->sku ?? '-' }}</div>
                        </td>
                        <td>
                            <span style="background:rgba(41,128,185,.08);color:#2980B9;font-size:.76rem;font-weight:600;padding:.2rem .6rem;border-radius:20px">
                                {{ $product->category_name ?? 'Parfum' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <strong>{{ number_format($product->total_qty ?? 0) }}</strong>
                            <div style="font-size:.75rem;color:#aaa">unit</div>
                        </td>
                        <td class="text-right">
                            <strong style="color:var(--secondary)">Rp {{ number_format($product->total_revenue ?? 0, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-right">
                            @php $pct = $totalRevenue > 0 ? ($product->total_revenue / $totalRevenue * 100) : 0; @endphp
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:.5rem">
                                <div style="width:60px;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden">
                                    <div style="width:{{ min(100,$pct) }}%;height:100%;background:var(--primary);border-radius:3px"></div>
                                </div>
                                <span style="font-size:.8rem;color:#888;min-width:35px;text-align:right">{{ number_format($pct,1) }}%</span>
                            </div>
                        </td>
                        <td class="text-right" style="color:#888">
                            Rp {{ ($product->total_qty ?? 0) > 0 ? number_format(($product->total_revenue ?? 0) / $product->total_qty, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-box-open d-block mb-2" style="font-size:2rem;color:#ddd"></i>
                            <span style="color:#aaa;font-size:.88rem">Tidak ada data produk pada periode ini</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SLOW MOVING PRODUCTS --}}
    <div class="slow-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div style="display:flex;align-items:center;gap:.6rem">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(231,76,60,.1);color:#E74C3C;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:var(--secondary)">Slow Moving Products</div>
                    <div style="font-size:.78rem;color:#aaa">Produk dengan penjualan rendah — perlu perhatian</div>
                </div>
            </div>
            <span class="slow-badge">{{ ($slowMoving ?? collect())->count() }} produk</span>
        </div>
        @if(($slowMoving ?? collect())->isEmpty())
        <div class="text-center py-3" style="color:#aaa;font-size:.86rem">
            <i class="fas fa-check-circle d-block mb-1" style="font-size:1.5rem;color:#27AE60;opacity:.5"></i>
            Tidak ada slow moving products. Semua produk bergerak dengan baik.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-modern" style="background:transparent">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th class="text-right">Qty Terjual</th>
                        <th class="text-right">Stok Tersisa</th>
                        <th class="text-right">Hari Tanpa Penjualan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slowMoving ?? [] as $item)
                    <tr>
                        <td><strong>{{ $item->product_name ?? $item->name }}</strong></td>
                        <td>{{ $item->category_name ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->total_qty ?? 0) }}</td>
                        <td class="text-right">{{ number_format($item->stock ?? 0) }}</td>
                        <td class="text-right">{{ $item->days_no_sale ?? '-' }}</td>
                        <td>
                            @if(($item->days_no_sale ?? 0) > 60)
                            <span style="background:rgba(231,76,60,.1);color:#E74C3C;font-size:.74rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Kritis</span>
                            @elseif(($item->days_no_sale ?? 0) > 30)
                            <span style="background:rgba(243,156,18,.1);color:#F39C12;font-size:.74rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Perhatian</span>
                            @else
                            <span style="background:rgba(39,174,96,.1);color:#27AE60;font-size:.74rem;font-weight:600;padding:.2rem .55rem;border-radius:20px">Normal</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
const productLabels = @json(($topProducts ?? collect())->take(10)->map(fn($p) => \Str::limit($p->product_name ?? $p->name ?? '', 20))->values());
const productRevenue = @json(($topProducts ?? collect())->take(10)->pluck('total_revenue')->values());
const productQty = @json(($topProducts ?? collect())->take(10)->pluck('total_qty')->values());

const ctx = document.getElementById('productBarChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [
            {
                label: 'Revenue (Rp)',
                data: productRevenue,
                backgroundColor: 'rgba(255,107,53,0.75)',
                borderRadius: 6,
                borderWidth: 0,
                yAxisID: 'y'
            },
            {
                label: 'Qty Terjual',
                data: productQty,
                backgroundColor: 'rgba(39,174,96,0.6)',
                borderRadius: 6,
                borderWidth: 0,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        if (ctx.datasetIndex === 0) return 'Revenue: Rp ' + ctx.parsed.x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        return 'Qty: ' + ctx.parsed.x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' unit';
                    }
                }
            }
        },
        scales: {
            x:  { beginAtZero:true, grid:{ color:'rgba(0,0,0,.04)' }, ticks:{ font:{size:10}, callback: v => 'Rp '+(v/1000000).toFixed(0)+'jt' } },
            y:  { grid:{ display:false }, ticks:{ font:{size:10} } },
            y1: { display:false }
        }
    }
});

function exportReport(type) {
    Swal.fire({ title:'Mengexport...', text:'Memproses laporan produk', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
    const url = new URL(window.location.href);
    url.searchParams.set('export', type);
    window.open(url.toString(), '_blank');
    setTimeout(() => Swal.close(), 4000);
}
</script>
@endpush
