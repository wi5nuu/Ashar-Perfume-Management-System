@extends('layouts.app')
@section('title', 'Catat Pengeluaran')

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4166 100%);
    padding: 1.5rem 1.75rem; border-radius: 12px; margin-bottom: 1.5rem; color: #fff;
}
.page-header-apms h1 { font-size: 1.6rem; font-weight: 700; margin: 0; }
.page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; }
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.7); }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item+.breadcrumb-item::before { color: rgba(255,255,255,.4); }
.card-apms { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); margin-bottom: 1.5rem; }
.card-apms .card-header {
    background: #fff; border-bottom: 2px solid #f0f0f0; padding: 1rem 1.5rem;
    border-radius: 12px 12px 0 0; display: flex; align-items: center; gap: .6rem;
}
.card-apms .card-header .section-icon {
    width: 32px; height: 32px; border-radius: 8px;
    background: rgba(255,107,53,.1); display: flex; align-items: center;
    justify-content: center; color: var(--primary); font-size: .85rem;
}
.card-apms .card-header h3 { margin: 0; font-size: .95rem; font-weight: 700; color: var(--secondary); }
.card-apms .card-body { padding: 1.5rem; }
.form-label-apms { font-size: .8rem; font-weight: 600; color: #495057; text-transform: uppercase; letter-spacing: .4px; margin-bottom: .4rem; display: block; }
.form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,.15); }
.form-control { border-radius: 8px; border-color: #dee2e6; }
.input-group .input-group-text { border-radius: 8px 0 0 8px; background: #f8f9fa; border-color: #dee2e6; color: #495057; }
.input-group .form-control { border-radius: 0 8px 8px 0; }
.input-group .input-group-append .input-group-text { border-radius: 0 8px 8px 0; }
.btn-primary-apms { background: var(--primary); border-color: var(--primary); color: #fff; border-radius: 8px; font-weight: 600; font-size: .9rem; padding: .55rem 1.5rem; transition: background .2s, box-shadow .2s; }
.btn-primary-apms:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #fff; box-shadow: 0 4px 12px rgba(255,107,53,.3); }
.required-mark { color: var(--primary); }
/* Drag & Drop Upload */
.drop-zone {
    border: 2px dashed #dee2e6; border-radius: 12px; padding: 2.5rem 1.5rem;
    text-align: center; cursor: pointer; transition: all .2s;
    background: #fafafa; position: relative;
}
.drop-zone:hover, .drop-zone.dragover { border-color: var(--primary); background: rgba(255,107,53,.03); }
.drop-zone .drop-icon { font-size: 2.5rem; color: #dee2e6; margin-bottom: .75rem; transition: color .2s; }
.drop-zone:hover .drop-icon, .drop-zone.dragover .drop-icon { color: var(--primary); }
.drop-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.drop-zone .drop-label { font-size: .9rem; color: #6c757d; }
.drop-zone .drop-label span { color: var(--primary); font-weight: 600; cursor: pointer; }
.drop-zone .drop-formats { font-size: .75rem; color: #adb5bd; margin-top: .3rem; }
.preview-img { max-width: 100%; max-height: 200px; border-radius: 10px; margin-top: 1rem; display: none; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
.amount-display { font-size: .8rem; color: #6c757d; margin-top: .3rem; min-height: 1.2em; }
/* Category card select */
.cat-option { display: none; }
.cat-card {
    border: 2px solid #e9ecef; border-radius: 10px; padding: .75rem 1rem;
    cursor: pointer; text-align: center; transition: all .2s; font-size: .82rem;
    font-weight: 600; color: #6c757d; background: #fff;
}
.cat-option:checked + .cat-card { border-color: var(--primary); background: rgba(255,107,53,.06); color: var(--primary); }
.cat-card i { display: block; font-size: 1.4rem; margin-bottom: .35rem; }
</style>

@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-plus-circle mr-2"></i>Catat Pengeluaran Baru</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Biaya Operasional</a></li>
                        <li class="breadcrumb-item active">Catat Baru</li>
                    </ol>
                </div>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px; border:none; box-shadow:0 2px 8px rgba(220,53,69,.2);">
        <i class="fas fa-exclamation-triangle mr-2"></i><strong>Perbaiki kesalahan berikut:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" id="expenseForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">

                {{-- Data Utama --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-receipt"></i></div>
                        <h3>Detail Pengeluaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Tanggal <span class="required-mark">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                            value="{{ old('date', date('Y-m-d')) }}" required>
                                    </div>
                                    @error('date') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Jumlah <span class="required-mark">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="number" name="amount" id="amount"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}"
                                            placeholder="0" min="0" step="100" required>
                                    </div>
                                    @error('amount') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                    <div class="amount-display" id="amountDisplay"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-apms">Kategori <span class="required-mark">*</span></label>
                            <select name="category_id" id="category_id" class="form-control select2 @error('category_id') is-invalid @enderror" required>
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
                            <label class="form-label-apms">Deskripsi <span class="required-mark">*</span></label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Keterangan singkat pengeluaran ini..."
                                required>{{ old('description') }}</textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label-apms">Vendor / Penerima</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-store"></i></span></div>
                                <input type="text" name="vendor" class="form-control @error('vendor') is-invalid @enderror"
                                    value="{{ old('vendor') }}" placeholder="Nama toko, vendor, atau penerima pembayaran">
                            </div>
                            @error('vendor') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Upload Bukti --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-paperclip"></i></div>
                        <h3>Bukti Pengeluaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="drop-zone" id="dropZone">
                            <input type="file" name="proof_image" id="proofImage" accept="image/jpeg,image/png,image/jpg">
                            <div class="drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                            <div class="drop-label">
                                Seret & lepas foto bukti di sini, atau <span onclick="document.getElementById('proofImage').click()">klik untuk pilih</span>
                            </div>
                            <div class="drop-formats">Format: JPG, PNG &bull; Maks. 2 MB</div>
                        </div>
                        <img id="previewImg" class="preview-img" src="" alt="Preview">
                        @error('proof_image') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                {{-- Catatan --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-sticky-note"></i></div>
                        <h3>Catatan Tambahan</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="form-label-apms">Catatan Internal</label>
                            <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Informasi tambahan yang perlu dicatat...">{{ old('notes') }}</textarea>
                            @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-wallet"></i></div>
                        <h3>Metode Pembayaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="form-label-apms">Bayar Via</label>
                            <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                <option value="cash"     {{ old('payment_method','cash') === 'cash'     ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="debit"    {{ old('payment_method') === 'debit'    ? 'selected' : '' }}>Kartu Debit</option>
                                <option value="credit"   {{ old('payment_method') === 'credit'   ? 'selected' : '' }}>Kartu Kredit</option>
                                <option value="ewallet"  {{ old('payment_method') === 'ewallet'  ? 'selected' : '' }}>E-Wallet</option>
                            </select>
                            @error('payment_method') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="card card-apms" style="background: linear-gradient(135deg, #fff8f5, #fff);">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small font-weight-600">TOTAL PENGELUARAN</span>
                        </div>
                        <div class="font-weight-700 text-danger" style="font-size:1.5rem;" id="summaryAmount">Rp 0</div>
                        <div class="text-muted small" id="summaryAmountText"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-apms btn-block mb-2">
                    <i class="fas fa-save mr-1"></i> Simpan Pengeluaran
                </button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-block" style="border-radius:8px;">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select2 for category
    if ($.fn.select2) {
        $('#category_id').select2({ placeholder: '-- Pilih Kategori --', width: '100%' });
    }

    // Amount formatter
    function formatRupiah(angka) {
        if (!angka || angka == 0) return '';
        const terbilang = (n) => {
            const satuan = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];
            if (n < 12) return satuan[n];
            if (n < 20) return satuan[n - 10] + ' belas';
            if (n < 100) return satuan[Math.floor(n/10)] + ' puluh ' + satuan[n%10];
            if (n < 200) return 'seratus ' + terbilang(n - 100);
            if (n < 1000) return satuan[Math.floor(n/100)] + ' ratus ' + terbilang(n%100);
            if (n < 2000) return 'seribu ' + terbilang(n - 1000);
            if (n < 1000000) return terbilang(Math.floor(n/1000)) + ' ribu ' + terbilang(n%1000);
            if (n < 1000000000) return terbilang(Math.floor(n/1000000)) + ' juta ' + terbilang(n%1000000);
            return terbilang(Math.floor(n/1000000000)) + ' miliar ' + terbilang(n%1000000000);
        };
        const num = parseInt(angka);
        const formatted = 'Rp ' + num.toLocaleString('id-ID');
        const words = terbilang(num).replace(/\s+/g, ' ').trim();
        return { formatted, words };
    }

    $('#amount').on('input', function() {
        const val = $(this).val();
        const result = formatRupiah(val);
        if (result) {
            $('#amountDisplay').text(result.words + ' rupiah');
            $('#summaryAmount').text(result.formatted);
            $('#summaryAmountText').text(result.words + ' rupiah');
        } else {
            $('#amountDisplay').text('');
            $('#summaryAmount').text('Rp 0');
            $('#summaryAmountText').text('');
        }
    });

    // Drag & Drop
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('proofImage');
    const previewImg = document.getElementById('previewImg');

    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleFile(file);
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files[0]) handleFile(this.files[0]);
    });

    function handleFile(file) {
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'warning', title: 'File Terlalu Besar', text: 'Ukuran file maksimal 2 MB.', confirmButtonColor: '#FF6B35' });
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            dropZone.querySelector('.drop-icon').innerHTML = '<i class="fas fa-check-circle" style="color:#28a745;"></i>';
            dropZone.querySelector('.drop-label').innerHTML = '<strong>' + file.name + '</strong>';
            dropZone.querySelector('.drop-formats').textContent = (file.size / 1024).toFixed(1) + ' KB';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
