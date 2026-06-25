@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')
<div class="container-fluid pt-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h4 class="font-weight-bold mb-0"><i class="fas fa-user-edit text-primary mr-2"></i>Edit Karyawan</h4>
                    <small class="text-muted">{{ $employee->can_login ? 'Akses Login' : 'Karyawan Toko' }} — {{ $employee->full_name ?? $employee->name }}</small>
                </div>
                <form action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-id-card mr-2"></i>Informasi Pribadi</h5></div>
                            <div class="col-md-4 form-group">
                                <label>Nama Panggilan <span class="text-danger">*</span></label>
                                <input type="text" name="nickname" class="form-control @error('nickname') is-invalid @enderror" value="{{ old('nickname', $employee->nickname ?? $employee->name) }}" required>
                                @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $employee->full_name ?? $employee->name) }}" required>
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label>NIK (KTP)</label>
                                <input type="text" name="nik" class="form-control" value="{{ old('nik', $employee->nik ?? '') }}" placeholder="16 digit NIK" maxlength="16">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Jenis Kelamin</label>
                                <select name="gender" class="form-control">
                                    <option value="">— Pilih —</option>
                                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="place_of_birth" class="form-control" value="{{ old('place_of_birth', $employee->place_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tgl Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Agama</label>
                                <select name="religion" class="form-control">
                                    <option value="">— Pilih —</option>
                                    <option value="islam" {{ old('religion', $employee->religion) == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="protestan" {{ old('religion', $employee->religion) == 'protestan' ? 'selected' : '' }}>Protestan</option>
                                    <option value="katolik" {{ old('religion', $employee->religion) == 'katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="hindu" {{ old('religion', $employee->religion) == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ old('religion', $employee->religion) == 'buddha' ? 'selected' : '' }}>Buddha</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Status Pernikahan</label>
                                <select name="marital_status" class="form-control">
                                    <option value="">— Pilih —</option>
                                    <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Lajang</option>
                                    <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Menikah</option>
                                    <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Cerai</option>
                                    <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Duda/Janda</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="last_education" class="form-control" value="{{ old('last_education', $employee->last_education ?? '') }}" placeholder="SMA/SMK / D3 / S1">
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-briefcase mr-2"></i>Informasi Pekerjaan</h5></div>
                            <div class="col-md-3 form-group">
                                <label>ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $employee->employee_id ?? '') }}" placeholder="AGP-XXX">
                            </div>
                            <div class="col-md-3 form-group" id="editRoleField" style="{{ $employee->can_login ? '' : 'display:none' }}">
                                <label>Posisi</label>
                                <select name="role" class="form-control">
                                    <option value="cashier" {{ $employee->role == 'cashier' ? 'selected' : '' }}>Kasir</option>
                                    <option value="manager" {{ $employee->role == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="supervisor" {{ $employee->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin Cabang</option>
                                    <option value="admin_pusat" {{ $employee->role == 'admin_pusat' ? 'selected' : '' }}>Admin Pusat</option>
                                    <option value="packing" {{ $employee->role == 'packing' ? 'selected' : '' }}>Packing</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Status Karyawan</label>
                                <select name="employment_status" class="form-control">
                                    <option value="">— Pilih —</option>
                                    <option value="permanent" {{ old('employment_status', $employee->employment_status) == 'permanent' ? 'selected' : '' }}>Tetap</option>
                                    <option value="contract" {{ old('employment_status', $employee->employment_status) == 'contract' ? 'selected' : '' }}>Kontrak</option>
                                    <option value="probation" {{ old('employment_status', $employee->employment_status) == 'probation' ? 'selected' : '' }}>Masa Percobaan</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tgl Masuk</label>
                                <input type="date" name="join_date" class="form-control" value="{{ old('join_date', $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-wallet mr-2"></i>Informasi Keuangan</h5></div>
                            <div class="col-md-3 form-group">
                                <label>Nama Bank</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name ?? '') }}" placeholder="BCA / Mandiri">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>No. Rekening</label>
                                <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $employee->bank_account_number ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Pemilik Rekening</label>
                                <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder', $employee->bank_account_holder ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>NPWP</label>
                                <input type="text" name="npwp" class="form-control" value="{{ old('npwp', $employee->npwp ?? '') }}" placeholder="XX.XXX.XXX.X-XXX.XXX">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Gaji Pokok</label>
                                <input type="number" name="basic_salary" class="form-control" value="{{ old('basic_salary', $employee->basic_salary ?? '') }}" placeholder="0">
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12"><h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-address-book mr-2"></i>Kontak & Darurat</h5></div>
                            <div class="col-md-4 form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required {{ !auth()->user()->isOwner() && !$employee->can_login ? 'readonly' : '' }}>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if(!auth()->user()->isOwner() && !$employee->can_login)
                                <small class="text-muted">Email karyawan toko hanya dapat diubah oleh Owner</small>
                                @endif
                            </div>
                            @if(auth()->user()->isOwner())
                            <div class="col-md-4 form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                <small class="text-muted">Min. 6 karakter — kosongkan jika tidak ingin mengubah password</small>
                            </div>
                            @endif
                            <div class="col-md-4 form-group">
                                <label>Nomor HP</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone ?? '') }}" placeholder="08XX...">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Skills / Keahlian</label>
                                <textarea name="skills" class="form-control" rows="2">{{ old('skills', $employee->skills ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Alamat Tinggal</label>
                                <textarea name="living_address" class="form-control" rows="2">{{ old('living_address', $employee->living_address ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Asal</label>
                                <input type="text" name="origin" class="form-control" value="{{ old('origin', $employee->origin ?? '') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Kontak Darurat (Nama)</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $employee->emergency_contact_name ?? '') }}" placeholder="Nama">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>No. HP Darurat</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone ?? '') }}" placeholder="No HP">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Hubungan Darurat</label>
                                <select name="emergency_contact_relation" class="form-control">
                                    <option value="">— Pilih —</option>
                                    <option value="Orang Tua" {{ old('emergency_contact_relation', $employee->emergency_contact_relation) == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                                    <option value="Suami/Istri" {{ old('emergency_contact_relation', $employee->emergency_contact_relation) == 'Suami/Istri' ? 'selected' : '' }}>Suami/Istri</option>
                                    <option value="Saudara" {{ old('emergency_contact_relation', $employee->emergency_contact_relation) == 'Saudara' ? 'selected' : '' }}>Saudara</option>
                                    <option value="Teman" {{ old('emergency_contact_relation', $employee->emergency_contact_relation) == 'Teman' ? 'selected' : '' }}>Teman</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <div class="custom-control custom-switch mt-3">
                                    <input type="checkbox" name="is_staying_in_mess" class="custom-control-input" id="isMess" value="1" {{ old('is_staying_in_mess', $employee->is_staying_in_mess) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="isMess">Tinggal di Mess</label>
                                </div>
                            </div>
                            @if(auth()->user()->isOwner() && $employee->role !== 'owner' && $employee->id !== auth()->id())
                            <div class="col-md-4 form-group">
                                <div class="custom-control custom-switch mt-3">
                                    <input type="checkbox" name="can_login" class="custom-control-input" id="canLogin" value="1" {{ old('can_login', $employee->can_login) ? 'checked' : '' }} onchange="toggleEditType()">
                                    <label class="custom-control-label font-weight-medium" for="canLogin">Akses Login Sistem</label>
                                </div>
                                <small class="text-muted d-block mt-1" style="margin-left:2.5rem">Nonaktifkan jika karyawan hanya untuk absensi</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top d-flex justify-content-between py-3">
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary-apms px-4" onclick="disableBtn(this, 'Menyimpan...')"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEditType() {
    const canLogin = document.getElementById('canLogin').checked;
    document.getElementById('editRoleField').style.display = canLogin ? 'block' : 'none';
}
function disableBtn(btn, text) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + text;
}
</script>
@endpush
