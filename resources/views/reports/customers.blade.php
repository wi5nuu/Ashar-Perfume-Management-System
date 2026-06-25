@extends('layouts.app')

@section('title', 'Analitik Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-users mr-2"></i>Analitik Pelanggan</h3>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    {{-- Customer Types --}}
                    <div class="row mb-4">
                        @forelse($customerTypes as $type)
                        <div class="col-md-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ ucfirst($type->type ?? 'Umum') }}</span>
                                    <span class="info-box-number">{{ $type->count }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12"><p class="text-muted">Tidak ada data tipe pelanggan.</p></div>
                        @endforelse
                    </div>

                    {{-- Top Customers --}}
                    <h5><i class="fas fa-trophy mr-1 text-warning"></i>Top 10 Pelanggan Terbaik</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Total Belanja</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $i => $customer)
                                <tr>
                                    <td>
                                        @if($i === 0) 🥇 @elseif($i === 1) 🥈 @elseif($i === 2) 🥉 @else {{ $i + 1 }} @endif
                                    </td>
                                    <td>{{ $customer->name }}</td>
                                    <td>Rp {{ number_format($customer->transactions_sum_total_amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Tidak ada data pelanggan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Customer Growth --}}
                    <h5 class="mt-4"><i class="fas fa-chart-line mr-1 text-success"></i>Pertumbuhan Pelanggan (30 Hari Terakhir)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr><th>Tanggal</th><th>Pelanggan Baru</th></tr>
                            </thead>
                            <tbody>
                                @forelse($customerGrowth as $growth)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($growth->date)->format('d M Y') }}</td>
                                    <td><span class="badge badge-success">+{{ $growth->count }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted">Tidak ada pelanggan baru dalam 30 hari terakhir.</td></tr>
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
