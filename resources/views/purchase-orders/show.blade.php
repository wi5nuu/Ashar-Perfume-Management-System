@extends('layouts.app')
@section('title', 'Detail Purchase Order')

@section('content')
<style>
    .btn-action-flow {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    @media (max-width: 575.98px) {
        .btn-action-flow {
            font-size: 0.7rem !important;
            padding: 0.4rem 0.6rem !important;
        }
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-file-invoice"></i> Detail Purchase Order</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{ $purchaseOrder->po_number }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- PO Number & Status Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 font-weight-bold text-primary-apms">
            <i class="fas fa-shopping-cart mr-2"></i> {{ $purchaseOrder->po_number }}
        </h4>
        <span class="badge badge-lg 
            @switch($purchaseOrder->status)
                @case('draft') badge-secondary @break
                @case('sent') badge-info @break
                @case('partial') badge-warning @break
                @case('received') badge-success @break
                @case('cancelled') badge-danger @break
            @endswitch p-2">
            {{ strtoupper($purchaseOrder->status) }}
        </span>
    </div>

    <div class="row">
        {{-- Informasi PO --}}
        <div class="col-lg-5 mb-4">
            <div class="card card-apms border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Informasi PO</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width:120px;">Supplier</td><td><strong>{{ $purchaseOrder->supplier->name ?? '-' }}</strong></td></tr>
                        <tr><td class="text-muted">Cabang</td><td>{{ $purchaseOrder->branch->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Tgl Order</td><td>{{ $purchaseOrder->order_date->format('d/m/Y') }}</td></tr>
                        <tr><td class="text-muted">Expected</td><td>{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="text-muted">Diterima</td><td>{{ $purchaseOrder->received_date ? $purchaseOrder->received_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="text-muted">Dibuat Oleh</td><td>{{ $purchaseOrder->user->name ?? '-' }}</td></tr>
                        @if($purchaseOrder->notes)
                        <tr><td class="text-muted">Catatan</td><td><em>{{ $purchaseOrder->notes }}</em></td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Item Pesanan --}}
        <div class="col-lg-7 mb-4">
            <div class="card card-apms border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-box mr-2"></i> Item Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty Order</th>
                                    <th class="text-center">Qty Diterima</th>
                                    <th>Harga/Unit</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '-' }} <small class="text-muted">{{ $item->product->size ?? '' }} {{ $item->product->unit ?? '' }}</small></td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">
                                        @if($item->received_quantity > 0)
                                            <span class="badge badge-success">{{ $item->received_quantity }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                        @if($item->remaining > 0 && $purchaseOrder->status !== 'received' && $purchaseOrder->status !== 'cancelled')
                                            <small class="text-warning ml-1">(sisa {{ $item->remaining }})</small>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-size:1.1rem">
                                    <td colspan="4" class="text-right font-weight-bold">Total:</td>
                                    <td class="text-right text-primary font-weight-bold">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Progress Bar & Workflow --}}
        <div class="col-lg-8 mb-4">
            @if($purchaseOrder->status !== 'cancelled')
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-tasks mr-2"></i> Progress Penerimaan</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalOrdered = $purchaseOrder->items->sum('quantity');
                        $totalReceived = $purchaseOrder->items->sum('received_quantity');
                        $progress = $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100) : 0;
                    @endphp
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ $totalReceived }} / {{ $totalOrdered }} unit diterima</span>
                        <span class="font-weight-bold">{{ $progress }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar 
                            @if($progress == 100) bg-success 
                            @elseif($progress > 0) bg-warning 
                            @else bg-secondary @endif" 
                            role="progressbar" style="width: {{ $progress }}%" 
                            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $progress }}%
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Workflow Actions --}}
        <div class="col-lg-4 mb-4">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-primary-apms"><i class="fas fa-tasks mr-2"></i> Alur Kerja</h5>
                </div>
                <div class="card-body">
                    @if($purchaseOrder->status === 'draft')
                        <div class="alert alert-secondary border-0 small">
                            PO masih dalam tahap draft. Kirim ke supplier untuk memulai proses pemesanan.
                        </div>
                        <form action="{{ route('purchase-orders.send', $purchaseOrder) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-info btn-block btn-action-flow"
                                onclick="return confirm('Kirim PO ini ke supplier?')">
                                <i class="fas fa-paper-plane mr-2"></i> KIRIM KE SUPPLIER
                            </button>
                        </form>
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block btn-action-flow"
                                onclick="return confirm('Batalkan PO ini?')">
                                <i class="fas fa-times mr-2"></i> BATALKAN
                            </button>
                        </form>

                    @elseif($purchaseOrder->status === 'sent')
                        <div class="alert alert-info border-0 small">
                            PO telah dikirim ke supplier. Menunggu barang datang.
                        </div>
                        <a href="{{ route('purchase-orders.receive-form', $purchaseOrder) }}" class="btn btn-success btn-block btn-action-flow mb-2">
                            <i class="fas fa-boxes mr-2"></i> TERIMA BARANG
                        </a>
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block btn-action-flow"
                                onclick="return confirm('Batalkan PO ini?')">
                                <i class="fas fa-times mr-2"></i> BATALKAN
                            </button>
                        </form>

                    @elseif($purchaseOrder->status === 'partial')
                        <div class="alert alert-warning border-0 small">
                            Sebagian barang sudah diterima. Terima sisa barang untuk menyelesaikan PO.
                        </div>
                        <a href="{{ route('purchase-orders.receive-form', $purchaseOrder) }}" class="btn btn-success btn-block btn-action-flow mb-2">
                            <i class="fas fa-boxes mr-2"></i> TERIMA SISA BARANG
                        </a>
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block btn-action-flow"
                                onclick="return confirm('Batalkan PO ini? Sisa barang yang belum diterima akan diabaikan.')">
                                <i class="fas fa-times mr-2"></i> BATALKAN
                            </button>
                        </form>

                    @elseif($purchaseOrder->status === 'received')
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5 class="font-weight-bold">PO Selesai</h5>
                            <p class="text-muted">Semua barang telah diterima dan stok telah diperbarui.</p>
                            <p class="text-muted small">Diterima: {{ $purchaseOrder->received_date ? $purchaseOrder->received_date->format('d/m/Y') : '-' }}</p>
                        </div>

                    @elseif($purchaseOrder->status === 'cancelled')
                        <div class="text-center py-4">
                            <i class="fas fa-ban fa-4x text-danger mb-3"></i>
                            <h5 class="font-weight-bold">PO Dibatalkan</h5>
                            <p class="text-muted">Purchase Order ini telah dibatalkan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('purchase-orders.index') }}" class="btn btn-light btn-block">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
