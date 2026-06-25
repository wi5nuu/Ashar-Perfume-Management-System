@extends('layouts.app')

@section('title', 'Daftar Transaksi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Daftar Transaksi</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="dateRange" class="form-control float-right" placeholder="Filter tanggal">
                            <div class="input-group-append">
                                <button class="btn btn-default">
                                    <i class="fas fa-calendar"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="paymentFilter" class="form-control">
                                <option value="">Semua Pembayaran</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="customerFilter" class="form-control">
                                <option value="">Semua Pelanggan</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th class="d-none d-md-table-cell">Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th class="d-none d-md-table-cell">Tipe</th>
                                    <th>Total</th>
                                    <th class="d-none d-sm-table-cell">Pembayaran</th>
                                    <th class="d-none d-lg-table-cell">Kasir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction->id) }}">
                                            {{ $transaction->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transaction->customer->name ?? 'Umum' }}</td>
                                    <td class="d-none d-md-table-cell">
                                        @if($transaction->customer_type == 'wholesale')
                                            <span class="badge badge-info">Grosir</span>
                                        @else
                                            <span class="badge badge-secondary">Retail</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge badge-light">{{ strtoupper($transaction->payment_method) }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $transaction->user->name }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('transactions.show', $transaction->id) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('transactions.print', $transaction->id) }}" 
                                               class="btn btn-primary btn-sm" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @can('manage_transactions')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                    data-form-id="delete-form-{{ $transaction->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $transaction->id }}" 
                                                  action="{{ route('transactions.destroy', $transaction->id) }}" 
                                                  method="POST" class="d-none no-loading">
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
                                        <div class="empty-state">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data transaksi</h5>
                                            <p class="text-muted mb-0">Transaksi yang Anda buat akan muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
    // Date range picker
    $('#dateRange').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    
    // Filter by payment method
    $('#paymentFilter').change(function() {
        const value = $(this).val().toLowerCase();
        $('#transactionsTable tbody tr').filter(function() {
            $(this).toggle(value === '' || $(this).find('td:eq(5)').text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Filter by customer type
    $('#customerFilter').change(function() {
        const value = $(this).val();
        if (value === '') {
            $('#transactionsTable tbody tr').show();
        } else {
            $('#transactionsTable tbody tr').each(function() {
                const type = $(this).find('td:eq(3)').text().toLowerCase();
                $(this).toggle(type.indexOf(value) > -1);
            });
        }
    });
});
</script>
@endpush