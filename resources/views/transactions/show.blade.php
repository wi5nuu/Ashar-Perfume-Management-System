@extends('layouts.app')

@section('title', 'Invoice - ' . $transaction->invoice_number)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="h3 mb-1">Detail Transaksi</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Transaksi</a></li>
                            <li class="breadcrumb-item active">{{ $transaction->invoice_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="{{ route('transactions.print', $transaction->id) }}" class="btn btn-primary-apms" target="_blank">
                        <i class="fas fa-print"></i> Cetak Invoice
                    </a>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card card-apms" id="invoiceCard">

                <!-- Invoice Header -->
                <div class="card-body pb-0" style="border-bottom: 3px solid var(--primary);">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3"
                                     style="width: 52px; height: 52px; background: var(--primary); flex-shrink: 0;">
                                    <i class="fas fa-wine-bottle fa-lg text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 font-weight-bold" style="color: var(--primary);">APMS</h4>
                                    <small class="text-muted">Ashar Grosir Perfume Management System</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 text-md-right">
                            <h2 class="h3 font-weight-bold mb-1">INVOICE</h2>
                            <div class="font-weight-bold" style="font-size: 1.1rem; color: var(--primary);">
                                {{ $transaction->invoice_number }}
                            </div>
                            <div class="text-muted small">
                                {{ $transaction->created_at?->format('d F Y, H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Customer & Transaction Info -->
                    <div class="row mb-4">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <div class="small text-muted text-uppercase font-weight-bold mb-2">
                                    <i class="fas fa-user mr-1"></i> Tagihan Kepada
                                </div>
                                <div class="font-weight-bold h6 mb-1">
                                    {{ $transaction->customer?->name ?? 'Pelanggan Umum' }}
                                </div>
                                @if($transaction->customer?->phone)
                                <div class="small text-muted">
                                    <i class="fas fa-phone mr-1"></i> {{ $transaction->customer->phone }}
                                </div>
                                @endif
                                @if($transaction->customer?->address)
                                <div class="small text-muted mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $transaction->customer->address }}
                                </div>
                                @endif
                                <div class="mt-2">
                                    @if($transaction->customer_type == 'wholesale')
                                        <span class="badge badge-info">
                                            <i class="fas fa-building mr-1"></i> Pelanggan Grosir
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-user mr-1"></i> Pelanggan Retail
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <div class="small text-muted text-uppercase font-weight-bold mb-2">
                                    <i class="fas fa-info-circle mr-1"></i> Info Transaksi
                                </div>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="pl-0 text-muted small" width="40%">No. Invoice</td>
                                        <td class="pr-0 font-weight-bold small">{{ $transaction->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted small">Tanggal</td>
                                        <td class="pr-0 small">{{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted small">Kasir</td>
                                        <td class="pr-0 small">{{ $transaction->user?->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted small">Metode Bayar</td>
                                        <td class="pr-0 small">
                                            @php
                                                $paymentLabels = ['cash'=>'Cash','qris'=>'QRIS','transfer'=>'Transfer Bank','credit'=>'Kredit/Cicilan'];
                                            @endphp
                                            <span class="badge badge-light">
                                                {{ $paymentLabels[$transaction->payment_method] ?? strtoupper($transaction->payment_method) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 text-muted small">Status</td>
                                        <td class="pr-0 small">
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle mr-1"></i> Lunas
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-4">
                        <table class="table" style="border: 1px solid #dee2e6;">
                            <thead style="background: var(--primary); color: white;">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Produk</th>
                                    <th class="text-right d-none d-sm-table-cell">Harga Satuan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right d-none d-md-table-cell">Diskon</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->details as $index => $detail)
                                <tr>
                                    <td class="text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $detail->product?->name ?? 'Produk Dihapus' }}</div>
                                        @if($detail->product?->size)
                                        <div class="small text-muted">{{ $detail->product->size }} {{ $detail->product->unit }}</div>
                                        @endif
                                        @if($detail->notes)
                                        <div class="small text-info"><i class="fas fa-sticky-note mr-1"></i>{{ $detail->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right d-none d-sm-table-cell">
                                        Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light">{{ $detail->quantity }}</span>
                                    </td>
                                    <td class="text-right d-none d-md-table-cell text-danger">
                                        @if($detail->discount > 0)
                                            -Rp {{ number_format($detail->discount, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary & Payment Info -->
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            @if($transaction->notes)
                            <div class="p-3 rounded border-left border-info" style="background: #f0f7ff; border-left-width: 4px !important;">
                                <div class="small font-weight-bold text-info mb-1">
                                    <i class="fas fa-sticky-note mr-1"></i> Catatan
                                </div>
                                <div class="small">{{ $transaction->notes }}</div>
                            </div>
                            @endif

                            <!-- Payment Info -->
                            <div class="mt-3 p-3 rounded" style="background: #f8f9fa;">
                                <div class="small text-muted text-uppercase font-weight-bold mb-2">
                                    <i class="fas fa-wallet mr-1"></i> Info Pembayaran
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small text-muted">Metode</span>
                                    <span class="small font-weight-bold">
                                        {{ $paymentLabels[$transaction->payment_method] ?? strtoupper($transaction->payment_method) }}
                                    </span>
                                </div>
                                @if(isset($transaction->amount_paid) && $transaction->amount_paid)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small text-muted">Jumlah Bayar</span>
                                    <span class="small font-weight-bold">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="small text-muted">Kembalian</span>
                                    <span class="small font-weight-bold text-success">
                                        Rp {{ number_format(max(0, $transaction->amount_paid - $transaction->total_amount), 0, ',', '.') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <!-- Price Summary -->
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span>Rp {{ number_format($transaction->details->sum('subtotal'), 0, ',', '.') }}</span>
                                </div>
                                @php $totalDiscount = $transaction->details->sum('discount'); @endphp
                                @if($totalDiscount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Diskon</span>
                                    <span class="text-danger">-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                @if(isset($transaction->tax_amount) && $transaction->tax_amount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Pajak</span>
                                    <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold h6 mb-0">TOTAL</span>
                                    <span class="h4 mb-0 font-weight-bold" style="color: var(--primary);">
                                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mt-3 text-center">
                                <div class="d-inline-block px-4 py-2 rounded" style="background: rgba(40,199,111,0.1); border: 2px solid #28c76f;">
                                    <i class="fas fa-check-circle text-success fa-lg mr-2"></i>
                                    <span class="font-weight-bold text-success">PEMBAYARAN LUNAS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer text-center text-muted small">
                    Terima kasih atas kepercayaan Anda berbelanja di APMS &bull; Simpan invoice ini sebagai bukti transaksi
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .sidebar, .navbar, .breadcrumb, .btn, .card-tools, nav { display: none !important; }
    .content-wrapper { margin: 0 !important; padding: 0 !important; }
    #invoiceCard { box-shadow: none !important; border: none !important; }
    body { background: white !important; }
}
</style>
@endpush

@push('scripts')
<script>
$(function() {
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json(session('success')),
        timer: 2000,
        showConfirmButton: false
    });
    @endif
});
</script>
@endpush
