@php $user = auth()->user();
@endphp
@extends('layouts.app')
@section('title', 'Penerimaan Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title">Penerimaan Barang</h3>
                    @can('goods_receipts.create')
                    <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary-apms btn-sm">
                        <i class="fas fa-plus"></i> Catat Penerimaan
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_quantity']) }}</h3>
                                    <p>Total Barang Masuk</p>
                                </div>
                                <div class="icon"><i class="fas fa-boxes"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>Rp {{ number_format($stats['total_cost']) }}</h3>
                                    <p>Total Biaya</p>
                                </div>
                                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($stats['this_month_quantity']) }}</h3>
                                    <p>Bulan Ini</p>
                                </div>
                                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>Rp {{ number_format($stats['this_month_cost']) }}</h3>
                                    <p>Biaya Bulan Ini</p>
                                </div>
                                <div class="icon"><i class="fas fa-chart-line"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No. Receipt</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Supplier</th>
                                    <th>Pengantar</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Total Biaya</th>
                                    <th>Dicatat Oleh</th>
                                    @if($user->isOwner())<th>Cabang</th>@endif
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receipts as $receipt)
                                <tr>
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>{{ $receipt->product->name ?? '-' }}</td>
                                    <td>{{ number_format($receipt->quantity) }}</td>
                                    <td>{{ $receipt->supplier_name ?? '-' }}</td>
                                    <td>{{ $receipt->delivery_person ?? '-' }}</td>
                                    <td>{{ $receipt->received_date ? $receipt->received_date->format('d/m/Y') : '-' }}</td>
                                    <td>Rp {{ number_format($receipt->total_cost) }}</td>
                                    <td>{{ $receipt->recorder?->name ?? '-' }}</td>
                                    @if($user->isOwner())<td>{{ $receipt->branch->name ?? '-' }}</td>@endif
                                    <td>
                                        <a href="{{ route('goods-receipts.show', $receipt) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $user->isOwner() ? 10 : 9 }}" class="text-center">Belum ada data penerimaan barang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $receipts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
