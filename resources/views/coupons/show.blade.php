@extends('layouts.app')

@section('title', 'Detail Kupon')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Kupon</h3>
                    <div class="card-tools">
                        <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <a href="{{ route('coupons.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:180px;">Kode</th>
                                <td><strong>{{ $coupon->code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tipe</th>
                                <td><span class="badge badge-info">{{ $coupon->type }}</span></td>
                            </tr>
                            <tr>
                                <th>Nilai</th>
                                <td>
                                    @if($coupon->is_percentage)
                                        {{ $coupon->value }}%
                                    @else
                                        Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Berlaku Hingga</th>
                                <td>{{ $coupon->expiration_date ? $coupon->expiration_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Penggunaan</th>
                                <td>{{ $coupon->used_count }} / {{ $coupon->max_usage }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($coupon->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            @if($coupon->customer)
                            <tr>
                                <th>Pelanggan</th>
                                <td>{{ $coupon->customer?->name ?? '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
