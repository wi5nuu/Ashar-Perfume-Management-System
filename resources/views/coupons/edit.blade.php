@extends('layouts.app')

@section('title', 'Edit Kupon')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Kupon</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('coupons.update', $coupon) }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="form-group">
                            <label>Kode Kupon</label>
                            <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
                        </div>

                        <div class="form-group">
                            <label>Tipe</label>
                            <select name="type" class="form-control" required>
                                <option value="discount" {{ $coupon->type == 'discount' ? 'selected' : '' }}>Diskon</option>
                                <option value="bonus" {{ $coupon->type == 'bonus' ? 'selected' : '' }}>Bonus</option>
                                <option value="cashback" {{ $coupon->type == 'cashback' ? 'selected' : '' }}>Cashback</option>
                                <option value="other" {{ $coupon->type == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nilai</label>
                                    <input type="number" name="value" step="0.01" class="form-control" value="{{ $coupon->value }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_percentage" value="1" {{ $coupon->is_percentage ? 'checked' : '' }}> Persentase
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Berlaku Hingga</label>
                            <input type="date" name="expiration_date" class="form-control" value="{{ $coupon->expiration_date->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Maksimal Penggunaan</label>
                            <input type="number" name="max_usage" class="form-control" value="{{ $coupon->max_usage }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
