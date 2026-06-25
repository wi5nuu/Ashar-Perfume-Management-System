@extends('layouts.app')

@section('title', 'Laporan & Analitik')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Report Cards -->
        <div class="col-12">
            <div class="row">
                @foreach($reportCards as $card)
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $card['color'] }}">
                        <div class="inner">
                            <h3>{{ $card['value'] }}</h3>
                            <p>{{ $card['title'] }}</p>
                        </div>
                        <div class="icon">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <a href="{{ $card['link'] }}" class="small-box-footer">
                            Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Quick Reports -->
        <div class="col-md-8">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Laporan Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Sales Report -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Penjualan</h3>
                                </div>
                                <div class="card-body">
                                    <form id="salesReportForm">
                                        <div class="form-group">
                                            <label>Periode</label>
                                            <select class="form-control" id="salesPeriod">
                                                <option value="today">Hari Ini</option>
                                                <option value="yesterday">Kemarin</option>
                                                <option value="this_week">Minggu Ini</option>
                                                <option value="last_week">Minggu Lalu</option>
                                                <option value="this_month">Bulan Ini</option>
                                                <option value="last_month">Bulan Lalu</option>
                                                <option value="custom">Custom</option>
                                            </select>
                                        </div>
                                        <div class="form-group custom-date" style="display: none;">
                                            <label>Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="startDate">
                                        </div>
                                        <div class="form-group custom-date" style="display: none;">
                                            <label>Tanggal Akhir</label>
                                            <input type="date" class="form-control" id="endDate">
                                        </div>
                                        <button type="submit" class="btn btn-primary-apms btn-block">
                                            <i class="fas fa-file-pdf"></i> Generate PDF
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Inventory Report -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Inventory</h3>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="{{ route('reports.inventory') }}" class="list-group-item list-group-item-action">
                                            <i class="fas fa-boxes mr-2"></i> Stok Saat Ini
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" 
                                           onclick="generateLowStockReport()">
                                            <i class="fas fa-exclamation-triangle mr-2"></i> Stok Rendah
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action"
                                           onclick="generateExpiryReport()">
                                            <i class="fas fa-calendar-times mr-2"></i> Akan Kadaluarsa
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action"
                                           onclick="generateStockMovementReport()">
                                            <i class="fas fa-exchange-alt mr-2"></i> Perpindahan Stok
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Reports -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Laporan Lanjutan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('reports.profit-loss') }}" class="btn btn-info btn-block mb-2">
                                                <i class="fas fa-chart-line"></i> Profit & Loss
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('reports.customers') }}" class="btn btn-warning btn-block mb-2">
                                                <i class="fas fa-users"></i> Analitik Pelanggan
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="#" class="btn btn-success btn-block mb-2" 
                                               onclick="generateProductPerformance()">
                                                <i class="fas fa-chart-bar"></i> Performa Produk
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="#" class="btn btn-primary btn-block mb-2" 
                                               onclick="generateEmployeeReport()">
                                                <i class="fas fa-user-tie"></i> Performa Karyawan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Reports -->
        <div class="col-md-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Laporan Terbaru</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Laporan</th>
                                    <th>Tanggal</th>
                                    <th>Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReports as $report)
                                <tr>
                                    <td>{{ $report->name }}</td>
                                    <td>{{ $report->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ $report->file_url }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card card-apms mt-3">
                <div class="card-header">
                    <h3 class="card-title">Statistik Cepat</h3>
                </div>
                <div class="card-body">
                    <canvas id="quickStatsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Reports -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Ringkasan Performa Bulan Ini</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="monthlyPerformanceChart" height="300" style="max-height: 300px;"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box mb-3 bg-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Revenue</span>
                                    <span class="info-box-number">Rp {{ number_format($monthlyStats['revenue'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="info-box mb-3 bg-warning">
                                <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expenses</span>
                                    <span class="info-box-number">Rp {{ number_format($monthlyStats['expenses'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="info-box mb-3 bg-info">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Profit</span>
                                    <span class="info-box-number">Rp {{ number_format($monthlyStats['profit'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="info-box mb-3 bg-danger">
                                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Products Sold</span>
                                    <span class="info-box-number">{{ $monthlyStats['products_sold'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Toggle custom date range
    $('#salesPeriod').change(function() {
        if ($(this).val() === 'custom') {
            $('.custom-date').show();
        } else {
            $('.custom-date').hide();
        }
    });
    
    // Sales report form
    $('#salesReportForm').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({title:'Mengexport...', text:'Memproses laporan penjualan', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
        
        const period = $('#salesPeriod').val();
        let url = '/reports/sales/pdf?period=' + period;
        
        if (period === 'custom') {
            const start = $('#startDate').val();
            const end = $('#endDate').val();
            if (!start || !end) {
                Swal.close();
                Swal.fire('Error', 'Harap pilih rentang tanggal', 'error');
                return;
            }
            url += `&start_date=${start}&end_date=${end}`;
        }
        
        window.open(url, '_blank');
        setTimeout(() => Swal.close(), 4000);
    });
    
    // Quick Stats Chart
    const quickStatsCtx = document.getElementById('quickStatsChart').getContext('2d');
    const quickStatsChart = new Chart(quickStatsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Penjualan', 'Produk Terjual', 'Pelanggan Baru', 'Transaksi'],
            datasets: [{
                data: [65, 15, 10, 10],
                backgroundColor: ['#FF6B35', '#3498db', '#2ecc71', '#f39c12']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyChartData['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($monthlyChartData['revenue']),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Expenses',
                data: @json($monthlyChartData['expenses']),
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Profit',
                data: @json($monthlyChartData['profit']),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + 
                                   context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
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
    window.open('/reports/employees/performance/pdf', '_blank');
}
</script>
@endpush