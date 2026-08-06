@extends('layouts.app')
@section('title', 'Detail Purchase Order')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-apms mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="page-header-title"><i class="fas fa-file-invoice mr-2"></i> Detail Purchase Order</h1>
                <p class="page-header-subtitle">{{ $purchaseOrder->po_number }}</p>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Status Banner --}}
    @php
        $statusConfig = [
            'draft'     => ['class' => 'secondary', 'icon' => 'fa-pencil-alt',   'label' => 'DRAFT'],
            'sent'      => ['class' => 'info',      'icon' => 'fa-paper-plane',  'label' => 'DIKIRIM'],
            'partial'   => ['class' => 'warning',   'icon' => 'fa-box-open',     'label' => 'PARSIAL'],
            'received'  => ['class' => 'success',   'icon' => 'fa-check-circle', 'label' => 'SELESAI'],
            'cancelled' => ['class' => 'danger',    'icon' => 'fa-ban',          'label' => 'DIBATALKAN'],
        ];
        $sc = $statusConfig[$purchaseOrder->status] ?? ['class' => 'secondary', 'icon' => 'fa-question', 'label' => strtoupper($purchaseOrder->status)];
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <span class="badge badge-{{ $sc['class'] }} px-3 py-2" style="font-size:0.95rem; border-radius:6px;">
                    <i class="fas {{ $sc['icon'] }} mr-1"></i> {{ $sc['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Informasi PO --}}
        <div class="col-lg-5 mb-4">
            <div class="card card-apms h-100">
                <div class="card-header">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-info-circle mr-2 text-primary-apms"></i> Informasi PO</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0" style="table-layout:fixed;">
                        <colgroup><col style="width:130px;"><col></colgroup>
                        <tbody>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-3">Supplier</td>
                                <td class="py-3 font-weight-bold pr-3">{{ $purchaseOrder->supplier->name ?? '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Cabang</td>
                                <td class="py-2 pr-3">{{ $purchaseOrder->branch->name ?? '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Tgl Order</td>
                                <td class="py-2 pr-3">{{ $purchaseOrder->order_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Expected</td>
                                <td class="py-2 pr-3">{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted pl-3 py-2">Diterima</td>
                                <td class="py-2 pr-3">{{ $purchaseOrder->received_date ? $purchaseOrder->received_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr @if(!$purchaseOrder->notes) class="border-bottom" @endif>
                                <td class="text-muted pl-3 py-2">Dibuat Oleh</td>
                                <td class="py-2 pr-3">{{ $purchaseOrder->user->name ?? '-' }}</td>
                            </tr>
                            @if($purchaseOrder->notes)
                            <tr>
                                <td class="text-muted pl-3 py-2">Catatan</td>
                                <td class="py-2 pr-3"><em class="text-muted">{{ $purchaseOrder->notes }}</em></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Item Pesanan --}}
        <div class="col-lg-7 mb-4">
            <div class="card card-apms h-100">
                <div class="card-header">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-box mr-2 text-warning"></i> Item Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty Order</th>
                                    <th class="text-center">Qty Diterima</th>
                                    <th class="text-right">Harga/Unit</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td>
                                        <span class="font-weight-600">{{ $item->product->name ?? '-' }}</span>
                                        @if($item->product->size || $item->product->unit)
                                            <small class="text-muted d-block">{{ $item->product->size ?? '' }} {{ $item->product->unit ?? '' }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">
                                        @if($item->received_quantity > 0)
                                            <span class="badge-modern badge-modern-success">{{ $item->received_quantity }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                        @if($item->remaining > 0 && !in_array($purchaseOrder->status, ['received', 'cancelled']))
                                            <small class="text-warning d-block">(sisa {{ $item->remaining }})</small>
                                        @endif
                                    </td>
                                    <td class="text-right">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:#f8f9fa;">
                                    <td colspan="4" class="text-right font-weight-bold py-3 pr-3">Total:</td>
                                    <td class="text-right font-weight-bold py-3 pr-3" style="color:var(--primary); font-size:1.05rem;">
                                        Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Progress Penerimaan --}}
        @if($purchaseOrder->status !== 'cancelled')
        <div class="col-lg-8 mb-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-tasks mr-2 text-info"></i> Progress Penerimaan</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalOrdered  = $purchaseOrder->items->sum('quantity');
                        $totalReceived = $purchaseOrder->items->sum('received_quantity');
                        $progress      = $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100) : 0;
                    @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ $totalReceived }} / {{ $totalOrdered }} unit diterima</span>
                        <span class="font-weight-bold">{{ $progress }}%</span>
                    </div>
                    <div class="progress" style="height: 18px; border-radius: 10px;">
                        <div class="progress-bar
                            @if($progress == 100) bg-success
                            @elseif($progress > 0) bg-warning
                            @else bg-secondary @endif"
                            role="progressbar"
                            style="width: {{ $progress }}%; border-radius: 10px; transition: width 0.6s ease;"
                            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $progress > 10 ? $progress . '%' : '' }}
                        </div>
                    </div>
                    @if($progress == 100)
                        <small class="text-success mt-2 d-block"><i class="fas fa-check-circle mr-1"></i> Semua barang telah diterima.</small>
                    @elseif($progress > 0)
                        <small class="text-warning mt-2 d-block"><i class="fas fa-exclamation-circle mr-1"></i> Penerimaan parsial — sisa {{ $totalOrdered - $totalReceived }} unit belum diterima.</small>
                    @else
                        <small class="text-muted mt-2 d-block"><i class="fas fa-clock mr-1"></i> Menunggu penerimaan barang.</small>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Alur Kerja --}}
        <div class="col-lg-{{ $purchaseOrder->status !== 'cancelled' ? '4' : '12' }} mb-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h5 class="mb-0 font-weight-bold" style="color:var(--primary);">
                        <i class="fas fa-sitemap mr-2"></i> Alur Kerja
                    </h5>
                </div>
                <div class="card-body">
                    @if($purchaseOrder->status === 'draft')
                        <div class="alert alert-secondary border-0 small mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            PO masih draft. Kirim ke supplier untuk memulai pemesanan.
                        </div>
                        <form action="{{ route('purchase-orders.send', $purchaseOrder) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-info btn-block font-weight-600"
                                onclick="return confirm('Kirim PO ini ke supplier?')">
                                <i class="fas fa-paper-plane mr-2"></i> KIRIM KE SUPPLIER
                            </button>
                        </form>
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block font-weight-600"
                                onclick="return confirm('Batalkan PO ini?')">
                                <i class="fas fa-times mr-2"></i> BATALKAN
                            </button>
                        </form>

                    @elseif($purchaseOrder->status === 'sent')
                        <div class="alert alert-info border-0 small mb-3">
                            <i class="fas fa-paper-plane mr-1"></i>
                            PO dikirim ke supplier. Menunggu barang datang.
                        </div>
                        <a href="{{ route('purchase-orders.receive-form', $purchaseOrder) }}" class="btn btn-success btn-block font-weight-600 mb-2">
                            <i class="fas fa-boxes mr-2"></i> TERIMA BARANG
                        </a>
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block font-weight-600"
                                onclick="return confirm('Batalkan PO ini?')">
                                <i class="fas fa-times mr-2"></i> BATALKAN
                            </button>
                        </form>

                    @elseif($purchaseOrder->status === 'partial')
                        <div class="alert alert-warning border-0 small mb-3">
                            <i class="fas fa-box-open mr-1"></i>
                            Sebagian barang diterima. Terima sisa untuk menyelesaikan PO.
                        </div>
                        <a href="{{ route('purchase-orders.receive-form', $purchaseOrder) }}" class="btn btn-success btn-block font-weight-600 mb-2">
                            <i class="fas fa-boxes mr-2"></i> TERIMA SISA BARANG
                        </a>
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block font-weight-600"
                                onclick="return confirm('Batalkan PO ini? Sisa barang yang belum diterima akan diabaikan.')">
                                <i class="fas fa-times mr-2"></i> BATALKAN
                            </button>
                        </form>

                    @elseif($purchaseOrder->status === 'received')
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="font-weight-bold">PO Selesai</h6>
                            <p class="text-muted small mb-0">Semua barang diterima & stok diperbarui.</p>
                            @if($purchaseOrder->received_date)
                                <small class="text-muted">{{ $purchaseOrder->received_date->format('d/m/Y') }}</small>
                            @endif
                        </div>

                    @elseif($purchaseOrder->status === 'cancelled')
                        <div class="text-center py-3">
                            <i class="fas fa-ban fa-3x text-danger mb-3"></i>
                            <h6 class="font-weight-bold">PO Dibatalkan</h6>
                            <p class="text-muted small mb-0">Purchase Order ini telah dibatalkan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.font-weight-600 { font-weight: 600; }
</style>
@endpush
