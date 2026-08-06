@extends('layouts.app')

@section('title', 'Pusat Laporan & Analitik')

@push('styles')
<style>
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
    --success: #27AE60;
    --warning: #F39C12;
    --info: #2980B9;
    --danger: #E74C3C;
}

/* Hero Section */
.reports-hero {
    background: linear-gradient(135deg, var(--secondary) 0%, #1a1d2e 60%, #3d2b1f 100%);
    border-radius: 16px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.reports-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,107,53,0.15);
}
.reports-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: -40px;
    width: 140px; height: 140px;
    border-radius: 50%;
    background: rgba(255,107,53,0.08);
}
.reports-hero h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: .3rem; }
.reports-hero p { color: rgba(255,255,255,.7); margin-bottom: 0; }

/* KPI Cards */
.kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.4rem 1.6rem;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.05);
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 24px rgba(0,0,0,.12); }
.kpi-card .kpi-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    margin-bottom: 1rem;
}
.kpi-card .kpi-value { font-size: 1.5rem; font-weight: 700; color: var(--secondary); line-height: 1.2; }
.kpi-card .kpi-label { font-size: .78rem; color: #888; text-transform: uppercase; letter-spacing: .5px; margin-top: .25rem; }
.kpi-card .kpi-trend { font-size: .8rem; margin-top: .5rem; }
.kpi-card .trend-up { color: var(--success); }
.kpi-card .trend-down { color: var(--danger); }
.kpi-orange .kpi-icon { background: rgba(255,107,53,.12); color: var(--primary); }
.kpi-green .kpi-icon  { background: rgba(39,174,96,.12);  color: var(--success); }
.kpi-blue .kpi-icon   { background: rgba(41,128,185,.12); color: var(--info); }
.kpi-purple .kpi-icon { background: rgba(142,68,173,.12); color: #8e44ad; }

/* Report Module Cards */
.report-module-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.6rem;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.05);
    transition: transform .2s, box-shadow .2s, border-color .2s;
    height: 100%;
    cursor: pointer;
    text-decoration: none;
    display: block;
    color: inherit;
}
.report-module-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(255,107,53,.18);
    border-color: var(--primary);
    text-decoration: none;
    color: inherit;
}
.report-module-card .module-icon {
    width: 60px; height: 60px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 1.1rem;
}
.report-module-card h5 { font-weight: 700; font-size: 1rem; color: var(--secondary); margin-bottom: .4rem; }
.report-module-card p { font-size: .82rem; color: #888; margin-bottom: 1rem; line-height: 1.5; }
.report-module-card .btn-open {
    font-size: .8rem; font-weight: 600;
    padding: .35rem .9rem;
    border-radius: 8px;
    border: 2px solid var(--primary);
    color: var(--primary);
    background: transparent;
    transition: all .2s;
    display: inline-flex; align-items: center; gap: .4rem;
}
.report-module-card:hover .btn-open {
    background: var(--primary);
    color: #fff;
}

/* Section Header */
.section-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.2rem;
}
.section-header h4 {
    font-size: 1rem; font-weight: 700; color: var(--secondary);
    margin: 0; display: flex; align-items: center; gap: .5rem;
}
.section-header h4::before {
    content: '';
    width: 4px; height: 18px;
    background: var(--primary);
    border-radius: 2px;
    display: inline-block;
}

/* Recent Reports List */
.recent-report-item {
    display: flex; align-items: center; gap: 1rem;
    padding: .85rem 1rem;
    border-radius: 10px;
    transition: background .15s;
    border-bottom: 1px solid #f5f5f5;
}
.recent-report-item:last-child { border-bottom: none; }
.recent-report-item:hover { background: #fafafa; }
.recent-report-item .ri-icon {
    width: 38px; height: 38px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
}
.recent-report-item .ri-info { flex: 1; }
.recent-report-item .ri-name { font-size: .88rem; font-weight: 600; color: var(--secondary); }
.recent-report-item .ri-date { font-size: .75rem; color: #999; }

/* Chart card */
.chart-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.4rem 1.6rem;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.05);
}
.chart-card .chart-title {
    font-size: .95rem; font-weight: 700; color: var(--secondary);
    margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem;
}

/* Date picker bar */
.filter-hero-bar {
    display: flex; flex-wrap: wrap; align-items: flex-end; gap: .8rem;
    margin-top: 1.2rem;
    position: relative; z-index: 1;
}
.filter-hero-bar label { font-size: .78rem; color: rgba(255,255,255,.7); margin-bottom: .3rem; display: block; }
.filter-hero-bar .form-control {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff; border-radius: 8px;
    font-size: .85rem; padding: .45rem .8rem;
}
.filter-hero-bar .form-control:focus { background: rgba(255,255,255,.18); outline: none; box-shadow: none; border-color: var(--primary); }
.filter-hero-bar .form-control option { color: #333; background: #fff; }
.btn-hero-filter {
    background: var(--primary); border: none; color: #fff;
    padding: .45rem 1.2rem; border-radius: 8px; font-size: .85rem; font-weight: 600;
    transition: background .2s;
}
.btn-hero-filter:hover { background: var(--primary-dark); }

/* Inventory quick links */
.inv-link {
    display: flex; align-items: center; gap: .75rem;
    padding: .7rem 1rem;
    border-radius: 9px;
    color: var(--secondary);
    font-size: .86rem; font-weight: 500;
    transition: background .15s, color .15s;
    text-decoration: none;
    border-bottom: 1px solid #f5f5f5;
}
.inv-link:last-child { border-bottom: none; }
.inv-link:hover { background: rgba(255,107,53,.08); color: var(--primary); text-decoration: none; }
.inv-link .inv-link-icon {
    width: 32px; height: 32px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HERO SECTION --}}
    <div class="reports-hero">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1><i class="fas fa-chart-pie mr-2" style="color:var(--primary)"></i>Pusat Laporan &amp; Analitik</h1>
                <p>Dashboard terpadu untuk semua laporan bisnis distribusi parfum Anda. Data real-time, export PDF/Excel.</p>
                <form action="{{ route('reports.sales') }}" method="GET" class="filter-hero-bar">
                    <div>
                        <label>Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" style="min-width:145px">
                    </div>
                    <div>
                        <label>Tanggal Akhir</label>
                        <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" style="min-width:145px">
                    </div>
                    <div>
                        <label>Granularitas</label>
                        <select class="form-control" name="type" style="min-width:130px">
                            <option value="daily">Harian</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-hero-filter">
                        <i class="fas fa-search mr-1"></i> Lihat Penjualan
                    </button>
                </form>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end align-items-center">
                <div style="opacity:.15; font-size:8rem; line-height:1;">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK KPI STATS --}}
    <div class="row mb-4">
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card kpi-orange">
                <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="kpi-value">Rp {{ number_format($monthlyStats['revenue'] ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-label">Revenue Bulan Ini</div>
                <div class="kpi-trend trend-up"><i class="fas fa-arrow-up mr-1"></i>vs bulan lalu</div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
                <div class="kpi-value">{{ number_format($monthlyStats['products_sold'] ?? 0) }}</div>
                <div class="kpi-label">Produk Terjual</div>
                <div class="kpi-trend trend-up"><i class="fas fa-arrow-up mr-1"></i>item bulan ini</div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                <div class="kpi-value">Rp {{ number_format($monthlyStats['profit'] ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-label">Laba Bersih</div>
                <div class="kpi-trend {{ ($monthlyStats['profit'] ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-{{ ($monthlyStats['profit'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>periode ini
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="kpi-value">Rp {{ number_format($monthlyStats['expenses'] ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Pengeluaran</div>
                <div class="kpi-trend" style="color:#8e44ad"><i class="fas fa-calendar mr-1"></i>bulan ini</div>
            </div>
        </div>
    </div>

    {{-- REPORT MODULE GRID --}}
    <div class="section-header mb-3">
        <h4>Modul Laporan</h4>
    </div>
    <div class="row mb-4">
        {{-- Laporan Penjualan --}}
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="{{ route('reports.sales') }}" class="report-module-card">
                <div class="module-icon" style="background:rgba(255,107,53,.12);color:var(--primary)">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h5>Penjualan</h5>
                <p>Analisis revenue, tren, dan performa transaksi per periode.</p>
                <span class="btn-open"><i class="fas fa-arrow-right"></i> Buka</span>
            </a>
        </div>
        {{-- Laporan Produk Terlaris --}}
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="{{ route('reports.sales') }}" class="report-module-card">
                <div class="module-icon" style="background:rgba(39,174,96,.12);color:var(--success)">
                    <i class="fas fa-star"></i>
                </div>
                <h5>Produk Terlaris</h5>
                <p>Ranking produk, kontribusi revenue, dan slow-moving items.</p>
                <span class="btn-open"><i class="fas fa-arrow-right"></i> Buka</span>
            </a>
        </div>
        {{-- Laporan Pelanggan --}}
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="{{ route('reports.customers') }}" class="report-module-card">
                <div class="module-icon" style="background:rgba(41,128,185,.12);color:var(--info)">
                    <i class="fas fa-users"></i>
                </div>
                <h5>Pelanggan</h5>
                <p>Analitik pelanggan, loyalitas, dan nilai transaksi rata-rata.</p>
                <span class="btn-open"><i class="fas fa-arrow-right"></i> Buka</span>
            </a>
        </div>
        {{-- Laporan Keuangan --}}
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="{{ route('reports.profit-loss') }}" class="report-module-card">
                <div class="module-icon" style="background:rgba(142,68,173,.12);color:#8e44ad">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h5>Keuangan</h5>
                <p>Profit &amp; Loss, neraca keuangan, dan arus kas bisnis.</p>
                <span class="btn-open"><i class="fas fa-arrow-right"></i> Buka</span>
            </a>
        </div>
        {{-- Laporan Inventory --}}
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="{{ route('reports.inventory') }}" class="report-module-card">
                <div class="module-icon" style="background:rgba(243,156,18,.12);color:var(--warning)">
                    <i class="fas fa-boxes"></i>
                </div>
                <h5>Inventory</h5>
                <p>Stok saat ini, stok rendah, dan produk akan kadaluarsa.</p>
                <span class="btn-open"><i class="fas fa-arrow-right"></i> Buka</span>
            </a>
        </div>
        {{-- Laporan Karyawan --}}
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="#" onclick="generateEmployeeReport(); return false;" class="report-module-card">
                <div class="module-icon" style="background:rgba(231,76,60,.12);color:var(--danger)">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h5>Karyawan</h5>
                <p>Performa karyawan, penjualan per staf, dan produktivitas.</p>
                <span class="btn-open"><i class="fas fa-arrow-right"></i> Buka</span>
            </a>
        </div>
    </div>

    {{-- BOTTOM ROW: Chart + Inventory Quick Links + Recent Reports --}}
    <div class="row">
        {{-- Monthly Performance Chart --}}
        <div class="col-lg-6 mb-4">
            <div class="chart-card" style="height:100%">
                <div class="chart-title">
                    <i class="fas fa-chart-bar" style="color:var(--primary)"></i>
                    Ringkasan Performa Bulan Ini
                </div>
                <canvas id="monthlyPerformanceChart" height="220" style="max-height:220px"></canvas>
            </div>
        </div>

        {{-- Right column: Inventory + Recent Reports --}}
        <div class="col-lg-3 mb-4">
            <div class="chart-card h-100">
                <div class="chart-title">
                    <i class="fas fa-boxes" style="color:var(--warning)"></i>
                    Laporan Inventory Cepat
                </div>
                <a href="{{ route('reports.inventory') }}" class="inv-link">
                    <span class="inv-link-icon" style="background:rgba(39,174,96,.1);color:var(--success)"><i class="fas fa-boxes"></i></span>
                    Stok Saat Ini
                </a>
                <a href="#" class="inv-link" onclick="generateLowStockReport(); return false;">
                    <span class="inv-link-icon" style="background:rgba(243,156,18,.1);color:var(--warning)"><i class="fas fa-exclamation-triangle"></i></span>
                    Stok Rendah
                </a>
                <a href="#" class="inv-link" onclick="generateExpiryReport(); return false;">
                    <span class="inv-link-icon" style="background:rgba(231,76,60,.1);color:var(--danger)"><i class="fas fa-calendar-times"></i></span>
                    Akan Kadaluarsa
                </a>
                <a href="#" class="inv-link" onclick="generateStockMovementReport(); return false;">
                    <span class="inv-link-icon" style="background:rgba(41,128,185,.1);color:var(--info)"><i class="fas fa-exchange-alt"></i></span>
                    Perpindahan Stok
                </a>
                <hr style="margin:.75rem 0">
                <div class="chart-title mt-2">
                    <i class="fas fa-history" style="color:var(--secondary)"></i>
                    Laporan Terbaru
                </div>
                @forelse($recentReports as $report)
                <div class="recent-report-item">
                    <div class="ri-icon" style="background:rgba(255,107,53,.1);color:var(--primary)"><i class="fas fa-file-alt"></i></div>
                    <div class="ri-info">
                        <div class="ri-name">{{ $report->name }}</div>
                        <div class="ri-date">{{ $report->created_at->format('d M Y') }}</div>
                    </div>
                    <a href="{{ $report->file_url }}" class="btn btn-sm" style="background:rgba(255,107,53,.1);color:var(--primary);border-radius:7px;padding:.3rem .55rem">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:.84rem">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size:1.8rem;opacity:.3"></i>
                    Belum ada laporan tersimpan
                </div>
                @endforelse
            </div>
        </div>

        {{-- Doughnut quick stats --}}
        <div class="col-lg-3 mb-4">
            <div class="chart-card h-100">
                <div class="chart-title">
                    <i class="fas fa-pie-chart" style="color:var(--primary)"></i>
                    Komposisi Bisnis
                </div>
                <canvas id="quickStatsChart" height="180" style="max-height:180px"></canvas>
                <div class="mt-3">
                    @foreach($reportCards as $card)
                    <div class="d-flex justify-content-between align-items-center py-1" style="font-size:.82rem;border-bottom:1px solid #f5f5f5">
                        <span><i class="{{ $card['icon'] }} mr-1" style="color:var(--primary)"></i>{{ $card['title'] }}</span>
                        <strong>{{ $card['value'] }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Toggle custom date range in hero
    $('#salesPeriod').change(function() {
        if ($(this).val() === 'custom') {
            $('.custom-date').show();
        } else {
            $('.custom-date').hide();
        }
    });

    // Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyChartData['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($monthlyChartData['revenue']),
                backgroundColor: 'rgba(255,107,53,0.75)',
                borderColor: 'rgba(255,107,53,1)',
                borderWidth: 0,
                borderRadius: 6
            }, {
                label: 'Pengeluaran',
                data: @json($monthlyChartData['expenses']),
                backgroundColor: 'rgba(45,48,71,0.55)',
                borderColor: 'rgba(45,48,71,1)',
                borderWidth: 0,
                borderRadius: 6
            }, {
                label: 'Profit',
                data: @json($monthlyChartData['profit']),
                backgroundColor: 'rgba(39,174,96,0.7)',
                borderColor: 'rgba(39,174,96,1)',
                borderWidth: 0,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: {
                        font: { size: 11 },
                        callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'jt'
                    }
                },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });

    // Quick Stats Doughnut
    const quickCtx = document.getElementById('quickStatsChart').getContext('2d');
    new Chart(quickCtx, {
        type: 'doughnut',
        data: {
            labels: ['Penjualan', 'Produk Terjual', 'Pelanggan Baru', 'Transaksi'],
            datasets: [{
                data: [65, 15, 10, 10],
                backgroundColor: ['#FF6B35', '#27AE60', '#2980B9', '#F39C12'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
            }
        }
    });
});

function generateLowStockReport() {
    Swal.fire({title:'Mengexport...', text:'Memproses laporan stok rendah', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
    window.open('{{ route('reports.inventory.low-stock.pdf') }}', '_blank');
    setTimeout(() => Swal.close(), 4000);
}

function generateExpiryReport() {
    Swal.fire({title:'Mengexport...', text:'Memproses laporan kadaluarsa', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
    window.open('{{ route('reports.inventory.expiry.pdf') }}', '_blank');
    setTimeout(() => Swal.close(), 4000);
}

function generateStockMovementReport() {
    Swal.fire('Info', 'Laporan pergerakan stok belum tersedia. Gunakan menu Stock Audit untuk melihat riwayat.', 'info');
}

function generateProductPerformance() {
    Swal.fire('Info', 'Laporan performa produk belum tersedia.', 'info');
}

function generateEmployeeReport() {
    Swal.fire({title:'Mengexport...', text:'Memproses laporan karyawan', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
    window.open('/reports/employees/performance/pdf', '_blank');
    setTimeout(() => Swal.close(), 4000);
}
</script>
@endpush
