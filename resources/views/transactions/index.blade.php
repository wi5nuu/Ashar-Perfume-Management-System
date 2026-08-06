@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-exchange-alt mr-2"></i>Daftar Transaksi</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Transaksi</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="exportTransactions('pdf')">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="exportTransactions('csv')">
                        <i class="fas fa-file-csv mr-1"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    <!-- KPI Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-4 col-12 mb-2">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255, 107, 53, 0.1);">
                                <i class="fas fa-receipt fa-lg" style="color: var(--primary);"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 ml-3">
                            <div class="text-muted small">Total Transaksi Hari Ini</div>
                            <div class="h4 mb-0 font-weight-bold" id="todayTransactionCount">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-2">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(40, 199, 111, 0.1);">
                                <i class="fas fa-money-bill-wave fa-lg text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 ml-3">
                            <div class="text-muted small">Pendapatan Hari Ini</div>
                            <div class="h4 mb-0 font-weight-bold text-success" id="todayRevenue">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-2">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(52, 152, 219, 0.1);">
                                <i class="fas fa-chart-line fa-lg text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3 ml-3">
                            <div class="text-muted small">Rata-rata Transaksi</div>
                            <div class="h4 mb-0 font-weight-bold text-info" id="avgTransaction">
                                <span class="spinner-border spinner-border-sm"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title mb-0">Riwayat Transaksi</h3>
                </div>
                
                <!-- Filter Bar -->
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-3 col-12 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Rentang Tanggal</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="dateRange" class="form-control" placeholder="Pilih rentang tanggal">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Metode Bayar</label>
                            <select id="paymentFilter" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer</option>
                                <option value="credit">Kredit</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Tipe Customer</label>
                            <select id="customerFilter" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Cari</label>
                            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari invoice, pelanggan...">
                        </div>
                        <div class="col-md-2 col-12 mb-2 mb-md-0 d-flex align-items-end">
                            <button class="btn btn-secondary btn-sm btn-block" onclick="resetFilters()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-modern mb-0" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th class="d-none d-md-table-cell">Tanggal & Jam</th>
                                    <th>Pelanggan</th>
                                    <th class="d-none d-lg-table-cell">Tipe</th>
                                    <th class="d-none d-sm-table-cell">Items</th>
                                    <th>Total</th>
                                    <th class="d-none d-sm-table-cell">Metode Bayar</th>
                                    <th class="d-none d-lg-table-cell">Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction->id) }}" class="font-weight-bold text-primary">
                                            {{ $transaction->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="small">
                                            <div class="font-weight-bold">{{ $transaction->created_at?->format('d/m/Y') ?? '-' }}</div>
                                            <div class="text-muted">{{ $transaction->created_at?->format('H:i') ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-weight-bold">{{ $transaction->customer?->name ?? 'Umum' }}</div>
                                            <div class="small text-muted d-md-none">{{ $transaction->created_at?->format('d/m H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($transaction->customer_type == 'wholesale')
                                            <span class="badge badge-modern badge-info">
                                                <i class="fas fa-building"></i> Grosir
                                            </span>
                                        @else
                                            <span class="badge badge-modern badge-secondary">
                                                <i class="fas fa-user"></i> Retail
                                            </span>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge badge-light">{{ $transaction->details->count() }} item</span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-success">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @php
                                            $paymentIcons = [
                                                'cash' => 'fa-money-bill-wave',
                                                'qris' => 'fa-qrcode',
                                                'transfer' => 'fa-exchange-alt',
                                                'credit' => 'fa-credit-card'
                                            ];
                                            $icon = $paymentIcons[$transaction->payment_method] ?? 'fa-wallet';
                                        @endphp
                                        <span class="badge badge-light">
                                            <i class="fas {{ $icon }}"></i> {{ strtoupper($transaction->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge badge-modern badge-success">
                                            <i class="fas fa-check-circle"></i> Lunas
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('transactions.show', $transaction->id) }}" 
                                               class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('transactions.print', $transaction->id) }}" 
                                               class="btn btn-success btn-sm" target="_blank" title="Print Invoice">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @can('manage_transactions')
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="deleteTransaction({{ $transaction->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $transaction->id }}" 
                                                  action="{{ route('transactions.destroy', $transaction->id) }}" 
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="100%" class="text-center py-5">
                                        <div class="empty-state py-5">
                                            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum Ada Transaksi</h5>
                                            <p class="text-muted mb-0">Transaksi yang dibuat akan muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
                        </div>
                        <div>
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.card-modern {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border-radius: 8px;
    transition: all 0.3s ease;
}
.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}
.table-modern thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 12px;
}
.table-modern tbody tr {
    transition: all 0.2s;
}
.table-modern tbody tr:hover {
    background-color: #f8f9fa;
}
.badge-modern {
    padding: 6px 12px;
    font-weight: 500;
    border-radius: 6px;
}
@media (max-width: 767.98px) {
    .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 0.75rem;
    }
    .table-modern {
        font-size: 0.85rem;
    }
}
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
    // Load KPI data
    loadKPIData();
    
    // Date range picker
    $('#dateRange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: 'Clear',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal'
        },
        ranges: {
            'Hari Ini': [moment(), moment()],
            'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
            '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    
    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        filterTransactions();
    });
    
    $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        filterTransactions();
    });
    
    // Filter handlers
    $('#paymentFilter, #customerFilter').change(filterTransactions);
    $('#searchInput').on('keyup', debounce(filterTransactions, 300));
});

function loadKPIData() {
    // Simulate loading - replace with actual AJAX call
    setTimeout(() => {
        $('#todayTransactionCount').html('{{ $transactions->where("created_at", ">=", now()->startOfDay())->count() }}');
        $('#todayRevenue').html('Rp ' + formatNumber({{ $transactions->where('created_at', '>=', now()->startOfDay())->sum('total_amount') }}));
        const todayTrans = {{ $transactions->where('created_at', '>=', now()->startOfDay())->count() }};
        const todayRev = {{ $transactions->where('created_at', '>=', now()->startOfDay())->sum('total_amount') }};
        const avg = todayTrans > 0 ? todayRev / todayTrans : 0;
        $('#avgTransaction').html('Rp ' + formatNumber(Math.round(avg)));
    }, 100);
}

function filterTransactions() {
    const search = $('#searchInput').val().toLowerCase();
    const payment = $('#paymentFilter').val().toLowerCase();
    const customer = $('#customerFilter').val().toLowerCase();
    
    $('#transactionsTable tbody tr').each(function() {
        const $row = $(this);
        const text = $row.text().toLowerCase();
        let show = true;
        
        if (search && text.indexOf(search) === -1) show = false;
        if (payment && text.indexOf(payment) === -1) show = false;
        if (customer) {
            const typeText = $row.find('td').eq(3).text().toLowerCase();
            if (typeText.indexOf(customer) === -1) show = false;
        }
        
        $row.toggle(show);
    });
}

function resetFilters() {
    $('#searchInput').val('');
    $('#paymentFilter').val('');
    $('#customerFilter').val('');
    $('#dateRange').val('');
    $('#transactionsTable tbody tr').show();
}

function deleteTransaction(id) {
    Swal.fire({
        title: 'Hapus Transaksi?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#delete-form-' + id).submit();
        }
    });
}

function exportTransactions(format) {
    // Ambil filter tanggal aktif dari input dateRange jika ada
    var dateRangeVal = $('#dateRange').val() || '';
    var startDate = '';
    var endDate = '';
    if (dateRangeVal.indexOf(' - ') !== -1) {
        var parts = dateRangeVal.split(' - ');
        startDate = parts[0].trim();
        endDate   = parts[1].trim();
    }

    var params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate)   params.append('end_date', endDate);

    var url = '';
    if (format === 'csv') {
        url = '{{ route("reports.export.transactions") }}';
    } else {
        // PDF menggunakan export sales
        url = '{{ route("reports.export.sales") }}';
    }

    if (params.toString()) {
        url += '?' + params.toString();
    }

    window.location.href = url;
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
