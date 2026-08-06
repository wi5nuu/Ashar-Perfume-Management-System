@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="h3 mb-1">Tambah Produk Baru</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
                            <li class="breadcrumb-item active">Tambah Baru</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus-circle mr-1" style="color:var(--primary);"></i> Form Produk Baru
                    </h3>
                </div>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Basic Information -->
                                <div class="card create-section-card mb-3">
                                    <div class="card-header create-section-header">
                                        <span class="section-number">1</span>
                                        <h6 class="mb-0 ml-2">Informasi Dasar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">Nama Produk *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="product_category_id">Kategori *</label>
                                                    <select class="form-control select2 @error('product_category_id') is-invalid @enderror" 
                                                            id="product_category_id" name="product_category_id" required>
                                                        <option value="">Pilih Kategori</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" 
                                                                {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_category_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="brand">Merek</label>
                                                    <input type="text" class="form-control" id="brand" name="brand" 
                                                           value="{{ old('brand') }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="size">Ukuran *</label>
                                                    <select class="form-control @error('size') is-invalid @enderror" 
                                                            id="size" name="size" required>
                                                        <option value="">Pilih Ukuran</option>
                                                        <option value="10ml" {{ old('size') == '10ml' ? 'selected' : '' }}>10ml</option>
                                                        <option value="20ml" {{ old('size') == '20ml' ? 'selected' : '' }}>20ml</option>
                                                        <option value="30ml" {{ old('size') == '30ml' ? 'selected' : '' }}>30ml</option>
                                                        <option value="50ml" {{ old('size') == '50ml' ? 'selected' : '' }}>50ml</option>
                                                        <option value="100ml" {{ old('size') == '100ml' ? 'selected' : '' }}>100ml</option>
                                                        <option value="1L" {{ old('size') == '1L' ? 'selected' : '' }}>1 Liter</option>
                                                    </select>
                                                    @error('size')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="unit">Satuan *</label>
                                                    <select class="form-control @error('unit') is-invalid @enderror" 
                                                            id="unit" name="unit" required>
                                                        <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>ml</option>
                                                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>pcs</option>
                                                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>liter</option>
                                                    </select>
                                                    @error('unit')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="description">Deskripsi Produk</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Inventory Information -->
                                <div class="card create-section-card mt-3">
                                    <div class="card-header create-section-header">
                                        <span class="section-number">2</span>
                                        <h6 class="mb-0 ml-2">Informasi Stok Awal</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="initial_stock">Stok Awal *</label>
                                                    <input type="number" class="form-control" 
                                                           id="initial_stock" name="initial_stock" 
                                                           value="{{ old('initial_stock', 0) }}" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="minimum_stock">Minimal Stok</label>
                                                    <input type="number" class="form-control" 
                                                           id="minimum_stock" name="minimum_stock" 
                                                           value="{{ old('minimum_stock', 10) }}" min="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Price Preview Panel -->
                                <div class="card create-section-card mb-3" id="pricePreviewPanel">
                                    <div class="card-header create-section-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                        <span class="section-number" style="background: rgba(255,255,255,0.3);">
                                            <i class="fas fa-calculator"></i>
                                        </span>
                                        <h6 class="mb-0 ml-2 text-white">Preview Harga Real-time</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="small text-muted">Harga Beli</div>
                                                <div class="font-weight-bold text-secondary" id="prev_purchase">Rp 0</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="small text-muted">Harga Jual</div>
                                                <div class="font-weight-bold" style="color:var(--primary);" id="prev_selling">Rp 0</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="small text-muted">Margin</div>
                                                <div class="font-weight-bold text-success" id="prev_margin">0%</div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="small text-muted">Harga Grosir</div>
                                                <div class="font-weight-bold text-info" id="prev_wholesale">Rp 0</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="small text-muted">Profit Grosir</div>
                                                <div class="font-weight-bold text-success" id="prev_wholesale_margin">0%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Information -->
                                <div class="card create-section-card mb-3">
                                    <div class="card-header create-section-header">
                                        <span class="section-number">3</span>
                                        <h6 class="mb-0 ml-2">Informasi Harga</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="purchase_price">Harga Beli *</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number" step="0.01" 
                                                               class="form-control @error('purchase_price') is-invalid @enderror" 
                                                               id="purchase_price" name="purchase_price" 
                                                               value="{{ old('purchase_price') }}" required>
                                                    </div>
                                                    @error('purchase_price')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="selling_price">Harga Jual *</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number" step="0.01" 
                                                               class="form-control @error('selling_price') is-invalid @enderror" 
                                                               id="selling_price" name="selling_price" 
                                                               value="{{ old('selling_price') }}" required>
                                                    </div>
                                                    @error('selling_price')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="wholesale_price">Harga Grosir</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" step="0.01" 
                                                       class="form-control @error('wholesale_price') is-invalid @enderror" 
                                                       id="wholesale_price" name="wholesale_price" 
                                                       value="{{ old('wholesale_price') }}">
                                            </div>
                                            @error('wholesale_price')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" 
                                                   id="apply_wholesale" name="apply_wholesale" value="1"
                                                   {{ old('apply_wholesale') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="apply_wholesale">
                                                Terapkan harga grosir untuk pelanggan wholesale
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Refill Price (shown when is_refill checked) -->
                                <div class="card create-section-card mt-3" id="refillPriceCard" style="display:none;">
                                    <div class="card-header create-section-header" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                                        <span class="section-number" style="background: rgba(255,255,255,0.3);">
                                            <i class="fas fa-fill-drip"></i>
                                        </span>
                                        <h6 class="mb-0 ml-2 text-white">Harga Isi Ulang</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Harga per ml <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" class="form-control @error('refill_price_per_ml') is-invalid @enderror"
                                                       id="refill_price_per_ml" name="refill_price_per_ml"
                                                       value="{{ old('refill_price_per_ml') }}" step="1" min="0"
                                                       placeholder="Contoh: 2000">
                                            </div>
                                            <small class="text-muted">Harga per mililiter. Contoh: Rp 2.000/ml, maka 50ml = Rp 100.000</small>
                                            @error('refill_price_per_ml')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Product Image -->
                                <div class="card create-section-card mt-3">
                                    <div class="card-header create-section-header" style="background: linear-gradient(135deg, #fd7e14, #e55a00);">
                                        <span class="section-number" style="background: rgba(255,255,255,0.3);">
                                            <i class="fas fa-image"></i>
                                        </span>
                                        <h6 class="mb-0 ml-2 text-white">Gambar Produk</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="form-group">
                                            <div class="image-upload-container">
                                                <div class="image-preview mb-3" id="imagePreview">
                                                    <div class="image-preview-default">
                                                        <i class="fas fa-wine-bottle fa-5x text-muted"></i>
                                                        <p class="mt-2">Belum ada gambar</p>
                                                    </div>
                                                </div>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                                                           id="image" name="image" accept="image/*" onchange="previewImage(event)">
                                                    <label class="custom-file-label" for="image">Pilih gambar...</label>
                                                    @error('image')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <small class="text-muted">Format: JPG, PNG, GIF | Maksimal: 2MB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Additional Settings -->
                                <div class="card create-section-card mt-3">
                                    <div class="card-header create-section-header" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                        <span class="section-number" style="background: rgba(255,255,255,0.3);">
                                            <i class="fas fa-cog"></i>
                                        </span>
                                        <h6 class="mb-0 ml-2 text-white">Pengaturan Tambahan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="is_active" name="is_active" value="1" checked>
                                                <label class="custom-control-label" for="is_active">Produk Aktif</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="track_inventory" name="track_inventory" value="1" checked>
                                                <label class="custom-control-label" for="track_inventory">Lacak Inventory</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="allow_discount" name="allow_discount" value="1" checked>
                                                <label class="custom-control-label" for="allow_discount">Izinkan Diskon</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="is_refill" name="is_refill" value="1">
                                                <label class="custom-control-label" for="is_refill">
                                                    <span class="text-info"><i class="fas fa-fill-drip mr-1"></i> Isi Ulang</span>
                                                    <small class="text-muted d-block">Produk dijual per ml via tab Isi Ulang di Kasir</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex flex-wrap">
                            <button type="submit" name="action" value="save" class="btn btn-primary-apms mr-2 mb-2 mb-md-0">
                                <i class="fas fa-save mr-1"></i> Simpan Produk
                            </button>
                            <button type="submit" name="action" value="save_new" class="btn btn-success mr-2 mb-2 mb-md-0">
                                <i class="fas fa-plus-circle mr-1"></i> Simpan & Tambah Baru
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary mb-2 mb-md-0">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                        </div>
                        <div class="text-muted small d-none d-md-block">
                            <i class="fas fa-info-circle mr-1"></i> Field bertanda <span class="text-danger">*</span> wajib diisi
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── Section cards ── */
.create-section-card {
    border: 1px solid #e8e8e8;
    border-radius: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.create-section-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 9px 9px 0 0 !important;
    padding: 10px 16px;
    display: flex;
    align-items: center;
}
.create-section-header h6 {
    color: white;
    font-weight: 600;
    letter-spacing: 0.3px;
    margin: 0;
}
.section-number {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* ── Image upload ── */
.image-upload-container {
    text-align: center;
}
.image-preview {
    width: 180px;
    height: 180px;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #fafafa;
    transition: border-color 0.2s;
    cursor: pointer;
}
.image-preview:hover {
    border-color: var(--primary);
    background: #fff8f5;
}
.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.image-preview-default { color: #bbb; }

/* ── Price preview panel ── */
#pricePreviewPanel .card-body {
    background: #f8fff8;
}
#pricePreviewPanel .h6 { font-size: 0.95rem; }

/* ── Form labels ── */
.form-group label {
    font-weight: 600;
    font-size: 0.82rem;
    color: #444;
    margin-bottom: 4px;
}
.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.15rem rgba(255,107,53,0.15);
}
</style>
@endpush

@push('scripts')
<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-fluid';
            preview.appendChild(img);
        }
        reader.readAsDataURL(input.files[0]);
        const fileName = input.files[0].name;
        input.nextElementSibling.innerText = fileName;
    }
}

function formatRp(val) {
    if (!val || isNaN(val)) return 'Rp 0';
    return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function updatePricePreview() {
    const purchase  = parseFloat($('#purchase_price').val())  || 0;
    const selling   = parseFloat($('#selling_price').val())   || 0;
    const wholesale = parseFloat($('#wholesale_price').val()) || 0;

    $('#prev_purchase').text(formatRp(purchase));
    $('#prev_selling').text(formatRp(selling));
    $('#prev_wholesale').text(formatRp(wholesale));

    const margin = purchase > 0 ? ((selling - purchase) / purchase * 100) : 0;
    const wMargin = purchase > 0 ? ((wholesale - purchase) / purchase * 100) : 0;

    $('#prev_margin').text(margin.toFixed(1) + '%')
        .toggleClass('text-success', margin >= 0)
        .toggleClass('text-danger', margin < 0);
    $('#prev_wholesale_margin').text(wMargin.toFixed(1) + '%')
        .toggleClass('text-success', wMargin >= 0)
        .toggleClass('text-danger', wMargin < 0);
}

function generateSKU() {
    const name = $('#name').val().trim();
    const cat  = $('#product_category_id option:selected').text().trim();
    const size = $('#size').val() || '';

    if (!name) {
        Swal.fire({ icon: 'warning', title: 'Isi Nama Produk', text: 'Nama produk diperlukan untuk generate SKU.', timer: 2000, showConfirmButton: false });
        return;
    }

    const namePart = name.replace(/[^a-zA-Z0-9]/g, '').substring(0, 4).toUpperCase();
    const catPart  = cat.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase();
    const sizePart = size.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    const rand     = Math.floor(100 + Math.random() * 900);
    const sku      = [namePart, catPart, sizePart, rand].filter(Boolean).join('-');

    $('#sku').val(sku);
    Swal.fire({ icon: 'success', title: 'SKU Digenerate', text: sku, timer: 1500, showConfirmButton: false });
}

$(function() {
    // Select2
    $('.select2').select2({ theme: 'bootstrap4' });

    // Auto-suggest selling price on purchase_price change
    $('#purchase_price').on('input change', function() {
        const purchase = parseFloat($(this).val()) || 0;
        if (purchase > 0 && !$('#selling_price').val()) {
            $('#selling_price').val(Math.round(purchase * 1.5));
        }
        if (purchase > 0 && !$('#wholesale_price').val()) {
            $('#wholesale_price').val(Math.round(purchase * 1.25));
        }
        updatePricePreview();
    });

    // Live price preview on all price fields
    $('#selling_price, #wholesale_price').on('input change', updatePricePreview);

    // Toggle refill price card
    $('#is_refill').on('change', function() {
        if ($(this).is(':checked')) {
            $('#refillPriceCard').slideDown(200);
        } else {
            $('#refillPriceCard').slideUp(200);
        }
    });

    // Initial preview update (for old() values on validation fail)
    updatePricePreview();
});
</script>
@endpush