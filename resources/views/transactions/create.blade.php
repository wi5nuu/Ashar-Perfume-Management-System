@extends('layouts.app')

@section('title', 'Kasir - APMS')

@section('content')
<div class="container-fluid">
    <!-- Mobile Tab Toggle -->
    <div class="d-md-none mb-2">
        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
            <label class="btn btn-primary-apms active" id="mobileTabProducts">
                <input type="radio" name="mobilePosTab" value="products" checked>
                <i class="fas fa-box mr-1"></i> Produk
            </label>
            <label class="btn btn-outline-primary" id="mobileTabCart">
                <input type="radio" name="mobilePosTab" value="cart">
                <i class="fas fa-shopping-cart mr-1"></i> Keranjang <span class="badge badge-light ml-1" id="mobileTabCartCount">0</span>
            </label>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Product Selection -->
        <div class="col-md-9 col-12" id="leftColumn" style="transition: all 0.3s ease;">
            <!-- Product Search + Category Filter -->
            <div class="card card-apms mb-3">
                <div class="card-header d-flex align-items-center flex-wrap" style="gap:8px;">
                    <h3 class="card-title mb-0 text-nowrap">Daftar Produk</h3>
                    {{-- Category pill filters --}}
                    <div class="d-flex flex-nowrap overflow-auto" style="gap:4px; flex:1; min-width:0;">
                        <button class="btn btn-sm btn-secondary active px-2 py-1" id="showAllProducts" style="white-space:nowrap; font-size:0.75rem; flex-shrink:0;">Semua</button>
                        @foreach($categories as $category)
                        @php
                            $tierDefaultColor = match($category->tier ?? 'biasa') {
                                'premium' => '#FFB300',
                                'sedang'  => '#78909C',
                                'biasa'   => '#66BB6A',
                                default   => '#FF6B35',
                            };
                            $catColor = $category->color && preg_match('/^#[0-9a-fA-F]{6}$/', $category->color)
                                ? $category->color
                                : $tierDefaultColor;
                        @endphp
                        <button class="btn btn-sm btn-category px-2 py-1"
                                data-category="{{ $category->id }}"
                                data-tier="{{ $category->tier ?? 'biasa' }}"
                                style="background-color:{{ $catColor }}; color:#fff; white-space:nowrap; font-size:0.75rem; flex-shrink:0;">
                            {{ $category->name }}
                        </button>
                        @endforeach
                    </div>
                    {{-- Search box --}}
                    <div class="d-flex align-items-center" style="flex-shrink:0; width:220px;">
                        <div class="input-group input-group-sm">
                            <input type="text" id="productSearch" class="form-control"
                                   placeholder="Cari / scan barcode...">
                            <div class="input-group-append">
                                <button class="btn btn-primary-apms" type="button" onclick="openScanner()" title="Scan Barcode">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Product Type Tabs --}}
                <div class="px-3 pt-2 pb-0" style="background:#f8f9fc;border-top:1px solid #eef0f7;">
                    <ul class="nav mb-0" id="productTypeTab" role="tablist" style="gap:4px;">
                        <li class="nav-item">
                            <a class="nav-link pos-tab-link active" id="tab-regular" data-toggle="tab" href="#productsRegular" role="tab">
                                <i class="fas fa-box mr-1"></i> Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pos-tab-link" id="tab-refill" data-toggle="tab" href="#productsRefill" role="tab">
                                <i class="fas fa-fill-drip mr-1"></i> Isi Ulang
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    {{-- Regular Products Tab --}}
                    <div class="tab-pane fade show active" id="productsRegular" role="tabpanel">
                        <div class="card-body product-grid-body py-2">
                            <div class="row" id="productGrid">
                                @foreach($products->where('is_refill', false) as $product)
                                @php
                                    $inventory = $product->inventories->first();
                                    $currentStock = $inventory ? (int)($inventory->current_stock ?? 0) : 0;
                                    // Ukuran per porsi dalam ml (30ml default)
                                    $rawSize = strtolower(preg_replace('/\s+/', '', $product->size ?? '30ml'));
                                    $porsiMl = match(true) {
                                        str_contains($rawSize, '100') => 100,
                                        str_contains($rawSize, '50')  => 50,
                                        default                       => 30,
                                    };
                                    $disabled = $currentStock < $porsiMl;
                                    $prodTier = $product->category?->tier ?? 'biasa';
                                    // Deteksi ukuran untuk harga yang benar
                                    $prodSizeKey = in_array($rawSize, ['30ml','50ml','100ml']) ? $rawSize : '30ml';
                                    $tierBadgeClass = match($prodTier) {
                                        'premium' => 'badge-warning',
                                        'sedang'  => 'badge-secondary',
                                        'biasa'   => 'badge-light text-dark border',
                                        default   => 'badge-light',
                                    };
                                    $tierBadgeLabel = match($prodTier) {
                                        'premium' => '⭐ Premium',
                                        'sedang'  => '🥈 Sedang',
                                        'biasa'   => '🏷️ Biasa',
                                        default   => $prodTier,
                                    };
                                @endphp
                                <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2 mb-md-3 product-item"
                                     data-id="{{ $product->id }}"
                                     data-category="{{ $product->product_category_id }}"
                                     data-tier="{{ $prodTier }}"
                                     data-size="{{ $product->size }}"
                                     data-name="{{ $product->name }}"
                                     data-price="{{ $product->selling_price }}"
                                     data-wholesale="{{ $product->wholesale_price }}"
                                     data-stock="{{ $currentStock }}"
                                     data-barcode="{{ $product->barcode }}">
                                    <div class="card product-card {{ $disabled ? 'bg-light' : '' }} {{ $prodTier === 'premium' ? 'border-warning' : ($prodTier === 'sedang' ? 'border-secondary' : '') }} h-100"
                                         onclick="{{ !$disabled ? 'addToCart(' . $product->id . ')' : '' }}">
                                        <div class="card-body p-0 d-flex flex-column justify-content-between" style="min-height:90px;">
                                            {{-- Nama aroma di tengah --}}
                                            <div class="flex-grow-1 d-flex align-items-center justify-content-center py-2 px-1">
                                                <h6 class="mb-0 product-name text-center">{{ $product->name }}</h6>
                                            </div>
                                            {{-- Footer: ml tersisa --}}
                                            <div class="product-card-footer d-flex justify-content-center align-items-center px-2 py-1">
                                                <span class="product-footer-stock
                                                    @if($currentStock < $porsiMl) stock-habis
                                                    @elseif($currentStock < ($porsiMl * 3)) stock-sedikit
                                                    @else stock-oke
                                                    @endif">
                                                    @if($currentStock < $porsiMl)Habis
                                                    @else {{ \App\Helpers\PerformanceHelper::formatMl($currentStock) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- Refill Products Tab --}}
                    <div class="tab-pane fade" id="productsRefill" role="tabpanel">
                        <div class="card-body product-grid-body py-2">
                            <div class="row" id="refillGrid">
                                @php $refillProducts = $products->where('is_refill', true); @endphp
                                @forelse($refillProducts as $product)
                                @php
                                    $inventory = $product->inventories->first();
                                    $bulkStock = $inventory ? (int)($inventory->current_stock ?? 0) : 0;
                                @endphp
                                <div class="col-xl-4 col-lg-6 col-md-6 col-12 mb-2 mb-md-3 refill-item" 
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->name }}"
                                     data-price-per-ml="{{ $product->refill_price_per_ml ?? 0 }}"
                                     data-bulk-stock="{{ $bulkStock }}"
                                     data-barcode="{{ $product->barcode }}">
                                    <div class="card product-card h-100 border-secondary" style="background-image:none;background:#fff;min-height:unset;">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width:50px;height:50px;object-fit:cover;" class="rounded" loading="lazy">
                                                    @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width:50px;height:50px;">
                                                        <i class="fas fa-fill-drip fa-lg text-secondary"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <h6 class="mb-0 font-weight-bold text-secondary">{{ $product->name }}</h6>
                                                    <small class="text-muted d-block">{{ $product->size }}</small>
                                                    <strong class="text-secondary">
                                                        Rp {{ number_format($product->refill_price_per_ml ?? 0, 0, ',', '.') }}/ml
                                                    </strong>
                                                    <div class="mt-1">
                                                        @if($bulkStock <= 0)
                                                            <span class="badge badge-danger">Stok Habis</span>
                                                        @elseif($bulkStock < 100)
                                                            <span class="badge badge-warning">Sisa {{ \App\Helpers\PerformanceHelper::formatMl($bulkStock) }}</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ \App\Helpers\PerformanceHelper::formatMl($bulkStock) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 d-flex align-items-center">
                                                <div class="input-group input-group-sm mr-2" style="max-width:130px;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-secondary text-white">ml</span>
                                                    </div>
                                                    <input type="number" class="form-control refill-volume-input" value="50" min="10" max="{{ $bulkStock }}">
                                                </div>
                                                <button class="btn btn-secondary btn-sm flex-shrink-0" onclick="addRefillToCart({{ $product->id }}, this)">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center text-muted py-4">
                                    <i class="fas fa-fill-drip fa-3x mb-2 d-block"></i>
                                    <h6>Belum ada produk isi ulang</h6>
                                    <small>Silakan tambah produk dengan centang "Isi Ulang" di manajemen produk.</small>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Middle Column: New Customer Form -->
        <div class="col-md-3 right-panel" id="middleColumn" style="display: none;">
            <div class="card card-apms mb-3 shadow-sm" style="border-top: 4px solid #ff6b35;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <h3 class="card-title text-primary-apms m-0" style="font-size:1rem;"><i class="fas fa-user-plus"></i> Tambah Pelanggan</h3>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="hideInlineNewCustomer()"><i class="fas fa-times"></i></button>
                </div>
                <div class="card-body p-2">
                    <form id="newCustomerForm">
                        @csrf
                        <input type="hidden" name="is_active" value="1">
                        <input type="hidden" name="type" value="retail">
                        <div class="form-group mb-2">
                            <label class="mb-0"><small>Nama Lengkap *</small></label>
                            <input type="text" class="form-control form-control-sm" name="name" required placeholder="Nama pelanggan">
                        </div>
                        <div class="form-group mb-3">
                            <label class="mb-0"><small>Nomor HP *</small></label>
                            <input type="text" class="form-control form-control-sm" name="phone" required placeholder="08xxxxxxxxxx">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary-apms btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan Pelanggan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Right Column: Cart & Payment -->
        <div class="col-md-3 col-12 right-panel" id="rightColumn" style="transition: all 0.3s ease;">
            <!-- Customer Info -->
            <div class="card card-apms mb-2">
                <div class="card-header py-2">
                    <h3 class="card-title" style="font-size:.82rem;">Informasi Pelanggan</h3>
                </div>
                <div class="card-body p-2">
                    {{-- Tipe selalu retail di POS ini; wholesale punya halaman tersendiri --}}
                    <input type="hidden" id="customerType" value="retail">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-primary px-2 py-1" style="font-size:.72rem;letter-spacing:.4px;">
                            <i class="fas fa-shopping-bag mr-1"></i> RETAIL
                        </span>
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0" style="font-size:.72rem;">Pilih Pelanggan</label>
                        <select class="form-control form-control-sm select2" id="customerSelect" style="width:100%;">
                            <option value="">Umum</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-phone="{{ $customer->phone }}"
                                    data-email="{{ $customer->email }}">
                                {{ $customer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="customer-details" style="display: none;">
                        <div class="alert alert-info p-1 mb-1" style="font-size:.72rem;">
                            <i class="fas fa-phone"></i> <span id="customerPhone"></span><br>
                            <i class="fas fa-envelope"></i> <span id="customerEmail"></span>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm btn-block mt-1" onclick="showInlineNewCustomer()" id="newCustomerBtnDisplay">
                        <i class="fas fa-user-plus"></i> Pelanggan Baru
                    </button>
                </div>
            </div>
            
            <!-- Cart -->
            <div class="card card-apms mb-2" id="cartSection">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-sm btn-outline-secondary d-md-none mr-2" onclick="switchMobileTab('products')" title="Kembali ke Produk">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <h3 class="card-title mb-0 d-inline" style="font-size:.82rem;">Keranjang Belanja</h3>
                    </div>
                    <span class="badge badge-primary" id="cartCount">0 item</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" id="cartTable">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Produk</th>
                                <th width="25%">Qty</th>
                                <th width="25%">Harga</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                            <tbody id="cartItems">
                                <!-- Cart items will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tambah Bonus -->
            <div class="card card-apms mb-3" id="bonusSection">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-success m-0" style="font-size: 1rem;">
                        <i class="fas fa-gift"></i> Parfum Bonus
                        <small class="text-muted ml-1" style="font-size:0.7rem;">(khusus produk 🏷️ Biasa)</small>
                    </h3>
                </div>
                <div class="card-body p-2">
                    <div class="alert alert-warning p-1 mb-2" style="font-size:0.7rem;">
                        <i class="fas fa-info-circle"></i>
                        Beli <strong>Premium 30ml</strong> → dapat <strong>bonus parfum Biasa</strong> gratis.
                        Pilih aroma di bawah atau klik <strong>Pilih Aroma Gratis</strong>.
                    </div>
                    <div class="row no-gutters" style="gap:0;">
                        <div class="col-12 mb-2">
                            <select class="form-control form-control-sm select2" id="bonusParfumSelect" style="width:100%;">
                                <option value="">-- Pilih Aroma Bonus --</option>
                                @foreach($products->where('is_refill', false)->sortBy('name') as $p)
                                    @php
                                        $pStock = $p->inventories->first()?->current_stock ?? 0;
                                    @endphp
                                    <option value="{{ $p->id }}"
                                        data-name="{{ $p->name }}"
                                        data-price="0"
                                        data-stock="{{ $pStock }}"
                                        data-barcode="{{ $p->barcode }}"
                                        {{ $pStock == 0 ? 'disabled' : '' }}>
                                        {{ $p->name }} — 20ml Biasa (Gratis) (Stok: {{ $pStock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex" style="gap:6px;">
                            <button class="btn btn-sm btn-success flex-fill" onclick="addParfumBonus()" title="Tambah Bonus">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                            <button class="btn btn-sm btn-outline-success flex-fill" onclick="openAromaModal()" title="Pilih Aroma Gratis">
                                <i class="fas fa-search"></i> Pilih Aroma
                            </button>
                        </div>
                    </div>
                    {{-- Daftar bonus yang sudah ditambah --}}
                    <div id="bonusListPreview" class="mt-2" style="display:none;">
                        <small class="text-muted d-block mb-1"><i class="fas fa-list"></i> Bonus dalam keranjang:</small>
                        <div id="bonusListItems"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Pilih Aroma Gratis -->
            <!-- Totals & Payment -->
            <div class="card card-apms" id="paymentSection">
                <div class="card-header py-2">
                    <h3 class="card-title" style="font-size:.82rem;">Pembayaran</h3>
                </div>
                <div class="card-body p-2">
                    <div class="row mb-1">
                        <div class="col-6"><small><strong>Subtotal</strong></small></div>
                        <div class="col-6 text-right"><small><span id="subtotal">Rp 0</span></small></div>
                    </div>
                    
                    <div class="row mb-1">
                        <div class="col-5"><small><strong>Diskon</strong></small></div>
                        <div class="col-7 text-right">
                            <div class="input-group input-group-sm">
                                <input type="number" id="discount" class="form-control form-control-sm text-right" 
                                       value="0" min="0" style="min-width:50px;">
                                <div class="input-group-append">
                                    <select class="form-control form-control-sm" id="discountType" style="min-width:45px;">
                                        <option value="fixed">Rp</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-1">
                        <div class="col-6">
                            <small><strong>PPN (10%)</strong></small>
                            <div class="custom-control custom-switch d-inline ml-1" style="transform:scale(.8);transform-origin:left center;">
                                <input type="checkbox" class="custom-control-input" id="taxToggle" checked>
                                <label class="custom-control-label" for="taxToggle"></label>
                            </div>
                        </div>
                        <div class="col-6 text-right"><small><span id="tax">Rp 0</span></small></div>
                    </div>
                    
                    <hr class="my-1">
                    
                    <div class="row mb-2">
                        <div class="col-6"><strong style="font-size:.85rem;">Total</strong></div>
                        <div class="col-6 text-right">
                            <strong id="totalAmount" class="text-primary" style="font-size:.95rem;">Rp 0</strong>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="form-group mb-2">
                        <label class="mb-1" style="font-size:.72rem;font-weight:600;">Metode Pembayaran</label>
                        <div class="btn-group btn-group-toggle btn-group-sm w-100 flex-wrap" data-toggle="buttons" id="paymentMethodGroup">
                            <label class="btn btn-outline-primary active" style="font-size:.68rem;padding:3px 5px;" title="Tunai">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <i class="fas fa-money-bill-wave"></i> Cash
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 5px;" title="QRIS">
                                <input type="radio" name="payment_method" value="qris">
                                <i class="fas fa-qrcode"></i> QRIS
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 5px;" title="Transfer Bank">
                                <input type="radio" name="payment_method" value="transfer">
                                <i class="fas fa-university"></i> Transfer
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 5px;" title="Dompet Digital">
                                <input type="radio" name="payment_method" value="ewallet">
                                <i class="fas fa-wallet"></i> E-Wallet
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 5px;" title="Kartu Debit">
                                <input type="radio" name="payment_method" value="debit_card">
                                <i class="fas fa-credit-card"></i> Debit
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 5px;" title="Kartu Kredit">
                                <input type="radio" name="payment_method" value="credit_card">
                                <i class="fas fa-credit-card text-warning"></i> Kredit
                            </label>
                        </div>
                    </div>

                    <!-- E-Wallet Type (tampil jika pilih E-Wallet) -->
                    <div class="form-group mb-2" id="ewalletTypeGroup" style="display:none;">
                        <label class="mb-1" style="font-size:.72rem;font-weight:600;">Pilih E-Wallet</label>
                        <div class="btn-group btn-group-toggle btn-group-sm w-100 flex-wrap" data-toggle="buttons" id="ewalletTypeButtons">
                            <label class="btn btn-outline-success active" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="ewallet_type" value="dana" checked> DANA
                            </label>
                            <label class="btn btn-outline-danger" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="ewallet_type" value="ovo"> OVO
                            </label>
                            <label class="btn btn-outline-info" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="ewallet_type" value="gopay"> GoPay
                            </label>
                            <label class="btn btn-outline-warning" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="ewallet_type" value="shopeepay"> ShopeePay
                            </label>
                            <label class="btn btn-outline-secondary" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="ewallet_type" value="other"> Lainnya
                            </label>
                        </div>
                    </div>

                    <!-- Transfer Bank Type (tampil jika pilih Transfer) -->
                    <div class="form-group mb-2" id="transferTypeGroup" style="display:none;">
                        <label class="mb-1" style="font-size:.72rem;font-weight:600;">Bank Tujuan</label>
                        <div class="btn-group btn-group-toggle btn-group-sm w-100 flex-wrap" data-toggle="buttons">
                            <label class="btn btn-outline-primary active" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="transfer_type" value="bca" checked> BCA
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="transfer_type" value="bri"> BRI
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="transfer_type" value="bni"> BNI
                            </label>
                            <label class="btn btn-outline-primary" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="transfer_type" value="mandiri"> Mandiri
                            </label>
                            <label class="btn btn-outline-secondary" style="font-size:.68rem;padding:3px 6px;">
                                <input type="radio" name="transfer_type" value="other"> Lainnya
                            </label>
                        </div>
                    </div>

                    <!-- Premium Bonus Warning -->
                    <div id="premiumBonusWarning" class="alert alert-danger p-1 mb-2" style="font-size:0.72rem;display:none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Wajib pilih bonus!</strong> Ada produk <strong>⭐ Premium</strong> di keranjang tanpa bonus aroma gratis.
                        Gulir ke bagian <strong>Parfum Bonus</strong> untuk memilih.
                    </div>

                    <!-- Amount Paid -->
                    <div class="form-group mb-2">
                        <label class="mb-1" style="font-size:.72rem;font-weight:600;">Jumlah Bayar</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-size:.72rem;">Rp</span>
                            </div>
                            <input type="number" id="paidAmount" class="form-control form-control-sm text-right"
                                   placeholder="0" min="0" style="font-size:.85rem;font-weight:600;">
                        </div>
                    </div>

                    <!-- Change -->
                    <div class="form-group mb-2">
                        <label class="mb-1" style="font-size:.72rem;font-weight:600;">Kembalian</label>
                        <input type="text" id="changeAmount" class="form-control form-control-sm text-right"
                               readonly style="background-color:#e8f5e9;font-weight:bold;color:#2e7d32;font-size:.85rem;">
                    </div>

                    <!-- Notes -->
                    <div class="form-group mb-2">
                        <label class="mb-1" style="font-size:.72rem;font-weight:600;">Catatan <small class="text-muted font-weight-normal">(Opsional)</small></label>
                        <textarea id="transactionNotes" class="form-control form-control-sm" rows="1"
                                  placeholder="Tambahkan catatan transaksi..."></textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row no-gutters mb-1">
                        <div class="col-6 pr-1">
                            <button class="btn btn-danger btn-sm btn-block" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Batal
                            </button>
                        </div>
                        <div class="col-6 pl-1">
                            <button class="btn btn-success btn-sm btn-block" onclick="processPayment()">
                                <i class="fas fa-check"></i> Bayar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Amounts -->
                    <div class="row mt-1 no-gutters">
                        @php $quickAmounts = [50000, 100000, 150000, 200000, 250000, 300000]; @endphp
                        @foreach($quickAmounts as $amount)
                        <div class="col-4 mb-1 px-1">
                            <button class="btn btn-outline-secondary btn-sm btn-block" style="font-size:.68rem;padding:2px 4px;"
                                    onclick="setPaidAmount({{ $amount }})">
                                {{ number_format($amount/1000, 0) }}rb
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Mobile Floating Cart Button -->
<div class="d-md-none position-fixed" style="bottom: 75px; right: 15px; z-index: 1050;">
    <button class="btn btn-primary-apms rounded-circle shadow-lg d-flex align-items-center justify-content-center" 
            style="width: 56px; height: 56px;"
            onclick="switchMobileTab('cart')">
        <i class="fas fa-shopping-cart fa-lg"></i>
        <span class="badge badge-danger position-absolute" id="mobileCartCount" 
              style="top: -2px; right: -2px; border-radius: 50%; min-width: 22px; padding: 3px 6px; font-size: 11px;">0</span>
    </button>
</div>


<!-- Modal Pilih Tier Produk -->
<div class="modal fade" id="tierModal" tabindex="-1" role="dialog" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;border:none;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <div class="modal-header py-2 px-3" style="background:#fff;border-bottom:1px solid #eee;">
                <div>
                    <h6 class="modal-title mb-0" style="font-size:.9rem;font-weight:700;color:#1a1a2e;">Pilih Ukuran &amp; Kualitas</h6>
                    <small class="text-muted" id="tierModalProductName" style="font-size:.75rem;"></small>
                </div>
                <button type="button" class="close ml-auto" onclick="$('#tierModal').modal('hide')" style="color:#888;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="background:#fff;">
                <!-- Pilih Ukuran -->
                <div class="mb-3">
                    <div class="small font-weight-600 text-muted mb-1">Ukuran</div>
                    <div class="btn-group btn-group-sm w-100" id="sizeSelector">
                        <button type="button" class="btn btn-outline-secondary size-btn active" data-size="30ml" onclick="selectSize('30ml', this)">30ml</button>
                        <button type="button" class="btn btn-outline-secondary size-btn" data-size="50ml" onclick="selectSize('50ml', this)">50ml</button>
                        <button type="button" class="btn btn-outline-secondary size-btn" data-size="100ml" onclick="selectSize('100ml', this)">100ml</button>
                    </div>
                </div>
                <hr class="my-2">
                <!-- Premium -->
                <button class="btn btn-block mb-2 tier-select-btn"
                        onclick="addToCartWithTier('premium')"
                        style="background:#fff;color:#1a1a2e;border:2px solid #FFB300;border-radius:7px;padding:10px 14px;text-align:left;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-weight:700;font-size:.88rem;color:#1a1a2e;">Premium Original</div>
                            <small style="color:#888;font-size:.72rem;">Kualitas terbaik</small>
                        </div>
                        <div style="font-size:.95rem;font-weight:700;color:#FFB300;" id="tierPricePremium">Rp {{ number_format($tierPrices['30ml']['premium'], 0, ',', '.') }}</div>
                    </div>
                </button>
                <!-- Sedang -->
                <button class="btn btn-block mb-2 tier-select-btn"
                        onclick="addToCartWithTier('sedang')"
                        style="background:#fff;color:#1a1a2e;border:2px solid #78909C;border-radius:7px;padding:10px 14px;text-align:left;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-weight:700;font-size:.88rem;color:#1a1a2e;">Sedang</div>
                            <small style="color:#888;font-size:.72rem;">Kualitas menengah</small>
                        </div>
                        <div style="font-size:.95rem;font-weight:700;color:#78909C;" id="tierPriceSedang">Rp {{ number_format($tierPrices['30ml']['sedang'], 0, ',', '.') }}</div>
                    </div>
                </button>
                <!-- Biasa/Standar -->
                <button class="btn btn-block mb-0 tier-select-btn"
                        onclick="addToCartWithTier('biasa')"
                        style="background:#fff;color:#1a1a2e;border:2px solid #66BB6A;border-radius:7px;padding:10px 14px;text-align:left;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-weight:700;font-size:.88rem;color:#1a1a2e;">Standar</div>
                            <small style="color:#888;font-size:.72rem;">Kualitas standar</small>
                        </div>
                        <div style="font-size:.95rem;font-weight:700;color:#66BB6A;" id="tierPriceBiasa">Rp {{ number_format($tierPrices['30ml']['biasa'], 0, ',', '.') }}</div>
                    </div>
                </button>
            </div>
            <div class="modal-footer py-2 px-3" style="background:#f8f9fa;border-top:1px solid #eee;">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('#tierModal').modal('hide')">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Aroma Bonus Gratis -->
<div class="modal fade" id="aromaModal" tabindex="-1" role="dialog" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-gift mr-1"></i> Pilih Aroma Bonus Gratis</h5>
                <button type="button" class="close text-white" onclick="closeAromaModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                {{-- Search & Filter --}}
                <div class="row mb-3">
                    <div class="col-12 col-md-8 mb-2 mb-md-0">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="aromaSearchInput"
                                   placeholder="Cari nama aroma..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select class="form-control form-control-sm" id="aromaStockFilter">
                            <option value="all" selected>Semua Stok</option>
                            <option value="available">Stok Tersedia</option>
                            <option value="empty">Stok Habis</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <small class="text-muted" id="aromaResultCount"></small>
                </div>
                {{-- Grid Aroma --}}
                <div class="row" id="aromaGrid">
                    @foreach($products->where('is_refill', false)->sortBy('name') as $p)
                        @php
                            $pBulkMl = (int)($p->inventories->first()?->current_stock ?? 0);
                            $pAvailable = $pBulkMl > 0;
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3 mb-2 aroma-card-item"
                             data-name="{{ strtolower($p->name) }}"
                             data-stock="{{ $pBulkMl }}">
                            <div class="card h-100 {{ !$pAvailable ? 'border-danger' : 'border-success' }}"
                                 style="cursor:{{ $pAvailable ? 'pointer' : 'not-allowed' }};opacity:{{ !$pAvailable ? '0.55' : '1' }};transition:.15s;"
                                 @if($pAvailable) onclick="selectAromaBonus({{ $p->id }},'{{ addslashes($p->name) }}',{{ $p->selling_price }},{{ $pBulkMl }},'{{ addslashes($p->barcode ?? '') }}')" @endif>
                                <div class="card-body p-2 text-center">
                                    <i class="fas fa-wine-bottle fa-lg {{ $pAvailable ? 'text-success' : 'text-muted' }} mb-1 d-block"></i>
                                    <div class="font-weight-bold" style="font-size:0.72rem;line-height:1.3;">{{ $p->name }}</div>
                                    <small class="text-success font-weight-bold d-block" style="font-size:0.65rem;">20ml Biasa</small>
                                    <span class="badge badge-warning mt-1" style="font-size:0.60rem;">GRATIS</span>
                                    @if($pAvailable)
                                        <span class="badge badge-success mt-1 d-block" style="font-size:0.62rem;">{{ \App\Helpers\PerformanceHelper::formatMl($pBulkMl) }} tersedia</span>
                                    @else
                                        <span class="badge badge-danger mt-1 d-block" style="font-size:0.62rem;">Habis</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer py-2">
                <small class="text-muted mr-auto"><i class="fas fa-hand-pointer"></i> Klik aroma untuk menambahkan sebagai bonus gratis</small>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeAromaModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Scan Barcode</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-2 text-center">
                <!-- Upload gambar barcode -->
                <div id="uploadArea" style="width:100%;height:180px;background:#f8f9fa;border:2px dashed #ccc;border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:.2s;" onclick="document.getElementById('barcodeFileInput').click()" onmouseover="this.style.borderColor='#FF6B35'" onmouseout="this.style.borderColor='#ccc'">
                    <i class="fas fa-barcode fa-3x text-muted mb-2"></i>
                    <span class="text-muted">Klik untuk upload gambar barcode</span>
                    <small class="text-muted">Format: JPG, PNG</small>
                    <input type="file" id="barcodeFileInput" accept="image/*" style="display:none;" onchange="decodeBarcodeImage(this)">
                </div>
                <div id="scanPreview" style="display:none;width:100%;max-height:200px;border-radius:8px;overflow:hidden;margin-top:8px;">
                    <img id="previewImg" style="width:100%;max-height:200px;object-fit:contain;">
                </div>
                <div id="scanProgress" class="mt-2" style="display:none;">
                    <small class="text-muted"><i class="fas fa-spinner fa-spin"></i> Mendeteksi barcode...</small>
                </div>
                <div class="mt-2">
                    <div class="input-group input-group-sm">
                        <input type="text" id="manualBarcode" class="form-control" placeholder="Atau ketik barcode manual..." maxlength="30">
                        <div class="input-group-append">
                            <button class="btn btn-primary-apms" onclick="manualBarcodeScan()">Cari</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.product-card {
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
    min-height: 120px;
    border: 1px solid #222;
    background-image: url('{{ asset('display-kasir.jpg') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    overflow: hidden;
}
/* overlay gelap agar teks tetap terbaca */
.product-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 20, 0.62);
    z-index: 0;
    border-radius: inherit;
    transition: background 0.2s ease;
}
.product-card:hover::before {
    background: rgba(10, 10, 20, 0.45);
}
.product-card .card-body {
    position: relative;
    z-index: 1;
}
.product-card .product-name,
.product-card .product-meta,
.product-card .badge,
.product-card small,
.product-card h6 {
    color: #fff !important;
    text-shadow: 0 1px 3px rgba(0,0,0,0.8);
}
.product-card .text-muted {
    color: #ccc !important;
}
/* Footer strip harga & stok */
.product-card-footer {
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(2px);
    border-top: 1px solid rgba(255,255,255,0.12);
    border-radius: 0 0 4px 4px;
}
.product-footer-price {
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff !important;
    text-shadow: 0 1px 3px rgba(0,0,0,0.9);
    white-space: nowrap;
}
.product-footer-stock {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 3px;
    white-space: nowrap;
}
.stock-habis  { background: #dc3545; color: #fff; }
.stock-sedikit{ background: #ffc107; color: #333; }
.stock-oke    { background: #28a745; color: #fff; }
.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.45);
}
.product-card.bg-light {
    cursor: not-allowed;
    opacity: 0.5;
}
.product-name {
    font-size: 0.8rem;
    line-height: 1.2;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 1.9rem;
}
.product-price {
    font-size: 0.85rem;
}
.product-img {
    height: 50px;
    width: 100%;
    object-fit: cover;
    border-radius: 4px;
}
.product-img-placeholder {
    height: 50px;
    border-radius: 4px;
    background: transparent !important;
}
#cartTable tbody tr {
    border-bottom: 1px solid #dee2e6;
}
#cartTable tbody tr:last-child {
    border-bottom: none;
}
.btn-category.active {
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.5);
}
.category-scroll {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.category-scroll::-webkit-scrollbar {
    display: none;
}
@media (max-width: 767.98px) {
    .product-img, .product-img-placeholder {
        height: 40px;
    }
    .product-name {
        font-size: 0.72rem;
    }
    .product-price {
        font-size: 0.78rem;
    }
    .product-card .card-body {
        padding: 6px !important;
    }
    #rightColumn .card {
        margin-bottom: 8px;
    }
    .btn-group-toggle .btn {
        font-size: 0.78rem;
        padding: 6px 8px;
    }
    .category-scroll .btn {
        font-size: 0.75rem;
        padding: 8px 12px !important;
    }
    /* Mobile POS tab toggle */
    #leftColumn, #rightColumn {
        display: block;
    }
    .pos-tab-cart-active #leftColumn {
        display: none;
    }
    .pos-tab-products-active #rightColumn {
        display: none;
    }
}
.right-panel {
    position: sticky;
    top: 70px;
    align-self: flex-start;
    max-height: calc(100vh - 80px);
    overflow-y: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.right-panel::-webkit-scrollbar { width: 0; display: none; }
.product-grid-body {
    max-height: calc(100vh - 260px);
    overflow-y: auto;
    scrollbar-width: thin;
}
.product-grid-body::-webkit-scrollbar { width: 4px; }
.product-grid-body::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

/* ===== Compact cart list UI ===== */
#cartTable thead th {
    font-size: 0.6rem;
    padding: 3px 6px;
    white-space: nowrap;
}
#cartTable tbody td {
    padding: 3px 6px;
    font-size: 0.72rem;
    vertical-align: middle;
}
#cartTable tbody td:first-child { width: 20px; }
#cartTable tbody .cart-prod-name { font-size: 0.72rem; }
#cartTable tbody .cart-badge { font-size: 0.58rem; padding: 1px 5px; }
#cartTable tbody .cart-qty-btn {
    padding: 0 6px;
    font-size: 0.7rem;
    line-height: 1.2;
}
#cartTable tbody .cart-qty-input {
    font-size: 0.7rem;
    height: 22px;
    padding: 0 2px;
    max-width: 32px;
}
#cartTable tbody .cart-price-row { font-size: 0.72rem; }
#cartTable tbody .cart-price-total { font-size: 0.72rem; }
#cartTable tbody .cart-remove-btn {
    padding: 0 5px;
    font-size: 0.65rem;
    line-height: 1.4;
}
#cartTable tbody .cart-empty { font-size: 0.78rem; padding: 16px 8px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let cart = [];
let customerType = 'retail';

// ID kategori per tier — diambil dari database
const premiumCategoryId = @json($categories->firstWhere('tier', 'premium')?->id ?? null);
const sedangCategoryId  = @json($categories->firstWhere('tier', 'sedang')?->id ?? null);
const biasaCategoryId   = @json($categories->firstWhere('tier', 'biasa')?->id ?? null);

// Semua ID kategori premium & sedang (array, jika ada lebih dari 1)
const premiumCategoryIds = @json($categories->where('tier', 'premium')->pluck('id'));
const sedangCategoryIds  = @json($categories->where('tier', 'sedang')->pluck('id'));
const biasaCategoryIds   = @json($categories->where('tier', 'biasa')->pluck('id'));

// Harga tetap per tier (dari settings)
const tierPrices = @json($tierPrices);


$(function() {
    try {
        loadCart();
    } catch (e) {
        console.warn('Cart data corrupted, resetting.', e);
        localStorage.removeItem('apms_cart');
        cart = [];
    }

    // Initialize Select2
    $('#customerSelect').select2({ theme: 'bootstrap4' });
    $('#bonusParfumSelect').select2({ theme: 'bootstrap4' });
    
    // Product search
    $('#productSearch').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        if (searchTerm === '') {
            $('.product-item').show();
            return;
        }
        $('.product-item').each(function() {
            const name    = ($(this).data('name') || '').toLowerCase();
            const barcode = ($(this).data('barcode') || '').toString().toLowerCase();
            const matches = name.includes(searchTerm) || barcode.includes(searchTerm);
            $(this).toggle(matches);
        });
    });
    
    // Filter by category
    $('.btn-category').click(function() {
        $('.btn-category').removeClass('active');
        $(this).addClass('active');
        
        const categoryId = $(this).data('category');
        $('.product-item').each(function() {
            const itemCategory = $(this).data('category');
            $(this).toggle(categoryId === itemCategory);
        });
    });
    
    // Show all products
    $('#showAllProducts').click(function() {
        $('.btn-category').removeClass('active');
        $('.product-item').show();
    });

    // Aroma modal search & filter
    $('#aromaSearchInput').on('input', filterAromaGrid);
    $('#aromaStockFilter').on('change', filterAromaGrid);
    filterAromaGrid(); // init count
    
    // Customer type change
    $('#customerType').change(function() {
        customerType = $(this).val();
        updateCartPrices();
    });
    
    // Customer select change
    $('#customerSelect').change(function() {
        const selected = $(this).find('option:selected');
        const phone = selected.data('phone');
        const email = selected.data('email');
        
        if (phone || email) {
            $('#customerPhone').text(phone || '-');
            $('#customerEmail').text(email || '-');
            $('.customer-details').show();
        } else {
            $('.customer-details').hide();
        }
    });
    
    // (Removed old newCustomerBtn click handler because it's now an inline function)
    
    // New customer form
    $('#newCustomerForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("customers.store") }}',
            method: 'POST',
            data: formData,
            headers: { 'Accept': 'application/json' },
            success: function(response) {
                // Add new customer to select with data attributes
                const newOption = new Option(response.name, response.id, false, true);
                $(newOption).attr('data-phone', response.phone || '');
                $(newOption).attr('data-email', response.email || '');
                $('#customerSelect').append(newOption).trigger('change');
                
                hideInlineNewCustomer();
                $('#newCustomerForm')[0].reset();
                
                Swal.fire('Berhasil', 'Pelanggan berhasil ditambahkan', 'success');
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Gagal menyimpan pelanggan. Silakan coba lagi.';
                Swal.fire('Gagal', msg, 'error');
            }
        });
    });
    
    // Calculate totals on input change
    $('#discount, #paidAmount').on('input', calculateTotals);
    $('#discountType, #taxToggle').change(calculateTotals);

    // Payment method toggle: tampilkan sub-field sesuai metode
    $('input[name="payment_method"]').on('change', function() {
        const val = $(this).val();
        $('#ewalletTypeGroup').toggle(val === 'ewallet');
        $('#transferTypeGroup').toggle(val === 'transfer');
        // Non-cash: jumlah bayar otomatis = total
        if (val !== 'cash') {
            const total = parseFloat($('#totalAmount').text().replace(/[^0-9]/g, '')) || 0;
            $('#paidAmount').val(total);
            calculateTotals();
        }
    });
    
    // Barcode scanner simulation
    $(document).keypress(function(e) {
        if (e.which === 13) { // Enter key
            const barcode = $('#productSearch').val();
            if (barcode.length >= 8) {
                scanBarcode(barcode);
                $('#productSearch').val('');
            }
        }
    });
});

// Produk yang sedang dipilih (pending tier selection)
let pendingProduct = null;
let selectedSize = '30ml';

function addToCart(productId) {
    const product = $(`.product-item[data-id="${productId}"]`);
    const stock = parseInt(product.data('stock'));

    if (stock === 0) {
        Swal.fire('Stok Habis', 'Produk ini tidak tersedia', 'warning');
        return;
    }

    // Simpan produk pending, lalu buka popup pilih tier
    pendingProduct = {
        id: productId,
        name: product.data('name'),
        stock: stock,
        barcode: product.data('barcode'),
        categoryId: parseInt(product.data('category')),
        tier: product.data('tier') || 'biasa',
        size: (product.data('size') || '').toString().toLowerCase(),
    };

    // Pindahkan modal ke body dulu, baru isi harga
    const $tierModal = $('#tierModal').appendTo('body');

    // Update nama produk
    $tierModal.find('#tierModalProductName').text(pendingProduct.name);

    // Reset ukuran ke 30ml saat modal dibuka
    $tierModal.find('.size-btn').removeClass('active btn-secondary').addClass('btn-outline-secondary');
    $tierModal.find('.size-btn[data-size="30ml"]').removeClass('btn-outline-secondary').addClass('btn-secondary active');
    selectedSize = '30ml';

    // Update harga sesuai ukuran default 30ml
    if (tierPrices && tierPrices['30ml']) {
        const prices = tierPrices['30ml'];
        $tierModal.find('#tierPricePremium').text('Rp ' + prices.premium.toLocaleString('id-ID'));
        $tierModal.find('#tierPriceSedang').text('Rp ' + prices.sedang.toLocaleString('id-ID'));
        $tierModal.find('#tierPriceBiasa').text('Rp ' + prices.biasa.toLocaleString('id-ID'));
    }

    $tierModal.modal('show');
}

function selectSize(size, btn) {
    selectedSize = size;
    // Update tombol aktif
    $('#sizeSelector .size-btn').removeClass('active btn-secondary').addClass('btn-outline-secondary');
    $(btn).removeClass('btn-outline-secondary').addClass('btn-secondary active');
    // Update harga sesuai ukuran baru
    if (tierPrices && tierPrices[size]) {
        const prices = tierPrices[size];
        $('#tierPricePremium').text('Rp ' + prices.premium.toLocaleString('id-ID'));
        $('#tierPriceSedang').text('Rp ' + prices.sedang.toLocaleString('id-ID'));
        $('#tierPriceBiasa').text('Rp ' + prices.biasa.toLocaleString('id-ID'));
    }
}

function addToCartWithTier(tier) {
    $('#tierModal').modal('hide');

    if (!pendingProduct) return;

    // Ambil harga sesuai ukuran yang dipilih user di modal & tier
    const sizeKey = ['30ml','50ml','100ml'].includes(selectedSize) ? selectedSize : '30ml';
    const prices  = tierPrices[sizeKey] || tierPrices['30ml'];
    const basePrice = prices[tier] || prices.biasa;

    const price = customerType === 'wholesale'
        ? Math.round(basePrice * 0.9)
        : basePrice;

    const isPremium = tier === 'premium';
    const isSedang  = tier === 'sedang';

    const existingIndex = cart.findIndex(i =>
        i.id === pendingProduct.id && !i.is_bonus_item && i.tier === tier && i.size === selectedSize
    );

    if (existingIndex >= 0) {
        if (cart[existingIndex].quantity >= pendingProduct.stock) {
            Swal.fire('Stok Tidak Cukup', 'Jumlah melebihi stok tersedia', 'warning');
            pendingProduct = null;
            return;
        }
        cart[existingIndex].quantity++;
    } else {
        cart.push({
            id: pendingProduct.id,
            name: pendingProduct.name,
            price: price,
            original_price: price,
            quantity: 1,
            stock: pendingProduct.stock,
            barcode: pendingProduct.barcode,
            tier: tier,
            size: selectedSize,
            is_premium: isPremium,
            is_sedang: isSedang,
            bonus_quantity: 0,
            is_bonus_item: false
        });
    }

    pendingProduct = null;
    saveCart();
    updateCartDisplay();
}

function _pushBonusItem(opt) {
    const existingIdx = cart.findIndex(i => i.id == opt.id && i.is_bonus_item);
    if (existingIdx >= 0) {
        if (cart[existingIdx].quantity < parseInt(opt.stock)) {
            cart[existingIdx].quantity++;
        }
    } else {
        cart.push({
            id: opt.id,
            name: opt.name,
            price: 0,
            original_price: tierPrices['30ml'] ? tierPrices['30ml'].biasa : 0,
            quantity: 1,
            stock: parseInt(opt.stock),
            barcode: opt.barcode || '',
            size: '20ml',
            tier: 'sedang',
            is_premium: false,
            is_sedang: true,
            bonus_quantity: 0,
            is_bonus_item: true,
            bonus_ml: 10
        });
    }
    saveCart();
    updateCartDisplay();
    updateBonusListPreview();
}

function updateBonusListPreview() {
    const bonusItems   = cart.filter(i => i.is_bonus_item);
    const premiumItems = cart.filter(i => i.is_premium && !i.is_bonus_item && !i.is_refill);
    const container    = $('#bonusListItems');
    container.empty();

    // Tampilkan warning real-time jika ada premium tanpa bonus
    if (premiumItems.length > 0 && bonusItems.length === 0) {
        $('#premiumBonusWarning').show();
    } else {
        $('#premiumBonusWarning').hide();
    }

    if (bonusItems.length === 0) {
        $('#bonusListPreview').hide();
        $('#autoBonusBadge').hide();
        return;
    }
    $('#bonusListPreview').show();
    bonusItems.forEach(item => {
        container.append(`<span class="badge badge-success mr-1 mb-1" style="font-size:0.72rem;">
            🎁 ${escapeHtml(item.name)} ×${item.quantity}
        </span>`);
    });
}

function removeFromCart(index) {
    cart.splice(index, 1);
    saveCart();
    updateCartDisplay();
}

function updateBonus(index, value) {
    cart[index].bonus_quantity = Math.max(0, parseInt(value) || 0);
    saveCart();
    // Don't re-render to avoid losing focus; just update the value in cart
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function updateCartDisplay() {
    const cartItems = $('#cartItems');
    cartItems.empty();
    
    if (cart.length === 0) {
        cartItems.html(`
            <tr>
                <td colspan="6" class="text-center text-muted cart-empty">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
                    Keranjang kosong
                </td>
            </tr>
        `);
        $('#cartCount').text('0 item');
        return;
    }
    
    let subtotal = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        const isBonusItem = item.is_bonus_item || false;
        const isPremium   = item.is_premium || false;
        const isSedang    = item.is_sedang  || false;
        const isRefill    = item.is_refill  || false;
        
        const row = `
            <tr class="${isRefill ? 'table-info' : (isPremium ? 'table-warning' : (isSedang ? 'table-secondary' : (isBonusItem ? 'table-success' : '')))}">
                <td>${index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="fas ${isRefill ? 'fa-fill-drip text-info' : (isBonusItem ? 'fa-gift text-success' : 'fa-wine-bottle')}"></i>
                        </div>
                        <div>
                            <small class="d-block font-weight-bold cart-prod-name">${escapeHtml(item.name)}</small>
                            ${isRefill    ? '<span class="badge badge-info cart-badge">Isi Ulang</span>' : ''}
                            ${isPremium   ? '<span class="badge badge-warning cart-badge">⭐ Premium</span>' : ''}
                            ${isSedang    ? '<span class="badge badge-secondary cart-badge">🥈 Sedang</span>' : ''}
                            ${(!isRefill && !isPremium && !isSedang && !isBonusItem) ? '<span class="badge badge-light border cart-badge" style="color:#555;">🏷️ Biasa</span>' : ''}
                            ${isBonusItem ? '<span class="badge badge-success cart-badge">🎁 Bonus Gratis</span>' : ''}
                        </div>
                    </div>
                </td>
                <td>
                    ${isRefill ?
                    `<div class="text-center font-weight-bold text-info" style="font-size:0.7rem;">${item.refill_volume_ml} ml</div>` :
                    (isBonusItem ? 
                    `<div class="text-center font-weight-bold" style="font-size:0.72rem;">${item.quantity}</div>` : 
                    `<div class="input-group input-group-sm flex-nowrap" style="width: 78px;">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary cart-qty-btn" type="button" onclick="updateQuantity(${index}, ${item.quantity - 1})">-</button>
                        </div>
                        <input type="text" class="form-control text-center px-1 cart-qty-input" value="${item.quantity}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary cart-qty-btn" type="button" onclick="updateQuantity(${index}, ${item.quantity + 1})">+</button>
                        </div>
                    </div>`)}
                </td>
                <td>
                    <div class="text-right cart-price-row">
                        ${isRefill ?
                        `<div><small class="text-muted" style="font-size:0.62rem;">Rp ${item.price_per_ml.toLocaleString('id-ID')}/ml × ${item.refill_volume_ml} ml</small></div>
                        <div class="font-weight-bold text-info cart-price-total">Rp ${itemTotal.toLocaleString('id-ID')}</div>` :
                        (isBonusItem ? `<div><del class="text-muted" style="font-size:0.65rem;">Rp ${item.original_price.toLocaleString('id-ID')}</del></div>
                        <div class="text-success font-weight-bold cart-price-total">Rp 0</div>` : 
                        `<div class="cart-price-total">Rp ${item.price.toLocaleString('id-ID')}</div>
                        <small class="text-muted cart-price-total">Total: Rp ${itemTotal.toLocaleString('id-ID')}</small>`)}
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm cart-remove-btn" 
                            onclick="removeFromCart(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        cartItems.append(row);
    });
    
    $('#cartCount').text(`${cart.length} item${cart.length > 1 ? 's' : ''}`);
    $('#mobileCartCount').text(cart.length);
    $('#mobileTabCartCount').text(cart.length);
    $('#subtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));
    calculateTotals();
    updateBonusListPreview();
}

function addRefillToCart(productId, btn) {
    const item = $(`.refill-item[data-id="${productId}"]`);
    const bulkStock = parseFloat(item.data('bulk-stock'));
    const pricePerMl = parseFloat(item.data('price-per-ml'));
    const name = item.data('name');

    if (bulkStock <= 0) {
        Swal.fire('Stok Habis', 'Stok isi ulang untuk produk ini sudah habis', 'warning');
        return;
    }

    const volumeInput = $(btn).closest('.card-body').find('.refill-volume-input');
    const volumeMl = parseFloat(volumeInput.val()) || 50;

    if (volumeMl < 10) {
        Swal.fire('Volume Minimal', 'Volume minimal 10 ml', 'warning');
        return;
    }

    if (volumeMl > bulkStock) {
        Swal.fire('Stok Tidak Cukup', 'Stok bulk hanya ' + bulkStock.toLocaleString('id-ID') + ' ml tersedia', 'warning');
        return;
    }

    const refillPrice = pricePerMl * volumeMl;

    cart.push({
        id: productId,
        name: name,
        price: refillPrice,
        original_price: refillPrice,
        quantity: 1,
        stock: bulkStock,
        is_refill: true,
        refill_volume_ml: volumeMl,
        price_per_ml: pricePerMl,
        barcode: item.data('barcode'),
        is_premium: false,
        bonus_quantity: 0,
        is_bonus_item: false
    });

    saveCart();
    updateCartDisplay();
}

function updateQuantity(index, newQuantity) {
    const item = cart[index];

    if (item.is_refill) {
        // For refill items, quantity represents volume — change via dedicated function
        return;
    }

    newQuantity = parseInt(newQuantity);
    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > item.stock) {
        Swal.fire('Stok Tidak Cukup', 'Jumlah melebihi stok tersedia', 'warning');
        newQuantity = item.stock;
    }
    
    item.quantity = newQuantity;
    saveCart();
    updateCartDisplay();
}

function toggleBonus(index) {
    cart[index].is_bonus_item = !cart[index].is_bonus_item;
    updateCartPrices();
}

function openAromaModal() {
    filterAromaGrid();
    $('#aromaModal').appendTo('body').modal('show');
}

function closeAromaModal() {
    $('#aromaModal').modal('hide');
}

function filterAromaGrid() {
    const search      = ($('#aromaSearchInput').val() || '').toLowerCase().trim();
    const stockFilter = $('#aromaStockFilter').val() || 'all';
    let visible = 0;

    $('.aroma-card-item').each(function() {
        const name  = $(this).data('name').toString().toLowerCase();
        const stock = parseInt($(this).data('stock')) || 0;

        const matchSearch = !search || name.includes(search);
        const matchStock  = stockFilter === 'all'
            || (stockFilter === 'available' && stock > 0)
            || (stockFilter === 'empty'     && stock === 0);

        if (matchSearch && matchStock) {
            $(this).show();
            visible++;
        } else {
            $(this).hide();
        }
    });

    $('#aromaResultCount').text(visible + ' aroma ditemukan');
}

function selectAromaBonus(productId, name, price, stock, barcode) {
    _pushBonusItem({ id: productId, name: name, price: price, stock: stock, barcode: barcode });
    $('#aromaModal').modal('hide');

    // Toast konfirmasi
    const toast = $(`<div style="position:fixed;bottom:80px;right:15px;z-index:9999;max-width:230px;">
        <div class="alert alert-success py-2 px-3 shadow" style="font-size:0.78rem;">
            <i class="fas fa-gift"></i> <b>${escapeHtml(name)}</b> ditambahkan sebagai bonus!
        </div>
    </div>`);
    $('body').append(toast);
    setTimeout(() => toast.fadeOut(400, function(){ $(this).remove(); }), 2500);
}

function addParfumBonus() {
    const select = $('#bonusParfumSelect');
    const option = select.find('option:selected');
    const productId = select.val();

    if (!productId) {
        Swal.fire('Pilih Parfum', 'Silakan pilih parfum bonus terlebih dahulu', 'warning');
        return;
    }

    const stock = parseInt(option.data('stock'));
    if (stock === 0) {
        Swal.fire('Stok Habis', 'Stok parfum bonus ini kosong', 'warning');
        return;
    }

    _pushBonusItem({
        id: productId,
        name: option.data('name'),
        price: option.data('price'),
        stock: stock,
        barcode: option.data('barcode') || ''
    });

    select.val('').trigger('change');
    updateBonusListPreview();
}

function showInlineNewCustomer() {
    const isMobile = window.innerWidth < 768;
    
    if (isMobile) {
        $('#middleColumn').insertAfter('#leftColumn').fadeIn(300);
        $('#leftColumn').hide();
        $('#newCustomerBtnDisplay').slideUp(200);
    } else {
        $('#leftColumn').removeClass('col-md-9').addClass('col-md-6');
        $('.product-item').removeClass('col-xl-2 col-lg-3 col-md-4 col-sm-6')
                         .addClass('col-xl-4 col-lg-6 col-md-12 col-sm-12');
        setTimeout(() => {
            $('#middleColumn').fadeIn(300);
        }, 200);
        $('#newCustomerBtnDisplay').slideUp(200);
    }
}

function hideInlineNewCustomer() {
    const isMobile = window.innerWidth < 768;
    
    if (isMobile) {
        $('#middleColumn').fadeOut(200, function() {
            $('#leftColumn').show();
            $('#newCustomerBtnDisplay').slideDown(200);
            $('.container-fluid > .row').append($('#middleColumn'));
        });
    } else {
        $('#middleColumn').fadeOut(200, function() {
            $('#leftColumn').removeClass('col-md-6').addClass('col-md-9');
            $('.product-item').removeClass('col-xl-4 col-lg-6 col-md-12 col-sm-12')
                             .addClass('col-xl-2 col-lg-3 col-md-4 col-sm-6');
            $('#newCustomerBtnDisplay').slideDown(200);
        });
    }
}

function updateCartPrices() {
    cart.forEach(item => {
        if (item.is_bonus_item) {
            item.price = 0;
        } else if (item.is_refill) {
            // Refill harga tetap dari volume
        } else {
            const rawSize = (item.size || '').replace(/\s+/g, '').toLowerCase();
            const sizeKey = ['30ml','50ml','100ml'].includes(rawSize) ? rawSize : '30ml';
            const prices  = tierPrices[sizeKey] || tierPrices['30ml'];
            const basePrice = prices[item.tier] || prices.biasa;
            item.price = customerType === 'wholesale'
                ? Math.round(basePrice * 0.9)
                : basePrice;
        }
    });
    saveCart();
    updateCartDisplay();
}

function calculateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountInput = $('#discount').val();
    const discountType = $('#discountType').val();
    const taxEnabled = $('#taxToggle').is(':checked');
    
    let discount = 0;
    if (discountType === 'percent') {
        discount = subtotal * (parseFloat(discountInput) || 0) / 100;
    } else {
        discount = parseFloat(discountInput) || 0;
    }
    
    let tax = 0;
    if (taxEnabled) {
        tax = Math.round((subtotal - discount) * 0.1); // 10% PPN rounded
    }
    
    const total = Math.max(0, subtotal - discount + tax);
    
    $('#tax').text('Rp ' + tax.toLocaleString('id-ID'));
    $('#totalAmount').text('Rp ' + Math.round(total).toLocaleString('id-ID'));
    
    // Calculate change
    const paid = parseFloat($('#paidAmount').val()) || 0;
    const change = paid - total;
    
    if (change >= 0) {
        $('#changeAmount').val('Rp ' + Math.round(change).toLocaleString('id-ID'));
        $('#changeAmount').removeClass('text-danger').addClass('text-success');
    } else {
        $('#changeAmount').val('Kurang: Rp ' + Math.round(Math.abs(change)).toLocaleString('id-ID'));
        $('#changeAmount').removeClass('text-success').addClass('text-danger');
    }
}

function setPaidAmount(amount) {
    $('#paidAmount').val(amount).trigger('input');
}

function processPayment() {
    if (cart.length === 0) {
        Swal.fire('Keranjang Kosong', 'Tambahkan produk terlebih dahulu', 'warning');
        return;
    }

    // Validasi: setiap item premium wajib ada bonus item di cart
    const premiumItems = cart.filter(i => i.is_premium && !i.is_bonus_item && !i.is_refill);
    if (premiumItems.length > 0) {
        const bonusItems = cart.filter(i => i.is_bonus_item);
        if (bonusItems.length === 0) {
            const names = premiumItems.map(i => '⭐ ' + escapeHtml(i.name)).join('<br>');
            Swal.fire({
                title: 'Bonus Belum Dipilih!',
                html: `Produk berikut adalah <strong>Premium</strong> dan wajib mendapat bonus aroma gratis:<br><br>${names}<br><br>
                       Gulir ke bagian <strong>🎁 Parfum Bonus</strong> untuk memilih aroma bonus terlebih dahulu.`,
                icon: 'warning',
                confirmButtonText: 'Pilih Bonus Sekarang',
                confirmButtonColor: '#f0ad4e',
            }).then(() => {
                // Scroll ke bonusSection
                document.getElementById('bonusSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
                $('#bonusSection').addClass('border-danger').css('box-shadow','0 0 0 3px rgba(220,53,69,0.35)');
                setTimeout(() => {
                    $('#bonusSection').removeClass('border-danger').css('box-shadow','');
                }, 2500);
            });
            return;
        }
    }

    const total = parseFloat($('#totalAmount').text().replace(/[^0-9]/g, ''));
    const paid  = parseFloat($('#paidAmount').val()) || 0;
    const paymentMethod = $('input[name="payment_method"]:checked').val();

    if (!paymentMethod) {
        Swal.fire('Metode Belum Dipilih', 'Silakan pilih metode pembayaran terlebih dahulu', 'warning');
        return;
    }

    if (paid < total) {
        Swal.fire('Pembayaran Kurang', `Jumlah bayar (Rp ${paid.toLocaleString('id-ID')}) kurang dari total (Rp ${total.toLocaleString('id-ID')})`, 'warning');
        return;
    }

    // Kumpulkan ewallet_type dan transfer_type jika relevan
    const ewalletType  = paymentMethod === 'ewallet'  ? ($('input[name="ewallet_type"]:checked').val() || null)  : null;
    const transferType = paymentMethod === 'transfer' ? ($('input[name="transfer_type"]:checked').val() || null) : null;

    // Collect transaction data
    const transactionData = {
        customer_id:    $('#customerSelect').val() || null,
        customer_type:  $('#customerType').val(),
        items: cart.map(item => ({
            product_id:      item.id,
            quantity:        item.is_refill ? 1 : item.quantity,
            price:           item.price,
            bonus_quantity:  item.bonus_quantity || 0,
            bonus_note:      item.is_bonus_item
                                ? 'Bonus 20ml Gratis: ' + escapeHtml(item.name)
                                : (item.is_premium && item.bonus_quantity > 0
                                    ? 'Bonus 20ml Sedang x' + item.bonus_quantity + ' untuk ' + escapeHtml(item.name)
                                    : null),
            refill_volume_ml: item.is_refill ? item.refill_volume_ml : null,
            is_bonus_item:   item.is_bonus_item || false,
            bonus_ml:        item.is_bonus_item ? 20 : 0,
            tier:            item.tier || 'biasa',
        })),
        discount_amount: parseFloat($('#discount').val()) || 0,
        discount_type:   $('#discountType').val(),
        tax_enabled:     $('#taxToggle').is(':checked'),
        payment_method:  paymentMethod,
        ewallet_type:    ewalletType,
        transfer_type:   transferType,
        paid_amount:     paid,
        notes:           $('#transactionNotes').val(),
        _token:          '{{ csrf_token() }}'
    };
    
    // Send to server
    $.ajax({
        url: '{{ route("transactions.store") }}',
        method: 'POST',
        data: JSON.stringify(transactionData),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Update stock display on product cards for each sold item
            updateStockAfterTransaction();

            const totalVal = parseFloat(response.total || total);
            const changeVal = parseFloat(response.change || 0);
            const invoiceNum = response.invoice_number;
            const customerPhone = $('#customerSelect option:selected').data('phone') || '';

            // Show success message
            Swal.fire({
                title: 'Transaksi Berhasil!',
                html: `
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h5>Invoice: ${invoiceNum}</h5>
                        <p class="mb-1">Total: <strong>Rp ${totalVal.toLocaleString('id-ID')}</strong></p>
                        <p>Kembalian: Rp ${changeVal.toLocaleString('id-ID')}</p>
                        
                        <div class="mt-3">
                            <div class="row no-gutters">
                                <div class="col-6 pr-1">
                                    <button class="btn btn-success btn-block mb-2 btn-sm" onclick="sendWhatsApp('${invoiceNum}', '${totalVal}', '${customerPhone}')">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button class="btn btn-info btn-block mb-2 btn-sm" onclick="shareSocial('${invoiceNum}', '${totalVal}')">
                                        <i class="fas fa-share-alt"></i> Lainnya
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-primary-apms btn-block mb-2" onclick="printReceipt('${response.transaction_id}')">
                                <i class="fas fa-print"></i> Cetak Struk Fisik
                            </button>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Transaksi Baru',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                clearCart();
            });
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan saat memproses transaksi';
            Swal.fire('Gagal', errorMsg, 'error');
        }
    });
}

function updateStockAfterTransaction() {
    // Porsi ml per tier per ukuran (harus sinkron dengan backend)
    const porsiMap = {
        '30ml':  { premium: 20, sedang: 15, biasa: 10 },
        '50ml':  { premium: 33, sedang: 25, biasa: 17 },
        '100ml': { premium: 65, sedang: 50, biasa: 33 },
    };

    cart.forEach(function(item) {
        if (item.is_bonus_item) return; // bonus item tidak update kartu sendiri

        const productId = item.id;
        const tier      = item.tier || 'biasa';
        const size      = item.size || '30ml';
        const qty       = item.quantity || 1;

        let deductMl = 0;
        if (item.is_refill) {
            deductMl = parseFloat(item.refill_volume_ml || 0);
        } else if (item.is_bonus_item) {
            // Bonus gratis: botol 20ml kualitas sedang = 10ml bibit
            deductMl = 10 * qty;
        } else {
            const sizeKey  = size.replace(/\s+/g, '').toLowerCase();
            const tierMap  = porsiMap[sizeKey] || porsiMap['30ml'];
            deductMl = (tierMap[tier] || tierMap['biasa']) * qty;
        }

        if (deductMl <= 0) return;

        // Update kartu produk reguler
        const $card = $('[data-id="' + productId + '"].product-item');
        if ($card.length) {
            const oldStock  = parseFloat($card.data('stock') || 0);
            const newStock  = Math.max(0, oldStock - deductMl);
            $card.data('stock', newStock);

            // Update label stok di kartu
            const $stockLabel = $card.find('.product-footer-stock');
            if ($stockLabel.length) {
                const porsiMl = porsiMap[size.replace(/\s+/g,'').toLowerCase()]?.[tier] || 10;
                if (newStock < porsiMl) {
                    $stockLabel.removeClass('stock-oke stock-sedikit').addClass('stock-habis').text('Habis');
                    $card.addClass('opacity-50');
                } else if (newStock < porsiMl * 3) {
                    const liter = (newStock / 1000).toFixed(1);
                    const label = newStock >= 1000 ? newStock + ' ml / ' + liter + ' L' : newStock + ' ml';
                    $stockLabel.removeClass('stock-oke stock-habis').addClass('stock-sedikit').text(label);
                } else {
                    const liter = (newStock / 1000).toFixed(1);
                    const label = newStock >= 1000 ? newStock + ' ml / ' + liter + ' L' : newStock + ' ml';
                    $stockLabel.removeClass('stock-sedikit stock-habis').addClass('stock-oke').text(label);
                }
            }
        }
    });
}

function printReceipt(transactionId) {
    window.open(`/transactions/${transactionId}/receipt?print=1`, '_blank');
}

function initWhatsAppShare(invoiceNum, total) {
    const phone = $('#customerSelect option:selected').data('phone') || '';
    sendWhatsApp(invoiceNum, total, phone);
}

function sendWhatsApp(invoiceNum, total, phone) {
    // Clean up phone string
    let phoneStr = String(phone).trim();
    if (phoneStr === 'undefined' || phoneStr === 'null' || !phoneStr || phoneStr === 'null') {
        phoneStr = '';
    }

    if (phoneStr.length < 5) {
        Swal.fire({
            title: 'Kirim via WhatsApp',
            text: 'Pelanggan ini belum memiliki nomor WhatsApp terdaftar.',
            input: 'text',
            inputLabel: 'Masukkan nomor WhatsApp (Contoh: 0812...)',
            inputPlaceholder: '0812XXXXXXXX',
            inputAttributes: {
                inputmode: 'numeric',
                pattern: '[0-9]*',
                autocomplete: 'tel'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745',
            focusConfirm: false,
            didOpen: () => {
                const input = Swal.getInput();
                if (input) {
                    input.focus();
                    // Filter non-numeric on input (tidak block event)
                    input.addEventListener('input', function() {
                        const pos = this.selectionStart;
                        const cleaned = this.value.replace(/[^0-9]/g, '');
                        if (this.value !== cleaned) {
                            this.value = cleaned;
                            this.setSelectionRange(pos - 1, pos - 1);
                        }
                    });
                    // Blokir karakter non-angka kecuali control keys
                    input.addEventListener('keydown', function(e) {
                        const allowed = ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Home','End'];
                        if (!allowed.includes(e.key) && !/^[0-9]$/.test(e.key)) {
                            e.preventDefault();
                        }
                    });
                    // Handle paste — strip non-numeric
                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const pasted = (e.clipboardData || window.clipboardData).getData('text');
                        const cleaned = pasted.replace(/[^0-9]/g, '');
                        document.execCommand('insertText', false, cleaned);
                    });
                }
            },
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Nomor WhatsApp tidak boleh kosong!';
                }
                if (value.replace(/[^0-9]/g, '').length < 10) {
                    return 'Nomor tidak valid (Minimal 10 digit)!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                executeWhatsAppSend(invoiceNum, total, result.value.replace(/[^0-9]/g, ''));
            }
        });
    } else {
        executeWhatsAppSend(invoiceNum, total, phoneStr);
    }
}

function executeWhatsAppSend(invoiceNum, total, phone) {
    // Format phone: ensure starts with 62
    let formattedPhone = phone.replace(/[^0-9]/g, '');
    if (formattedPhone.startsWith('0')) {
        formattedPhone = '62' + formattedPhone.slice(1);
    } else if (!formattedPhone.startsWith('62')) {
        formattedPhone = '62' + formattedPhone;
    }

    const message = `*ASHAR GROSIR PARFUM*\n` +
                    `--------------------------\n` +
                    `Terima kasih telah berbelanja!\n` +
                    `No. Invoice: *${invoiceNum}*\n` +
                    `Total Bayar: *Rp ${parseFloat(total).toLocaleString('id-ID')}*\n` +
                    `--------------------------\n` +
                    `Lihat struk digital Anda di:\n` +
                    `${window.location.origin}/view-invoice/${invoiceNum}\n\n` +
                    `Layanan Konsumen: 081251026345\n` +
                    `Website: ashargrosirparfum.com`;

    const waLink = `https://wa.me/${formattedPhone}?text=${encodeURIComponent(message)}`;
    window.open(waLink, '_blank');
}

function shareSocial(invoiceNum, total) {
    const url = `${window.location.origin}/view-invoice/${invoiceNum}`;
    const text = `Struk Digital Ashar Grosir Parfum\nInvoice: ${invoiceNum}\nTotal: Rp ${parseFloat(total).toLocaleString('id-ID')}`;
    
    const shareData = {
        title: 'Struk Ashar Grosir Parfum',
        text: text,
        url: url
    };

    if (navigator.share) {
        navigator.share(shareData).catch((err) => {
            // If user cancels or error occurs, fallback to clipboard
            copyToClipboard(text, url);
        });
    } else {
        copyToClipboard(text, url);
    }
}

function copyToClipboard(text, url) {
    const fullText = `${text}\nLink Struk: ${url}`;
    navigator.clipboard.writeText(fullText).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Link Disalin',
            text: 'Link struk telah disalin. Anda bisa menempelnya di Telegram, Email, atau media sosial lainnya.',
            confirmButtonColor: '#17a2b8'
        });
    }).catch(err => {
        Swal.fire('Gagal', 'Gagal menyalin link secara otomatis.', 'error');
    });
}

function clearCart() {
    cart = [];
    saveCart();
    updateCartDisplay();
    $('#paidAmount').val('');
    $('#discount').val(0);
    $('#transactionNotes').val('');
}

function saveCart() {
    localStorage.setItem('apms_cart', JSON.stringify(cart));
}

function loadCart() {
    const savedCart = localStorage.getItem('apms_cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        // Sanitize: pastikan bonus item selalu price 0, hindari data lama dari localStorage
        cart.forEach(item => {
            if (item.is_bonus_item) item.price = 0;
        });
        updateCartDisplay();
    }
}

// ── Barcode Scanner ──
function openScanner() {
    $('#scanPreview').hide();
    $('#uploadArea').show();
    $('#barcodeFileInput').val('');
    $('#scannerModal').modal('show');
}

$('#scannerModal').on('hidden.bs.modal', function () {
    $('#scanPreview').hide();
    $('#uploadArea').show();
    $('#barcodeFileInput').val('');
    $('#scanProgress').hide();
});

function decodeBarcodeImage(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const reader = new FileReader();
    var decodeTimer = setTimeout(function() {
        $('#scanProgress').html('<small class="text-danger">Memuat library scanner... pastikan koneksi internet aktif.</small>');
    }, 5000);

    reader.onload = function(e) {
        document.getElementById('uploadArea').style.display = 'none';
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('scanPreview').style.display = 'block';
        document.getElementById('scanProgress').style.display = 'block';
        document.getElementById('scanProgress').innerHTML = '<small class="text-muted"><i class="fas fa-spinner fa-spin"></i> Mendeteksi barcode...</small>';

        try {
            if (typeof Html5Qrcode === 'undefined') {
                clearTimeout(decodeTimer);
                $('#scanProgress').hide();
                Swal.fire('Library Error', 'Library scanner gagal dimuat. Periksa koneksi internet atau refresh halaman.', 'error');
                resetScanUI();
                return;
            }
            const scanner = new Html5Qrcode("scanPreview");
            scanner.decodeFileInBarcode(file, false, function(decodedText) {
                clearTimeout(decodeTimer);
                scanner.clear();
                $('#scanProgress').hide();
                $('#scannerModal').modal('hide');
                $('#productSearch').val(decodedText);
                scanBarcode(decodedText);
            }, function() {
                clearTimeout(decodeTimer);
                scanner.clear();
                $('#scanProgress').hide();
                Swal.fire('Barcode Tidak Terdeteksi', 'Gambar tidak mengandung barcode/QR yang valid. Coba upload gambar lain atau ketik manual.', 'warning');
                resetScanUI();
            });
        } catch(e) {
            clearTimeout(decodeTimer);
            $('#scanProgress').hide();
            Swal.fire('Error', 'Gagal memproses gambar: ' + e.message, 'error');
            resetScanUI();
        }
    };
    reader.readAsDataURL(file);
}

function resetScanUI() {
    document.getElementById('uploadArea').style.display = 'flex';
    document.getElementById('scanPreview').style.display = 'none';
    document.getElementById('barcodeFileInput').value = '';
}

function manualBarcodeScan() {
    const val = $('#manualBarcode').val().trim();
    if (val.length < 3) { Swal.fire('Input', 'Masukkan barcode yang valid.', 'warning'); return; }
    $('#scannerModal').modal('hide');
    $('#manualBarcode').val('');
    scanBarcode(val);
}

$('#manualBarcode').on('keypress', function(e) {
    if (e.which === 13) manualBarcodeScan();
});

function onScanSuccess(decodedText) {
    stopScanner();
    $('#scannerModal').modal('hide');
    $('#productSearch').val(decodedText);
    scanBarcode(decodedText);
}

function switchMobileTab(tab) {
    if (window.innerWidth >= 768) return;
    if (tab === 'cart') {
        $('#mobileTabCart').button('toggle');
        $('body').removeClass('pos-tab-products-active').addClass('pos-tab-cart-active');
    } else {
        $('#mobileTabProducts').button('toggle');
        $('body').removeClass('pos-tab-cart-active').addClass('pos-tab-products-active');
    }
}

$(document).on('change', 'input[name="mobilePosTab"]', function() {
    const val = $(this).val();
    switchMobileTab(val);
});

function scanBarcode(barcode) {
    const product = $('.product-item').filter(function() {
        return $(this).data('barcode') === barcode;
    }).first();

    if (product.length) {
        $('#productSearch').val('');
        addToCart(product.data('id'));
    } else {
        $.get('/api/products/search', { q: barcode })
            .done(function(products) {
                if (products.length > 0) {
                    const p = products[0];
                    const isDisabled = p.stock === 0;
                    const card = $('<div>', {
                        class: 'col-xl-2 col-lg-3 col-md-4 col-6 mb-2 mb-md-3 product-item',
                        'data-id': p.id, 'data-name': p.name, 'data-price': p.price,
                        'data-wholesale': p.price, 'data-stock': p.stock,
                        'data-barcode': p.barcode, 'data-category': ''
                    }).append(
                        $('<div>', {
                            class: 'card product-card h-100' + (isDisabled ? ' bg-light' : ''),
                            onclick: isDisabled ? '' : 'addToCart(' + p.id + ')'
                        }).append(
                            $('<div>', { class: 'card-body text-center p-2' }).append(
                                $('<div>', { class: 'bg-light d-flex align-items-center justify-content-center mb-1 product-img-placeholder' }).append(
                                    $('<i>', { class: 'fas fa-wine-bottle fa-2x text-muted' })
                                ),
                                $('<h6>', { class: 'mb-1 product-name' }).text(p.name),
                                $('<div>', { class: 'product-meta' }).append(
                                    $('<strong>', { class: 'text-primary product-price d-block mt-1' }).text('Rp ' + Number(p.price).toLocaleString('id-ID')),
                                    $('<div>', { class: 'mt-1' }).append(
                                        p.stock === 0
                                            ? $('<span>', { class: 'badge badge-danger' }).text('Habis')
                                            : $('<span>', { class: 'badge badge-success' }).text(p.stock)
                                    )
                                )
                            )
                        )
                    );
                    $('#productGrid').prepend(card);
                    addToCart(p.id);
                    $('#productSearch').val('');
                } else {
                    Swal.fire('Tidak Ditemukan', 'Produk dengan barcode ' + barcode + ' tidak ada.', 'warning');
                }
            }).fail(function() {
                Swal.fire('Error', 'Gagal mencari produk.', 'error');
            });
    }
}
</script>
@endpush