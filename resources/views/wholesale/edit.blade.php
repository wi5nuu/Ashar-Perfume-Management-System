@extends('layouts.app')

@section('title', 'Edit Pesanan Grosir')

@section('content')
<div class="container-fluid">
    <form action="{{ route('wholesale.update', $order->id) }}" method="POST" id="wholesaleForm">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary-apms">
                            <i class="fas fa-edit mr-2"></i> Edit Pesanan: {{ $order->invoice_number }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Volume</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                    @foreach($order->details as $idx => $detail)
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" name="items[{{ $idx }}][product_name]" class="form-control product-name" value="{{ $detail->product_name }}" required>
                                            <input type="hidden" name="items[{{ $idx }}][product_id]" class="product-id" value="{{ $detail->product_id }}">
                                            <input type="hidden" name="items[{{ $idx }}][wholesale_product_id]" class="wholesale-product-id" value="{{ $detail->wholesale_product_id }}">
                                            <input type="hidden" name="items[{{ $idx }}][price_per_ml]" class="price-per-ml-input" value="{{ $detail->price_per_ml }}">
                                        </td>
                                        <td><input type="number" name="items[{{ $idx }}][quantity]" class="form-control qty-input" value="{{ $detail->quantity }}" min="1" required></td>
                                        <td><input type="text" name="items[{{ $idx }}][unit]" class="form-control unit-input" value="{{ $detail->unit ?? 'pcs' }}"></td>
                                        <td><input type="number" name="items[{{ $idx }}][volume_ml]" class="form-control volume-input" value="{{ $detail->volume_ml }}" step="1"></td>
                                        <td><input type="number" name="items[{{ $idx }}][price]" class="form-control price-input" value="{{ $detail->price }}" required step="500"></td>
                                        <td class="subtotal-display font-weight-bold align-middle">Rp {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}</td>
                                        <td>
                                            @if($loop->first)
                                            @else
                                            <button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)"><i class="fas fa-times-circle"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addItemRow()">
                            <i class="fas fa-plus mr-1"></i> Tambah Item
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary-apms"><i class="fas fa-info-circle mr-2"></i> Info Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Target Nilai Paket (Rp) *</label>
                            <input type="number" name="package_target_amount" class="form-control form-control-lg text-primary font-weight-bold" value="{{ $order->package_target_amount }}" required>
                        </div>
                        <div class="form-group">
                            <label>Pelanggan</label>
                            <select name="customer_id" class="form-control select2">
                                <option value="">-- Pilih --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ $order->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Nama Penerima *</label>
                            <input type="text" name="recipient_name" class="form-control" value="{{ $order->recipient_name }}" required>
                        </div>
                        <div class="form-group">
                            <label>No. Telp *</label>
                            <input type="text" name="recipient_phone" class="form-control" value="{{ $order->recipient_phone }}" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat *</label>
                            <textarea name="shipping_address" class="form-control" rows="2" required>{{ $order->shipping_address }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Kurir</label>
                            <select name="shipping_courier" class="form-control">
                                <option value="">-- Pilih --</option>
                                @foreach(['J&T','JNE','Sicepat','Indah Cargo','Pos Indonesia','Gojek','Grab','Lainnya'] as $c)
                                <option value="{{ $c }}" {{ $order->shipping_courier == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Biaya Kirim (Rp)</label>
                            <input type="number" name="shipping_cost" class="form-control" value="{{ $order->shipping_cost }}" min="0">
                        </div>
                        <div class="form-group">
                            <label>Penanggung Jawab</label>
                            <select name="handler_id" class="form-control select2">
                                <option value="">-- Pilih --</option>
                                @foreach($handlers as $h)
                                <option value="{{ $h->id }}" {{ $order->handler_id == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Estimasi Packing (Hari)</label>
                            <input type="number" name="packing_days" class="form-control" value="{{ $order->packing_days ?? 1 }}" min="1">
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="notes" class="form-control" rows="2">{{ $order->notes }}</textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total:</span>
                            <span class="text-dark font-weight-bold" id="grandTotalDisplay">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <button type="submit" class="btn btn-primary-apms btn-block btn-lg shadow-sm mt-3">
                            <i class="fas fa-save mr-2"></i> Update Pesanan
                        </button>
                        <a href="{{ route('wholesale.show', $order->id) }}" class="btn btn-light btn-block mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let rowCount = {{ $order->details->count() }};

function addItemRow() {
    const html = `<tr class="item-row">
        <td>
            <input type="text" name="items[${rowCount}][product_name]" class="form-control product-name" placeholder="Nama barang" required>
            <input type="hidden" name="items[${rowCount}][product_id]" class="product-id">
            <input type="hidden" name="items[${rowCount}][wholesale_product_id]" class="wholesale-product-id">
            <input type="hidden" name="items[${rowCount}][price_per_ml]" class="price-per-ml-input">
        </td>
        <td><input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input" value="1" min="1" required></td>
        <td><input type="text" name="items[${rowCount}][unit]" class="form-control unit-input" placeholder="pcs"></td>
        <td><input type="number" name="items[${rowCount}][volume_ml]" class="form-control volume-input" placeholder="ml"></td>
        <td><input type="number" name="items[${rowCount}][price]" class="form-control price-input" placeholder="Harga" required step="500"></td>
        <td class="subtotal-display font-weight-bold align-middle">Rp 0</td>
        <td><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)"><i class="fas fa-times-circle"></i></button></td>
    </tr>`;
    $('#itemRows').append(html);
    rowCount++;
}

function removeRow(btn) {
    $(btn).closest('tr').remove();
    recalcTotal();
}

$(document).on('input', '.qty-input, .price-input', function() {
    const row = $(this).closest('tr');
    const qty = parseFloat(row.find('.qty-input').val()) || 0;
    const price = parseFloat(row.find('.price-input').val()) || 0;
    row.find('.subtotal-display').text('Rp ' + (qty * price).toLocaleString('id-ID'));
    recalcTotal();
});

function recalcTotal() {
    let total = 0;
    $('.item-row').each(function() {
        const qty = parseFloat($(this).find('.qty-input').val()) || 0;
        const price = parseFloat($(this).find('.price-input').val()) || 0;
        total += qty * price;
    });
    $('#grandTotalDisplay').text('Rp ' + total.toLocaleString('id-ID'));
}

$('document').ready(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    recalcTotal();
});
</script>
@endpush
@endsection
