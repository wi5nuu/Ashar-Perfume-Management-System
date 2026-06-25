@extends('layouts.app')

@section('title', 'Pelanggan Baru')

@section('content')
<div class="container-fluid pt-3">
    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h3 class="card-title text-primary font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Tambah Pelanggan Baru</h3>
        </div>
        <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
            @csrf
            <div class="card-body">
                <div class="row">
                    <!-- Personal Info -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold small">Nama Lengkap *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">NIK (KTP)</label>
                                <input type="text" name="nik" class="form-control" maxlength="16" value="{{ old('nik') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Jenis Kelamin</label>
                                <select name="gender" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small">Alamat Lengkap</label>
                            <textarea class="form-control" name="address" rows="3">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- Contact & Type -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Nomor Telepon *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold small">Tipe Pelanggan *</label>
                                <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="retail" {{ old('type') == 'retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="wholesale" {{ old('type') == 'wholesale' ? 'selected' : '' }}>Grosir</option>
                                    <option value="vip" {{ old('type') == 'vip' ? 'selected' : '' }}>VIP</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold small">Aroma Favorit</label>
                            <input type="text" class="form-control" name="aroma_preferences" value="{{ old('aroma_preferences') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white border-0 text-right">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary px-4 mr-2">Batal</a>
                <button type="submit" class="btn btn-primary-apms px-4 shadow-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
