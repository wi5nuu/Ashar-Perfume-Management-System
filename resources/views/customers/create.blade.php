@extends('layouts.app')

@section('title', 'Pelanggan Baru')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Tambah Pelanggan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Pelanggan</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <form action="{{ route('customers.store') }}" method="POST" id="customerForm" novalidate>
        @csrf
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">

                <!-- Seksi Data Pribadi -->
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-card mr-2" style="color: var(--primary);"></i>Data Pribadi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label-apms">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           id="name"
                                           value="{{ old('name') }}"
                                           placeholder="Masukkan nama lengkap pelanggan"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Nama sesuai identitas resmi</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">NIK (KTP)</label>
                                    <input type="text"
                                           name="nik"
                                           id="nik"
                                           class="form-control @error('nik') is-invalid @enderror"
                                           maxlength="16"
                                           value="{{ old('nik') }}"
                                           placeholder="16 digit NIK">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Opsional, 16 digit</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Jenis Kelamin</label>
                                    <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                            Laki-laki
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                            Perempuan
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="form-label-apms">Alamat Lengkap</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              name="address"
                                              rows="3"
                                              placeholder="Jl. Nama Jalan, No. XX, Kelurahan, Kecamatan, Kota">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seksi Kontak -->
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-phone-alt mr-2" style="color: var(--primary);"></i>Informasi Kontak
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Nomor Telepon <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        </div>
                                        <input type="text"
                                               name="phone"
                                               id="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               value="{{ old('phone') }}"
                                               placeholder="08xx-xxxx-xxxx"
                                               required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-apms">Alamat Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email"
                                               name="email"
                                               id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="email@contoh.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="form-label-apms">Aroma Favorit</label>
                                    <input type="text"
                                           class="form-control @error('aroma_preferences') is-invalid @enderror"
                                           name="aroma_preferences"
                                           value="{{ old('aroma_preferences') }}"
                                           placeholder="Contoh: Oud, Floral, Fresh, Woody...">
                                    @error('aroma_preferences')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Preferensi aroma pelanggan untuk rekomendasi produk</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seksi Catatan -->
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note mr-2" style="color: var(--primary);"></i>Catatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="form-label-apms">Catatan Tambahan</label>
                            <textarea class="form-control"
                                      name="notes"
                                      rows="3"
                                      placeholder="Catatan internal tentang pelanggan ini...">{{ old('notes') }}</textarea>
                            <small class="form-text text-muted">Catatan ini hanya terlihat oleh staf internal</small>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="col-lg-4">

                <!-- Seksi Tipe & Status -->
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tag mr-2" style="color: var(--primary);"></i>Tipe & Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label-apms">Tipe Pelanggan <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="retail" {{ old('type') == 'retail' ? 'selected' : '' }}>
                                    Retail
                                </option>
                                <option value="wholesale" {{ old('type') == 'wholesale' ? 'selected' : '' }}>
                                    Grosir (Wholesale)
                                </option>
                                <option value="vip" {{ old('type') == 'vip' ? 'selected' : '' }}>
                                    VIP
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipe Description Badges -->
                        <div id="typeDescription">
                            <div id="desc-retail" class="type-desc-card p-3 rounded" style="background: #f8f9fa; display: none;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-modern badge-secondary mr-2">Retail</span>
                                    <small class="text-muted">Pelanggan Eceran</small>
                                </div>
                                <small class="text-muted">Pelanggan individu yang membeli dalam jumlah normal. Harga standar berlaku.</small>
                            </div>
                            <div id="desc-wholesale" class="type-desc-card p-3 rounded" style="background: #e3f2fd; display: none;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-modern badge-info mr-2">Grosir</span>
                                    <small class="text-muted">Reseller / Distributor</small>
                                </div>
                                <small class="text-muted">Pelanggan yang membeli dalam jumlah besar. Mendapat harga grosir khusus.</small>
                            </div>
                            <div id="desc-vip" class="type-desc-card p-3 rounded" style="background: #fff8e1; display: none;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-modern badge-warning mr-2">VIP</span>
                                    <small class="text-muted">Pelanggan Istimewa</small>
                                </div>
                                <small class="text-muted">Pelanggan premium dengan treatment khusus, diskon terbaik, dan layanan prioritas.</small>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="form-label-apms">Status Awal</label>
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="isActive"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="isActive">
                                    <span id="statusLabel" class="font-weight-bold text-success">Aktif</span>
                                </label>
                            </div>
                            <small class="form-text text-muted">Pelanggan aktif dapat melakukan transaksi</small>
                        </div>
                    </div>
                </div>

                <!-- Preview Avatar -->
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle mr-2" style="color: var(--primary);"></i>Preview
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-circle-preview mx-auto mb-3" id="avatarPreview">--</div>
                        <h6 id="namePreview" class="font-weight-bold mb-1">Nama Pelanggan</h6>
                        <p id="typePreview" class="text-muted mb-0 small">Tipe Pelanggan</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card card-apms">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary-apms btn-block mb-2" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>Simpan Pelanggan
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-label-apms {
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
    margin-bottom: 0.4rem;
}

.avatar-circle-preview {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 28px;
}

.badge-modern {
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 4px;
    font-size: 0.75rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
    color: #6c757d;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    box-shadow: none;
    border-color: #ced4da;
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: var(--primary);
}

.input-group:focus-within .form-control {
    border-color: var(--primary);
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.15);
}

.form-control.is-invalid {
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--primary);
    border-color: var(--primary-dark);
}

.type-desc-card {
    border: 1px solid rgba(0,0,0,0.06);
}

@media (max-width: 768px) {
    .col-lg-4 {
        margin-top: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Live avatar preview
    $('#name').on('input', function() {
        const name = $(this).val().trim();
        const initials = name.length > 0
            ? name.split(' ').slice(0, 2).map(w => w[0].toUpperCase()).join('')
            : '--';
        $('#avatarPreview').text(initials);
        $('#namePreview').text(name || 'Nama Pelanggan');
    });

    // Type selector live description
    $('select[name="type"]').on('change', function() {
        const val = $(this).val();
        $('.type-desc-card').hide();
        if (val) {
            $('#desc-' + val).fadeIn(200);
        }
        const typeLabels = { retail: 'Pelanggan Retail', wholesale: 'Pelanggan Grosir', vip: 'Pelanggan VIP' };
        $('#typePreview').text(typeLabels[val] || 'Tipe Pelanggan');
    });

    // Status switch label
    $('#isActive').on('change', function() {
        if ($(this).is(':checked')) {
            $('#statusLabel').text('Aktif').removeClass('text-danger').addClass('text-success');
        } else {
            $('#statusLabel').text('Nonaktif').removeClass('text-success').addClass('text-danger');
        }
    });

    // NIK format (numbers only)
    $('#nik').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 16);
    });

    // Phone format
    $('#phone').on('input', function() {
        this.value = this.value.replace(/[^0-9\-+]/g, '');
    });

    // Form submission with loading state
    $('#customerForm').on('submit', function(e) {
        const btn = $('#submitBtn');
        const name = $('#name').val().trim();
        const phone = $('input[name="phone"]').val().trim();
        const type = $('select[name="type"]').val();

        if (!name || !phone || !type) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Lengkap',
                text: 'Nama, nomor telepon, dan tipe pelanggan wajib diisi',
                confirmButtonColor: 'var(--primary)'
            });
            return false;
        }

        btn.prop('disabled', true)
           .html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
    });
});
</script>
@endpush
