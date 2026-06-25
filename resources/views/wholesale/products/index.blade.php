@extends('layouts.app')

@section('title', 'Produk Grosir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center flex-wrap">
                    <h3 class="card-title font-weight-bold text-primary-apms mb-0">
                        <i class="fas fa-boxes mr-2"></i> Produk Grosir
                    </h3>
                    @can('wholesale.manage')
                    <a href="{{ route('wholesale.products.create') }}" class="btn btn-primary-apms ml-auto">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Produk
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <form action="{{ route('wholesale.products.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Tipe</option>
                                    @foreach($types as $t)
                                    <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Satuan</th>
                                    <th>Harga/Unit</th>
                                    <th>Harga/Buah</th>
                                    <th>Harga/ml</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td class="font-weight-bold">{{ $product->name }}</td>
                                    <td><span class="badge badge-info">{{ ucfirst($product->type) }}</span></td>
                                    <td>{{ $product->unit }} @if($product->pieces_per_unit > 1)({{ $product->pieces_per_unit }} buah)@endif</td>
                                    <td>Rp {{ number_format($product->price_per_unit, 0, ',', '.') }}</td>
                                    <td>
                                        @if($product->pieces_per_unit > 1)
                                            <span class="text-primary font-weight-bold">Rp {{ number_format($product->price_per_piece, 0, ',', '.') }}</span>
                                        @else
                                            Rp {{ number_format($product->price_per_unit, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>@if($product->price_per_ml) Rp {{ number_format($product->price_per_ml, 0, ',', '.') }} @else - @endif</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('wholesale.manage')
                                        <a href="{{ route('wholesale.products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('wholesale.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">Belum ada produk grosir.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
