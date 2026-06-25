@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Laporan Penjualan</h3>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.sales') }}" method="GET" class="row g-2 mb-4">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date" class="form-control" name="end_date" value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label>Tipe</label>
                            <select class="form-control" name="type">
                                <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary-apms">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Penjualan</span>
                                    <span class="info-box-number">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Transaksi</span>
                                    <span class="info-box-number">{{ $totalTransactions }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    @if($type === 'daily')
                                    <th>Tanggal</th>
                                    @else
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    @endif
                                    <th>Jumlah Transaksi</th>
                                    <th>Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $item)
                                <tr>
                                    @if($type === 'daily')
                                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                                    @else
                                    <td>{{ \Carbon\Carbon::create()->month($item->month)->format('F') }}</td>
                                    <td>{{ $item->year }}</td>
                                    @endif
                                    <td>{{ $item->transaction_count }}</td>
                                    <td>Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada data pada periode ini.</td></tr>
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
