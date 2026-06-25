@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Informasi Produk</h3>
                </div>
                <div class="card-body text-center">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" class="img-fluid mb-3" style="max-height: 300px;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center mb-3" 
                         style="height: 200px;">
                        <i class="fas fa-wine-bottle fa-5x text-muted"></i>
                    </div>
                    @endif
                    
                    <h3>{{ $product->name }}</h3>
                    <p class="text-muted">{{ $product->description }}</p>
                    
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Kategori</span>
                                    <span class="info-box-number text-center text-dark">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Ukuran</span>
                                    <span class="info-box-number text-center text-dark">
                                        {{ $product->size }} {{ $product->unit }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Detail Produk</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">ID Internal</th>
                            <td>{{ $product->internal_id }}</td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td>
                                {{ $product->barcode }}
                                <a href="{{ route('products.barcode', $product->id) }}" 
                                   class="btn btn-sm btn-primary ml-2" target="_blank">
                                    <i class="fas fa-barcode"></i> Cetak
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Harga Beli</th>
                            <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Harga Jual</th>
                            <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        </tr>
                        @if($product->wholesale_price)
                        <tr>
                            <th>Harga Grosir</th>
                            <td class="text-success">Rp {{ number_format($product->wholesale_price, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($product->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diupdate</th>
                            <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                    
                    <h4 class="mt-4">Riwayat Inventory</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Stok Masuk</th>
                                    <th>Stok Keluar</th>
                                    <th>Stok Saat Ini</th>
                                    <th>Batch</th>
                                    <th>Expired</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->inventories as $inventory)
                                <tr>
                                    <td>
                                        @if($inventory->date_received instanceof \Carbon\Carbon)
                                            {{ $inventory->date_received->format('d/m/Y') }}
                                        @else
                                            {{ $inventory->created_at->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td>{{ $inventory->stock_in }}</td>
                                    <td>{{ $inventory->stock_out }}</td>
                                    <td>{{ $inventory->current_stock }}</td>
                                    <td>{{ $inventory->batch_number ?? '-' }}</td>
                                    <td>
                                        @if($inventory->expiration_date)
                                            @if($inventory->expiration_date < now())
                                                <span class="badge badge-danger">
                                                    {{ $inventory->expiration_date->format('d/m/Y') }}
                                                </span>
                                            @elseif($inventory->expiration_date < now()->addDays(30))
                                                <span class="badge badge-warning">
                                                    {{ $inventory->expiration_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="badge badge-success">
                                                    {{ $inventory->expiration_date->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection