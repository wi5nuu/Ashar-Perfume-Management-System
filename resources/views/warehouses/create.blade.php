@extends('layouts.app')

@section('title', 'Tambah Gudang')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h4 class="font-weight-bold mb-0"><i class="fas fa-warehouse text-primary mr-2"></i> Tambah Gudang Baru</h4>
                    <small class="text-muted">Lengkapi data gudang untuk cabang yang dipilih</small>
                </div>
                <form action="{{ route('warehouses.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-medium"><i class="fas fa-store text-muted mr-1" style="width:16px"></i> Cabang <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                                <option value="">— Pilih Cabang —</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="form-group">
                            <label class="font-weight-medium"><i class="fas fa-tag text-muted mr-1" style="width:16px"></i> Nama Gudang <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="namaGudang" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="contoh: Gudang Utama" oninput="generateKode()" required>
                            <small class="text-muted">Nama unik untuk membedakan gudang dalam satu cabang</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-medium"><i class="fas fa-barcode text-muted mr-1" style="width:16px"></i> Kode Gudang <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="code" id="kodeGudang" class="form-control form-control-lg @error('code') is-invalid @enderror"
                                       value="{{ old('code') }}" placeholder="Otomatis dari nama" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateKode()" title="Generate ulang kode">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Format: <strong>NAMA-123456-ABV</strong>. Terisi otomatis, bisa diedit manual jika perlu.</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-medium" for="isActive">Aktif</label>
                            </div>
                            <small class="text-muted d-block mt-1" style="margin-left:2.5rem">Nonaktifkan jika gudang sudah tidak digunakan</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top d-flex justify-content-between py-3">
                        <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-apms px-4" onclick="disableBtn(this, 'Menyimpan...')">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function disableBtn(btn, loadingText) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + loadingText;
}
function generateKode() {
    var nama = document.getElementById('namaGudang').value.trim();
    var kode = document.getElementById('kodeGudang');
    if (!nama) { kode.value = ''; return; }
    var base = nama.toUpperCase()
        .replace(/[^A-Z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    var rand = function(l, chars) {
        var s = '';
        for (var i = 0; i < l; i++) s += chars[Math.floor(Math.random() * chars.length)];
        return s;
    };
    var suffix = rand(6, '0123456789') + '-' + rand(3, 'ABCDEFGHJKLMNPQRSTUVWXYZ');
    if (base) kode.value = base + '-' + suffix;
}
</script>
@endpush
