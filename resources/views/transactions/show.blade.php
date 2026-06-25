@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Detail Transaksi</h3>
                    <div class="card-tools">
                        <a href="{{ route('transactions.print', $transaction->id) }}" 
                           class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Cetak Invoice
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Informasi Transaksi</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">No. Invoice</th>
                                    <td>{{ $transaction->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Pelanggan</th>
                                    <td>{{ $transaction->customer->name ?? 'Umum' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Pelanggan</th>
                                    <td>
                                        @if($transaction->customer_type == 'wholesale')
                                            <span class="badge badge-info">Grosir</span>
                                        @else
                                            <span class="badge badge-secondary">Retail</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kasir</th>
                                    <td>{{ $transaction->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Pembayaran</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Metode</th>
                                    <td>{{ strtoupper($transaction->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Diskon</th>
                                    <td>Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-success"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Dibayar</th>
                                    <td>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Kembalian</th>
                                    <td>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <h4 class="mt-4">Daftar Produk</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $detail->product->name }}
                                        @if($detail->bonus_quantity > 0)
                                            <div class="small text-success">
                                                <i class="fas fa-gift"></i> Bonus: {{ $detail->bonus_quantity }} botol 20ml Sedang
                                            </div>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    @if($transaction->notes)
                    <div class="alert alert-info mt-3">
                        <strong>Catatan:</strong><br>
                        {{ $transaction->notes }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection