@extends('layouts.app')
@section('title', 'Detail Penerimaan Barang')

@section('content')
@php
    $isRefill = $goodsReceipt->product?->is_refill ?? false;
    $qty      = $goodsReceipt->quantity ?? 0;
    $qtyText  = $isRefill
        ? ($qty >= 1000
            ? number_format($qty/1000, 2, ',', '.').' L ('.number_format($qty, 0, ',', '.').' ml)'
            : number_format($qty, 0, ',', '.').' ml')
        : number_format($qty, 0, ',', '.').' botol';
    $unitLabel = $isRefill ? 'ml' : 'botol';
@endphp

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-dolly-flatbed mr-2"></i>Detail Penerimaan Barang</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('goods-receipts.index') }}">Penerimaan Barang</a></li>
                        <li class="breadcrumb-item active">{{ $goodsReceipt->receipt_number }}</li>
                    </ol>
                </div>
                <a href="{{ route('goods-receipts.index') }}" class="btn btn-secondary btn-sm" style="border-radius:8px;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-receipt mr-2" style="color:var(--primary);"></i>
                        {{ $goodsReceipt->receipt_number }}
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="35%" class="pl-3">Produk</th>
                            <td>
                                <span class="font-weight-600">{{ $goodsReceipt->product->name ?? '-' }}</span>
                                <span class="badge badge-sm {{ $isRefill ? 'badge-success' : 'badge-info' }} ml-2" style="font-size:.72rem;">
                                    {{ $isRefill ? 'BIBIT' : 'BOTOL' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="pl-3">Jumlah Diterima</th>
                            <td class="font-weight-bold text-primary">{{ $qtyText }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Supplier</th>
                            <td>{{ $goodsReceipt->supplier_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Pengantar</th>
                            <td>{{ $goodsReceipt->delivery_person ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Asal Barang</th>
                            <td>{{ $goodsReceipt->origin ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Tanggal Masuk</th>
                            <td>
                                <i class="fas fa-calendar-check text-success mr-1"></i>
                                {{ $goodsReceipt->received_date?->format('d F Y') ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="pl-3">Tanggal Kadaluarsa</th>
                            <td>
                                @if($goodsReceipt->expiration_date)
                                    @php
                                        $expired = $goodsReceipt->expiration_date->isPast();
                                        $soon    = !$expired && $goodsReceipt->expiration_date->diffInDays(now()) <= 30;
                                    @endphp
                                    <i class="fas fa-calendar-times {{ $expired ? 'text-danger' : ($soon ? 'text-warning' : 'text-muted') }} mr-1"></i>
                                    <span class="{{ $expired ? 'text-danger font-weight-bold' : ($soon ? 'text-warning font-weight-bold' : '') }}">
                                        {{ $goodsReceipt->expiration_date->format('d F Y') }}
                                    </span>
                                    @if($expired)
                                        <span class="badge badge-danger ml-1">Kadaluarsa</span>
                                    @elseif($soon)
                                        <span class="badge badge-warning ml-1">Segera Kadaluarsa</span>
                                    @endif
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="pl-3">Biaya per {{ $unitLabel }}</th>
                            <td>Rp {{ number_format($goodsReceipt->unit_cost ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Total Biaya</th>
                            <td class="font-weight-bold text-success">Rp {{ number_format($goodsReceipt->total_cost ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Catatan</th>
                            <td>{{ $goodsReceipt->notes ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Cabang</th>
                            <td>{{ $goodsReceipt->branch?->name ?? 'Pusat' }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Dicatat Oleh</th>
                            <td>{{ $goodsReceipt->recorder?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="pl-3">Dicatat Pada</th>
                            <td>{{ $goodsReceipt->created_at->format('d F Y, H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar: ringkasan stok --}}
        <div class="col-lg-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-boxes mr-2" style="color:var(--primary);"></i>Stok Setelah Masuk
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $inv = $goodsReceipt->product?->inventories->first();
                        $stockMl   = $inv ? (int)$inv->current_stock : 0;
                        $bulkMl    = $inv ? (int)($inv->bulk_stock_ml ?? 0) : 0;
                        $size      = $goodsReceipt->product
                            ? (float) preg_replace('/[^0-9.]/', '', $goodsReceipt->product->size ?? '0')
                            : 0;
                    @endphp
                    @if($isRefill)
                        <div class="text-center py-2">
                            <div class="text-muted small mb-1">Stok Bibit Saat Ini</div>
                            <div class="font-weight-bold" style="font-size:1.4rem; color:var(--primary);">
                                @if($stockMl >= 1000)
                                    {{ number_format($stockMl/1000, 2, ',', '.') }} L
                                @else
                                    {{ number_format($stockMl, 0, ',', '.') }} ml
                                @endif
                            </div>
                            <div class="text-muted small">{{ number_format($stockMl, 0, ',', '.') }} ml</div>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <div class="text-muted small mb-1">Stok Botol Saat Ini</div>
                            <div class="font-weight-bold" style="font-size:1.4rem; color:var(--primary);">
                                {{ $size > 0 ? number_format(floor($bulkMl/$size), 0, ',', '.') : number_format($bulkMl, 0, ',', '.') }} botol
                            </div>
                            <div class="text-muted small">{{ number_format($bulkMl, 0, ',', '.') }} ml</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
