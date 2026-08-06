@extends('layouts.app')
@section('title', 'Target Penjualan')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-bullseye mr-2"></i>Target Penjualan</h1>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.65);font-size:.82rem;">
                        Monitor dan kelola target penjualan per periode
                    </p>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Target Penjualan</li>
                    </ol>
                </div>
                <a href="{{ route('sales-targets.create') }}" class="btn btn-primary-apms">
                    <i class="fas fa-plus mr-1"></i> Target Baru
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />

    <div class="page-header-apms d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active">Target Penjualan</li>
                </ol>
            </nav>
            <h4 class="mb-0 mt-1 font-weight-bold">Target Penjualan</h4>
            <small style="opacity:.75">Monitor dan kelola target penjualan per periode</small>
        </div>
        <a href="{{ route('sales-targets.create') }}" class="btn-primary-apms btn">
            <i class="fas fa-plus mr-1"></i> Target Baru
        </a>
    </div>

    <div class="card card-apms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="d-none d-md-table-cell">Cabang</th>
                            <th class="d-none d-md-table-cell">Sales</th>
                            <th>Target</th>
                            <th class="d-none d-lg-table-cell">Pencapaian</th>
                            <th style="width:70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($targets as $t)
                        @php
                            $achievement = $t->achieved_amount ?? 0;
                            $pct = $t->target_amount > 0 ? min(100, round($achievement / $t->target_amount * 100)) : 0;
                            $barColor = $pct >= 100 ? '#10b981' : ($pct >= 75 ? '#3b82f6' : ($pct >= 50 ? '#f59e0b' : '#ef4444'));
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:700;font-size:.9rem;color:var(--secondary)">
                                    {{ DateTime::createFromFormat('!m', $t->month)->format('F') }} {{ $t->year }}
                                </div>
                                <div style="font-size:.75rem;color:#8a8fa8">Q{{ ceil($t->month / 3) }} {{ $t->year }}</div>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.85rem">
                                <span style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:.78rem;font-weight:600;color:#475569">
                                    {{ $t->branch?->name ?? 'Semua Cabang' }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:.85rem;color:#6b7280">
                                {{ $t->user?->name ?? 'Semua Sales' }}
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.92rem;color:var(--secondary)">
                                    Rp {{ number_format($t->target_amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell" style="min-width:160px">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-bar-wrap flex-grow-1">
                                        <div class="progress-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                                    </div>
                                    <span style="font-size:.78rem;font-weight:700;color:{{ $barColor }};min-width:36px">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('sales-targets.show', $t->id) }}"
                                   class="btn btn-sm btn-outline-primary px-2 py-1" title="Lihat Detail">
                                    <i class="fas fa-chart-bar" style="font-size:.75rem"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5">
                                    <i class="fas fa-bullseye" style="font-size:2.5rem;color:#d1d5e0"></i>
                                    <p class="mt-3 mb-1 font-weight-600" style="color:#3d4268">Belum ada target penjualan</p>
                                    <small class="text-muted">Mulai dengan membuat target pertama untuk tim Anda</small>
                                    <div class="mt-3">
                                        <a href="{{ route('sales-targets.create') }}" class="btn-primary-apms btn btn-sm">
                                            <i class="fas fa-plus mr-1"></i> Buat Target
                                        </a>
                                    </div>
                                </div>
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
