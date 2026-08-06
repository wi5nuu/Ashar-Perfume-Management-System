@extends('layouts.app')
@section('title', 'Tambah Supplier')

@push('styles')
<style>
:root {
    --primary: #FF6B35;
    --primary-dark: #E55A2B;
    --secondary: #2D3047;
}
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4166 100%);
    padding: 1.5rem 1.75rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    color: #fff;
}
.page-header-apms h1 { font-size: 1.6rem; font-weight: 700; margin: 0; }
.page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; }
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.7); }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item+.breadcrumb-item::before { color: rgba(255,255,255,.4); }
.card-apms {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 1.5rem;
}
.card-apms .card-header {
    background: #fff;
    border-bottom: 2px solid #f0f0f0;
    padding: 1rem 1.5rem;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    gap: .6rem;
}
.card-apms .card-header .section-icon {
    width: 32px; height: 32px; border-radius: 8px;
    background: rgba(255,107,53,.1);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); font-size: .85rem;
}
.card-apms .card-header h3 {
    margin: 0; font-size: .95rem; font-weight: 700; color: var(--secondary);
}
.card-apms .card-body { padding: 1.5rem; }
.form-label-apms {
    font-size: .8rem; font-weight: 600; color: #495057;
    text-transform: uppercase; letter-spacing: .4px;
    margin-bottom: .4rem; display: block;
}
.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255,107,53,.15);
}
.form-control { border-radius: 8px; border-color: #dee2e6; }
.input-group .input-group-text { border-radius: 8px 0 0 8px; background: #f8f9fa; border-color: #dee2e6; }
.input-group .form-control { border-radius: 0 8px 8px 0; }
.btn-primary-apms {
    background: var(--primary); border-color: var(--primary); color: #fff;
    border-radius: 8px; font-weight: 600; font-size: .9rem;
    padding: .55rem 1.5rem;
    transition: background .2s, box-shadow .2s;
}
.btn-primary-apms:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #fff; box-shadow: 0 4px 12px rgba(255,107,53,.3); }
.required-mark { color: var(--primary); }
.status-toggle label { cursor: pointer; }
.status-toggle input[type=radio] { display: none; }
.status-option {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .5rem 1.2rem; border-radius: 8px; border: 2px solid #dee2e6;
    font-weight: 600; font-size: .85rem; cursor: pointer;
    transition: all .2s; margin-right: .5rem;
}
.status-toggle input[value=active]:checked + .status-option  { border-color: #28a745; background: rgba(40,167,69,.08); color: #155724; }
.status-toggle input[value=inactive]:checked + .status-option { border-color: #6c757d; background: rgba(108,117,125,.08); color: #383d41; }
.char-counter { font-size: .75rem; color: #adb5bd; float: right; }
</style>

@endpush

@section('content')
{{-- Page Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-plus-circle mr-2"></i>Tambah Supplier Baru</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                        <li class="breadcrumb-item active">Tambah Baru</li>
                    </ol>
                </div>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px; border:none; box-shadow:0 2px 8px rgba(220,53,69,.2);">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Terdapat kesalahan pada formulir:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <form action="{{ route('suppliers.store') }}" method="POST" id="supplierForm">
        @csrf

        <div class="row">
            {{-- Kolom Kiri --}}
            <div class="col-lg-8">

                {{-- Data Supplier --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-building"></i></div>
                        <h3>Data Supplier</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label-apms">Nama Supplier <span class="required-mark">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}"
                                        placeholder="Contoh: PT. Aroma Nusantara"
                                        maxlength="100" required>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Kode Supplier</label>
                                    <input type="text" name="code" id="code"
                                        class="form-control @error('code') is-invalid @enderror"
                                        value="{{ old('code') }}"
                                        placeholder="SUP-001"
                                        maxlength="20"
                                        style="text-transform:uppercase; font-family:monospace;">
                                    @error('code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    <small class="text-muted">Kosongkan untuk auto-generate</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Nama Kontak / PIC</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user text-muted"></i></span></div>
                                        <input type="text" name="contact_person"
                                            class="form-control @error('contact_person') is-invalid @enderror"
                                            value="{{ old('contact_person') }}"
                                            placeholder="Nama penanggung jawab">
                                    </div>
                                    @error('contact_person') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span></div>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}"
                                            placeholder="supplier@email.com">
                                    </div>
                                    @error('email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Nomor Telepon</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone text-muted"></i></span></div>
                                        <input type="text" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}"
                                            placeholder="08xx-xxxx-xxxx">
                                    </div>
                                    @error('phone') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">NPWP</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card text-muted"></i></span></div>
                                        <input type="text" name="npwp"
                                            class="form-control @error('npwp') is-invalid @enderror"
                                            value="{{ old('npwp') }}"
                                            placeholder="00.000.000.0-000.000">
                                    </div>
                                    @error('npwp') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <h3>Alamat</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label-apms">Alamat Lengkap</label>
                            <textarea name="address" rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="Jalan, nomor, RT/RW, kelurahan/desa...">{{ old('address') }}</textarea>
                            @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Kota</label>
                                    <input type="text" name="city"
                                        class="form-control @error('city') is-invalid @enderror"
                                        value="{{ old('city') }}"
                                        placeholder="Jakarta">
                                    @error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Provinsi</label>
                                    <input type="text" name="province"
                                        class="form-control @error('province') is-invalid @enderror"
                                        value="{{ old('province') }}"
                                        placeholder="DKI Jakarta">
                                    @error('province') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Kode Pos</label>
                                    <input type="text" name="postal_code"
                                        class="form-control @error('postal_code') is-invalid @enderror"
                                        value="{{ old('postal_code') }}"
                                        placeholder="12345" maxlength="5">
                                    @error('postal_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informasi Bank --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-university"></i></div>
                        <h3>Informasi Bank</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Nama Bank</label>
                                    <select name="bank_name" class="form-control @error('bank_name') is-invalid @enderror">
                                        <option value="">-- Pilih Bank --</option>
                                        @foreach(['BCA','BRI','BNI','Mandiri','CIMB Niaga','Danamon','Permata','BTN','BRI Syariah','BSI','Jenius','Neo Bank','Lainnya'] as $bank)
                                        <option value="{{ $bank }}" {{ old('bank_name') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Nomor Rekening</label>
                                    <input type="text" name="bank_account_number"
                                        class="form-control @error('bank_account_number') is-invalid @enderror"
                                        value="{{ old('bank_account_number') }}"
                                        placeholder="1234567890"
                                        style="font-family:monospace; letter-spacing:.05em;">
                                    @error('bank_account_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-apms">Atas Nama</label>
                                    <input type="text" name="bank_account_name"
                                        class="form-control @error('bank_account_name') is-invalid @enderror"
                                        value="{{ old('bank_account_name') }}"
                                        placeholder="Nama pemilik rekening">
                                    @error('bank_account_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan --}}
            <div class="col-lg-4">

                {{-- Status --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-toggle-on"></i></div>
                        <h3>Status Supplier</h3>
                    </div>
                    <div class="card-body">
                        <div class="status-toggle">
                            <input type="radio" name="status" id="status_active" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <label for="status_active" class="status-option">
                                <i class="fas fa-check-circle text-success"></i> Aktif
                            </label>
                            <input type="radio" name="status" id="status_inactive" value="inactive" {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            <label for="status_inactive" class="status-option">
                                <i class="fas fa-times-circle text-secondary"></i> Nonaktif
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">Supplier aktif dapat digunakan pada Purchase Order.</small>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-sticky-note"></i></div>
                        <h3>Catatan Internal</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="form-label-apms">Catatan <span class="char-counter"><span id="noteCount">0</span>/500</span></label>
                            <textarea name="notes" id="notes" rows="5"
                                class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Catatan khusus tentang supplier ini..."
                                maxlength="500">{{ old('notes') }}</textarea>
                            @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Term Pembayaran --}}
                <div class="card card-apms">
                    <div class="card-header">
                        <div class="section-icon"><i class="fas fa-calendar-check"></i></div>
                        <h3>Term Pembayaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label-apms">Tempo Pembayaran</label>
                            <div class="input-group">
                                <input type="number" name="payment_term_days"
                                    class="form-control @error('payment_term_days') is-invalid @enderror"
                                    value="{{ old('payment_term_days', 30) }}"
                                    min="0" max="365" placeholder="30">
                                <div class="input-group-append"><span class="input-group-text">hari</span></div>
                            </div>
                            @error('payment_term_days') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            <small class="text-muted">0 = bayar langsung (cash on delivery)</small>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary-apms btn-block">
                        <i class="fas fa-save mr-1"></i> Simpan Supplier
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-block" style="border-radius:8px;">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate code from name
    $('#name').on('input', function() {
        if (!$('#code').val()) {
            const name = $(this).val();
            const words = name.toUpperCase().split(' ').filter(w => w.length > 0);
            let code = '';
            if (words.length === 1) {
                code = 'SUP-' + words[0].substring(0, 4);
            } else {
                code = 'SUP-' + words.map(w => w[0]).join('').substring(0, 4);
            }
            $('#code').val(code);
        }
    });

    // Char counter for notes
    $('#notes').on('input', function() {
        $('#noteCount').text($(this).val().length);
    });
    $('#noteCount').text($('#notes').val().length);

    // Format kode supplier to uppercase
    $('#code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>
@endpush
