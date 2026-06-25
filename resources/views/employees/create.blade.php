@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="container-fluid pt-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h4 class="font-weight-bold mb-0"><i class="fas fa-user-plus text-primary mr-2"></i>Tambah Karyawan Baru</h4>
                    <small class="text-muted">Pilih tipe karyawan terlebih dahulu</small>
                </div>
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf

                    {{-- Tipe Karyawan --}}
                    <div class="card-body pb-0">
                        <div class="btn-group btn-group-lg w-100 mb-4" role="group">
                            <input type="radio" class="btn-check" name="is_store_employee" id="typeLogin" value="0" autocomplete="off" checked onchange="toggleType()">
                            <label class="btn btn-outline-primary py-3" for="typeLogin">
                                <i class="fas fa-user-shield fa-lg d-block mb-1"></i>
                                <span class="font-weight-bold">Akses Login</span>
                                <small class="d-block text-muted mt-1">Dapat login dan menggunakan sistem</small>
                            </label>
                            <input type="radio" class="btn-check" name="is_store_employee" id="typeStore" value="1" autocomplete="off" onchange="toggleType()">
                            <label class="btn btn-outline-secondary py-3" for="typeStore">
                                <i class="fas fa-user-clock fa-lg d-block mb-1"></i>
                                <span class="font-weight-bold">Karyawan Toko</span>
                                <small class="d-block text-muted mt-1">Hanya absensi — tidak bisa login sistem</small>
                            </label>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Row: Personal Info -->
                        <div class="row">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-id-card mr-2"></i>Informasi Pribadi</h5></div>
                            <div class="col-md-4 form-group">
                                <label>Nama Panggilan <span class="text-danger">*</span></label>
                                <input type="text" name="nickname" class="form-control @error('nickname') is-invalid @enderror" placeholder="Nama Akrab" required>
                                @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="Sesuai KTP" required>
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label>NIK (KTP)</label>
                                <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" maxlength="16">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Jenis Kelamin</label>
                                <select name="gender" class="form-control">
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="place_of_birth" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tgl Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Agama</label>
                                <select name="religion" class="form-control">
                                    <option value="islam">Islam</option>
                                    <option value="protestan">Protestan</option>
                                    <option value="katolik">Katolik</option>
                                    <option value="hindu">Hindu</option>
                                    <option value="buddha">Buddha</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row: Job Info -->
                        <div class="row mt-4">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-briefcase mr-2"></i>Informasi Pekerjaan</h5></div>
                            <div class="col-md-3 form-group">
                                <label>ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control" placeholder="AGP-XXX">
                            </div>
                            <div class="col-md-3 form-group" id="roleField">
                                <label>Posisi <span class="text-danger">*</span></label>
                                <select name="role" class="form-control" required>
                                    <option value="cashier">Kasir</option>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="admin">Admin Cabang</option>
                                    <option value="admin_pusat">Admin Pusat</option>
                                    <option value="packing">Packing</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Status</label>
                                <select name="employment_status" class="form-control">
                                    <option value="permanent">Tetap</option>
                                    <option value="contract">Kontrak</option>
                                    <option value="probation">Masa Percobaan</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tgl Masuk</label>
                                <input type="date" name="join_date" class="form-control">
                            </div>
                        </div>

                        <!-- Row: Financial Info -->
                        <div class="row mt-4">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-wallet mr-2"></i>Informasi Keuangan</h5></div>
                            <div class="col-md-3 form-group">
                                <label>Nama Bank</label>
                                <input type="text" name="bank_name" class="form-control" placeholder="BCA / Mandiri">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>No. Rekening</label>
                                <input type="text" name="bank_account_number" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Pemilik Rekening</label>
                                <input type="text" name="bank_account_holder" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Gaji Pokok</label>
                                <input type="number" name="basic_salary" class="form-control" placeholder="0">
                            </div>
                        </div>

                        <!-- Row: Contact -->
                        <div class="row mt-4">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-address-book mr-2"></i>Kontak & Darurat</h5></div>
                            <div class="col-md-3 form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Nomor HP</label>
                                <input type="text" name="phone" class="form-control" placeholder="08XX...">
                            </div>
                            <div class="col-md-3 form-group" id="passwordField">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                <small class="text-muted" id="passHelp">Min. 8 karakter, huruf besar, angka, simbol</small>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Kontak Darurat (Nama)</label>
                                <input type="text" name="emergency_contact_name" class="form-control mb-1" placeholder="Nama">
                                <input type="text" name="emergency_contact_phone" class="form-control" placeholder="No HP">
                            </div>
                        </div>

                        <input type="hidden" name="name" id="hiddenName" value="">
                    </div>
                    <div class="card-footer bg-white border-top d-flex justify-content-between py-3">
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary-apms px-4" onclick="disableBtn(this, 'Menyimpan...')"><i class="fas fa-save mr-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleType() {
    const isStore = document.getElementById('typeStore').checked;
    document.getElementById('roleField').style.display = isStore ? 'none' : 'block';
    document.getElementById('passwordField').style.display = isStore ? 'none' : 'block';
    document.getElementById('passHelp').textContent = isStore ? 'Tidak perlu password (tidak bisa login)' : 'Min. 8 karakter, huruf besar, angka, simbol';
    const roleField = document.querySelector('[name="role"]');
    const passField = document.querySelector('[name="password"]');
    if (roleField) roleField.required = !isStore;
    if (passField) passField.required = !isStore;
    // Copy nickname to name field for validation
    document.getElementById('hiddenName').value = document.querySelector('[name="nickname"]').value || document.querySelector('[name="full_name"]').value;
}
function disableBtn(btn, text) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + text;
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('[name="nickname"]').addEventListener('input', function() {
        document.getElementById('hiddenName').value = this.value;
    });
});
</script>
@endpush
