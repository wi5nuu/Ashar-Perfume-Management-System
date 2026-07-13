@extends('layouts.app')

@section('title', 'Buat Pesanan Grosir')

@section('content')
<div class="container-fluid">
    <form action="{{ route('wholesale.store') }}" method="POST" id="wholesaleForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                {{-- Product Tabs --}}
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary-apms">
                            <i class="fas fa-boxes mr-2"></i> Pilih Produk
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="parfum-tab" data-toggle="tab" href="#parfum" role="tab">
                                    <i class="fas fa-spray-can mr-1"></i> Parfum / Aroma
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="produk-tab" data-toggle="tab" href="#produk" role="tab">
                                    <i class="fas fa-box mr-1"></i> Produk Grosir
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content p-3">
                            {{-- TAB 1: Parfum / Aroma --}}
                            <div class="tab-pane fade show active" id="parfum" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="searchParfum" placeholder="Cari parfum / aroma..." onkeyup="filterParfum(this.value)">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="parfumGrid">
                                    @forelse($products as $p)
                                    <div class="col-6 col-md-4 col-lg-3 mb-2 parfum-item" data-name="{{ strtolower($p->name) }}">
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block text-left product-btn-parfum"
                                            data-pid="{{ $p->id }}"
                                            data-name="{{ $p->name }}"
                                            data-price="{{ $p->wholesale_price ?: $p->selling_price }}"
                                            data-size="{{ $p->size }}">
                                            <strong>{{ $p->name }}</strong><br>
                                            <small>Rp {{ number_format($p->wholesale_price ?: $p->selling_price, 0, ',', '.') }}
                                            @if($p->size) | {{ $p->size }}ml @endif
                                            </small>
                                        </button>
                                    </div>
                                    @empty
                                    <div class="col-12 text-muted py-3">Tidak ada produk parfum.</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- TAB 2: Produk Grosir --}}
                            <div class="tab-pane fade" id="produk" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="btn-group btn-group-sm flex-wrap" role="group">
                                            <button type="button" class="btn btn-outline-primary active" onclick="filterProdukType('all', this)">Semua</button>
                                            @foreach(['botol', 'sarung', 'methanol', 'aksesoris', 'lainnya'] as $t)
                                            <button type="button" class="btn btn-outline-primary" onclick="filterProdukType('{{ $t }}', this)">{{ ucfirst($t) }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="produkGrid">
                                    @forelse($wholesaleProducts as $wp)
                                    <div class="col-6 col-md-4 col-lg-3 mb-2 produk-item" data-type="{{ $wp->type }}">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-block text-left product-btn-wp"
                                            data-wpid="{{ $wp->id }}"
                                            data-name="{{ $wp->name }}"
                                            data-price="{{ $wp->price_per_unit }}"
                                            data-unit="{{ $wp->unit }}"
                                            data-price-per-ml="{{ $wp->price_per_ml }}"
                                            data-pieces="{{ $wp->pieces_per_unit }}"
                                            data-price-per-piece="{{ $wp->price_per_piece }}">
                                            <strong>{{ $wp->name }}</strong><br>
                                            <small class="text-muted">
                                                @if($wp->pieces_per_unit > 1)
                                                    Rp {{ number_format($wp->price_per_piece, 0, ',', '.') }}/buah<br>
                                                    <span class="text-info">1 {{ $wp->unit }} ({{ $wp->pieces_per_unit }} buah) = Rp {{ number_format($wp->price_per_unit, 0, ',', '.') }}</span>
                                                @else
                                                    Rp {{ number_format($wp->price_per_unit, 0, ',', '.') }}/{{ $wp->unit }}
                                                @endif
                                                @if($wp->price_per_ml) | {{ number_format($wp->price_per_ml, 0) }}/ml @endif
                                            </small>
                                        </button>
                                    </div>
                                    @empty
                                    <div class="col-12 text-muted py-3">Belum ada produk grosir. <a href="{{ route('wholesale.products.index') }}">Tambah produk</a></div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Items Table --}}
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-list mr-1"></i> Item Pesanan</h6>
                        <span class="text-muted small" id="itemCount">0 item</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-sm" id="itemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th style="width:30%">Nama Barang</th>
                                        <th style="width:12%">Harga Satuan</th>
                                        <th style="width:10%">Jumlah</th>
                                        <th style="width:10%">Volume</th>
                                        <th style="width:8%">Satuan</th>
                                        <th style="width:15%">Subtotal</th>
                                        <th style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                    <tr class="item-row">
                                        <td class="row-num align-middle font-weight-bold text-muted">1</td>
                                        <td>
                                            <input type="text" name="items[0][product_name]" class="form-control form-control-sm product-name" placeholder="Pilih produk dari katalog di atas" readonly required>
                                            <input type="hidden" name="items[0][product_id]" class="product-id">
                                            <input type="hidden" name="items[0][wholesale_product_id]" class="wholesale-product-id">
                                            <input type="hidden" name="items[0][price_per_ml]" class="price-per-ml-input">
                                        </td>
                                        <td><input type="number" name="items[0][price]" class="form-control form-control-sm price-input" value="0" required step="500" readonly></td>
                                        <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" required></td>
                                        <td><input type="number" name="items[0][volume_ml]" class="form-control form-control-sm volume-input" placeholder="ml" step="1"></td>
                                        <td><input type="text" name="items[0][unit]" class="form-control form-control-sm unit-input" placeholder="pcs" readonly></td>
                                        <td class="subtotal-display font-weight-bold align-middle text-primary">Rp 0</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="addItemRow()">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris Kosong
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Panel --}}
            <div class="col-lg-4">
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-primary-apms"><i class="fas fa-info-circle mr-2"></i> Info Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Target Nilai Paket (Rp) *</label>
                            <input type="number" name="package_target_amount" class="form-control form-control-lg text-primary font-weight-bold" placeholder="Contoh: 10000000" required>
                        </div>
                        <div class="form-group">
                            <label>Pelanggan</label>
                            <select name="customer_id" class="form-control select2">
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" data-phone="{{ $c->phone }}">{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Nama Penerima *</label>
                            <input type="text" name="recipient_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>No. Telp Penerima *</label>
                            <input type="text" name="recipient_phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Referral (jika ada)</label>
                            <input type="text" name="referral_code" class="form-control" placeholder="Contoh: ABC12345" maxlength="20">
                            <small class="form-text text-muted">Kode referral dari pelanggan yang mereferensikan.</small>
                        </div>
                        <div class="form-group">
                            <label>Alamat Lengkap *</label>
                            <textarea name="shipping_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Kurir</label>
                            <select name="shipping_courier" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="J&T">J&T</option>
                                <option value="JNE">JNE</option>
                                <option value="Sicepat">Sicepat</option>
                                <option value="Indah Cargo">Indah Cargo</option>
                                <option value="Pos Indonesia">Pos Indonesia</option>
                                <option value="Gojek">Gojek</option>
                                <option value="Grab">Grab</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Biaya Kirim (Rp)</label>
                            <input type="number" name="shipping_cost" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Penanggung Jawab</label>
                            <select name="handler_id" class="form-control select2">
                                <option value="">-- Pilih --</option>
                                @foreach($handlers as $h)
                                <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Estimasi Packing (Hari)</label>
                            <input type="number" name="packing_days" class="form-control" value="1" min="1">
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan internal / instruksi khusus"></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Total Pesanan:</span>
                            <span class="font-weight-bold" id="grandTotalDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Kirim:</span>
                            <span id="shippingCostDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted small">
                            <span>Target:</span>
                            <span id="targetDisplay">Rp 0</span>
                        </div>
                        <button type="submit" class="btn btn-primary-apms btn-block btn-lg shadow-sm">
                            <i class="fas fa-save mr-2"></i> Simpan Pesanan Grosir
                        </button>
                        <a href="{{ route('wholesale.index') }}" class="btn btn-light btn-block mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')i 
<script>
let rowCount = 1;

// ── TAB 1: Filter Parfum ──
function filterParfum(val) {
    const q = val.toLowerCase().trim();
    document.querySelectorAll('.parfum-item').forEach(el => {
        el.style.display = (!q || el.dataset.name.includes(q)) ? '' : 'none';
    });
}

// ── TAB 2: Filter Produk Grosir ──
function filterProdukType(type, btn) {
    document.querySelector('#produkGrid').querySelectorAll('.btn.active').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    const items = document.querySelectorAll('.produk-item');
    if (type === 'all') { items.forEach(el => el.style.display = ''); return; }
    items.forEach(el => { el.style.display = el.dataset.type === type ? '' : 'none'; });
}

// ── Click Parfum (retail product) → add to table ──
$(document).on('click', '.product-btn-parfum', function() {
    const btn = $(this);
    const row = findOrCreateRow();
    row.find('.product-name').val(btn.data('name'));
    row.find('.product-id').val(btn.data('pid'));
    row.find('.wholesale-product-id').val('');
    row.find('.price-input').val(btn.data('price'));
    row.find('.unit-input').val('pcs');
    row.find('.price-per-ml-input').val('');
    if (btn.data('size')) row.find('.volume-input').val(btn.data('size'));
    updateRowNumbers();
    recalcAll();
    highlightRow(row); 
});

w
// ── Click Produk Grosir → add to table ──
$(document).on('click', '.product-btn-wp', function() {
    const btn = $(this);
    const row = findOrCreateRow();
    row.find('.product-name').val(btn.data('name'));
    row.find('.wholesale-product-id').val(btn.data('wpid'));
    row.find('.product-id').val('');
    row.find('.unit-input').val(btn.data('unit'));
    row.find('.price-per-ml-input').val(btn.data('price-per-ml') || '');
    row.find('.volume-input').val('');

    // Show price per piece prominently for multi-piece products
    const pieces = parseInt(btn.data('pieces')) || 1;
    const pricePerPiece = parseFloat(btn.data('price-per-piece')) || 0;
    const pricePerUnit = parseFloat(btn.data('price')) || 0;

    if (pieces > 1) {
        // For sarung, aksesoris — show price per piece, qty = pieces
        row.find('.price-input').val(pricePerPiece);
        row.find('.qty-input').val(pieces);
        row.find('.price-input').prop('readonly', true);
    } else {
        // Regular items
        row.find('.price-input').val(pricePerUnit);
        row.find('.qty-input').val(1);
        row.find('.price-input').prop('readonly', false);
    }

    updateRowNumbers();
    recalcAll();
    highlightRow(row);
});

function findOrCreateRow() {
    let empty = null;
    $('.item-row').each(function() {
        if (!$(this).find('.product-name').val()) { empty = $(this); return false; }
    });
    if (empty) return empty;
    addItemRow();
    return $('.item-row').last();
}

function addItemRow() {
    const html = `<tr class="item-row">
        <td class="row-num align-middle font-weight-bold text-muted">${rowCount + 1}</td>
        <td>
            <input type="text" name="items[${rowCount}][product_name]" class="form-control form-control-sm product-name" placeholder="Pilih produk dari katalog" readonly required>
            <input type="hidden" name="items[${rowCount}][product_id]" class="product-id">
            <input type="hidden" name="items[${rowCount}][wholesale_product_id]" class="wholesale-product-id">
            <input type="hidden" name="items[${rowCount}][price_per_ml]" class="price-per-ml-input">
        </td>
        <td><input type="number" name="items[${rowCount}][price]" class="form-control form-control-sm price-input" value="0" required step="500" readonly></td>
        <td><input type="number" name="items[${rowCount}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" required></td>
        <td><input type="number" name="items[${rowCount}][volume_ml]" class="form-control form-control-sm volume-input" placeholder="ml" step="1"></td>
        <td><input type="text" name="items[${rowCount}][unit]" class="form-control form-control-sm unit-input" placeholder="pcs" readonly></td>
        <td class="subtotal-display font-weight-bold align-middle text-primary">Rp 0</td>
        <td><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)"><i class="fas fa-times-circle"></i></button></td>
    </tr>`;
    $('#itemRows').append(html);
    rowCount++;
    updateRowNumbers();
}

function removeRow(btn) {
    $(btn).closest('tr').remove();
    updateRowNumbers();
    recalcAll();
}

function updateRowNumbers() {
    $('.item-row').each(function(i) {
        $(this).find('.row-num').text(i + 1);
    });
    const count = $('.item-row').length;
    $('#itemCount').text(count + ' item');
}

function highlightRow(row) {
    row.css('background', '#fff3cd');
    setTimeout(() => row.css('background', ''), 1200);
}

$(document).on('input', '.qty-input, .price-input', function() {
    recalcAll();
});

$(document).on('input', 'input[name="shipping_cost"]', recalcAll);
$(document).on('input', 'input[name="package_target_amount"]', updateTarget);

function recalcRow(row) {
    const qty = parseFloat(row.find('.qty-input').val()) || 0;
    const price = parseFloat(row.find('.price-input').val()) || 0;
    row.find('.subtotal-display').text('Rp ' + (qty * price).toLocaleString('id-ID'));
}

function recalcAll() {
    let total = 0;
    let itemCount = 0;
    $('.item-row').each(function() {
        const qty = parseFloat($(this).find('.qty-input').val()) || 0;
        const price = parseFloat($(this).find('.price-input').val()) || 0;
        const sub = qty * price;
        total += sub;
        if ($(this).find('.product-name').val()) itemCount++;
        $(this).find('.subtotal-display').text('Rp ' + sub.toLocaleString('id-ID'));
    });
    $('#grandTotalDisplay').text('Rp ' + total.toLocaleString('id-ID'));
    $('#itemCount').text(itemCount + ' item');
    updateTarget();
}

function updateTarget() {
    const target = parseFloat($('input[name="package_target_amount"]').val()) || 0;
    $('#targetDisplay').text('Rp ' + target.toLocaleString('id-ID'));
    const shipping = parseFloat($('input[name="shipping_cost"]').val()) || 0;
    $('#shippingCostDisplay').text('Rp ' + shipping.toLocaleString('id-ID'));
}

$('document').ready(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    $('select[name="customer_id"]').change(function() {
        const opt = $(this).find('option:selected');
        const phone = opt.data('phone');
        const name = opt.text().split(' (')[0];
        if (name && !$('input[name="recipient_name"]').val()) $('input[name="recipient_name"]').val(name);
        if (phone && !$('input[name="recipient_phone"]').val()) $('input[name="recipient_phone"]').val(phone);
    });
    updateRowNumbers();
    recalcAll();
});
</script>
@endpush
@endsection
