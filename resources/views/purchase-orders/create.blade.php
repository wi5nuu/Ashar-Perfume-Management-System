@extends('layouts.app')
@section('title', 'Buat Purchase Order')

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4166 100%);
    padding: 1.5rem 1.75rem; border-radius: 12px; margin-bottom: 1.5rem; color: #fff;
}
.page-header-apms h1 { font-size: 1.6rem; font-weight: 700; margin: 0; }
.page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; }
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.7); }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item+.breadcrumb-item::before { color: rgba(255,255,255,.4); }
.card-apms { border:none; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.07); margin-bottom:1.5rem; }
.card-apms .card-header { background:#fff; border-bottom:2px solid #f0f0f0; padding:1rem 1.5rem; border-radius:12px 12px 0 0; display:flex; align-items:center; gap:.6rem; }
.card-apms .card-header .section-icon { width:32px; height:32px; border-radius:8px; background:rgba(255,107,53,.1); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:.85rem; }
.card-apms .card-header h3 { margin:0; font-size:.95rem; font-weight:700; color:var(--secondary); }
.card-apms .card-body { padding:1.5rem; }
.form-label-apms { font-size:.8rem; font-weight:600; color:#495057; text-transform:uppercase; letter-spacing:.4px; margin-bottom:.4rem; display:block; }
.form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.15); }
.form-control { border-radius:8px; border-color:#dee2e6; }
.input-group .input-group-text { border-radius:8px 0 0 8px; background:#f8f9fa; border-color:#dee2e6; }
.input-group .form-control { border-radius:0 8px 8px 0; }
.btn-primary-apms { background:var(--primary); border-color:var(--primary); color:#fff; border-radius:8px; font-weight:600; font-size:.9rem; padding:.55rem 1.5rem; transition:background .2s,box-shadow .2s; }
.btn-primary-apms:hover { background:var(--primary-dark); border-color:var(--primary-dark); color:#fff; box-shadow:0 4px 12px rgba(255,107,53,.3); }
.required-mark { color:var(--primary); }
/* Product Table */
.product-table { border-radius:10px; overflow:hidden; border:1px solid #e9ecef; }
.product-table thead th { background:#f8f9fa; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; border-bottom:2px solid #e9ecef; padding:.75rem .9rem; white-space:nowrap; }
.product-table tbody td { padding:.65rem .9rem; vertical-align:middle; border-color:#f5f5f5; font-size:.88rem; }
.product-table tbody tr:hover { background:#fffaf8; }
.product-table tfoot td { padding:.75rem .9rem; background:#f8f9fa; font-weight:600; font-size:.88rem; border-top:2px solid #e9ecef; }
.btn-add-row { border:2px dashed #dee2e6; color:#6c757d; background:#fff; border-radius:8px; padding:.5rem 1rem; font-size:.85rem; transition:all .2s; width:100%; margin-top:.75rem; }
.btn-add-row:hover { border-color:var(--primary); color:var(--primary); background:rgba(255,107,53,.03); }
.btn-remove-row { color:#dc3545; background:transparent; border:none; padding:.2rem .4rem; border-radius:4px; font-size:.85rem; transition:background .2s; }
.btn-remove-row:hover { background:rgba(220,53,69,.1); }
/* Summary Box */
.summary-box { background:linear-gradient(135deg,#fff8f5,#fff); border:1px solid rgba(255,107,53,.15); border-radius:12px; padding:1.25rem 1.5rem; }
.summary-row { display:flex; justify-content:space-between; align-items:center; padding:.35rem 0; font-size:.9rem; border-bottom:1px solid #f5f5f5; }
.summary-row:last-child { border-bottom:none; }
.summary-row.total { font-size:1.1rem; font-weight:700; color:var(--secondary); margin-top:.5rem; padding-top:.75rem; border-top:2px solid #e9ecef; border-bottom:none; }
.summary-row.total .val { color:var(--primary); font-size:1.25rem; }
.po-number-display { font-family:monospace; background:#f8f9fa; padding:.4rem .8rem; border-radius:6px; font-size:.9rem; color:var(--secondary); font-weight:700; border:1px solid #e9ecef; }
</style>

@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-plus-circle mr-2"></i>Buat Purchase Order</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Order</a></li>
                        <li class="breadcrumb-item active">Buat Baru</li>
                    </ol>
                </div>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(220,53,69,.2);">
        <i class="fas fa-exclamation-triangle mr-2"></i><strong>Perbaiki kesalahan berikut:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <form method="POST" action="{{ route('purchase-orders.store') }}" id="poForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">

                {{-- Header PO --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-file-alt"></i></div>
                        <h3>Informasi Purchase Order</h3>
                        <div class="ml-auto">
                            <span class="po-number-display" id="poNumberDisplay">PO-AUTO</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Supplier <span class="required-mark">*</span></label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ old('supplier_id', $supplierId) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label-apms">Tanggal PO</label>
                                    <input type="date" name="po_date" class="form-control" value="{{ old('po_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label-apms">Expected Date</label>
                                    <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Gudang Tujuan</label>
                                    <select name="warehouse_id" class="form-control @error('warehouse_id') is-invalid @enderror">
                                        <option value="">-- Pilih Gudang --</option>
                                        @foreach($warehouses ?? [] as $wh)
                                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Catatan</label>
                                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Catatan untuk supplier...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Produk --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-boxes"></i></div>
                        <h3>Daftar Produk</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="product-table table mb-0" id="productTable">
                                <thead>
                                    <tr>
                                        <th style="min-width:220px;">Produk</th>
                                        <th style="width:100px;">SKU</th>
                                        <th style="width:90px;">Qty</th>
                                        <th style="width:130px;">Harga Beli</th>
                                        <th style="width:100px;">Diskon (%)</th>
                                        <th style="width:130px;">Subtotal</th>
                                        <th style="width:40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="productRows"></tbody>
                                <tfoot>
                                    <tr id="emptyRow" style="display:none;">
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x d-block mb-2"></i>
                                            Belum ada produk. Klik tombol di bawah untuk menambah.
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn-add-row" id="addRowBtn">
                            <i class="fas fa-plus mr-2"></i> Tambah Produk
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                {{-- Summary --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-calculator"></i></div>
                        <h3>Ringkasan PO</h3>
                    </div>
                    <div class="card-body">
                        <div class="summary-box">
                            <div class="summary-row">
                                <span class="text-muted">Subtotal</span>
                                <span class="font-weight-600" id="summarySubtotal">Rp 0</span>
                            </div>
                            <div class="summary-row">
                                <span class="text-muted">Total Diskon</span>
                                <span class="text-success font-weight-600" id="summaryDiscount">- Rp 0</span>
                            </div>
                            <div class="summary-row">
                                <span class="text-muted">PPN (11%)</span>
                                <span class="font-weight-600" id="summaryTax">Rp 0</span>
                            </div>
                            <div class="summary-row total">
                                <span>TOTAL</span>
                                <span class="val" id="summaryTotal">Rp 0</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="include_tax" id="includeTax" class="form-check-input" value="1" {{ old('include_tax', 1) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="includeTax">Sertakan PPN 11%</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card card-apms">
                    <div class="card-body">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-block mb-2" style="border-radius:8px; font-weight:600;">
                            <i class="fas fa-save mr-1"></i> Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary-apms btn-block">
                            <i class="fas fa-paper-plane mr-1"></i> Submit PO ke Supplier
                        </button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-link btn-block text-muted mt-1" style="font-size:.85rem;">
                            Batal
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
    const SUPPLIER_PRICES = @json($supplierPrices ?? []);
    const PRODUCTS = @json($products ?? []);
    let rowIndex = 0;

    // Select2
    if ($.fn.select2) {
        $('#supplier_id').select2({ placeholder: '-- Pilih Supplier --', width: '100%' });
    }

    function formatRp(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function calcRow($row) {
        const qty    = parseFloat($row.find('.qty-input').val())   || 0;
        const cost   = parseFloat($row.find('.cost-input').val())  || 0;
        const disc   = parseFloat($row.find('.disc-input').val())  || 0;
        const sub    = qty * cost * (1 - disc / 100);
        $row.find('.subtotal-display').text(formatRp(sub));
        $row.find('.subtotal-input').val(sub.toFixed(2));
        calcSummary();
    }

    function calcSummary() {
        let subtotal = 0, totalDisc = 0;
        $('#productRows tr').each(function() {
            const qty  = parseFloat($(this).find('.qty-input').val())  || 0;
            const cost = parseFloat($(this).find('.cost-input').val()) || 0;
            const disc = parseFloat($(this).find('.disc-input').val()) || 0;
            subtotal  += qty * cost;
            totalDisc += qty * cost * (disc / 100);
        });
        const afterDisc = subtotal - totalDisc;
        const includeTax = $('#includeTax').is(':checked');
        const tax  = includeTax ? afterDisc * 0.11 : 0;
        const total = afterDisc + tax;

        $('#summarySubtotal').text(formatRp(subtotal));
        $('#summaryDiscount').text('- ' + formatRp(totalDisc));
        $('#summaryTax').text(formatRp(tax));
        $('#summaryTotal').text(formatRp(total));
    }

    function buildProductOptions(selectedId) {
        let opts = '<option value="">-- Pilih Produk --</option>';
        PRODUCTS.forEach(p => {
            const sel = p.id == selectedId ? 'selected' : '';
            opts += `<option value="${p.id}" data-price="${p.cost_price || 0}" data-sku="${p.sku || ''}" ${sel}>${p.name}</option>`;
        });
        return opts;
    }

    function addRow(data) {
        data = data || {};
        const i = rowIndex++;
        const $tr = $(`
            <tr data-index="${i}">
                <td>
                    <select name="items[${i}][product_id]" class="form-control form-control-sm product-select" style="border-radius:6px;" required>
                        ${buildProductOptions(data.product_id || '')}
                    </select>
                </td>
                <td><span class="sku-display text-muted small" style="font-family:monospace;">${data.sku || '-'}</span></td>
                <td><input type="number" name="items[${i}][quantity]" class="form-control form-control-sm qty-input" style="border-radius:6px;" value="${data.qty || 1}" min="1" required></td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text" style="border-radius:6px 0 0 6px; font-size:.75rem;">Rp</span></div>
                        <input type="number" name="items[${i}][cost_price]" class="form-control cost-input" style="border-radius:0 6px 6px 0;" value="${data.cost || 0}" min="0" step="100" required>
                    </div>
                </td>
                <td><input type="number" name="items[${i}][discount]" class="form-control form-control-sm disc-input" style="border-radius:6px;" value="${data.disc || 0}" min="0" max="100" step="0.1"></td>
                <td><span class="subtotal-display font-weight-600" style="color:var(--secondary);">Rp 0</span><input type="hidden" name="items[${i}][subtotal]" class="subtotal-input" value="0"></td>
                <td><button type="button" class="btn-remove-row" title="Hapus"><i class="fas fa-times"></i></button></td>
            </tr>
        `);

        if ($.fn.select2) {
            $tr.find('.product-select').select2({ placeholder: '-- Pilih Produk --', width: '100%' });
        }

        $('#productRows').append($tr);
        calcRow($tr);
        $('#emptyRow').hide();
    }

    $('#addRowBtn').on('click', () => addRow());

    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        calcSummary();
        if ($('#productRows tr').length === 0) $('#emptyRow').show();
    });

    $(document).on('input', '.qty-input, .cost-input, .disc-input', function() {
        calcRow($(this).closest('tr'));
    });

    $(document).on('change', '.product-select', function() {
        const $row = $(this).closest('tr');
        const productId = $(this).val();
        const opt = $(this).find(':selected');
        const sku = opt.data('sku') || '-';
        $row.find('.sku-display').text(sku);
        if (SUPPLIER_PRICES && SUPPLIER_PRICES[productId]) {
            $row.find('.cost-input').val(SUPPLIER_PRICES[productId]);
        } else {
            $row.find('.cost-input').val(opt.data('price') || 0);
        }
        calcRow($row);
    });

    $('#includeTax').on('change', calcSummary);

    // Auto-generate PO number display
    const today = new Date();
    const pad = n => String(n).padStart(2,'0');
    const poNum = `PO-${today.getFullYear()}${pad(today.getMonth()+1)}${pad(today.getDate())}-AUTO`;
    $('#poNumberDisplay').text(poNum);

    // Supplier change -> reload prices
    $('#supplier_id').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            window.location.href = '{{ route("purchase-orders.create") }}?supplier_id=' + supplierId;
        }
    });

    // Start with one row
    addRow();

    // Confirm submit
    $('button[name=action][value=submit]').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        Swal.fire({
            title: 'Submit Purchase Order?',
            text: 'PO akan dikirim ke supplier dan tidak dapat diubah lagi.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Submit!',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $('<input type="hidden" name="action" value="submit">').appendTo('#poForm');
                $('#poForm').submit();
            }
        });
    });
});
</script>
@endpush
