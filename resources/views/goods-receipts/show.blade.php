@extends('layouts.app')
@section('title', 'Detail Penerimaan Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Penerimaan Barang</h3>
                    <div class="card-tools">
                        <a href="{{ route('goods-receipts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">No. Receipt</th>
                            <td>{{ $goodsReceipt->receipt_number }}</td>
                        </tr>
                        <tr>
                            <th>Produk</th>
                            <td>{{ $goodsReceipt->product->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>{{ number_format($goodsReceipt->quantity) }}</td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>{{ $goodsReceipt->supplier_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pengantar</th>
                            <td>{{ $goodsReceipt->delivery_person ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Asal Barang</th>
                            <td>{{ $goodsReceipt->origin ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Masuk</th>
                            <td>{{ $goodsReceipt->received_date ? $goodsReceipt->received_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Biaya per Unit</th>
                            <td>Rp {{ number_format($goodsReceipt->unit_cost) }}</td>
                        </tr>
                        <tr>
                            <th>Total Biaya</th>
                            <td>Rp {{ number_format($goodsReceipt->total_cost) }}</td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $goodsReceipt->notes ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dicatat Oleh</th>
                            <td>{{ $goodsReceipt->recorder?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>{{ $goodsReceipt->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dicatat Pada</th>
                            <td>{{ $goodsReceipt->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
