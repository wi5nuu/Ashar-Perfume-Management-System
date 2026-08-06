@extends('layouts.app')
@section('title', 'Deposit Pelanggan')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-wallet mr-2"></i>Deposit Pelanggan</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Kelola saldo deposit dan prepaid pelanggan
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Deposit Pelanggan</li>
                    </ol>
                </div>
                <a href="{{ route('customer-deposits.create') }}" class="btn btn-primary-apms">
                    <i class="fas fa-plus mr-1"></i> Deposit Baru
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    {{-- KPI --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(255,107,53,.1)"><i class="fas fa-wallet" style="color:var(--primary)"></i></div>
                <div>
                    <div class="kpi-value">{{ $accounts->total() }}</div>
                    <div class="kpi-label">Total Akun</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(16,185,129,.1)"><i class="fas fa-check-circle" style="color:#10b981"></i></div>
                <div>
                    <div class="kpi-value">{{ $accounts->where('status','active')->count() }}</div>
                    <div class="kpi-label">Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(59,130,246,.1)"><i class="fas fa-coins" style="color:#3b82f6"></i></div>
                <div>
                    <div class="kpi-value" style="font-size:1.1rem">Rp {{ number_format($accounts->sum('balance'),0,',','.') }}</div>
                    <div class="kpi-label">Total Saldo</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="kpi-card d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:rgba(139,92,246,.1)"><i class="fas fa-user-tag" style="color:#8b5cf6"></i></div>
                <div>
                    <div class="kpi-value">{{ $accounts->where('balance','>',0)->count() }}</div>
                    <div class="kpi-label">Bersaldo</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Saldo Deposit</th>
                            <th class="d-none d-md-table-cell">Total Top-up</th>
                            <th>Status</th>
                            <th style="width:70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $a)
                        @php
                            $colors = ['#FF6B35','#10b981','#3b82f6','#8b5cf6','#f59e0b'];
                            $ci = abs(crc32($a->customer->name ?? 'X')) % count($colors);
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cust-avatar" style="background:{{ $colors[$ci] }}">
                                        {{ strtoupper(substr($a->customer->name ?? 'X', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:.88rem">{{ $a->customer->name ?? '-' }}</div>
                                        <div style="font-size:.75rem;color:#8a8fa8">{{ $a->customer->phone ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:700;font-size:.95rem;color:{{ $a->balance > 0 ? '#1a7a45' : '#6b7280' }}">
                                    Rp {{ number_format($a->balance, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.84rem;color:#6b7280">
                                Rp {{ number_format($a->total_topup ?? 0, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($a->status === 'active')
                                    <span class="badge-modern badge-active"><i class="fas fa-circle" style="font-size:.5rem"></i> Aktif</span>
                                @else
                                    <span class="badge-modern badge-inactive"><i class="fas fa-circle" style="font-size:.5rem"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('customer-deposits.show', $a->id) }}"
                                   class="btn btn-sm btn-outline-primary px-2 py-1" title="Lihat Detail">
                                    <i class="fas fa-eye" style="font-size:.75rem"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <i class="fas fa-wallet" style="font-size:2.5rem;color:#d1d5e0"></i>
                                    <p class="mt-3 mb-1 font-weight-600" style="color:#3d4268">Belum ada deposit</p>
                                    <small class="text-muted">Mulai tambahkan deposit pelanggan pertama</small>
                                    <div class="mt-3">
                                        <a href="{{ route('customer-deposits.create') }}" class="btn-primary-apms btn btn-sm">
                                            <i class="fas fa-plus mr-1"></i> Deposit Baru
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($accounts->hasPages())
            <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f2f3f8">
                <small class="text-muted">{{ $accounts->firstItem() }}–{{ $accounts->lastItem() }} dari {{ $accounts->total() }}</small>
                {{ $accounts->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
