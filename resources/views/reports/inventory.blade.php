@extends('layouts.app')

@section('title', 'Laporan Inventory')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Laporan Inventory</h3>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    {{-- Low Stock --}}
                    <h5 class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Stok Rendah</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-sm">
                            <thead class="thead-warning">
                                <tr><th>Produk</th><th>Stok Saat Ini</th><th>Stok Minimum</th></tr>
                            </thead>
                            <tbody>
                                @forelse($lowStock as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td><span class="badge badge-warning">{{ $item->current_stock }}</span></td>
                                    <td>{{ $item->minimum_stock }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Tidak ada produk dengan stok rendah.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Out of Stock --}}
                    <h5 class="text-danger"><i class="fas fa-times-circle mr-1"></i>Stok Habis</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-sm">
                            <thead class="thead-danger">
                                <tr><th>Produk</th><th>Stok Saat Ini</th></tr>
                            </thead>
                            <tbody>
                                @forelse($outOfStock as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td><span class="badge badge-danger">Habis</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted">Tidak ada produk yang habis.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Expiring Soon --}}
                    <h5 class="text-info"><i class="fas fa-calendar-times mr-1"></i>Akan Kadaluarsa (30 hari)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="thead-info">
                                <tr><th>Produk</th><th>Tanggal Kadaluarsa</th><th>Stok</th></tr>
                            </thead>
                            <tbody>
                                @forelse($expiringSoon as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->expiration_date)->format('d M Y') }}</td>
                                    <td>{{ $item->current_stock }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Tidak ada produk yang akan kadaluarsa.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
