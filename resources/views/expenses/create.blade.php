@extends('layouts.app')

@section('title', 'Catat Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Form Catat Pengeluaran Baru</h3>
                </div>
                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="date">Tanggal *</label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id">Kategori Pengeluaran *</label>
                            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount">Jumlah Pengeluaran (Rp) *</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Contoh: 50000" min="0" required>
                                @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="vendor">Vendor / Toko (Opsional)</label>
                            <input type="text" name="vendor" class="form-control" value="{{ old('vendor') }}" placeholder="Contoh: PLN, PDAM, atau Nama Toko">
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi / Detail *</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Jelaskan untuk apa pengeluaran ini..." required>{{ old('description') }}</textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="proof_image">Upload Bukti / Nota (Opsional)</label>
                            <div class="custom-file">
                                <input type="file" name="proof_image" class="custom-file-input @error('proof_image') is-invalid @enderror" id="proof_image" accept="image/*">
                                <label class="custom-file-label" for="proof_image">Pilih file...</label>
                            </div>
                            <small class="text-muted">Format: JPG, PNG | Max: 2MB</small>
                            @error('proof_image') <span class="invalid-feedback" style="display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary-apms">
                            <i class="fas fa-save mr-1"></i> Simpan Catatan
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-default">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update label custom-file-input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
