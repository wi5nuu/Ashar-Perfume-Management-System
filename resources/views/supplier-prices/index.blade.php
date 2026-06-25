@extends('layouts.app')
@section('title', 'Harga Supplier')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-hand-holding-usd"></i> Harga Supplier</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Harga Supplier</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Add / Update Price Form --}}
    <div class="card card-apms mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i>Tambah / Update Harga</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('supplier-prices.store') }}" class="form-inline">
                @csrf
                <div class="form-group mr-2 mb-2">
                    <select name="supplier_id" class="form-control form-control-sm" required>
                        <option value="">-- Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $selectedSupplier == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="product_id" class="form-control form-control-sm" required>
                        <option value="">-- Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->size ?? '' }} {{ $p->unit ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="number" name="unit_cost" class="form-control form-control-sm" placeholder="Harga/Unit" min="0" step="100" required style="width:120px">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="number" name="minimum_order_qty" class="form-control form-control-sm" placeholder="Min. Order" min="1" style="width:100px">
                </div>
                <button type="submit" class="btn btn-primary-apms btn-sm mb-2">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    {{-- Price List --}}
    <div class="card card-apms">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Daftar Harga Supplier</h3>
        </div>
        <div class="card-body">
            {{-- Filter --}}
            <form method="GET" class="form-inline mb-3">
                <select name="supplier_id" class="form-control form-control-sm mr-2">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $selectedSupplier == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Supplier</th>
                            <th>Produk</th>
                            <th>Harga/Unit</th>
                            <th>Min. Order</th>
                            <th>Terakhir Quoted</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prices as $sp)
                        <tr>
                            <td>{{ $sp->supplier->name ?? '-' }}</td>
                            <td>{{ $sp->product->name ?? '-' }} <small class="text-muted">{{ $sp->product->size ?? '' }} {{ $sp->product->unit ?? '' }}</small></td>
                            <td>Rp {{ number_format($sp->unit_cost, 0, ',', '.') }}</td>
                            <td>{{ $sp->minimum_order_qty ?? '-' }}</td>
                            <td>{{ $sp->last_quoted_at ? $sp->last_quoted_at->format('d/m/Y') : '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('supplier-prices.destroy', $sp) }}" class="d-inline" onsubmit="return confirm('Hapus harga ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data harga supplier.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $prices->links() }}
        </div>
    </div>
</div>
@endsection
