@extends('layouts.app')

@section('title', 'Edit Produk Grosir')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-primary-apms"><i class="fas fa-edit mr-2"></i> Edit Produk Grosir</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('wholesale.products.update', $wholesaleProduct->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Produk *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $wholesaleProduct->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tipe *</label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe</option>
                                        @foreach(['botol', 'sarung', 'methanol', 'aroma', 'aksesoris', 'lainnya'] as $t)
                                        <option value="{{ $t }}" {{ old('type', $wholesaleProduct->type) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Satuan *</label>
                                    <select name="unit" class="form-control @error('unit') is-invalid @enderror" required>
                                        <option value="">Pilih</option>
                                        @foreach(['pcs', 'botol', 'liter', 'ml', 'kg', 'meter', 'pack'] as $u)
                                        <option value="{{ $u }}" {{ old('unit', $wholesaleProduct->unit) == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Per Unit (Rp) *</label>
                                    <input type="number" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', $wholesaleProduct->price_per_unit) }}" required>
                                    @error('price_per_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Per Ml (Rp)</label>
                                    <input type="number" name="price_per_ml" class="form-control @error('price_per_ml') is-invalid @enderror" value="{{ old('price_per_ml', $wholesaleProduct->price_per_ml) }}" step="0.01">
                                    <small class="text-muted">Untuk produk cair</small>
                                    @error('price_per_ml') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah Buah per Satuan</label>
                                    <input type="number" name="pieces_per_unit" class="form-control @error('pieces_per_unit') is-invalid @enderror" value="{{ old('pieces_per_unit', $wholesaleProduct->pieces_per_unit ?? 1) }}" min="1">
                                    <small class="text-muted" id="editPreview">1 {{ $wholesaleProduct->unit ?? 'unit' }} = <strong id="editPieces">{{ $wholesaleProduct->pieces_per_unit ?? 1 }}</strong> buah, harga/buah = <strong id="editPrice">Rp {{ number_format($wholesaleProduct->price_per_piece, 0, ',', '.') }}</strong></small>
                                    @error('pieces_per_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stok</label>
                                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $wholesaleProduct->stock) }}" min="0">
                                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        @push('scripts')
                        <script>
                        $(document).on('input', 'input[name="pieces_per_unit"], input[name="price_per_unit"]', function() {
                            const pieces = parseInt($('input[name="pieces_per_unit"]').val()) || 1;
                            const price = parseFloat($('input[name="price_per_unit"]').val()) || 0;
                            $('#editPieces').text(pieces);
                            $('#editPrice').text('Rp ' + (price / pieces).toLocaleString('id-ID', {maximumFractionDigits: 0}));
                        });
                        </script>
                        @endpush

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $wholesaleProduct->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" {{ old('is_active', $wholesaleProduct->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="isActive">Aktif</label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary-apms"><i class="fas fa-save mr-1"></i> Update</button>
                            <a href="{{ route('wholesale.products.index') }}" class="btn btn-light">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
