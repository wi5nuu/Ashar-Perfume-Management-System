@extends('layouts.app')
@section('title', 'Manajemen Cabang - APMS')

@section('content')
<div class="container-fluid pt-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="font-weight-bold mb-0 text-dark"><i class="fas fa-store-alt mr-2 text-primary"></i>Manajemen Cabang</h3>
            <small class="text-muted">Kelola semua cabang toko dan pantau kinerjanya secara terpusat</small>
        </div>
        <a href="{{ route('branches.create') }}" class="btn btn-primary-apms shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Cabang
        </a>
    </div>

    {{-- Period Filter --}}
    <div class="card card-apms border-0 mb-3 shadow-sm">
        <div class="card-body py-2">
            <div class="d-flex align-items-center flex-wrap">
                <span class="font-weight-bold text-muted mr-3"><i class="fas fa-calendar-alt mr-1"></i>Periode:</span>
                @foreach(['today' => 'Hari Ini', 'this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini', 'this_year' => 'Tahun Ini'] as $key => $label)
                    <a href="{{ route('branches.index', ['period' => $key]) }}"
                       class="btn btn-sm {{ $period === $key ? 'btn-primary-apms' : 'btn-outline-secondary' }} mr-1">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card card-apms shadow-sm border-0 border-left-primary">
                <div class="card-body">
                    <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Total Omzet ({{ $periodLabel }})</div>
                    <h3 class="font-weight-bold text-primary mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-apms shadow-sm border-0 border-left-danger">
                <div class="card-body">
                    <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Total Pengeluaran</div>
                    <h3 class="font-weight-bold text-danger mb-0">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-apms shadow-sm border-0 border-left-success">
                <div class="card-body">
                    <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">Profit Bersih</div>
                    <h3 class="font-weight-bold {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Branch Table --}}
    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="card-title mb-0 font-weight-bold text-dark"><i class="fas fa-list-ul mr-2 text-primary"></i>Daftar Semua Cabang</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-4">Nama Cabang</th>
                            <th>Manajer</th>
                            <th>Staf</th>
                            <th class="text-right">Omzet</th>
                            <th class="text-right">Pengeluaran</th>
                            <th class="text-right">Profit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                        <tr>
                            <td class="pl-4">
                                <div class="font-weight-bold text-dark">{{ $branch->name }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $branch->city }}</div>
                            </td>
                            <td>{{ $branch->manager_name ?? '-' }}</td>
                            <td><span class="badge badge-light border">{{ $branch->users_count }}</span></td>
                            <td class="text-right text-primary font-weight-bold">Rp {{ number_format($branch->period_revenue, 0, ',', '.') }}</td>
                            <td class="text-right text-danger font-weight-bold">Rp {{ number_format($branch->period_expenses, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold {{ $branch->period_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($branch->period_profit, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($branch->is_active)
                                    <span class="badge badge-success-soft">Aktif</span>
                                @else
                                    <span class="badge badge-secondary-soft">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-primary" title="Laporan Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-store-slash fa-2x mb-2"></i><br>
                                Belum ada cabang. Tambah sekarang untuk mulai memantau.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
