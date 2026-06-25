@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Edit Catatan Pengeluaran</h3>
                </div>
                <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="date">Tanggal *</label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
                            @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id">Kategori Pengeluaran *</label>
                            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
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
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" min="0" required>
                                @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="vendor">Vendor / Toko (Opsional)</label>
                            <input type="text" name="vendor" class="form-control" value="{{ old('vendor', $expense->vendor) }}">
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi / Detail *</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $expense->description) }}</textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="proof_image">Ganti Bukti / Nota (Opsional)</label>
                            @if($expense->proof_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $expense->proof_image) }}" class="img-thumbnail" style="max-height: 150px;">
                                    <p class="small text-muted">Bukti saat ini</p>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="proof_image" class="custom-file-input @error('proof_image') is-invalid @enderror" id="proof_image" accept="image/*">
                                <label class="custom-file-label" for="proof_image">Pilih file baru...</label>
                            </div>
                            @error('proof_image') <span class="invalid-feedback" style="display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary-apms">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
