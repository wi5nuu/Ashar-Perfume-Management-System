@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@push('styles')
<style>
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
}

.page-header-bar {
    background: #fff;
    border-radius: 14px;
    padding: 1.2rem 1.6rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .8rem;
}
.page-header-bar h4 { font-weight: 700; color: var(--secondary); margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: .5rem; }
.page-header-bar h4 i { color: var(--primary); }

.filter-bar {
    background: #fff;
    border-radius: 14px;
    padding: 1.2rem 1.6rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid rgba(0,0,0,.05);
}
.filter-bar .filter-label {
    font-size: .75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: #888; margin-bottom: .35rem;
}
.filter-bar .form-control, .filter-bar .custom-select {
    border-radius: 8px; border: 1.5px solid #e8e8e8;
    font-size: .86rem; padding: .45rem .8rem;
    transition: border-color .2s;
}
.filter-bar .form-control:focus, .filter-bar .custom-select:focus {
    border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,.12);
}
.btn-filter-apply {
    background: var(--primary); color: #fff; border: none;
    padding: .48rem 1.3rem; border-radius: 8px; font-weight: 600; font-size: .86rem;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: background .2s;
}
.btn-filter-apply:hover { background: var(--primary-dark); color: #fff; }
.btn-filter-reset {
    background: transparent; color: #888; border: 1.5px solid #e8e8e8;
    padding: .48rem 1rem; border-radius: 8px; font-size: .86rem;
    transition: all .2s;
}
.btn-filter-reset:hover { border-color: var(--primary); color: var(--primary); }

/* KPI */
.kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.3rem 1.5rem;
    box-shadow: 0 2px 14px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.04);
    transition: transform .2s, box-shadow .2s;
    height: 100%;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 24px rgba(0,0,0,.11); }
.kpi-card .kpi-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: .8rem; }
.kpi-card .kpi-icon {
    width: 46px; height: 46px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.kpi-card .kpi-badge {
    font-size: .72rem; font-weight: 600; padding: .25rem .6rem; border-radius: 20px;
}
.kpi-card .kpi-value { font-size: 1.45rem; font-weight: 800; color: var(--secondary); line-height: 1.1; }
.kpi-card .kpi-label { font-size: .75rem; color: #999; text-transform: uppercase; letter-spacing: .4px; margin-top: .25rem; }
.kpi-card .kpi-sub { font-size: .78rem; color: #aaa; margin-top: .4rem; }
.badge-success-soft { background: rgba(39,174,96,.12); color: #27AE60; }
.badge-danger-soft  { background: rgba(231,76,60,.12);  color: #E74C3C; }
.badge-info-soft    { background: rgba(41,128,185,.12); color: #2980B9; }
.badge-warning-soft { background: rgba(243,156,18,.12); color: #F39C12; }

/* Chart card */
.chart-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.4rem 1.6rem;
    box-shadow: 0 2px 14px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.04);
    margin-bottom: 1.5rem;
}
.chart-card-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.2rem;
}
.chart-card-title {
    font-size: .95rem; font-weight: 700; color: var(--secondary);
    display: flex; align-items: center; gap: .5rem;
}
.chart-card-title i { color: var(--primary); }

/* Table */
.table-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.04);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.table-card-header {
    padding: 1.1rem 1.5rem;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #f5f5f5;
}
.table-card-header h5 { font-size: .95rem; font-weight: 700; color: var(--secondary); margin: 0; display: flex; align-items: center; gap: .5rem; }
.table-card-header h5 i { color: var(--primary); }

.table-modern { margin: 0; width: 100%; }
.table-modern thead th {
    background: #f8f9fb; color: #666; font-size: .75rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    padding: .8rem 1.2rem; border: none;
    white-space: nowrap;
}
.table-modern tbody td {
    padding: .85rem 1.2rem; border: none;
    border-bottom: 1px solid #f5f5f5;
    font-size: .86rem; color: var(--secondary);
    vertical-align: middle;
}
.table-modern tbody tr:last-child td { border-bottom: none; }
.table-modern tbody tr:hover td { background: #fafafa; }
.table-modern .text-right { text-align: right; }

/* Export buttons */
.btn-export-pdf {
    background: #E74C3C; color: #fff; border: none;
    padding: .42rem 1rem; border-radius: 8px; font-size: .82rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: background .2s;
}
.btn-export-pdf:hover { background: #c0392b; color: #fff; }
.btn-export-excel {
    background: #27AE60; color: #fff; border: none;
    padding: .42rem 1rem; border-radius: 8px; font-size: .82rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: background .2s;
}
.btn-export-excel:hover { background: #1e8449; color: #fff; }

/* Period tabs */
.period-tabs { display: flex; gap: .4rem; flex-wrap: wrap; }
.period-tab {
    font-size: .78rem; font-weight: 600; padding: .3rem .75rem;
    border-radius: 20px; cursor: pointer; border: 1.5px solid #e8e8e8;
    color: #888; transition: all .2s; background: #fff;
}
.period-tab.active, .period-tab:hover {
    background: var(--primary); color: #fff; border-color: var(--primary);
}
</style>

@endpush

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <h4><i class="fas fa-chart-line"></i> Laporan Penjualan</h4>
        <div class="d-flex align-items-center gap-2" style="gap:.6rem">
            <a href="{{ route('reports.index') }}" class="btn btn-filter-reset">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button class="btn-export-pdf" onclick="exportPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <button class="btn-export-excel" onclick="exportExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form action="{{ route('reports.sales') }}" method="GET" id="filterForm">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Tanggal Mulai</div>
                    <input type="date" class="form-control" name="start_date"
                        value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Tanggal Akhir</div>
                    <input type="date" class="form-control" name="end_date"
                        value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Granularitas</div>
                    <select class="form-control custom-select" name="type">
                        <option value="daily"   {{ $type === 'daily'   ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                    <div class="filter-label">Metode Bayar</div>
                    <select class="form-control custom-select" name="payment_method">
                        <option value="">Semua Metode</option>
                        <option value="cash"     {{ request('payment_method')==='cash'     ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ request('payment_method')==='transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="qris"     {{ request('payment_method')==='qris'     ? 'selected' : '' }}>QRIS</option>
                        <option value="credit"   {{ request('payment_method')==='credit'   ? 'selected' : '' }}>Kredit</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="filter-label">&nbsp;</div>
                    <div class="d-flex" style="gap:.5rem">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('reports.sales') }}" class="btn btn-filter-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI CARDS --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(255,107,53,.1);color:var(--primary)">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <span class="kpi-badge badge-success-soft">+12.4%</span>
                </div>
                <div class="kpi-value">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-sub"><i class="fas fa-calendar mr-1"></i>Periode yang dipilih</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(39,174,96,.1);color:#27AE60">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <span class="kpi-badge badge-info-soft">periode ini</span>
                </div>
                <div class="kpi-value">{{ number_format($totalTransactions ?? 0) }}</div>
                <div class="kpi-label">Total Transaksi</div>
                <div class="kpi-sub"><i class="fas fa-shopping-cart mr-1"></i>semua metode bayar</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(41,128,185,.1);color:#2980B9">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <span class="kpi-badge badge-warning-soft">rata-rata</span>
                </div>
                <div class="kpi-value">
                    Rp {{ ($totalTransactions ?? 0) > 0 ? number_format(($totalSales ?? 0) / $totalTransactions, 0, ',', '.') : '0' }}
                </div>
                <div class="kpi-label">Rata-rata Nilai Transaksi</div>
                <div class="kpi-sub"><i class="fas fa-chart-line mr-1"></i>per transaksi</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background:rgba(142,68,173,.1);color:#8e44ad">
                        <i class="fas fa-chart-area"></i>
                    </div>
                    <span class="kpi-badge badge-success-soft">vs lalu</span>
                </div>
                <div class="kpi-value" style="color:#27AE60">+8.3%</div>
                <div class="kpi-label">Pertumbuhan Revenue</div>
                <div class="kpi-sub"><i class="fas fa-arrow-up mr-1" style="color:#27AE60"></i>vs periode sebelumnya</div>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW --}}
    <div class="row mb-2">
        {{-- Revenue Trend Line Chart --}}
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-card-title">
                        <i class="fas fa-chart-line"></i> Tren Revenue
                    </div>
                    <div class="period-tabs">
                        <span class="period-tab {{ $type==='daily' ? 'active' : '' }}" onclick="switchType('daily')">Harian</span>
                        <span class="period-tab {{ $type==='monthly' ? 'active' : '' }}" onclick="switchType('monthly')">Bulanan</span>
                    </div>
                </div>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>

        {{-- Payment Method Bar Chart --}}
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-card-title">
                        <i class="fas fa-wallet"></i> Per Metode Bayar
                    </div>
                </div>
                <canvas id="paymentChart" height="200" style="max-height:200px"></canvas>
            </div>
        </div>
    </div>

    {{-- DETAIL TABLE --}}
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="fas fa-table"></i> Detail Transaksi per Periode</h5>
            <span class="badge" style="background:rgba(255,107,53,.1);color:var(--primary);font-size:.8rem;padding:.35rem .75rem;border-radius:20px">
                {{ $salesData->count() }} periode
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        @if($type === 'daily')
                        <th>Tanggal</th>
                        @else
                        <th>Bulan</th>
                        <th>Tahun</th>
                        @endif
                        <th>Total Transaksi</th>
                        <th class="text-right">Total Penjualan</th>
                        <th class="text-right">Rata-rata/Transaksi</th>
                        <th class="text-right">% Kontribusi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = $salesData->sum('total_sales'); @endphp
                    @forelse($salesData as $item)
                    <tr>
                        @if($type === 'daily')
                        <td>
                            <span style="font-weight:600;color:var(--secondary)">
                                {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                            </span>
                            <br>
                            <small style="color:#aaa">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('l') }}</small>
                        </td>
                        @else
                        <td><strong>{{ \Carbon\Carbon::create()->month($item->month)->format('F') }}</strong></td>
                        <td>{{ $item->year }}</td>
                        @endif
                        <td>
                            <span class="badge" style="background:rgba(41,128,185,.1);color:#2980B9;padding:.3rem .65rem;border-radius:20px;font-size:.8rem">
                                {{ number_format($item->transaction_count) }} transaksi
                            </span>
                        </td>
                        <td class="text-right">
                            <strong style="color:var(--secondary)">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-right" style="color:#888">
                            Rp {{ $item->transaction_count > 0 ? number_format($item->total_sales / $item->transaction_count, 0, ',', '.') : '0' }}
                        </td>
                        <td class="text-right">
                            @php $pct = $grandTotal > 0 ? ($item->total_sales / $grandTotal * 100) : 0; @endphp
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:.5rem">
                                <div style="width:60px;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden">
                                    <div style="width:{{ min(100,$pct) }}%;height:100%;background:var(--primary);border-radius:3px"></div>
                                </div>
                                <span style="font-size:.8rem;color:#888">{{ number_format($pct, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-chart-bar d-block mb-2" style="font-size:2rem;color:#ddd"></i>
                            <span style="color:#aaa;font-size:.88rem">Tidak ada data pada periode ini</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($salesData->count() > 0)
                <tfoot>
                    <tr style="background:#f8f9fb">
                        <td colspan="{{ $type === 'daily' ? 1 : 2 }}" style="font-weight:700;padding:.9rem 1.2rem;font-size:.86rem;color:var(--secondary)">
                            TOTAL
                        </td>
                        <td style="padding:.9rem 1.2rem">
                            <span class="badge" style="background:rgba(255,107,53,.12);color:var(--primary);padding:.3rem .65rem;border-radius:20px;font-size:.8rem">
                                {{ number_format($totalTransactions ?? 0) }} transaksi
                            </span>
                        </td>
                        <td class="text-right" style="font-weight:800;font-size:1rem;color:var(--primary);padding:.9rem 1.2rem">
                            Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-right" style="padding:.9rem 1.2rem;color:#888;font-size:.86rem">
                            Rp {{ ($totalTransactions ?? 0) > 0 ? number_format(($totalSales ?? 0) / $totalTransactions, 0, ',', '.') : '0' }}
                        </td>
                        <td class="text-right" style="padding:.9rem 1.2rem">
                            <span style="font-weight:700;color:var(--primary)">100%</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Build chart labels and data from Blade
const chartLabels = @json($salesData->map(function($item) use ($type) {
    if ($type === 'daily') {
        return \Carbon\Carbon::parse($item->date)->format('d/m');
    } else {
        return \Carbon\Carbon::create()->month($item->month)->format('M') . ' ' . $item->year;
    }
})->values());

const chartRevenue = @json($salesData->pluck('total_sales')->values());

// Revenue Trend Chart
const revCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revCtx, {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Revenue',
            data: chartRevenue,
            borderColor: '#FF6B35',
            backgroundColor: 'rgba(255,107,53,0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#FF6B35',
            pointRadius: 4,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#2D3047',
                padding: 12,
                callbacks: {
                    label: ctx => 'Rp ' + ctx.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,.04)' },
                ticks: {
                    font: { size: 11 },
                    callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt'
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 }, maxTicksLimit: 12 }
            }
        }
    }
});

// Payment Method Chart
const payCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(payCtx, {
    type: 'bar',
    data: {
        labels: ['Tunai', 'Transfer', 'QRIS', 'Kredit'],
        datasets: [{
            label: 'Transaksi',
            data: [45, 30, 18, 7],
            backgroundColor: ['#FF6B35','#27AE60','#2980B9','#F39C12'],
            borderRadius: 6,
            borderWidth: 0
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 11 } } },
            y: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

function switchType(type) {
    const url = new URL(window.location.href);
    url.searchParams.set('type', type);
    window.location.href = url.toString();
}

function exportPDF() {
    Swal.fire({
        title: 'Mengexport PDF...',
        text: 'Memproses laporan penjualan',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'pdf');
    window.open(url.toString(), '_blank');
    setTimeout(() => Swal.close(), 4000);
}

function exportExcel() {
    Swal.fire({
        title: 'Mengexport Excel...',
        text: 'Memproses laporan penjualan',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'excel');
    window.open(url.toString(), '_blank');
    setTimeout(() => Swal.close(), 4000);
}
</script>
@endpush
