@extends('layouts.app')

@section('title', 'Kasir Grosir - APMS')

@push('styles')
<style>
.product-card { cursor: pointer; transition: box-shadow .15s, transform .15s; border-radius: 8px; }
.product-card:hover:not(.disabled-card) { box-shadow: 0 4px 16px rgba(255,107,53,.25); transform: translateY(-2px); }
.product-card.disabled-card { opacity: .5; cursor: not-allowed; }
.cart-table th, .cart-table td { font-size: .82rem; vertical-align: middle; }
.stock-ok    { color: #2e7d32; font-weight: 600; }
.stock-low   { color: #e65100; font-weight: 600; }
.stock-empty { color: #c62828; font-weight: 600; }
.badge-grosir { background:#1565c0; color:#fff; font-size:.72rem; padding:2px 7px; border-radius:10px; }

/* Pembatas tinggi grid produk agar tidak scroll jauh */
.product-grid-container {
    max-height: calc(100vh - 280px);
    overflow-y: auto;
    overflow-x: hidden;
}

/* Pastikan footer selalu di bawah */
html, body { height: 100%; margin: 0; }
.wrapper { min-height: 100vh; display: flex; flex-direction: column; }
.content-wrapper { flex: 1; }

/* Modal center vertical */
#addProductModal .modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100vh - 60px);
    margin: 30px auto;
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Page header --}}
    <div class="d-flex align-items-center mb-3" style="gap:10px;">
        <h4 class="mb-0">Kasir</h4>
        <span class="badge-grosir"><i class="fas fa-truck mr-1"></i>GROSIR</span>
        <a href="{{ route('wholesale.index') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="fas fa-list mr-1"></i> Riwayat Pesanan
        </a>
    </div>

    <form method="POST" action="{{ route('wholesale.store') }}" id="wholesaleForm">
        @csrf
        {{-- hidden cart items injected by JS --}}
        <div id="hiddenCartInputs"></div>

    <div class="row">

        {{-- ═══════════════════════════════════════════════════════
             LEFT: Product Grid
        ═══════════════════════════════════════════════════════ --}}
        <div class="col-md-8 col-12">

            {{-- Search + Category filter --}}
            <div class="card card-apms mb-3">
                <div class="card-header d-flex align-items-center flex-wrap" style="gap:8px;">
                    <h3 class="card-title mb-0 text-nowrap">Daftar Produk</h3>
                    <div class="d-flex flex-nowrap overflow-auto" style="gap:4px; flex:1; min-width:0;">
                        <button type="button" class="btn btn-sm btn-secondary active px-2 py-1" id="showAllProducts"
                                style="white-space:nowrap;font-size:.75rem;flex-shrink:0;">Semua</button>
                        @foreach($categories as $cat)
                        @php
                            $defColor = match($cat->tier ?? 'biasa') {
                                'premium' => '#FFB300', 'sedang' => '#78909C', default => '#66BB6A'
                            };
                            $catColor = ($cat->color && preg_match('/^#[0-9a-fA-F]{6}$/', $cat->color))
                                ? $cat->color : $defColor;
                        @endphp
                        <button type="button" class="btn btn-sm btn-category px-2 py-1"
                                data-category="{{ $cat->id }}"
                                style="background:{{ $catColor }};color:#fff;white-space:nowrap;font-size:.75rem;flex-shrink:0;border:none;">
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                    <div class="input-group input-group-sm ml-1" style="max-width:180px;flex-shrink:0;">
                        <input type="text" id="productSearch" class="form-control" placeholder="Cari aroma...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Tabs: Parfum & Aksesori --}}
            <ul class="nav nav-tabs mb-2" id="productTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-parfum" data-toggle="tab" href="#tabParfum" role="tab">
                        <i class="fas fa-wine-bottle mr-1"></i> Parfum
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-aksesori" data-toggle="tab" href="#tabAksesori" role="tab">
                        <i class="fas fa-box mr-1"></i> Aksesori
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="productTabContent">
                {{-- Tab Parfum (botol, is_refill=false) --}}
                <div class="tab-pane fade show active" id="tabParfum" role="tabpanel">
                    <div class="product-grid-container">
                        <div class="row product-grid" data-type="parfum">
                        @forelse($products->where('is_refill', false) as $product)
                        @php
                            // Ambil stok dari inventories.current_stock (ml bibit), bukan initial_stock
                            $inv = $product->inventories->where('branch_id', null)->first();
                            $stock = $inv ? $inv->current_stock : 0; // dalam ml
                            $stockLabel = $stock >= 1000 
                                ? number_format($stock/1000, 1).' L' 
                                : $stock.' ml';
                            $disabled = $stock <= 0;
                            $tier = $product->category?->tier ?? 'biasa';
                            $borderClass = match($tier) {
                                'premium' => 'border-warning', 'sedang' => 'border-secondary', default => ''
                            };
                        @endphp
                        <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2 product-item"
                             data-id="{{ $product->id }}"
                             data-name="{{ $product->name }}"
                             data-stock="{{ $stock }}"
                             data-type="product"
                             data-category="{{ $product->product_category_id }}">
                            <div class="card product-card {{ $borderClass }} {{ $disabled ? 'disabled-card' : '' }} h-100"
                                 onclick="{{ !$disabled ? 'openAddModal('.$product->id.',\'product\')' : 'void(0)' }}">
                                <div class="card-body p-0 d-flex flex-column justify-content-between" style="min-height:85px;">
                                    <div class="flex-grow-1 d-flex align-items-center justify-content-center py-2 px-1">
                                        <h6 class="mb-0 text-center" style="font-size:.78rem;">{{ $product->name }}</h6>
                                    </div>
                                    <div class="px-1 pb-1 text-center">
                                        @if($disabled)
                                            <span class="badge badge-danger" style="font-size:.65rem;">Habis</span>
                                        @else
                                            <span class="badge badge-info" style="font-size:.65rem;">{{ $stockLabel }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-wine-bottle fa-2x mb-2"></i>
                            <p>Belum ada produk parfum.</p>
                        </div>
                        @endforelse
                    </div>
                    </div>
                </div>

                {{-- Tab Aksesori --}}
                <div class="tab-pane fade" id="tabAksesori" role="tabpanel">
                    <div class="product-grid-container">
                        <div class="row product-grid" data-type="aksesori">
                        @forelse($accessories as $acc)
                        @php
                            $stock = $acc->current_stock ?? 0;
                            $stockLabel = $stock.' '.$acc->unit;
                            $disabled = $stock <= 0;
                        @endphp
                        <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2 product-item"
                             data-id="{{ $acc->id }}"
                             data-name="{{ $acc->name }}"
                             data-stock="{{ $stock }}"
                             data-type="accessory"
                             data-category="">
                            <div class="card product-card {{ $disabled ? 'disabled-card' : '' }} h-100"
                                 onclick="{{ !$disabled ? 'openAddModal('.$acc->id.',\'accessory\')' : 'void(0)' }}">
                                <div class="card-body p-0 d-flex flex-column justify-content-between" style="min-height:85px;">
                                    <div class="flex-grow-1 d-flex align-items-center justify-content-center py-2 px-1">
                                        <h6 class="mb-0 text-center" style="font-size:.78rem;">{{ $acc->name }}</h6>
                                    </div>
                                    <div class="px-1 pb-1 text-center">
                                        @if($disabled)
                                            <span class="badge badge-danger" style="font-size:.65rem;">Habis</span>
                                        @else
                                            <span class="badge badge-info" style="font-size:.65rem;">{{ $stockLabel }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-box fa-2x mb-2"></i>
                            <p>Belum ada aksesori.</p>
                        </div>
                        @endforelse
                    </div>
                    </div>
                </div>
            </div>

        </div>{{-- end left col --}}

        {{-- ═══════════════════════════════════════════════════════
             RIGHT: Cart + Shipping Form
        ═══════════════════════════════════════════════════════ --}}
        <div class="col-md-4 col-12">

            {{-- Cart --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0" style="font-size:.85rem;"><i class="fas fa-shopping-cart mr-1"></i>Keranjang</h3>
                    <span class="badge badge-primary" id="cartCount">0</span>
                </div>
                <div class="card-body p-2">
                    <div id="cartEmpty" class="text-center text-muted py-3">
                        <i class="fas fa-shopping-cart fa-2x mb-2 d-block"></i>
                        <small>Belum ada produk</small>
                    </div>
                    <div id="cartTableWrap" style="display:none;">
                        <table class="table table-sm table-bordered cart-table mb-1">
                            <thead class="thead-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Vol</th>
                                    <th>Harga/ml</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-2 px-1">
                        <strong style="font-size:.82rem;">Total Produk:</strong>
                        <strong id="cartTotal" style="font-size:.82rem;">Rp 0</strong>
                    </div>
                </div>
            </div>

            {{-- Shipping Info --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0" style="font-size:.85rem;"><i class="fas fa-truck mr-1"></i>Informasi Pengiriman</h3>
                </div>
                <div class="card-body p-2">
                    @if($errors->any())
                    <div class="alert alert-danger alert-sm p-2 mb-2" style="font-size:.78rem;">
                        <ul class="mb-0 pl-3">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="form-group mb-2">
                        <label class="mb-0" style="font-size:.75rem;font-weight:600;">Nama Penerima *</label>
                        <input type="text" name="recipient_name" class="form-control form-control-sm"
                               value="{{ old('recipient_name') }}" required placeholder="Nama lengkap penerima">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0" style="font-size:.75rem;font-weight:600;">No. HP Penerima *</label>
                        <input type="text" name="recipient_phone" class="form-control form-control-sm"
                               value="{{ old('recipient_phone') }}" required placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0" style="font-size:.75rem;font-weight:600;">Alamat Pengiriman *</label>
                        <textarea name="shipping_address" class="form-control form-control-sm" rows="2"
                                  required placeholder="Alamat lengkap...">{{ old('shipping_address') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size:.75rem;font-weight:600;">Kurir</label>
                                <input type="text" name="shipping_courier" class="form-control form-control-sm"
                                       value="{{ old('shipping_courier') }}" placeholder="JNE / JNT / dll">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size:.75rem;font-weight:600;">Ongkos Kirim</label>
                                <input type="number" name="shipping_cost" class="form-control form-control-sm"
                                       value="{{ old('shipping_cost', 0) }}" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0" style="font-size:.75rem;font-weight:600;">Target Jumlah Paket</label>
                        <input type="number" name="package_target_amount" class="form-control form-control-sm"
                               value="{{ old('package_target_amount') }}" min="1" placeholder="Jumlah paket">
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-0" style="font-size:.75rem;font-weight:600;">Catatan</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="1"
                                  placeholder="Catatan pesanan...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Summary & Submit --}}
            <div class="card card-apms mb-3">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Total Produk</small>
                        <small id="summaryProducts">Rp 0</small>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small>Ongkos Kirim</small>
                        <small id="summaryShipping">Rp 0</small>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between mb-2">
                        <strong style="font-size:.85rem;">Grand Total</strong>
                        <strong id="summaryGrand" style="font-size:.85rem;color:#1565c0;">Rp 0</strong>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-sm" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane mr-1"></i> Buat Pesanan Grosir
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-block btn-sm mt-1" onclick="clearCart()">
                        <i class="fas fa-trash mr-1"></i> Bersihkan Keranjang
                    </button>
                </div>
            </div>

        </div>{{-- end right col --}}

    </div>{{-- end .row --}}
    </form>

</div>{{-- end container --}}

{{-- ═══════════════════════════════════════════════════════
     MODAL: Input untuk Parfum (ml) atau Aksesori (pcs)
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" style="font-size:.9rem;">
                    <i class="fas fa-box mr-1 text-primary" id="modalIcon"></i>
                    <span id="modalProductName">-</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <small class="text-muted d-block mb-2" id="modalStockInfo"></small>
                
                {{-- Input untuk Parfum: Volume (ml) --}}
                <div id="modalParfumInputs">
                    <div class="form-group mb-2">
                        <label style="font-size:.78rem;font-weight:600;">Volume (ml) *</label>
                        <input type="number" id="modalVolume" class="form-control form-control-sm"
                               min="1" placeholder="Contoh: 400, 1000, 2000">
                        <small class="text-muted">Masukkan volume dalam ml (1000ml = 1L)</small>
                    </div>
                    <div class="form-group mb-0">
                        <label style="font-size:.78rem;font-weight:600;">Harga Total (dari nota) *</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" id="modalPriceParfum" class="form-control form-control-sm"
                                   min="0" placeholder="Harga total untuk volume ini">
                        </div>
                        <small class="text-muted">Harga total dari nota buku grosir</small>
                    </div>
                </div>

                {{-- Input untuk Aksesori: Qty (pcs) --}}
                <div id="modalAccessoryInputs" style="display:none;">
                    <div class="form-group mb-2">
                        <label style="font-size:.78rem;font-weight:600;">Quantity (pcs) *</label>
                        <input type="number" id="modalQty" class="form-control form-control-sm"
                               min="1" value="1" placeholder="Jumlah pcs">
                    </div>
                    <div class="form-group mb-0">
                        <label style="font-size:.78rem;font-weight:600;">Harga Satuan (dari nota) *</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" id="modalPriceAccessory" class="form-control form-control-sm"
                                   min="0" placeholder="Harga per pcs">
                        </div>
                        <small class="text-muted">Harga per pcs dari nota buku grosir</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="confirmAddToCart()">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Cart State ────────────────────────────────────────────────
let cart = [];
let pendingProductId = null;

// ── On DOM Ready ──────────────────────────────────────────────
$(document).ready(function () {
    loadCart();

    // Category filter
    $('#showAllProducts').on('click', function () {
        $('.btn-category').removeClass('active');
        $(this).addClass('active');
        filterProducts();
    });
    $('.btn-category').on('click', function () {
        $('#showAllProducts').removeClass('active');
        $('.btn-category').removeClass('active');
        $(this).addClass('active');
        filterProducts();
    });

    // Search filter
    $('#productSearch').on('input', filterProducts);

    // Shipping cost change → update summary
    $('input[name="shipping_cost"]').on('input', updateSummary);
});

// ── Product Filtering ─────────────────────────────────────────
function filterProducts() {
    const catId  = $('.btn-category.active').data('category') || null;
    const search = $('#productSearch').val().toLowerCase().trim();

    $('.product-item').each(function () {
        const matchCat    = !catId || $(this).data('category') == catId;
        const matchSearch = !search || $(this).data('name').toLowerCase().includes(search);
        $(this).toggle(matchCat && matchSearch);
    });
}

// ── Add to Cart Modal ─────────────────────────────────────────
let pendingItemType = 'product'; // 'product' or 'accessory'

function openAddModal(itemId, itemType) {
    const el = $(`.product-item[data-id="${itemId}"][data-type="${itemType}"]`);
    const stock = parseInt(el.data('stock')) || 0;
    if (stock <= 0) {
        Swal.fire('Stok Habis', 'Produk ini tidak tersedia.', 'warning');
        return;
    }
    
    pendingProductId = itemId;
    pendingItemType = itemType;
    $('#modalProductName').text(el.data('name'));
    
    if (itemType === 'product') {
        // Parfum: tampilkan input volume ml
        $('#modalIcon').removeClass('fa-box').addClass('fa-wine-bottle');
        const stockLabel = stock >= 1000 ? (stock/1000).toFixed(1)+' L' : stock+' ml';
        $('#modalStockInfo').text('Stok tersedia: ' + stockLabel);
        $('#modalVolume').val('').attr('max', stock);
        $('#modalPriceParfum').val('');
        $('#modalParfumInputs').show();
        $('#modalAccessoryInputs').hide();
        $('#addProductModal').modal('show');
        setTimeout(() => $('#modalVolume').focus(), 400);
    } else {
        // Aksesori: tampilkan input qty pcs
        $('#modalIcon').removeClass('fa-wine-bottle').addClass('fa-box');
        $('#modalStockInfo').text('Stok tersedia: ' + stock + ' pcs');
        $('#modalQty').val(1).attr('max', stock);
        $('#modalPriceAccessory').val('');
        $('#modalParfumInputs').hide();
        $('#modalAccessoryInputs').show();
        $('#addProductModal').modal('show');
        setTimeout(() => $('#modalQty').focus(), 400);
    }
}

function confirmAddToCart() {
    const el = $(`.product-item[data-id="${pendingProductId}"][data-type="${pendingItemType}"]`);
    const stock = parseInt(el.data('stock')) || 0;

    if (pendingItemType === 'product') {
        // Parfum: validasi volume ml + harga total
        const volume = parseFloat($('#modalVolume').val()) || 0;
        const price = parseFloat($('#modalPriceParfum').val()) || 0;

        if (volume <= 0) {
            Swal.fire('Input Error', 'Volume harus diisi dan lebih dari 0.', 'warning');
            return;
        }
        if (price <= 0) {
            Swal.fire('Input Error', 'Harga total harus diisi dan lebih dari 0.', 'warning');
            return;
        }

        // Check total volume in cart + new doesn't exceed stock
        const existingVol = cart
            .filter(i => i.product_id == pendingProductId)
            .reduce((s, i) => s + (i.volume_ml || 0), 0);

        if (existingVol + volume > stock) {
            const stockLabel = stock >= 1000 ? (stock/1000).toFixed(1)+' L' : stock+' ml';
            Swal.fire('Stok Tidak Cukup', `Stok hanya ${stockLabel}.`, 'warning');
            return;
        }

        // Check if same product+volume+price already in cart → merge
        const existIdx = cart.findIndex(i => i.product_id == pendingProductId && i.volume_ml == volume && i.price == price);

        if (existIdx >= 0) {
            Swal.fire('Sudah Ada', 'Item dengan volume dan harga yang sama sudah ada di cart.', 'info');
            return;
        }

        cart.push({
            product_id: pendingProductId,
            accessory_id: null,
            product_name: el.data('name'),
            volume_ml: volume,
            price: price,
        });

    } else {
        // Aksesori: validasi qty pcs + harga satuan
        const qty = Math.max(1, parseInt($('#modalQty').val()) || 1);
        const pricePerUnit = parseFloat($('#modalPriceAccessory').val()) || 0;

        if (qty <= 0) {
            Swal.fire('Input Error', 'Quantity harus diisi dan lebih dari 0.', 'warning');
            return;
        }
        if (pricePerUnit <= 0) {
            Swal.fire('Input Error', 'Harga satuan harus diisi dan lebih dari 0.', 'warning');
            return;
        }

        // Check total qty in cart + new doesn't exceed stock
        const existingQty = cart
            .filter(i => i.accessory_id == pendingProductId)
            .reduce((s, i) => s + (i.quantity || 0), 0);

        if (existingQty + qty > stock) {
            Swal.fire('Stok Tidak Cukup', `Stok hanya ${stock} pcs.`, 'warning');
            return;
        }

        // Check if same accessory+price already in cart → merge qty
        const existIdx = cart.findIndex(i => i.accessory_id == pendingProductId && i.price_per_unit == pricePerUnit);

        if (existIdx >= 0) {
            cart[existIdx].quantity += qty;
        } else {
            cart.push({
                product_id: null,
                accessory_id: pendingProductId,
                product_name: el.data('name'),
                quantity: qty,
                price_per_unit: pricePerUnit,
            });
        }
    }

    $('#addProductModal').modal('hide');
    saveCart();
    updateCartDisplay();
}

// ── Cart Display ──────────────────────────────────────────────
function updateCartDisplay() {
    const tbody = $('#cartBody');
    tbody.empty();

    let totalProducts = 0;

    if (cart.length === 0) {
        $('#cartEmpty').show();
        $('#cartTableWrap').hide();
        $('#submitBtn').prop('disabled', true);
        updateSummary(0);
        updateHiddenInputs();
        $('#cartCount').text(0);
        return;
    }

    $('#cartEmpty').hide();
    $('#cartTableWrap').show();
    $('#submitBtn').prop('disabled', false);
    $('#cartCount').text(cart.length);

    cart.forEach((item, idx) => {
        let lineTotal, displayInfo;
        
        if (item.product_id) {
            // Parfum: volume ml + harga total
            lineTotal = item.price;
            const volLabel = item.volume_ml >= 1000 
                ? (item.volume_ml/1000).toFixed(1)+' L' 
                : item.volume_ml+' ml';
            displayInfo = `<small>${escHtml(item.product_name)}</small>`;
            displayInfo += `<td><small>${volLabel}</small></td>`;
            displayInfo += `<td><small>Rp ${Math.round(lineTotal).toLocaleString('id-ID')}</small></td>`;
        } else {
            // Aksesori: qty pcs × harga satuan
            lineTotal = item.price_per_unit * item.quantity;
            displayInfo = `<small>${escHtml(item.product_name)}</small>`;
            displayInfo += `<td><small>${item.quantity} pcs</small></td>`;
            displayInfo += `<td><small>Rp ${item.price_per_unit.toLocaleString('id-ID')} × ${item.quantity}</small></td>`;
        }
        
        totalProducts += lineTotal;

        tbody.append(`
            <tr>
                <td>${displayInfo}</td>
                <td><small>Rp ${Math.round(lineTotal).toLocaleString('id-ID')}</small></td>
                <td><button type="button" class="btn btn-xs btn-outline-danger px-1 py-0"
                        onclick="removeFromCart(${idx})"><i class="fas fa-times"></i></button></td>
            </tr>
        `);
    });

    $('#cartTotal').text('Rp ' + Math.round(totalProducts).toLocaleString('id-ID'));
    updateSummary(totalProducts);
    updateHiddenInputs();
}

function updateSummary(totalProducts) {
    if (typeof totalProducts !== 'number') {
        // recalculate from cart
        totalProducts = cart.reduce((s, i) => {
            if (i.product_id) return s + i.price; // Parfum: harga total
            else return s + (i.price_per_unit * i.quantity); // Aksesori: harga × qty
        }, 0);
    }
    const shipping = parseFloat($('input[name="shipping_cost"]').val()) || 0;
    const grand    = totalProducts + shipping;
    $('#summaryProducts').text('Rp ' + Math.round(totalProducts).toLocaleString('id-ID'));
    $('#summaryShipping').text('Rp ' + Math.round(shipping).toLocaleString('id-ID'));
    $('#summaryGrand').text('Rp ' + Math.round(grand).toLocaleString('id-ID'));
}

function updateHiddenInputs() {
    const container = $('#hiddenCartInputs');
    container.empty();
    cart.forEach((item, idx) => {
        if (item.product_id) {
            // Parfum: kirim product_id + volume_ml + price (total)
            container.append(`<input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">`);
            container.append(`<input type="hidden" name="items[${idx}][product_name]" value="${escAttr(item.product_name)}">`);
            container.append(`<input type="hidden" name="items[${idx}][volume_ml]" value="${item.volume_ml}">`);
            container.append(`<input type="hidden" name="items[${idx}][quantity]" value="1">`);
            container.append(`<input type="hidden" name="items[${idx}][price]" value="${Math.round(item.price)}">`);
            container.append(`<input type="hidden" name="items[${idx}][unit]" value="ml">`);
        } else if (item.accessory_id) {
            // Aksesori: kirim accessory_id + quantity + price (total)
            container.append(`<input type="hidden" name="items[${idx}][accessory_id]" value="${item.accessory_id}">`);
            container.append(`<input type="hidden" name="items[${idx}][product_name]" value="${escAttr(item.product_name)}">`);
            container.append(`<input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">`);
            container.append(`<input type="hidden" name="items[${idx}][price]" value="${Math.round(item.price_per_unit * item.quantity)}">`);
            container.append(`<input type="hidden" name="items[${idx}][unit]" value="pcs">`);
        }
    });
}

// ── Cart Helpers ──────────────────────────────────────────────
function removeFromCart(idx) {
    cart.splice(idx, 1);
    saveCart();
    updateCartDisplay();
}

function clearCart() {
    cart = [];
    saveCart();
    updateCartDisplay();
}

function saveCart() {
    try { localStorage.setItem('apms_wholesale_cart', JSON.stringify(cart)); } catch(e) {}
}

function loadCart() {
    try {
        const saved = localStorage.getItem('apms_wholesale_cart');
        if (saved) { cart = JSON.parse(saved) || []; }
    } catch(e) { cart = []; }
    updateCartDisplay();
}

// ── Form Submit Guard ─────────────────────────────────────────
$('#wholesaleForm').on('submit', function (e) {
    if (cart.length === 0) {
        e.preventDefault();
        Swal.fire('Keranjang Kosong', 'Tambahkan produk terlebih dahulu.', 'warning');
        return false;
    }
    // Inject latest hidden inputs before submit
    updateHiddenInputs();
});

// ── Utilities ─────────────────────────────────────────────────
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(str) {
    return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
</script>
@endpush
