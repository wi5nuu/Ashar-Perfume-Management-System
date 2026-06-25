@extends('layouts.app')

@section('title', 'Tambah Kupon')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Kupon Baru</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('coupons.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>Kode Kupon</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" required>
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Tipe</label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Pilih Tipe</option>
                                <option value="discount">Diskon</option>
                                <option value="bonus">Bonus</option>
                                <option value="cashback">Cashback</option>
                                <option value="other">Lainnya</option>
                            </select>
                            @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nilai</label>
                                    <input type="number" name="value" step="0.01" class="form-control @error('value') is-invalid @enderror" required>
                                    @error('value') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_percentage" value="1"> Persentase
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Berlaku Hingga</label>
                            <input type="date" name="expiration_date" class="form-control @error('expiration_date') is-invalid @enderror" required>
                            @error('expiration_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Maksimal Penggunaan</label>
                            <input type="number" name="max_usage" class="form-control @error('max_usage') is-invalid @enderror" value="1" required>
                            @error('max_usage') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
