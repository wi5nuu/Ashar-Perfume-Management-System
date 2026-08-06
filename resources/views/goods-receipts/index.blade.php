@php $user = auth()->user(); @endphp
@extends('layouts.app')
@section('title', 'Penerimaan Barang')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-dolly-flatbed mr-2"></i>Penerimaan Barang</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Penerimaan Barang</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('goods_receipts.create')
                    <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Buat Penerimaan
                    </a>
                    @endcan
                    <a href="{{ route('goods-receipts.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(40,167,69,.2);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- KPI Strip --}}
    <div class="kpi-strip">
        <div class="kpi-card">
            <div class="kpi-icon orange"><i class="fas fa-truck-loading"></i></div>
            <div>
                <div class="kpi-label">Penerimaan Bulan Ini</div>
                <div class="kpi-value">{{ number_format($stats['this_month_quantity'] ?? 0) }}</div>
                <div class="kpi-sub">{{ now()->format('F Y') }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon yellow"><i class="fas fa-clock"></i></div>
            <div>
                <div class="kpi-label">Pending Verifikasi</div>
                <div class="kpi-value">{{ $stats['pending_verification'] ?? 0 }}</div>
                <div class="kpi-sub">Menunggu konfirmasi</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="kpi-label">Total Nilai</div>
                <div class="kpi-value" style="font-size:1rem;">Rp {{ number_format($stats['total_cost'] ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">Semua penerimaan</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green"><i class="fas fa-boxes"></i></div>
            <div>
                <div class="kpi-label">Item Diterima</div>
                <div class="kpi-value">{{ number_format($stats['total_quantity'] ?? 0) }}</div>
                <div class="kpi-sub">Total unit masuk</div>
            </div>
        </div>
    </div>

    <div class="card card-apms">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title mb-0" style="font-size:1rem; font-weight:700; color:var(--secondary);">
                <i class="fas fa-list mr-2" style="color:var(--primary);"></i>Daftar Penerimaan Barang
            </h3>
            <small class="text-muted">{{ $receipts->total() }} penerimaan</small>
        </div>
        <div class="card-body">

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('goods-receipts.index') }}">
                <div class="filter-bar">
                    <div class="row align-items-end g-2">
                        <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Sampai</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Supplier</label>
                            <select name="supplier_id" class="form-control form-control-sm">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers ?? [] as $s)
                                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
                                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                                <option value="partial"  {{ request('status') === 'partial'  ? 'selected' : '' }}>Partial</option>
                                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-6 mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary-apms btn-sm w-100"><i class="fas fa-filter"></i></button>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>No. GR</th>
                            <th>Produk</th>
                            <th>Supplier</th>
                            <th class="d-none d-md-table-cell">Tgl Masuk</th>
                            <th class="d-none d-md-table-cell">Tgl Kadaluarsa</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-right d-none d-lg-table-cell">Nilai</th>
                            @if($user->isOwner())
                            <th class="d-none d-lg-table-cell">Cabang</th>
                            @endif
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                        @php
                            $isRefill = $receipt->product?->is_refill ?? false;
                            $qty      = $receipt->quantity ?? 0;
                            $qtyText  = $isRefill
                                ? ($qty >= 1000
                                    ? number_format($qty/1000, 2, ',', '.').' L ('.$qty.' ml)'
                                    : number_format($qty, 0, ',', '.').' ml')
                                : number_format($qty, 0, ',', '.').' botol';
                        @endphp
                        <tr>
                            <td>
                                <span style="font-family:monospace; font-size:.82rem; font-weight:600; color:var(--secondary);">
                                    {{ $receipt->receipt_number }}
                                </span>
                                <div class="small text-muted">{{ $receipt->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td>
                                <div class="font-weight-600" style="font-size:.88rem; color:var(--secondary);">
                                    {{ $receipt->product->name ?? '-' }}
                                </div>
                                <small class="badge badge-sm {{ $isRefill ? 'badge-success' : 'badge-info' }}" style="font-size:.72rem;">
                                    {{ $isRefill ? 'BIBIT' : 'BOTOL' }}
                                </small>
                            </td>
                            <td>
                                <div style="font-size:.88rem;">{{ $receipt->supplier_name ?? '-' }}</div>
                                @if($receipt->origin)
                                <small class="text-muted">{{ $receipt->origin }}</small>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div style="font-size:.85rem;">
                                    {{ $receipt->received_date?->format('d M Y') ?? '-' }}
                                </div>
                                <small class="text-muted">{{ $receipt->recorder?->name ?? '' }}</small>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($receipt->expiration_date)
                                    @php $expired = $receipt->expiration_date->isPast(); $soon = !$expired && $receipt->expiration_date->diffInDays(now()) <= 30; @endphp
                                    <span class="{{ $expired ? 'text-danger font-weight-bold' : ($soon ? 'text-warning font-weight-bold' : 'text-dark') }}" style="font-size:.85rem;">
                                        {{ $receipt->expiration_date->format('d M Y') }}
                                    </span>
                                    @if($expired)
                                    <div><small class="text-danger">Kadaluarsa</small></div>
                                    @elseif($soon)
                                    <div><small class="text-warning">Segera kadaluarsa</small></div>
                                    @endif
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="font-weight-600" style="font-size:.88rem;">{{ $qtyText }}</span>
                            </td>
                            <td class="text-right d-none d-lg-table-cell font-weight-600">
                                Rp {{ number_format($receipt->total_cost ?? 0, 0, ',', '.') }}
                            </td>
                            @if($user->isOwner())
                            <td class="d-none d-lg-table-cell">
                                <small class="text-muted">{{ $receipt->branch?->name ?? 'Pusat' }}</small>
                            </td>
                            @endif
                            <td class="text-center">
                                <a href="{{ route('goods-receipts.show', $receipt) }}" class="btn btn-sm btn-outline-info btn-action" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $user->isOwner() ? 9 : 8 }}">
                                <div class="text-center py-5">
                                    <i class="fas fa-dolly-flatbed fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-1">Belum ada data penerimaan barang.</p>
                                    @can('goods_receipts.create')
                                    <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary-apms btn-sm mt-2">
                                        <i class="fas fa-plus mr-1"></i> Buat Penerimaan Pertama
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $receipts->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
