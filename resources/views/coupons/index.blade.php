@extends('layouts.app')

@section('title', 'Manajemen Kupon')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Daftar Kupon</h3>
                    <a href="{{ route('coupons.create') }}" class="btn btn-primary-apms ml-auto">
                        <i class="fas fa-plus"></i> Kupon Baru
                    </a>
                </div>
                <div class="card-body">
                    @if($coupons->count())
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tipe</th>
                                <th>Nilai</th>
                                <th>Berlaku Hingga</th>
                                <th>Digunakan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $coupon)
                            <tr>
                                <td><strong>{{ $coupon->code }}</strong></td>
                                <td><span class="badge badge-info">{{ $coupon->type }}</span></td>
                                <td>
                                    @if($coupon->is_percentage)
                                        {{ $coupon->value }}%
                                    @else
                                        Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>{{ $coupon->expiration_date ? $coupon->expiration_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $coupon->used_count }} / {{ $coupon->max_usage }}</td>
                                <td>
                                    @if($coupon->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info">Belum ada kupon</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
