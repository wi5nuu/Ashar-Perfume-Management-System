@extends('layouts.app')
@section('title', ($isEdit ? 'Edit' : 'Tambah') . ' Cabang')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="font-weight-bold mb-0 text-dark">
                <i class="fas fa-store-alt mr-2 text-primary"></i>{{ $isEdit ? 'Edit Cabang' : 'Tambah Cabang Baru' }}
            </h3>
            <small class="text-muted">Isi data operasional lengkap untuk cabang toko</small>
        </div>
        <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('branches.update', $branch) : route('branches.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        
        <div class="row">
            <!-- Left Column: Basic Info -->
            <div class="col-md-6">
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>Informasi Dasar
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="font-weight-bold small">Nama Cabang <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Kode Cabang</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $branch->code) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small">Alamat Lengkap</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $branch->address) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Kota / Kabupaten</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $branch->city) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Tgl Buka</label>
                                <input type="date" name="opening_date" class="form-control" value="{{ old('opening_date', $branch->opening_date) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row: Shift Info -->
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Operasional & Shift
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Jam Mulai Shift</label>
                                <input type="time" name="shift_start" class="form-control" value="{{ old('shift_start', $branch->shift_start) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Jam Selesai Shift</label>
                                <input type="time" name="shift_end" class="form-control" value="{{ old('shift_end', $branch->shift_end) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small">Jam Operasional (Deskripsi)</label>
                            <input type="text" name="operational_hours" class="form-control" value="{{ old('operational_hours', $branch->operational_hours) }}" placeholder="Contoh: 08:00 - 22:00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact & Management -->
            <div class="col-md-6">
                <div class="card card-apms border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3 font-weight-bold text-primary">
                        <i class="fas fa-user-tie mr-2"></i>Kontak & Manajer
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small">Manajer / PIC Cabang</label>
                            <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $branch->manager_name) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">No. Telepon Cabang</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Email Cabang</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $branch->email) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small">Catatan Internal</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $branch->notes) }}</textarea>
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch custom-switch-md">
                                <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" value="1" {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="isActive">Aktifkan Cabang</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-5">
            <button type="submit" class="btn btn-primary-apms px-5 shadow">
                <i class="fas fa-save mr-1"></i> Simpan Data Cabang
            </button>
        </div>
    </form>
</div>
@endsection
