@extends('layouts.app')
@section('title', 'Catat Penerimaan Barang')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Catat Penerimaan Barang</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('goods-receipts.index') }}">Penerimaan Barang</a></li>
                    <li class="breadcrumb-item active">Catat Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Ada kesalahan:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('goods-receipts.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Kolom Kiri --}}
            <div class="col-lg-8">

                {{-- Info Produk --}}
                <div class="card card-apms mb-3">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-wine-bottle mr-2" style="color:var(--primary);"></i>Informasi Produk
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="font-weight-600">Produk <span class="text-danger">*</span></label>
                                    <select name="product_id" class="form-control select2" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $product)
                                        @php
                                            $inv = $product->centralInventory;
                                            $stokMl    = $inv ? (int)$inv->current_stock : 0;
                                            $stokBotol = $inv ? (int)($inv->bulk_stock_ml ?? 0) : 0;
                                        @endphp
                                        <option value="{{ $product->id }}"
                                            data-is-refill="{{ $product->is_refill ? '1' : '0' }}"
                                            data-current-stock="{{ $stokMl }}"
                                            data-bulk-stock="{{ $stokBotol }}"
                                            data-size="{{ preg_replace('/[^0-9.]/', '', $product->size ?? '0') }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                            @if($product->barcode) ({{ $product->barcode }}) @endif
                                            — {{ $product->size ?? '-' }}
                                            @if($product->is_refill) [BIBIT] @else [BOTOL] @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    {{-- Info stok saat ini --}}
                                    <div id="stockInfo" class="mt-2 d-none">
                                        <small class="text-muted">Stok saat ini: </small>
                                        <span id="stockInfoText" class="font-weight-bold text-primary small"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-600" id="qtyLabel">Jumlah <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="quantity" id="qtyInput" class="form-control"
                                               value="{{ old('quantity') }}" min="1"
                                               placeholder="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="qtyUnit">pcs</span>
                                        </div>
                                    </div>
                                    <small class="text-muted" id="qtyHint"></small>
                                    @error('quantity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">
                                        <i class="fas fa-calendar-check mr-1 text-success"></i>
                                        Tanggal Masuk <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="received_date" class="form-control"
                                           value="{{ old('received_date', date('Y-m-d')) }}" required>
                                    @error('received_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">
                                        <i class="fas fa-calendar-times mr-1 text-danger"></i>
                                        Tanggal Kadaluarsa
                                    </label>
                                    <input type="date" name="expiration_date" class="form-control"
                                           value="{{ old('expiration_date') }}">
                                    <small class="text-muted">Kosongkan jika tidak ada kadaluarsa</small>
                                    @error('expiration_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-600">Biaya per Unit (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" name="unit_cost" class="form-control"
                                               value="{{ old('unit_cost', 0) }}" min="0" step="100"
                                               id="unitCost" required>
                                    </div>
                                    @error('unit_cost')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-600">Total Biaya</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" class="form-control bg-light" id="totalCost" readonly value="0">
                                    </div>
                                    <small class="text-muted">Otomatis dihitung</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Supplier --}}
                <div class="card card-apms mb-3">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-truck mr-2" style="color:var(--primary);"></i>Informasi Supplier & Pengiriman
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Nama Supplier</label>
                                    <input type="text" name="supplier_name" class="form-control"
                                           value="{{ old('supplier_name') }}"
                                           placeholder="Nama perusahaan / supplier">
                                    @error('supplier_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600">Nama Pengantar</label>
                                    <input type="text" name="delivery_person" class="form-control"
                                           value="{{ old('delivery_person') }}"
                                           placeholder="Siapa yang mengantarkan?">
                                    @error('delivery_person')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-600">Asal Barang</label>
                            <input type="text" name="origin" class="form-control"
                                   value="{{ old('origin') }}"
                                   placeholder="Kota / daerah asal barang">
                            @error('origin')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card card-apms mb-3">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-sticky-note mr-2" style="color:var(--primary);"></i>Catatan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Catatan tambahan tentang penerimaan ini...">{{ old('notes') }}</textarea>
                            @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="col-lg-4">

                {{-- Ringkasan --}}
                <div class="card card-apms mb-3">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-receipt mr-2" style="color:var(--primary);"></i>Ringkasan
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted pl-0">Jumlah</td>
                                <td class="text-right font-weight-bold pr-0" id="summaryQty">—</td>
                            </tr>
                            <tr>
                                <td class="text-muted pl-0">Tgl Masuk</td>
                                <td class="text-right font-weight-bold pr-0" id="summaryReceived">—</td>
                            </tr>
                            <tr>
                                <td class="text-muted pl-0">Tgl Kadaluarsa</td>
                                <td class="text-right pr-0" id="summaryExpiry">
                                    <span class="text-muted">Tidak ada</span>
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted pl-0 pt-2">Total Biaya</td>
                                <td class="text-right font-weight-bold text-success pr-0 pt-2" id="summaryTotal">Rp 0</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Cabang (owner only) --}}
                @if(auth()->user()->isOwner() && $branches->count() > 0)
                <div class="card card-apms mb-3">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-store mr-2" style="color:var(--primary);"></i>Cabang
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-600">Cabang Tujuan</label>
                            <select name="branch_id" class="form-control select2">
                                <option value="">— Stok Pusat (semua cabang) —</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Kosongkan untuk stok terpusat</small>
                        </div>
                    </div>
                </div>
                @else
                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                @endif

                {{-- Tombol Aksi --}}
                <div class="card card-apms">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary-apms btn-block mb-2">
                            <i class="fas fa-save mr-1"></i> Simpan Penerimaan
                        </button>
                        <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function formatRp(val) {
        return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
    }
    function formatDate(val) {
        if (!val) return '—';
        const d = new Date(val);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    // Ketika produk dipilih — update satuan, label, dan info stok
    $('select[name=product_id]').on('change', function () {
        const selected  = $(this).find('option:selected');
        const isRefill  = selected.data('is-refill') == '1';
        const stockMl   = parseInt(selected.data('current-stock')) || 0;
        const stockBotol= parseInt(selected.data('bulk-stock')) || 0;
        const size      = parseFloat(selected.data('size')) || 0;

        if (isRefill) {
            // Bibit: satuan ml
            $('#qtyLabel').html('Jumlah Bibit <span class="text-danger">*</span>');
            $('#qtyUnit').text('ml');
            $('#qtyInput').attr('placeholder', '0');
            // Stok saat ini
            const stokL = (stockMl / 1000).toFixed(2);
            $('#stockInfoText').text(
                stockMl >= 1000
                    ? stokL + ' L (' + stockMl.toLocaleString('id-ID') + ' ml)'
                    : stockMl.toLocaleString('id-ID') + ' ml'
            );
            $('#qtyHint').text('Masukkan volume dalam ml yang diterima dari supplier.');
        } else {
            // Botol/packaging: satuan pcs
            $('#qtyLabel').html('Jumlah Botol <span class="text-danger">*</span>');
            $('#qtyUnit').text('pcs');
            $('#qtyInput').attr('placeholder', '0');
            // Stok botol saat ini (bulk_stock_ml ÷ size)
            const botolSisa = size > 0 ? Math.floor(stockBotol / size) : stockBotol;
            $('#stockInfoText').text(
                botolSisa.toLocaleString('id-ID') + ' botol' +
                (size > 0 ? ' (' + stockBotol.toLocaleString('id-ID') + ' ml)' : '')
            );
            $('#qtyHint').text('Masukkan jumlah botol (pcs) yang diterima dari supplier.');
        }

        if (selected.val()) {
            $('#stockInfo').removeClass('d-none');
        } else {
            $('#stockInfo').addClass('d-none');
        }

        updateSummary();
    });

    function updateSummary() {
        const selected = $('select[name=product_id]').find('option:selected');
        const isRefill = selected.data('is-refill') == '1';
        const qty      = parseInt($('#qtyInput').val()) || 0;
        const unit     = isRefill ? 'ml' : 'pcs';
        const cost     = parseFloat($('#unitCost').val()) || 0;
        const total    = qty * cost;
        const received = $('input[name=received_date]').val();
        const expiry   = $('input[name=expiration_date]').val();

        let qtyText = '—';
        if (qty > 0) {
            if (isRefill) {
                qtyText = qty >= 1000
                    ? (qty / 1000).toFixed(2) + ' L (' + qty.toLocaleString('id-ID') + ' ml)'
                    : qty.toLocaleString('id-ID') + ' ml';
            } else {
                qtyText = qty.toLocaleString('id-ID') + ' botol';
            }
        }

        $('#summaryQty').text(qtyText);
        $('#summaryReceived').text(formatDate(received));
        $('#summaryExpiry').html(expiry
            ? '<span class="text-danger font-weight-bold">' + formatDate(expiry) + '</span>'
            : '<span class="text-muted">Tidak ada</span>');
        $('#totalCost').val(total.toLocaleString('id-ID'));
        $('#summaryTotal').text(formatRp(total));
    }

    $('input[name=quantity], #unitCost, input[name=received_date], input[name=expiration_date]')
        .on('input change', updateSummary);

    // Trigger jika ada old value
    if ($('select[name=product_id]').val()) {
        $('select[name=product_id]').trigger('change');
    }
    updateSummary();
});
</script>
@endpush
