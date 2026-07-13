@extends('layouts.app')

@section('title', 'Dashboard')

@php
$user = auth()->user();
$greeting = now()->format('H') < 10 ? 'Selamat Pagi' : (now()->format('H') < 15 ? 'Selamat Siang' : (now()->format('H') < 18 ? 'Selamat Sore' : 'Selamat Malam'));
@endphp

@section('content')
<div class="container-fluid">
    {{-- Greeting Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-apms bg-gradient-primary border-0 mb-3">
                <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h4 class="mb-1 text-white font-weight-bold">{{ $greeting }}, {{ $user->name }}!</h4>
                        <p class="mb-0 text-white-50">
                            <i class="fas fa-store mr-1"></i> {{ $user->branch?->name ?? 'Pusat' }}
                            &middot; {{ now()->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-sm-0">
                        @if(!$user->isOwner())
                        @if($activeShift)
                        <span class="badge badge-success px-3 py-2 mr-2">
                            <i class="fas fa-clock mr-1"></i> Shift Aktif
                        </span>
                        @else
                        <span class="badge badge-secondary px-3 py-2 mr-2">
                            <i class="fas fa-power-off mr-1"></i> Shift Tutup
                        </span>
                        @can('manage_transactions')
                        <a href="{{ route('shifts.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-play mr-1"></i> Buka Shift
                        </a>
                        @endcan
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row: 6 Cards — 3 per row on desktop, 2 per row on tablet --}}
    @can('transactions.view')
    <div class="row">
        <div class="col-lg-4 col-md-6 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    <p>Penjualan Eceran Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <a href="{{ route('transactions.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $bg }}success">
                <div class="inner">
                    <h3>Rp {{ number_format($wholesaleSalesToday, 0, ',', '.') }}</h3>
                    <p>Penjualan Grosir Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-people-carry"></i></div>
                <a href="{{ route('wholesale.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $todayTransactions ?? 0 }}</h3>
                    <p>Total Transaksi</p>
                </div>
                <div class="icon"><i class="fas fa-cash-register"></i></div>
                <a href="{{ route('transactions.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @if($user->isOwner() || $user->can('stock_requests.view'))
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $bg }}info">
                <div class="inner">
                    <h3>{{ $pendingStockRequests ?? 0 }}</h3>
                    <p>Permintaan Stok Pending</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <a href="{{ route('stock-requests.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endif
        @if($user->isOwner() || $user->can('expenses.view'))
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $bg }}danger">
                <div class="inner">
                    <h3>Rp {{ number_format($todayExpenses, 0, ',', '.') }}</h3>
                    <p>Biaya Hari Ini</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
                <a href="{{ route('expenses.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endif
    </div>

    {{-- Stats Row 2: Additional KPIs --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-secondary">
                <div class="inner">
                    <h3>{{ $totalCustomers ?? 0 }}</h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('customers.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-6">
            <div class="small-box bg-gradient-pink">
                <div class="inner">
                    <h3>{{ $lowStockProductsCount ?? 0 }}</h3>
                    <p>Stok Menipis</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('inventory.index') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-teal">
                <div class="inner">
                    <h3>Rp {{ number_format($monthSales, 0, ',', '.') }}</h3>
                    <p>Penjualan Bulan Ini</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <a href="{{ route('reports.sales') }}" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    @endcan

    @can('reports.view')
    {{-- Full Dashboard for roles with reports --}}
    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="card card-apms">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 px-3">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-balance-scale mr-1"></i> Perbandingan Periode
                    </h5>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-sm btn-outline-primary active" id="btn-mom">
                            <input type="radio" name="comp_mode" value="mom" checked> MoM
                        </label>
                        <label class="btn btn-sm btn-outline-primary" id="btn-yoy">
                            <input type="radio" name="comp_mode" value="yoy"> YoY
                        </label>
                    </div>
                </div>
                <div class="card-body pt-1 pb-2 px-2" id="comparison-body">
                    <div class="text-center text-muted py-2" id="comparison-loading" style="font-size:0.8rem;">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Loading comparison...
                    </div>
                    <div id="comparison-content" style="display:none;">
                        <div id="comparison-cards" class="row g-0"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-apms h-100">
                <div class="card-header py-2">
                    <h5 class="card-title mb-0 font-weight-bold"><i class="fas fa-chart-pie mr-1"></i> Distribusi Pembayaran</h5>
                </div>
                <div class="card-body py-1 px-2">
                    <div class="row align-items-center g-0">
                        <div class="col-5 text-center">
                            <canvas id="paymentChart" height="80" style="max-height:80px;max-width:80px;margin:0 auto;"></canvas>
                        </div>
                        <div class="col-7">
                            <canvas id="paymentBarChart" height="80" style="max-height:80px;width:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-apms">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Grafik Penjualan {{ date('Y') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary-apms btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="salesChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            @can('transactions.view')
            <div class="card card-apms">
                <div class="card-header border-0">
                    <h3 class="card-title">Transaksi Terbaru</h3>
                    <div class="card-tools">
                        <a href="{{ route('transactions.index') }}" class="btn btn-primary-apms btn-sm">
                            Lihat Semua <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Kasir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="text-primary">
                                        {{ $transaction->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $transaction->customer->name ?? 'Umum' }}</td>
                                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-light">{{ strtoupper($transaction->payment_method) }}</span>
                                </td>
                                <td>{{ $transaction->user?->name ?? '-' }}</td>
                                <td>
                                    @if($transaction->paid_amount >= $transaction->total_amount)
                                        <span class="badge badge-success">Lunas</span>
                                    @else
                                        <span class="badge badge-warning">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endcan
        </div>
        <div class="col-lg-4">
            @can('inventory.view')
            <div class="card card-apms">
                <div class="card-header border-0 py-2 px-3">
                    <h3 class="card-title mb-0">Peringatan Stok</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card px-3">
                        @foreach($lowStockAlerts as $alert)
                        <li class="item">
                            <div class="product-img">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    {{ $alert->name }}
                                    <span class="badge badge-warning float-right">{{ $alert->current_stock }} stok</span>
                                </a>
                                <span class="product-description">
                                    Minimum: {{ $alert->minimum_stock }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                        @foreach($expiringAlerts as $alert)
                        <li class="item">
                            <div class="product-img">
                                <i class="fas fa-calendar-times fa-2x text-danger"></i>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    {{ $alert->product?->name ?? 'Produk dihapus' }}
                                    @if($alert->expiration_date)
                                    <span class="badge badge-danger float-right">
                                        {{ \Carbon\Carbon::parse($alert->expiration_date)->diffForHumans() }}
                                    </span>
                                    @endif
                                </a>
                                <span class="product-description">
                                    @if($alert->expiration_date)
                                    Exp: {{ \Carbon\Carbon::parse($alert->expiration_date)->format('d/m/Y') }}
                                    @else
                                    Exp: -
                                    @endif
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('inventory.index') }}" class="uppercase">Lihat Semua Peringatan</a>
                </div>
            </div>
            @endcan
            @can('reports.view')
            <div class="card card-apms">
                <div class="card-header border-0">
                    <h3 class="card-title">Produk Terlaris</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card px-3">
                        @foreach($topProducts as $index => $product)
                        <li class="item">
                            <div class="product-img">
                                <span class="badge badge-primary">{{ $index + 1 }}</span>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    {{ $product->name }}
                                    <span class="badge badge-primary float-right">{{ $product->total_sold }} pcs</span>
                                </a>
                                <span class="product-description">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endcan
            @can('expenses.view')
            <div class="card card-apms bg-gradient-success">
                <div class="card-header py-2">
                    <h5 class="card-title mb-0 font-weight-bold"><i class="fas fa-wallet mr-1"></i> Ringkasan Keuangan</h5>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">Rp {{ number_format($monthSales, 0, ',', '.') }}</h5>
                            <small>Revenue</small>
                        </div>
                        <div class="col-4 border-left border-right">
                            <h5 class="mb-0">Rp {{ number_format($monthExpenses, 0, ',', '.') }}</h5>
                            <small>Expense</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">Rp {{ number_format($profit, 0, ',', '.') }}</h5>
                            <small>Profit</small>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
    @else
    {{-- Compact Dashboard for Admin Cabang / roles without reports.view --}}
    <div class="row">
        <div class="col-lg-7">
            @can('transactions.view')
            <div class="card card-apms">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-receipt mr-1 text-primary"></i> Transaksi Terbaru</h3>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary btn-sm">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentTransactions->take(5) as $t)
                        <a href="{{ route('transactions.show', $t->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                            <div>
                                <strong class="text-primary">{{ $t->invoice_number }}</strong>
                                <small class="d-block text-muted">{{ $t->customer?->name ?? 'Umum' }} &middot; {{ $t->created_at?->format('H:i') ?? '-' }}</small>
                            </div>
                            <div class="text-right">
                                <span class="font-weight-bold">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</span>
                                <span class="d-block">
                                    @if($t->paid_amount >= $t->total_amount)
                                        <span class="badge badge-success">Lunas</span>
                                    @else
                                        <span class="badge badge-warning">Hutang</span>
                                    @endif
                                </span>
                            </div>
                        </a>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada transaksi hari ini
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endcan

            @can('expenses.view')
            <div class="card card-apms">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-coins mr-1 text-danger"></i> Biaya Terbaru</h3>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger btn-sm">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentExpenses->take(5) as $e)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <div>
                                <strong>{{ $e->category->name ?? 'Umum' }}</strong>
                                <small class="d-block text-muted">{{ $e->description ?? ($e->date ? $e->date->format('d/m/Y') : '-') }}</small>
                            </div>
                            <span class="font-weight-bold text-danger">Rp {{ number_format($e->amount, 0, ',', '.') }}</span>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            Belum ada biaya hari ini
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endcan
        </div>

        <div class="col-lg-5">
            @can('stock_requests.view')
            <div class="card card-apms">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-clipboard-list mr-1 text-info"></i> Permintaan Stok</h3>
                    @can('stock_requests.create')
                    <a href="{{ route('stock-requests.create') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-plus mr-1"></i> Baru
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        @php
                            $srStatuses = ['pending', 'approved', 'shipped', 'received'];
                            $srColors = ['pending' => 'warning', 'approved' => 'info', 'shipped' => 'primary', 'received' => 'success'];
                            $srIcons = ['pending' => 'fa-clock', 'approved' => 'fa-check', 'shipped' => 'fa-truck', 'received' => 'fa-box-open'];
                            $srLabels = ['pending' => 'Pending', 'approved' => 'Disetujui', 'shipped' => 'Dikirim', 'received' => 'Diterima'];
                        @endphp
                        @foreach($srStatuses as $s)
                        <div class="col-3 px-1">
                            <div class="border rounded py-2 bg-light">
                                <i class="fas {{ $srIcons[$s] }} text-{{ $srColors[$s] }}"></i>
                                <div class="font-weight-bold small">{{ $stockRequestStats[$s] ?? 0 }}</div>
                                <small class="text-muted" style="font-size:0.6rem;">{{ $srLabels[$s] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentStockRequests->take(4) as $sr)
                        <a href="{{ route('stock-requests.show', $sr) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-0 border-0">
                            <div>
                                <small class="font-weight-bold">{{ $sr->request_number }}</small>
                                <small class="d-block text-muted">{{ $sr->requester->name ?? '-' }}</small>
                            </div>
                            <span class="badge badge-{{ $srColors[$sr->status] ?? 'secondary' }}">
                                {{ $srLabels[$sr->status] ?? ucfirst($sr->status) }}
                            </span>
                        </a>
                        @empty
                        <div class="text-center text-muted py-3">
                            <small>Belum ada permintaan stok</small>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endcan

            @canany(['transactions.create', 'stock_requests.create', 'expenses.manage'])
            <div class="card card-apms">
                <div class="card-header border-0">
                    <h3 class="card-title mb-0"><i class="fas fa-bolt mr-1 text-warning"></i> Aksi Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @can('transactions.create')
                        <div class="col-6">
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary-apms btn-block mb-2 py-2">
                                <i class="fas fa-cash-register fa-lg d-block mb-1"></i>
                                <small>Kasir</small>
                            </a>
                        </div>
                        @endcan
                        @can('stock_requests.create')
                        <div class="col-6">
                            <a href="{{ route('stock-requests.create') }}" class="btn btn-info btn-block mb-2 py-2">
                                <i class="fas fa-clipboard-list fa-lg d-block mb-1"></i>
                                <small>Minta Stok</small>
                            </a>
                        </div>
                        @endcan
                        @can('expenses.manage')
                        <div class="col-6">
                            <a href="{{ route('expenses.create') }}" class="btn btn-danger btn-block mb-2 py-2">
                                <i class="fas fa-coins fa-lg d-block mb-1"></i>
                                <small>Catat Biaya</small>
                            </a>
                        </div>
                        @endcan
                        @can('products.view')
                        <div class="col-6">
                            <a href="{{ route('products.index') }}" class="btn btn-success btn-block mb-2 py-2">
                                <i class="fas fa-spray-can fa-lg d-block mb-1"></i>
                                <small>Produk</small>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany
        </div>
    </div>
    @endcan

    @can('reports.view')
    @if(count($smartInsights) > 0)
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <div class="card card-apms border-left-primary">
                    <div class="card-header bg-white pt-2 pb-2">
                        <h5 class="card-title text-primary font-weight-bold mb-0">
                            <i class="fas fa-robot mr-1"></i> AI Strategic Advisor
                        </h5>
                        <div class="card-tools">
                            <span class="badge badge-info">Smart Insights</span>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="row">
                            @foreach($smartInsights as $insight)
                            <div class="col-md-3">
                                <div class="d-flex align-items-start">
                                    <div class="mr-2">
                                        <i class="fas {{ $insight['icon'] ?? '' }} fa-lg {{ $insight['color'] ?? '' }}"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size:0.85rem;">{{ $insight['title'] ?? '' }}</strong>
                                        <p class="small text-muted mb-0" style="font-size:0.75rem;">{{ $insight['text'] ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endcan
</div>
@endsection

@push('scripts')
<script>
$(function() {
    @can('reports.view')
    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
    var salesChartData = {
        labels: @json(collect($salesData)->pluck('month')),
        datasets: [{
            label: 'Penjualan',
            backgroundColor: 'rgba(255, 107, 53, 0.2)',
            borderColor: 'rgba(255, 107, 53, 1)',
            pointBackgroundColor: 'rgba(255, 107, 53, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(255, 107, 53, 1)',
            data: @json(collect($salesData)->pluck('sales'))
        }]
    };
    new Chart(salesChartCanvas, {
        type: 'line',
        data: salesChartData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    grid: { display: true },
                    ticks: {
                        callback: function(value) { return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
                    }
                }
            }
        }
    });

    @php
        $pmLabels = ['cash' => 'Cash', 'qris' => 'QRIS', 'transfer' => 'Transfer', 'ewallet' => 'E-Wallet', 'debit_card' => 'Debit Card', 'credit_card' => 'Credit Card'];
        $pmColors = ['cash' => '#FF6B35', 'qris' => '#3498db', 'transfer' => '#2ecc71', 'ewallet' => '#e74c3c', 'debit_card' => '#9b59b6', 'credit_card' => '#f39c12'];
        $pmData = $paymentData ?? [];
        $pmKeys = array_keys($pmData);
        $pmChartLabels = array_map(fn($k) => $pmLabels[$k] ?? ucfirst($k), $pmKeys);
        $pmChartColors = array_map(fn($k) => $pmColors[$k] ?? '#6c757d', $pmKeys);
        $pmChartValues = array_values($pmData);
    @endphp
    if ($('#paymentChart').length) {
        var pmCtx = $('#paymentChart').get(0).getContext('2d');
        new Chart(pmCtx, {
            type: 'doughnut',
            data: {
                labels: @json($pmChartLabels),
                datasets: [{
                    data: @json($pmChartValues),
                    backgroundColor: @json($pmChartColors)
                }]
            },
            options: { maintainAspectRatio: false, responsive: true, cutout: '70%', plugins: { legend: { display: false } } }
        });
        var pmBarCtx = $('#paymentBarChart').get(0).getContext('2d');
        new Chart(pmBarCtx, {
            type: 'bar',
            data: {
                labels: @json($pmChartLabels),
                datasets: [{
                    data: @json($pmChartValues),
                    backgroundColor: @json($pmChartColors),
                    borderRadius: 3
                }]
            },
            options: {
                indexAxis: 'y', maintainAspectRatio: false, responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { display: false } },
                    y: { grid: { display: false }, ticks: { font: { size: 8 } } }
                }
            }
        });
    }

    function loadComparison(mode) {
        $('#comparison-loading').show();
        $('#comparison-content').hide();
        $.getJSON('/api/dashboard/comparison', { mode: mode }, function(data) {
            var html = '';
            var icons = { revenue: 'fa-money-bill-wave', transactions: 'fa-receipt', profit: 'fa-chart-line', avg_basket: 'fa-shopping-cart' };
            $.each(data.kpis, function(key, kpi) {
                var d = parseFloat(kpi.delta);
                var arrow = d >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger';
                var badgeClass = d >= 0 ? 'badge-success' : 'badge-danger';
                var deltaText = (d >= 0 ? '+' : '') + d + '%';
                html += '<div class="col-lg-3 col-md-6 mb-2">' +
                    '<div class="card card-apms border-left-primary h-100">' +
                    '<div class="card-body py-2 px-3">' +
                    '<div class="d-flex align-items-center">' +
                    '<div class="mr-3">' +
                    '<i class="fas ' + (icons[key]||'fa-chart-bar') + ' fa-2x text-primary"></i>' +
                    '</div>' +
                    '<div class="flex-grow-1">' +
                    '<p class="mb-0 text-muted small text-uppercase font-weight-bold">' + kpi.label + '</p>' +
                    '<h5 class="mb-0 font-weight-bold">' + kpi.current + '</h5>' +
                    '<div class="d-flex align-items-center mt-1">' +
                    '<small class="text-muted mr-2"><i class="fas fa-history mr-1"></i>' + kpi.previous + '</small>' +
                    '<span class="badge ' + badgeClass + ' px-2"><i class="fas ' + arrow + ' mr-1"></i>' + deltaText + '</span>' +
                    '</div></div></div></div></div>';
            });
            $('#comparison-cards').html(html);
            $('#comparison-loading').hide();
            $('#comparison-content').show();
        });
    }
    loadComparison('mom');
    $('[name="comp_mode"]').on('change', function() { loadComparison($(this).val()); });
    @endcan
});
</script>
@endpush