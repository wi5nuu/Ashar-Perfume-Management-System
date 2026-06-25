@extends('layouts.app')
@section('title', 'Terima Barang PO')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-boxes"></i> Terima Barang</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.show', $purchaseOrder) }}">{{ $purchaseOrder->po_number }}</a></li>
                    <li class="breadcrumb-item active">Terima Barang</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" id="receiveForm">
        @csrf
        <div class="card card-apms border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 font-weight-bold text-primary-apms">
                        <i class="fas fa-shopping-cart mr-2"></i> {{ $purchaseOrder->po_number }}
                    </h5>
                    <small class="text-muted">Supplier: {{ $purchaseOrder->items->first()->product->name ?? '' }} | Total {{ $purchaseOrder->items->count() }} item</small>
                </div>
                <span class="badge badge-lg 
                    @if($purchaseOrder->status === 'sent') badge-info 
                    @else badge-warning @endif p-2">
                    {{ strtoupper($purchaseOrder->status) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty Order</th>
                                <th class="text-center">Sudah Diterima</th>
                                <th class="text-center">Sisa</th>
                                <th class="text-center" style="width:150px">Qty Diterima Sekarang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td>
                                    {{ $item->product->name ?? '-' }}
                                    <small class="text-muted d-block">{{ $item->product->size ?? '' }} {{ $item->product->unit ?? '' }}</small>
                                </td>
                                <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                                <td class="text-center">
                                    @if($item->received_quantity > 0)
                                        <span class="badge badge-success">{{ $item->received_quantity }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">{{ $item->remaining }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="number" 
                                        name="received[{{ $item->id }}]" 
                                        class="form-control form-control-sm receive-input text-center" 
                                        min="0" 
                                        max="{{ $item->remaining }}" 
                                        value="0"
                                        data-remaining="{{ $item->remaining }}"
                                        data-item-name="{{ $item->product->name ?? '-' }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <div>
                    <button type="button" class="btn btn-outline-info mr-2" id="receiveAllBtn">
                        <i class="fas fa-check-double mr-1"></i> Terima Semua
                    </button>
                    <button type="submit" class="btn btn-primary-apms" id="submitBtn"
                        onclick="return confirm('Konfirmasi penerimaan barang? Stok akan diperbarui secara otomatis.')">
                        <i class="fas fa-save mr-1"></i> Konfirmasi Penerimaan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // "Terima Semua" button — fill all inputs with their remaining qty
    $('#receiveAllBtn').on('click', function() {
        $('.receive-input').each(function() {
            $(this).val($(this).data('remaining'));
        });
    });

    // Validate max on input
    $(document).on('input', '.receive-input', function() {
        const max = parseInt($(this).data('remaining')) || 0;
        let val = parseInt($(this).val()) || 0;
        if (val > max) {
            $(this).val(max);
        }
        if (val < 0) {
            $(this).val(0);
        }
    });
});
</script>
@endpush
