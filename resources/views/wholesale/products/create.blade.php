@extends('layouts.app')

@section('title', 'Tambah Produk Grosir')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-primary-apms"><i class="fas fa-plus-circle mr-2"></i> Tambah Produk Grosir</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('wholesale.products.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Produk *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tipe *</label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="botol" {{ old('type') == 'botol' ? 'selected' : '' }}>Botol</option>
                                        <option value="sarung" {{ old('type') == 'sarung' ? 'selected' : '' }}>Sarung Botol</option>
                                        <option value="methanol" {{ old('type') == 'methanol' ? 'selected' : '' }}>Methanol</option>
                                        <option value="aroma" {{ old('type') == 'aroma' ? 'selected' : '' }}>Aroma Parfum</option>
                                        <option value="aksesoris" {{ old('type') == 'aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                                        <option value="lainnya" {{ old('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Satuan *</label>
                                    <select name="unit" class="form-control @error('unit') is-invalid @enderror" required>
                                        <option value="">Pilih</option>
                                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="botol" {{ old('unit') == 'botol' ? 'selected' : '' }}>Botol</option>
                                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                        <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Ml</option>
                                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                                        <option value="meter" {{ old('unit') == 'meter' ? 'selected' : '' }}>Meter</option>
                                        <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                                    </select>
                                    @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Per Unit (Rp) *</label>
                                    <input type="number" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit') }}" required>
                                    @error('price_per_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Per Ml (Rp)</label>
                                    <input type="number" name="price_per_ml" class="form-control @error('price_per_ml') is-invalid @enderror" value="{{ old('price_per_ml') }}" step="0.01">
                                    <small class="text-muted">Untuk produk cair</small>
                                    @error('price_per_ml') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah Buah per Satuan</label>
                                    <input type="number" name="pieces_per_unit" class="form-control @error('pieces_per_unit') is-invalid @enderror" value="{{ old('pieces_per_unit', 1) }}" min="1">
                                    <small class="text-muted">1 pack = <strong id="previewPieces">1</strong> buah, harga/buah = <strong id="previewPrice">Rp 0</strong></small>
                                    @error('pieces_per_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stok Awal</label>
                                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0">
                                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        @push('scripts')
                        <script>
                        $(document).on('input', 'input[name="pieces_per_unit"], input[name="price_per_unit"]', function() {
                            const pieces = parseInt($('input[name="pieces_per_unit"]').val()) || 1;
                            const price = parseFloat($('input[name="price_per_unit"]').val()) || 0;
                            $('#previewPieces').text(pieces);
                            $('#previewPrice').text('Rp ' + (price / pieces).toLocaleString('id-ID', {maximumFractionDigits: 0}));
                        });
                        </script>
                        @endpush

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="isActive">Aktif</label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary-apms"><i class="fas fa-save mr-1"></i> Simpan</button>
                            <a href="{{ route('wholesale.products.index') }}" class="btn btn-light">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
