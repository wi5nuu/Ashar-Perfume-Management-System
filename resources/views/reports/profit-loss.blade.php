@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Laporan Laba & Rugi</h3>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    {{-- Filter --}}
                    <form action="{{ route('reports.profit-loss') }}" method="GET" class="row g-2 mb-4">
                        <div class="col-md-3">
                            <label>Bulan</label>
                            <select name="month" class="form-control">
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Tahun</label>
                            <input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2030">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary-apms">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>

                    {{-- Summary Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Pemasukan</span>
                                    <span class="info-box-number">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Pengeluaran</span>
                                    <span class="info-box-number">Rp {{ number_format($expenses, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box {{ $profit >= 0 ? 'bg-primary' : 'bg-warning' }}">
                                <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Laba / Rugi</span>
                                    <span class="info-box-number">Rp {{ number_format($profit, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expense Breakdown --}}
                    <h5>Rincian Pengeluaran</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr><th>Kategori</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($expenseBreakdown as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted">Tidak ada data pengeluaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Revenue by Method --}}
                    <h5 class="mt-4">Pemasukan per Metode Pembayaran</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr><th>Metode</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByMethod as $item)
                                <tr>
                                    <td>{{ strtoupper($item->payment_method) }}</td>
                                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted">Tidak ada data pemasukan.</td></tr>
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
