@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@push('styles')
<style>
:root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }
.page-header-apms {
    background: linear-gradient(135deg, var(--secondary) 0%, #3d4268 100%);
    border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff;
}
.page-header-apms .breadcrumb { background: transparent; padding: 0; margin: 0; }
.page-header-apms .breadcrumb-item,
.page-header-apms .breadcrumb-item a { color: rgba(255,255,255,.65); font-size: .82rem; }
.page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,.9); }
.page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.4); }
.card-apms { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(45,48,71,.07); }
.section-heading {
    font-size: .78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--primary); margin-bottom: 1.1rem;
    padding-bottom: .55rem; border-bottom: 2px solid rgba(255,107,53,.15);
    display: flex; align-items: center; gap: .5rem;
}
.form-control {
    border-radius: 8px; border: 1.5px solid #e0e3ef;
    font-size: .88rem; padding: .55rem .9rem; color: #2d3047;
    transition: border-color .15s, box-shadow .15s;
}
.form-control:focus {
    border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,.12); outline: none;
}
.form-control.is-invalid { border-color: #dc3545; }
label { font-size: .82rem; font-weight: 600; color: #5a5f7d; margin-bottom: .35rem; display: block; }
.type-selector { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
.type-card {
    flex: 1; border: 2px solid #e0e3ef; border-radius: 12px; padding: 1.25rem;
    cursor: pointer; transition: all .18s; background: #fff; text-align: center;
}
.type-card:hover { border-color: var(--primary); background: rgba(255,107,53,.03); }
.type-card.selected { border-color: var(--primary); background: rgba(255,107,53,.06); }
.type-card input[type="radio"] { display: none; }
.type-card .type-icon {
    width: 52px; height: 52px; border-radius: 12px; margin: 0 auto .75rem;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
}
.type-card .type-title { font-weight: 700; font-size: .9rem; color: var(--secondary); }
.type-card .type-desc { font-size: .77rem; color: #8a8fa8; margin-top: .25rem; }
.btn-primary-apms {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; color: #fff; border-radius: 8px;
    font-weight: 600; font-size: .88rem; padding: .6rem 1.4rem;
    transition: all .2s; box-shadow: 0 3px 10px rgba(255,107,53,.25);
}
.btn-primary-apms:hover { background: linear-gradient(135deg, var(--primary-dark), #c94d22); color: #fff; transform: translateY(-1px); box-shadow: 0 5px 15px rgba(255,107,53,.35); }
.form-section { background: #fff; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; border: 1px solid #f0f1f8; }
.required-star { color: #dc3545; }
</style>

@endpush

@section('content')
<div class="container-fluid pt-2 pb-4">
    {{-- Page Header --}}
    <div class="page-header-apms">
        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:.75rem;">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home mr-1"></i> Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Karyawan</a></li>
                        <li class="breadcrumb-item active">Tambah Karyawan</li>
                    </ol>
                </nav>
                <h4 class="mb-0 font-weight-bold" style="font-size:1.35rem;">
                    <i class="fas fa-user-plus mr-2" style="color:var(--primary);"></i>Tambah Karyawan Baru
                </h4>
                <p class="mb-0 mt-1" style="color:rgba(255,255,255,.6);font-size:.82rem;">Isi formulir berikut untuk menambahkan karyawan baru</p>
            </div>
            <a href="{{ route('employees.index') }}" class="btn" style="border:1.5px solid rgba(255,255,255,.4);color:#fff;border-radius:8px;font-size:.85rem;font-weight:600;padding:.5rem 1.1rem;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger border-0 rounded-lg mb-4" style="background:#fff5f5;border-left:4px solid #dc3545!important;">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-circle mr-2 text-danger"></i>
            <strong style="color:#c0392b;">Terdapat {{ $errors->count() }} kesalahan pada formulir:</strong>
        </div>
        <ul class="mb-0 pl-4" style="font-size:.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" id="employeeForm">
        @csrf
        <input type="hidden" name="name" id="hiddenName">

        {{-- Step 0: Tipe Karyawan --}}
        <div class="form-section mb-4">
            <div class="section-heading"><i class="fas fa-user-tag"></i> Tipe Karyawan</div>
            <div class="type-selector">
                <label class="type-card selected" id="cardLogin" for="typeLogin">
                    <input type="radio" name="is_store_employee" id="typeLogin" value="0" checked onchange="toggleType()">
                    <div class="type-icon" style="background:rgba(59,91,219,.1);color:#3b5bdb;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="type-title">Akses Login</div>
                    <div class="type-desc">Dapat login dan menggunakan sistem APMS</div>
                </label>
                <label class="type-card" id="cardStore" for="typeStore">
                    <input type="radio" name="is_store_employee" id="typeStore" value="1" onchange="toggleType()">
                    <div class="type-icon" style="background:rgba(255,107,53,.1);color:var(--primary);">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="type-title">Karyawan Toko</div>
                    <div class="type-desc">Hanya absensi — tidak bisa login sistem</div>
                </label>
            </div>
        </div>

        {{-- Seksi 1: Data Pribadi --}}
        <div class="form-section">
            <div class="section-heading"><i class="fas fa-id-card"></i> Data Pribadi</div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Nama Panggilan <span class="required-star">*</span></label>
                    <input type="text" name="nickname" id="nicknameField"
                           class="form-control @error('nickname') is-invalid @enderror"
                           value="{{ old('nickname') }}" placeholder="Nama akrab karyawan" required>
                    @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 form-group">
                    <label>Nama Lengkap <span class="required-star">*</span></label>
                    <input type="text" name="full_name"
                           class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name') }}" placeholder="Sesuai KTP" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 form-group">
                    <label>NIK (KTP)</label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                           value="{{ old('nik') }}" placeholder="16 digit NIK" maxlength="16">
                    @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Jenis Kelamin</label>
                    <select name="gender" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="male" {{ old('gender')=='male'?'selected':'' }}>Laki-laki</option>
                        <option value="female" {{ old('gender')=='female'?'selected':'' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="place_of_birth" class="form-control"
                           value="{{ old('place_of_birth') }}" placeholder="Kota kelahiran">
                </div>
                <div class="col-md-3 form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" class="form-control"
                           value="{{ old('date_of_birth') }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Agama</label>
                    <select name="religion" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="islam"     {{ old('religion')=='islam'?'selected':'' }}>Islam</option>
                        <option value="protestan" {{ old('religion')=='protestan'?'selected':'' }}>Protestan</option>
                        <option value="katolik"   {{ old('religion')=='katolik'?'selected':'' }}>Katolik</option>
                        <option value="hindu"     {{ old('religion')=='hindu'?'selected':'' }}>Hindu</option>
                        <option value="buddha"    {{ old('religion')=='buddha'?'selected':'' }}>Buddha</option>
                        <option value="konghucu"  {{ old('religion')=='konghucu'?'selected':'' }}>Konghucu</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Seksi 2: Kontak --}}
        <div class="form-section">
            <div class="section-heading"><i class="fas fa-address-book"></i> Informasi Kontak</div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="2"
                              placeholder="Jalan, RT/RW, Kelurahan, Kecamatan">{{ old('address') }}</textarea>
                </div>
                <div class="col-md-3 form-group">
                    <label>Nomor Telepon</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;">
                                <i class="fas fa-phone text-muted" style="font-size:.8rem;"></i>
                            </span>
                        </div>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                               style="border-radius:0 8px 8px 0;">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3 form-group" id="emailFieldGroup">
                    <label>Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;">
                                <i class="fas fa-envelope text-muted" style="font-size:.8rem;"></i>
                            </span>
                        </div>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="email@domain.com"
                               style="border-radius:0 8px 8px 0;">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Seksi 3: Pekerjaan --}}
        <div class="form-section">
            <div class="section-heading"><i class="fas fa-briefcase"></i> Data Pekerjaan</div>
            <div class="row">
                <div class="col-md-3 form-group" id="roleFieldGroup">
                    <label>Jabatan / Role <span class="required-star" id="roleRequired">*</span></label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror">
                        <option value="">-- Pilih Jabatan --</option>
                        <option value="cashier"   {{ old('role')=='cashier'?'selected':'' }}>Kasir</option>
                        <option value="manager"   {{ old('role')=='manager'?'selected':'' }}>Manager</option>
                        <option value="supervisor"{{ old('role')=='supervisor'?'selected':'' }}>Supervisor</option>
                        <option value="warehouse" {{ old('role')=='warehouse'?'selected':'' }}>Gudang</option>
                        <option value="admin"     {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Departemen</label>
                    <select name="department" class="form-control">
                        <option value="">-- Pilih Departemen --</option>
                        <option value="operations" {{ old('department')=='operations'?'selected':'' }}>Operasional</option>
                        <option value="sales"      {{ old('department')=='sales'?'selected':'' }}>Penjualan</option>
                        <option value="warehouse"  {{ old('department')=='warehouse'?'selected':'' }}>Gudang</option>
                        <option value="finance"    {{ old('department')=='finance'?'selected':'' }}>Keuangan</option>
                        <option value="hr"         {{ old('department')=='hr'?'selected':'' }}>SDM</option>
                        <option value="delivery"   {{ old('department')=='delivery'?'selected':'' }}>Pengiriman</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Tanggal Bergabung</label>
                    <input type="date" name="join_date" class="form-control"
                           value="{{ old('join_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3 form-group">
                    <label>Tipe Kontrak</label>
                    <select name="contract_type" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="permanent" {{ old('contract_type')=='permanent'?'selected':'' }}>Tetap</option>
                        <option value="contract"  {{ old('contract_type')=='contract'?'selected':'' }}>Kontrak</option>
                        <option value="probation" {{ old('contract_type')=='probation'?'selected':'' }}>Percobaan</option>
                        <option value="freelance" {{ old('contract_type')=='freelance'?'selected':'' }}>Freelance</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Seksi 4: Gaji --}}
        <div class="form-section">
            <div class="section-heading"><i class="fas fa-money-bill-wave"></i> Informasi Gaji</div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Gaji Pokok</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;font-size:.8rem;font-weight:700;">Rp</span>
                        </div>
                        <input type="number" name="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror"
                               value="{{ old('basic_salary') }}" placeholder="0" min="0"
                               style="border-radius:0 8px 8px 0;">
                        @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>Tunjangan</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;font-size:.8rem;font-weight:700;">Rp</span>
                        </div>
                        <input type="number" name="allowance" class="form-control"
                               value="{{ old('allowance', 0) }}" placeholder="0" min="0"
                               style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>Potongan BPJS</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;font-size:.8rem;font-weight:700;">Rp</span>
                        </div>
                        <input type="number" name="bpjs_deduction" class="form-control"
                               value="{{ old('bpjs_deduction', 0) }}" placeholder="0" min="0"
                               style="border-radius:0 8px 8px 0;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Seksi 5: Akses Sistem --}}
        <div class="form-section" id="loginSection">
            <div class="section-heading"><i class="fas fa-lock"></i> Akses Sistem</div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Username <span class="required-star">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;">
                                <i class="fas fa-at text-muted" style="font-size:.8rem;"></i>
                            </span>
                        </div>
                        <input type="text" name="username"
                               class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username') }}" placeholder="username unik"
                               style="border-radius:0 8px 8px 0;" autocomplete="new-password">
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>Password <span class="required-star">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0 bg-light" style="border-radius:8px 0 0 8px;">
                                <i class="fas fa-key text-muted" style="font-size:.8rem;"></i>
                            </span>
                        </div>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 karakter"
                               style="border-radius:0 8px 8px 0 !important;" autocomplete="new-password">
                        <div class="input-group-append">
                            <button type="button" class="btn bg-light border-0" onclick="togglePass()"
                                    style="border-radius:0 8px 8px 0;border:1.5px solid #e0e3ef;border-left:none;">
                                <i class="fas fa-eye" id="eyeIcon" style="font-size:.8rem;color:#8a8fa8;"></i>
                            </button>
                        </div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <small class="text-muted" id="passHelp" style="font-size:.75rem;">Min. 8 karakter, kombinasi huruf besar, angka, simbol</small>
                </div>
                <div class="col-md-4 form-group">
                    <label>Cabang</label>
                    <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                        <option value="">-- Pusat --</option>
                        @foreach(\App\Models\Branch::orderBy('name')->get() as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id')==$branch->id?'selected':'' }}>
                            {{ $branch->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-end mt-4" style="gap:.75rem;">
            <a href="{{ route('employees.index') }}" class="btn btn-light" style="border-radius:8px;font-weight:600;padding:.6rem 1.4rem;border:1.5px solid #e0e3ef;">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary-apms" onclick="disableBtn(this, 'Menyimpan...')">
                <i class="fas fa-save mr-1"></i> Simpan Karyawan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleType() {
    var isStore = document.getElementById('typeStore').checked;
    document.getElementById('cardLogin').classList.toggle('selected', !isStore);
    document.getElementById('cardStore').classList.toggle('selected', isStore);
    document.getElementById('loginSection').style.display = isStore ? 'none' : 'block';
    document.getElementById('passHelp').textContent = isStore
        ? 'Tidak perlu password (tidak bisa login)'
        : 'Min. 8 karakter, huruf besar, angka, simbol';
    var roleField = document.querySelector('[name="role"]');
    var passField = document.querySelector('[name="password"]');
    if (roleField) roleField.required = !isStore;
    if (passField) passField.required = !isStore;
    document.getElementById('hiddenName').value =
        document.getElementById('nicknameField').value ||
        document.querySelector('[name="full_name"]').value;
}
function togglePass() {
    var p = document.querySelector('[name="password"]');
    var i = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; i.classList.replace('fa-eye','fa-eye-slash'); }
    else { p.type = 'password'; i.classList.replace('fa-eye-slash','fa-eye'); }
}
function disableBtn(btn, text) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + text;
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nicknameField').addEventListener('input', function() {
        document.getElementById('hiddenName').value = this.value;
    });
});
</script>
@endpush
