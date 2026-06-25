@extends('layouts.app')
@section('title', 'Buat Purchase Order')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-plus-circle"></i> Buat Purchase Order</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
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

    <form method="POST" action="{{ route('purchase-orders.store') }}" id="poForm">
        @csrf
        <div class="card card-apms">
            <div class="card-header">
                <h3 class="card-title">Informasi PO</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $supplierId) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Expected Date</label>
                            <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Catatan</label>
                            <input type="text" name="notes" class="form-control" placeholder="Opsional..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="card card-apms">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Item Pesanan</h3>
                <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" id="itemsTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:35%">Produk</th>
                                <th style="width:15%">Qty</th>
                                <th style="width:20%">Harga Beli/Unit</th>
                                <th style="width:20%">Subtotal</th>
                                <th style="width:10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            {{-- Rows added dynamically --}}
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="3" class="text-right">TOTAL:</td>
                                <td id="grandTotal">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary-apms">
                    <i class="fas fa-save"></i> Simpan PO
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Product options for select --}}
<script>
const PRODUCTS = @json($products);
const SUPPLIER_PRICES = @json($supplierPrices);
</script>
@endsection

@push('scripts')
<script>
$(function() {
    let rowIndex = 0;

    function productOptions() {
        let html = '<option value="">-- Pilih Produk --</option>';
        PRODUCTS.forEach(p => {
            html += `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (${p.size || ''} ${p.unit || ''})</option>`;
        });
        return html;
    }

    function addRow() {
        rowIndex++;
        const html = `
        <tr class="item-row" data-index="${rowIndex}">
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-control form-control-sm product-select" required>
                    ${productOptions()}
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm qty-input" min="1" value="1" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][unit_cost]" class="form-control form-control-sm cost-input" min="0" step="100" value="0" required>
            </td>
            <td class="subtotal-cell">Rp 0</td>
            <td>
                <button type="button" class="btn btn-xs btn-danger remove-row"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
        $('#itemsBody').append(html);
    }

    function calcRow($row) {
        const qty = parseInt($row.find('.qty-input').val()) || 0;
        const cost = parseFloat($row.find('.cost-input').val()) || 0;
        const sub = qty * cost;
        $row.find('.subtotal-cell').text('Rp ' + sub.toLocaleString('id-ID'));
        calcGrandTotal();
    }

    function calcGrandTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const qty = parseInt($(this).find('.qty-input').val()) || 0;
            const cost = parseFloat($(this).find('.cost-input').val()) || 0;
            total += qty * cost;
        });
        $('#grandTotal').text('Rp ' + total.toLocaleString('id-ID'));
    }

    // Events
    $('#addItemBtn').click(addRow);
    $(document).on('click', '.remove-row', function() { $(this).closest('tr').remove(); calcGrandTotal(); });
    $(document).on('input', '.qty-input, .cost-input', function() { calcRow($(this).closest('tr')); });

    // Auto-fill cost from supplier prices on product change
    $(document).on('change', '.product-select', function() {
        const $select = $(this);
        const productId = $select.val();
        const $row = $select.closest('tr');
        if (SUPPLIER_PRICES && SUPPLIER_PRICES[productId]) {
            $row.find('.cost-input').val(SUPPLIER_PRICES[productId]);
        } else {
            const opt = $select.find(':selected');
            $row.find('.cost-input').val(opt.data('price') || 0);
        }
        calcRow($row);
    });

    // Supplier change -> reload prices
    $('#supplier_id').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            window.location.href = '{{ route("purchase-orders.create") }}?supplier_id=' + supplierId;
        }
    });

    // Start with one row
    addRow();
});
</script>
@endpush
