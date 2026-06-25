@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Edit Pelanggan</h3>
                </div>
                <form action="{{ route('customers.update', $customer->id) }}" method="POST" id="customerForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Pelanggan *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon *</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                            @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $customer->email) }}">
                            @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="type">Tipe Pelanggan *</label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="retail" {{ old('type', $customer->type) == 'retail' ? 'selected' : '' }}>Retail</option>
                                <option value="wholesale" {{ old('type', $customer->type) == 'wholesale' ? 'selected' : '' }}>Grosir</option>
                                <option value="vip" {{ old('type', $customer->type) == 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="is_active">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-apms">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pelanggan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Kode Pelanggan</th>
                            <td>{{ $customer->customer_code }}</td>
                        </tr>
                        <tr>
                            <th>Total Transaksi</th>
                            <td>{{ $customer->transactions->count() }}</td>
                        </tr>
                        <tr>
                            <th>Total Belanja</th>
                            <td>Rp {{ number_format($customer->transactions->sum('total_amount'), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Poin Loyalty</th>
                            <td>{{ floor($customer->transactions->sum('total_amount') / 10000) }}</td>
                        </tr>
                        <tr>
                            <th>Terdaftar</th>
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#customerForm').submit(function(e) {
        if (!$('#name').val() || !$('#phone').val() || !$('#type').val()) {
            e.preventDefault();
            Swal.fire('Error', 'Harap isi semua field yang diperlukan', 'error');
            return false;
        }
    });
});
</script>
@endpush
