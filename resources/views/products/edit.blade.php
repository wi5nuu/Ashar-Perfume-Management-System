@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-apms mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="page-header-title"><i class="fas fa-edit mr-2"></i> Edit Produk</h1>
                <p class="page-header-subtitle">{{ $product->name }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-6">

                {{-- Informasi Dasar --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-tag mr-2 text-primary-apms"></i> Informasi Dasar</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-600">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_category_id" class="font-weight-600">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('product_category_id') is-invalid @enderror"
                                            id="product_category_id" name="product_category_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
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
                                    <label for="brand" class="font-weight-600">Brand</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                           id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                    @error('brand')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="size" class="font-weight-600">Ukuran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('size') is-invalid @enderror"
                                           id="size" name="size" value="{{ old('size', $product->size) }}" required>
                                    @error('size')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit" class="font-weight-600">Satuan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('unit') is-invalid @enderror"
                                            id="unit" name="unit" required>
                                        <option value="">Pilih Satuan</option>
                                        <option value="ml" {{ old('unit', $product->unit) == 'ml' ? 'selected' : '' }}>ML</option>
                                        <option value="gr" {{ old('unit', $product->unit) == 'gr' ? 'selected' : '' }}>Gram</option>
                                        <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="liter" {{ old('unit', $product->unit) == 'liter' ? 'selected' : '' }}>Liter</option>
                                    </select>
                                    @error('unit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="description" class="font-weight-600">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Informasi Stok --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-cubes mr-2 text-warning"></i> Informasi Stok</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="initial_stock" class="font-weight-600">Stok Awal</label>
                                    <input type="number" class="form-control"
                                           id="initial_stock" name="initial_stock"
                                           value="{{ old('initial_stock', $product->initial_stock) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="minimum_stock" class="font-weight-600">Minimal Stok</label>
                                    <input type="number" class="form-control"
                                           id="minimum_stock" name="minimum_stock"
                                           value="{{ old('minimum_stock', $product->minimum_stock ?? 10) }}" min="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stok Current (Live) --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-warehouse mr-2 text-warning"></i> Stok Current (Live)</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $globalInventory = $product->inventories->firstWhere('branch_id', null);
                            $totalStock = $product->inventories->sum('current_stock');
                        @endphp

                        {{-- Info total stok semua branch --}}
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Total stok semua cabang: <strong>{{ \App\Helpers\PerformanceHelper::formatMl($totalStock) }}</strong>
                        </div>

                        {{-- Edit stok global (branch_id = null) --}}
                        @if($globalInventory)
                        <div class="form-group mb-2">
                            <label class="font-weight-600">Stok Global (semua cabang)
                                <small class="text-muted font-weight-normal">— langsung edit nilai ml</small>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       name="current_stock_global"
                                       id="current_stock_global"
                                       value="{{ old('current_stock_global', (int) $globalInventory->current_stock) }}"
                                       min="0" step="1">
                                <div class="input-group-append">
                                    <span class="input-group-text">ml</span>
                                </div>
                            </div>
                            <small class="text-muted">Saat ini: <strong>{{ \App\Helpers\PerformanceHelper::formatMl($globalInventory->current_stock) }}</strong></small>
                        </div>
                        @else
                        <div class="alert alert-warning py-2 px-3 mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Belum ada inventory global. Stok akan dibuat otomatis saat disimpan.
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-600">Stok Awal Global (ml)</label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       name="current_stock_global"
                                       id="current_stock_global"
                                       value="{{ old('current_stock_global', 0) }}"
                                       min="0" step="1">
                                <div class="input-group-append">
                                    <span class="input-group-text">ml</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Daftar stok per cabang (read-only) --}}
                        @if($product->inventories->where('branch_id', '!=', null)->count() > 0)
                        <div class="mt-3">
                            <label class="font-weight-600 text-muted small">Stok Per Cabang (read-only)</label>
                            <div class="table-responsive" style="max-height:180px; overflow-y:auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="small">Cabang</th>
                                            <th class="small text-right">Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->inventories->where('branch_id', '!=', null)->sortByDesc('current_stock')->take(20) as $inv)
                                        <tr>
                                            <td class="small">Cabang #{{ $inv->branch_id }}</td>
                                            <td class="small text-right">{{ \App\Helpers\PerformanceHelper::formatMl($inv->current_stock) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Pengaturan Tambahan --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-cog mr-2 text-secondary"></i> Pengaturan Tambahan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="track_inventory" name="track_inventory" value="1"
                                       {{ $product->track_inventory ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-600" for="track_inventory">Lacak Stok</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="is_active" name="is_active" value="1"
                                       {{ $product->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-600" for="is_active">Produk Aktif</label>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="is_refill" name="is_refill" value="1"
                                       {{ $product->is_refill ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-600" for="is_refill">
                                    <span class="text-info"><i class="fas fa-fill-drip mr-1"></i> Isi Ulang</span>
                                    <small class="text-muted d-block font-weight-normal">Produk dijual per ml via tab Isi Ulang di Kasir</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-lg-6">

                {{-- Informasi Harga --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-tags mr-2 text-success"></i> Informasi Harga</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="purchase_price" class="font-weight-600">Harga Beli (Cost) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('purchase_price') is-invalid @enderror"
                                       id="purchase_price" name="purchase_price"
                                       value="{{ old('purchase_price', $product->purchase_price) }}"
                                       step="0.01" min="0" required>
                            </div>
                            @error('purchase_price')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="selling_price" class="font-weight-600">Harga Jual (Retail) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                       id="selling_price" name="selling_price"
                                       value="{{ old('selling_price', $product->selling_price) }}"
                                       step="0.01" min="0" required>
                            </div>
                            @error('selling_price')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="wholesale_price" class="font-weight-600">Harga Grosir (Wholesale)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('wholesale_price') is-invalid @enderror"
                                       id="wholesale_price" name="wholesale_price"
                                       value="{{ old('wholesale_price', $product->wholesale_price) }}"
                                       step="0.01" min="0">
                            </div>
                            @error('wholesale_price')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Markup Info --}}
                        <div class="p-3 rounded" style="background: var(--light-bg, #f8f9fa); border: 1px solid #e9ecef;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small font-weight-600">Markup Keuntungan</span>
                                <span class="font-weight-bold text-success" id="marginInfo" style="font-size:1.1rem;">
                                    @if($product->purchase_price > 0)
                                        {{ round((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Harga Isi Ulang --}}
                <div class="card card-apms mb-4" id="refillPriceCard" style="{{ $product->is_refill ? '' : 'display:none;' }}">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-fill-drip mr-2 text-info"></i> Harga Isi Ulang</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-600">Harga per ml <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('refill_price_per_ml') is-invalid @enderror"
                                       id="refill_price_per_ml" name="refill_price_per_ml"
                                       value="{{ old('refill_price_per_ml', $product->refill_price_per_ml) }}" step="1" min="0"
                                       placeholder="Contoh: 2000">
                            </div>
                            <small class="text-muted">Harga per mililiter. Contoh: Rp 2.000/ml → 50ml = Rp 100.000</small>
                            @error('refill_price_per_ml')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Foto Produk --}}
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-image mr-2 text-info"></i> Foto Produk</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="image-preview-apms" id="imagePreview">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Preview">
                            @else
                                <span class="image-preview-placeholder">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <small class="d-block text-muted mt-2">Belum ada foto</small>
                                </span>
                            @endif
                        </div>
                        <div class="custom-file mt-3">
                            <input type="file" class="custom-file-input" id="image" name="image"
                                   accept="image/*" onchange="previewImage(event)">
                            <label class="custom-file-label" for="image">
                                {{ $product->image ? 'Ubah Foto' : 'Pilih Foto' }}
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-between align-items-center pt-2 pb-4">
            <a href="{{ route('products.index') }}" class="btn btn-light">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary-apms px-4">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<style>
.image-preview-apms {
    width: 200px;
    height: 200px;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #f8f9fa;
}
.image-preview-apms img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.font-weight-600 { font-weight: 600; }
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
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">';
        };
        reader.readAsDataURL(input.files[0]);
        input.nextElementSibling.innerText = input.files[0].name;
    }
}

$(function() {
    $('.select2').select2({ theme: 'bootstrap4' });

    $('#purchase_price, #selling_price').on('input change', calculateMargin);

    $('#is_refill').on('change', function() {
        if ($(this).is(':checked')) {
            $('#refillPriceCard').slideDown(200);
        } else {
            $('#refillPriceCard').slideUp(200);
        }
    });
});

function calculateMargin() {
    const purchasePrice = parseFloat($('#purchase_price').val()) || 0;
    const sellingPrice  = parseFloat($('#selling_price').val())  || 0;
    if (purchasePrice > 0) {
        const margin = ((sellingPrice - purchasePrice) / purchasePrice) * 100;
        $('#marginInfo').text(Math.round(margin) + '%');
    }
}
</script>
@endpush
