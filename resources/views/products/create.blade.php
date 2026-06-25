@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </h3>
                </div>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Basic Information -->
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Dasar</h3>
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
                                <div class="card card-outline card-info mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Stok Awal</h3>
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
                                <!-- Pricing Information -->
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Harga</h3>
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
                                <div class="card card-outline card-info mt-3" id="refillPriceCard" style="display:none;">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-fill-drip mr-1 text-info"></i> Harga Isi Ulang</h3>
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
                                <div class="card card-outline card-warning mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Gambar Produk</h3>
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
                                <div class="card card-outline card-secondary mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Pengaturan Tambahan</h3>
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
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary-apms">
                            <i class="fas fa-save"></i> Simpan Produk
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.image-upload-container {
    text-align: center;
}
.image-preview {
    width: 200px;
    height: 200px;
    border: 2px dashed #ddd;
    border-radius: 10px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.image-preview-default {
    color: #999;
}
.card-outline {
    border-top: 3px solid;
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
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid">`;
        }
        
        reader.readAsDataURL(input.files[0]);
        
        // Update file label
        const fileName = input.files[0].name;
        input.nextElementSibling.innerText = fileName;
    }
}

$(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });
    
    // Auto calculate selling price
    $('#purchase_price').on('change', function() {
        const purchasePrice = parseFloat($(this).val()) || 0;
        const sellingPrice = purchasePrice * 1.5; // 50% markup
        if (!$('#selling_price').val()) {
            $('#selling_price').val(sellingPrice.toFixed(2));
        }
    });
    
    // Toggle refill price card
    $('#is_refill').on('change', function() {
        if ($(this).is(':checked')) {
            $('#refillPriceCard').slideDown(200);
        } else {
            $('#refillPriceCard').slideUp(200);
        }
    });
});
</script>
@endpush