@extends('layouts.app')

@section('title', 'Kasir - APMS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Column: Product Selection -->
        <div class="col-md-8 col-12" id="leftColumn" style="transition: all 0.3s ease;">
            <!-- Product Categories - Horizontal scroll on mobile -->
            <div class="card card-apms mb-3">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Kategori Produk</h3>
                </div>
                <div class="card-body">
                    <div class="category-scroll d-flex flex-nowrap overflow-auto pb-2">
                        @foreach($categories as $category)
                        @php $catColor = preg_match('/^#[0-9a-fA-F]{6}$/', $category->color) ? $category->color : '#FF6B35'; @endphp
                        <div class="flex-shrink-0 mr-2">
                            <button class="btn btn-category btn-sm py-2 px-3" 
                                    data-category="{{ $category->id }}"
                                    style="background-color: {{ $catColor }}; color: white; white-space: nowrap; min-width: fit-content;">
                                {{ $category->name }}
                            </button>
                        </div>
                        @endforeach
                        <div class="flex-shrink-0">
                            <button class="btn btn-secondary btn-sm py-2 px-3" id="showAllProducts" style="white-space: nowrap;">
                                Semua
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Search - Full width on mobile -->
            <div class="card card-apms mb-3">
                <div class="card-header d-flex align-items-center gap-2">
                    <h3 class="card-title mb-0 text-nowrap">Daftar Produk</h3>
                    <div class="ml-auto d-flex align-items-center" style="min-width:0;flex:1;max-width:400px;">
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="text" id="productSearch" class="form-control" 
                                   placeholder="Cari produk atau scan barcode...">
                            <div class="input-group-append">
                                <button class="btn btn-primary-apms" type="button" onclick="openScanner()" title="Scan Barcode">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Product Type Tabs --}}
                <div class="card-header bg-light py-2 border-top">
                    <ul class="nav nav-pills nav-fill mb-0" id="productTypeTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="tab-regular" data-toggle="tab" href="#productsRegular" role="tab" style="color:#ff6b35;">
                                <i class="fas fa-box mr-1"></i> Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" id="tab-refill" data-toggle="tab" href="#productsRefill" role="tab" style="color:#17a2b8;">
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
                                    $currentStock = $inventory ? $inventory->current_stock : 0;
                                    $disabled = $currentStock == 0;
                                @endphp
                                <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2 mb-md-3 product-item" 
                                     data-id="{{ $product->id }}"
                                     data-category="{{ $product->product_category_id }}"
                                     data-name="{{ $product->name }}"
                                     data-price="{{ $product->selling_price }}"
                                     data-wholesale="{{ $product->wholesale_price }}"
                                     data-stock="{{ $currentStock }}"
                                     data-barcode="{{ $product->barcode }}">
                                    <div class="card product-card {{ $disabled ? 'bg-light' : '' }} h-100"
                                         onclick="{{ !$disabled ? 'addToCart(' . $product->id . ')' : '' }}">
                                        <div class="card-body text-center p-2">
                                            @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->name }}"
                                                 class="img-fluid mb-1 product-img">
                                            @else
                                            <div class="bg-light d-flex align-items-center justify-content-center mb-1 product-img-placeholder">
                                                <i class="fas fa-wine-bottle fa-2x text-muted"></i>
                                            </div>
                                            @endif
                                            
                                            <h6 class="mb-1 product-name">{{ $product->name }}</h6>
                                            <div class="product-meta">
                                                <small class="text-muted d-block">{{ $product->size }}</small>
                                                <strong class="text-primary product-price d-block mt-1">
                                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                                </strong>
                                                <div class="mt-1">
                                                    @if($currentStock == 0)
                                                        <span class="badge badge-danger">Habis</span>
                                                    @elseif($currentStock < 10)
                                                        <span class="badge badge-warning">Sisa {{ $currentStock }}</span>
                                                    @else
                                                        <span class="badge badge-success">{{ $currentStock }}</span>
                                                    @endif
                                                </div>
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
                                    $bulkStock = $inventory ? $inventory->bulk_stock_ml : 0;
                                @endphp
                                <div class="col-xl-4 col-lg-6 col-md-6 col-12 mb-2 mb-md-3 refill-item" 
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->name }}"
                                     data-price-per-ml="{{ $product->refill_price_per_ml ?? 0 }}"
                                     data-bulk-stock="{{ $bulkStock }}"
                                     data-barcode="{{ $product->barcode }}">
                                    <div class="card product-card h-100 border-info">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width:50px;height:50px;object-fit:cover;" class="rounded">
                                                    @else
                                                    <div class="bg-info-light d-flex align-items-center justify-content-center rounded" style="width:50px;height:50px;">
                                                        <i class="fas fa-fill-drip fa-lg text-info"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 min-width-0">
                                                    <h6 class="mb-0 font-weight-bold text-info">{{ $product->name }}</h6>
                                                    <small class="text-muted d-block">{{ $product->size }}</small>
                                                    <strong class="text-info">
                                                        Rp {{ number_format($product->refill_price_per_ml ?? 0, 0, ',', '.') }}/ml
                                                    </strong>
                                                    <div class="mt-1">
                                                        @if($bulkStock <= 0)
                                                            <span class="badge badge-danger">Stok Habis</span>
                                                        @elseif($bulkStock < 500)
                                                            <span class="badge badge-warning">Sisa {{ number_format($bulkStock) }} ml</span>
                                                        @else
                                                            <span class="badge badge-info">{{ number_format($bulkStock) }} ml</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 d-flex align-items-center">
                                                <div class="input-group input-group-sm mr-2" style="max-width:130px;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-info text-white">ml</span>
                                                    </div>
                                                    <input type="number" class="form-control refill-volume-input" value="50" min="10" max="{{ $bulkStock }}">
                                                </div>
                                                <button class="btn btn-info btn-sm flex-shrink-0" onclick="addRefillToCart({{ $product->id }}, this)">
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
        <div class="col-md-4 right-panel" id="middleColumn" style="display: none;">
            <div class="card card-apms mb-3 shadow-sm" style="border-top: 4px solid #ff6b35;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <h3 class="card-title text-primary-apms m-0" style="font-size:1rem;"><i class="fas fa-user-plus"></i> Tambah Pelanggan</h3>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="hideInlineNewCustomer()"><i class="fas fa-times"></i></button>
                </div>
                <div class="card-body p-2">
                    <form id="newCustomerForm">
                        <input type="hidden" name="is_active" value="1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label class="mb-0"><small>NIK (Sesuai KTP)</small></label>
                                    <input type="text" class="form-control form-control-sm" name="nik" placeholder="16 digit NIK">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label class="mb-0"><small>Nama Lengkap *</small></label>
                                    <input type="text" class="form-control form-control-sm" name="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="mb-0"><small>Jenis Kelamin</small></label>
                                    <select class="form-control form-control-sm" name="gender">
                                        <option value="">-- Pilih --</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="mb-0"><small>Tanggal Lahir</small></label>
                                    <input type="date" class="form-control form-control-sm" name="birth_date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="mb-0"><small>Nomor Telepon</small></label>
                                    <input type="text" class="form-control form-control-sm" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label class="mb-0"><small>Tipe</small></label>
                                    <select class="form-control form-control-sm" name="type">
                                        <option value="retail">Retail</option>
                                        <option value="wholesale">Grosir</option>
                                        <option value="vip">VIP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label class="mb-0"><small>Email</small></label>
                            <input type="email" class="form-control form-control-sm" name="email">
                        </div>
                        <div class="form-group mb-3">
                            <label class="mb-0"><small>Alamat Lengkap</small></label>
                            <textarea class="form-control form-control-sm" name="address" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary-apms btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan Pelanggan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Cart & Payment -->
        <div class="col-md-4 col-12 right-panel" id="rightColumn" style="transition: all 0.3s ease;">
            <!-- Customer Info -->
            <div class="card card-apms mb-3">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pelanggan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe Pelanggan</label>
                                <select class="form-control" id="customerType">
                                    <option value="retail">Retail</option>
                                    <option value="wholesale">Wholesale</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pilih Pelanggan</label>
                                <select class="form-control select2" id="customerSelect">
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
                        </div>
                    </div>
                    <div class="customer-details" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info p-2">
                                    <small>
                                        <i class="fas fa-phone"></i> <span id="customerPhone"></span><br>
                                        <i class="fas fa-envelope"></i> <span id="customerEmail"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-outline-primary btn-block" onclick="showInlineNewCustomer()" id="newCustomerBtnDisplay">
                                <i class="fas fa-user-plus"></i> Pelanggan Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cart -->
            <div class="card card-apms mb-3" id="cartSection">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Keranjang Belanja</h3>
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
            <div class="card card-apms mb-3">
                <div class="card-header bg-light py-2">
                    <h3 class="card-title text-success m-0" style="font-size: 1rem;"><i class="fas fa-gift"></i> Tambah Bonus Transaksi</h3>
                </div>
                <div class="card-body p-2">
                    <div class="row align-items-center">
                        <div class="col-4 pr-1">
                            <select class="form-control form-control-sm" id="bonusTypeSelect" onchange="toggleBonusInput()">
                                <option value="">-- Pilih Bonus --</option>
                                <option value="parfum">Parfum Bonus</option>
                            </select>
                        </div>
                        <div class="col-6 px-1" id="bonusParfumDiv" style="display:none;">
                            <select class="form-control form-control-sm select2" id="bonusParfumSelect" style="width: 100%;">
                                <option value="">-- Pilih Aroma --</option>
                                @foreach($products as $p)
                                    @if(stripos($p->size, '20ml') !== false || stripos($p->name, '20ml') !== false || stripos($p->size, '30ml') !== false || stripos($p->name, '30ml') !== false || stripos($p->size, '50ml') !== false || stripos($p->name, '50ml') !== false)
                                        <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->selling_price }}" data-stock="{{ $p->inventories->first() ? $p->inventories->first()->current_stock : 0 }}" data-barcode="{{ $p->barcode }}">{{ $p->name }} (Stok: {{ $p->inventories->first() ? $p->inventories->first()->current_stock : 0 }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 pl-1" id="bonusParfumBtnDiv" style="display:none;">
                            <button class="btn btn-sm btn-success btn-block" onclick="addParfumBonus()"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Totals & Payment -->
            <div class="card card-apms" id="paymentSection">
                <div class="card-header">
                    <h3 class="card-title">Pembayaran</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <strong>Subtotal</strong>
                        </div>
                        <div class="col-6 text-right">
                            <span id="subtotal">Rp 0</span>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <strong>Diskon</strong>
                        </div>
                        <div class="col-6 text-right">
                            <div class="input-group input-group-sm">
                                <input type="number" id="discount" class="form-control text-right" 
                                       value="0" min="0" style="min-width: 70px;">
                                <div class="input-group-append">
                                    <select class="form-control form-control-sm" id="discountType" 
                                            style="min-width: 60px;">
                                        <option value="fixed">Rp</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <strong>PPN (10%)</strong>
                            <div class="custom-control custom-switch d-inline ml-2">
                                <input type="checkbox" class="custom-control-input" id="taxToggle" checked>
                                <label class="custom-control-label" for="taxToggle"></label>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <span id="tax">Rp 0</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <h5>Total</h5>
                        </div>
                        <div class="col-6 text-right">
                            <h4 id="totalAmount" class="text-primary font-weight-bold">Rp 0</h4>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <div class="btn-group btn-group-toggle w-100 flex-wrap" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="payment_method" value="cash" checked> Cash
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="payment_method" value="qris"> QRIS
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="payment_method" value="transfer"> Transfer
                            </label>
                        </div>
                    </div>
                    
                    <!-- Amount Paid -->
                    <div class="form-group">
                        <label>Jumlah Bayar</label>
                        <input type="number" id="paidAmount" class="form-control form-control-lg" 
                               placeholder="0" min="0">
                    </div>
                    
                    <!-- Change -->
                    <div class="form-group">
                        <label>Kembalian</label>
                        <input type="text" id="changeAmount" class="form-control form-control-lg" 
                               readonly style="background-color: #f8f9fa; font-weight: bold;">
                    </div>
                    
                    <!-- Notes -->
                    <div class="form-group">
                        <label>Catatan (Opsional)</label>
                        <textarea id="transactionNotes" class="form-control" rows="2" 
                                  placeholder="Tambahkan catatan transaksi..."></textarea>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row no-gutters">
                        <div class="col-6 pr-1">
                            <button class="btn btn-danger btn-block py-3" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Batal
                            </button>
                        </div>
                        <div class="col-6 pl-1">
                            <button class="btn btn-success btn-block py-3" onclick="processPayment()">
                                <i class="fas fa-check"></i> Bayar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Amounts -->
                    <div class="row mt-2 no-gutters">
                        @php
                            $quickAmounts = [50000, 100000, 150000, 200000, 250000, 300000];
                        @endphp
                        @foreach($quickAmounts as $amount)
                        <div class="col-4 mb-1 px-1">
                            <button class="btn btn-outline-secondary btn-sm btn-block" 
                                    onclick="setPaidAmount({{ $amount }})">
                                {{ number_format($amount, 0, ',', '.') }}
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
            onclick="$('html, body').animate({scrollTop: $('#cartSection').offset().top - 80}, 500)">
        <i class="fas fa-shopping-cart fa-lg"></i>
        <span class="badge badge-danger position-absolute" id="mobileCartCount" 
              style="top: -2px; right: -2px; border-radius: 50%; min-width: 22px; padding: 3px 6px; font-size: 11px;">0</span>
    </button>
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
    transition: all 0.2s ease;
    height: 100%;
    border: 1px solid #eee;
}
.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.product-card.bg-light {
    cursor: not-allowed;
    opacity: 0.6;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let cart = [];
let customerType = 'retail';
const premiumCategoryId = @json($categories->firstWhere('name', 'like', '%Premium%')?->id ?? 1);

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
        const searchTerm = $(this).val().toLowerCase();
        $('.product-item').each(function() {
            const name = $(this).data('name').toLowerCase();
            const barcode = $(this).data('barcode');
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
            url: '/api/customers',
            method: 'POST',
            data: formData,
            success: function(response) {
                // Add new customer to select with data attributes
                const newOption = new Option(response.name, response.id, false, true);
                $(newOption).attr('data-phone', response.phone || '');
                $(newOption).attr('data-email', response.email || '');
                $('#customerSelect').append(newOption).trigger('change');
                
                hideInlineNewCustomer();
                $('#newCustomerForm')[0].reset();
                
                Swal.fire('Berhasil', 'Pelanggan berhasil ditambahkan', 'success');
            }
        });
    });
    
    // Calculate totals on input change
    $('#discount, #paidAmount').on('input', calculateTotals);
    $('#discountType, #taxToggle').change(calculateTotals);
    
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

function addToCart(productId) {
    const product = $(`.product-item[data-id="${productId}"]`);
    const stock = parseInt(product.data('stock'));
    
    if (stock === 0) {
        Swal.fire('Stok Habis', 'Produk ini tidak tersedia', 'warning');
        return;
    }
    
    // Check if already in cart
    const existingIndex = cart.findIndex(item => item.id === productId);
    
    if (existingIndex >= 0) {
        if (cart[existingIndex].quantity >= stock) {
            Swal.fire('Stok Tidak Cukup', 'Jumlah melebihi stok tersedia', 'warning');
            return;
        }
        cart[existingIndex].quantity++;
        saveCart();
        updateCartDisplay();
    } else {
        const price = customerType === 'wholesale' 
            ? parseFloat(product.data('wholesale')) || parseFloat(product.data('price'))
            : parseFloat(product.data('price'));
        
        // Detect if Premium by category button or data
        const categoryId = parseInt(product.data('category'));
        const isPremium = categoryId === premiumCategoryId;
        
        cart.push({
            id: productId,
            name: product.data('name'),
            price: price,
            original_price: parseFloat(product.data('price')),
            quantity: 1,
            stock: stock,
            barcode: product.data('barcode'),
            is_premium: isPremium,
            bonus_quantity: 0,
            is_bonus_item: false
        });
        
        saveCart();
        updateCartDisplay();
    }
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
                <td colspan="6" class="text-center text-muted py-3">
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
        const isPremium = item.is_premium || false;
        const isRefill = item.is_refill || false;
        
        const row = `
            <tr class="${isRefill ? 'table-info' : (isPremium ? 'table-warning' : (isBonusItem ? 'table-success' : ''))}">
                <td>${index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="fas ${isRefill ? 'fa-fill-drip text-info' : (isBonusItem ? 'fa-gift text-success' : 'fa-wine-bottle')}"></i>
                        </div>
                        <div>
                            <small class="d-block font-weight-bold">${escapeHtml(item.name)}</small>
                            ${isRefill ? '<span class="badge badge-info" style="font-size:0.65rem;">Isi Ulang</span>' : ''}
                            ${isPremium ? '<span class="badge badge-warning" style="font-size:0.65rem;">⭐ Premium</span>' : ''}
                            ${isBonusItem ? '<span class="badge badge-success" style="font-size:0.65rem;">🎁 Bonus Gratis</span>' : ''}
                        </div>
                    </div>
                </td>
                <td>
                    ${isRefill ?
                    `<div class="text-center font-weight-bold text-info">${item.refill_volume_ml} ml</div>` :
                    (isBonusItem ? 
                    `<div class="text-center font-weight-bold">${item.quantity}</div>` : 
                    `<div class="input-group input-group-sm flex-nowrap" style="width: 90px;">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${index}, ${item.quantity - 1})">-</button>
                        </div>
                        <input type="text" class="form-control text-center px-1" value="${item.quantity}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${index}, ${item.quantity + 1})">+</button>
                        </div>
                    </div>`)}
                </td>
                <td>
                    <div class="text-right">
                        ${isRefill ?
                        `<div><small class="text-muted">Rp ${item.price_per_ml.toLocaleString('id-ID')}/ml × ${item.refill_volume_ml} ml</small></div>
                        <div class="font-weight-bold text-info">Rp ${itemTotal.toLocaleString('id-ID')}</div>` :
                        (isBonusItem ? `<div><del class="text-muted">Rp ${item.original_price.toLocaleString('id-ID')}</del></div>
                        <div class="text-success font-weight-bold">Rp 0</div>` : 
                        `<div>Rp ${item.price.toLocaleString('id-ID')}</div>
                        <small class="text-muted">Total: Rp ${itemTotal.toLocaleString('id-ID')}</small>`)}
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" 
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
    $('#subtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));
    calculateTotals();
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

function toggleBonusInput() {
    const type = $('#bonusTypeSelect').val();
    if (type === 'parfum') {
        $('#bonusParfumDiv').show();
        $('#bonusParfumBtnDiv').show();
    } else {
        $('#bonusParfumDiv').hide();
        $('#bonusParfumBtnDiv').hide();
    }
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
        Swal.fire('Stok Habis', 'Stok parfum ini kosong', 'warning');
        return;
    }
    
    const existingIndex = cart.findIndex(item => item.id == productId && item.is_bonus_item);
    
    if (existingIndex >= 0) {
        if (cart[existingIndex].quantity >= stock) {
            Swal.fire('Stok Tidak Cukup', 'Jumlah melebihi stok tersedia', 'warning');
            return;
        }
        cart[existingIndex].quantity++;
    } else {
        cart.push({
            id: productId,
            name: option.data('name'),
            price: 0,
            original_price: parseFloat(option.data('price')),
            quantity: 1,
            stock: stock,
            barcode: option.data('barcode'),
            is_premium: false,
            bonus_quantity: 0,
            is_bonus_item: true
        });
    }
    
    saveCart();
    updateCartDisplay();
    select.val('').trigger('change');
}

function showInlineNewCustomer() {
    const isMobile = window.innerWidth < 768;
    
    if (isMobile) {
        $('#middleColumn').insertAfter('#leftColumn').fadeIn(300);
        $('#leftColumn').hide();
        $('#newCustomerBtnDisplay').slideUp(200);
    } else {
        $('#leftColumn').removeClass('col-md-8').addClass('col-md-5');
        $('#rightColumn').removeClass('col-md-4').addClass('col-md-3');
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
            $('#leftColumn').removeClass('col-md-5').addClass('col-md-8');
            $('#rightColumn').removeClass('col-md-3').addClass('col-md-4');
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
        } else {
            // Update price based on customer type
            const product = $(`.product-item[data-id="${item.id}"]`);
            item.price = customerType === 'wholesale' 
                ? parseFloat(product.data('wholesale')) || parseFloat(product.data('price'))
                : parseFloat(product.data('price'));
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
    
    const total = parseFloat($('#totalAmount').text().replace(/[^0-9]/g, ''));
    const paid = parseFloat($('#paidAmount').val()) || 0;
    
    if (paid < total) {
        Swal.fire('Pembayaran Kurang', 'Jumlah pembayaran kurang dari total', 'warning');
        return;
    }
    
    // Collect transaction data
    const transactionData = {
        customer_id: $('#customerSelect').val(),
        customer_type: $('#customerType').val(),
        items: cart.map(item => ({
            product_id:      item.id,
            quantity:        item.is_refill ? 1 : item.quantity,
            price:           item.price,
            bonus_quantity:  item.bonus_quantity || 0,
            bonus_note:      item.is_premium && item.bonus_quantity > 0 
                                ? 'Bonus 20ml Sedang x' + item.bonus_quantity + ' untuk ' + escapeHtml(item.name) 
                                : null,
            refill_volume_ml: item.is_refill ? item.refill_volume_ml : null,
        })),
        discount_amount: parseFloat($('#discount').val()) || 0,
        discount_type: $('#discountType').val(),
        tax_enabled: $('#taxToggle').is(':checked'),
        payment_method: $('input[name="payment_method"]:checked').val(),
        paid_amount: paid,
        notes: $('#transactionNotes').val(),
        _token: '{{ csrf_token() }}'
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
            // Print receipt
            if (response.transaction_id) {
                printReceipt(response.transaction_id);
            }
            
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
                                    <button class="btn btn-success btn-block mb-2 btn-sm" onclick="initWhatsAppShare('${invoiceNum}', '${totalVal}')">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </button>
                                </div>
                                <div class="col-6 pl-1">
                                    <button class="btn btn-info btn-block mb-2 btn-sm" onclick="shareSocial('${invoiceNum}', '${totalVal}')">
                                        <i class="fas fa-share-alt"></i> Lainnya
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block mb-2" onclick="printReceipt('${response.transaction_id}')">
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
            input: 'tel',
            inputLabel: 'Masukkan nomor WhatsApp (Contoh: 0812...)',
            inputPlaceholder: '0812XXXXXXXX',
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745',
            focusConfirm: false,
            didOpen: () => {
                const input = Swal.getInput();
                if (input) {
                    input.focus();
                    // Add direct input event listener to ensure typing works
                    input.oninput = (e) => {
                        e.target.value = e.target.value.replace(/[^0-9]/g, '');
                    };
                }
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'Nomor WhatsApp tidak boleh kosong!';
                }
                if (value.length < 10) {
                    return 'Nomor tidak valid (Minimal 10 digit)!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                executeWhatsAppSend(invoiceNum, total, result.value);
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