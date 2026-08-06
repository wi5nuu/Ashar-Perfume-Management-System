@extends('layouts.app')
@section('title', 'Purchase Orders')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-shopping-cart mr-2"></i>Purchase Order</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Purchase Order</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Buat PO Baru
                    </a>
                    <a href="{{ route('purchase-orders.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
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
            <div class="kpi-icon orange"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="kpi-label">Total PO</div>
                <div class="kpi-value">{{ $stats['total'] ?? $orders->total() }}</div>
                <div class="kpi-sub">Semua waktu</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon yellow"><i class="fas fa-clock"></i></div>
            <div>
                <div class="kpi-label">PO Pending</div>
                <div class="kpi-value">{{ $stats['pending'] ?? 0 }}</div>
                <div class="kpi-sub">Draft &amp; Terkirim</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green"><i class="fas fa-check-double"></i></div>
            <div>
                <div class="kpi-label">PO Diterima</div>
                <div class="kpi-value">{{ $stats['received'] ?? 0 }}</div>
                <div class="kpi-sub">Bulan ini</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="kpi-label">Nilai PO Bulan Ini</div>
                <div class="kpi-value" style="font-size:1rem;">Rp {{ number_format($stats['this_month_value'] ?? 0, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ now()->format('F Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card card-apms">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title mb-0" style="font-size:1rem; font-weight:700; color:var(--secondary);">
                <i class="fas fa-list mr-2" style="color:var(--primary);"></i>Daftar Purchase Order
            </h3>
            <small class="text-muted">{{ $orders->total() }} PO ditemukan</small>
        </div>
        <div class="card-body">

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('purchase-orders.index') }}">
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
                                <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
                                <option value="sent"      {{ request('status') === 'sent'      ? 'selected' : '' }}>Terkirim</option>
                                <option value="partial"   {{ request('status') === 'partial'   ? 'selected' : '' }}>Partial</option>
                                <option value="received"  {{ request('status') === 'received'  ? 'selected' : '' }}>Diterima</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-6 mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary-apms btn-sm w-100"><i class="fas fa-filter"></i></button>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
                            <th>No. PO</th>
                            <th>Supplier</th>
                            <th class="d-none d-md-table-cell">Tanggal PO</th>
                            <th class="d-none d-lg-table-cell">Expected</th>
                            <th class="text-center d-none d-md-table-cell">Items</th>
                            <th class="text-right">Total Nilai</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $po)
                        <tr>
                            <td>
                                <span class="po-number">{{ $po->po_number ?? 'PO-' . str_pad($po->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="font-weight-600" style="font-size:.88rem; color:var(--secondary);">{{ $po->supplier->name ?? '-' }}</div>
                                <small class="text-muted">{{ $po->supplier->phone ?? '' }}</small>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div style="font-size:.85rem;">{{ $po->created_at ? \Carbon\Carbon::parse($po->created_at)->format('d M Y') : '-' }}</div>
                                <small class="text-muted">{{ $po->user->name ?? '' }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($po->expected_date)
                                    @php $exp = \Carbon\Carbon::parse($po->expected_date); @endphp
                                    <span class="{{ $exp->isPast() && !in_array($po->status, ['received','cancelled']) ? 'text-danger font-weight-600' : 'text-muted' }}" style="font-size:.85rem;">
                                        {{ $exp->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge badge-modern" style="background:rgba(0,123,255,.1);color:#0056b3;">
                                    {{ $po->items_count ?? $po->items->count() }} item
                                </span>
                            </td>
                            <td class="text-right font-weight-700" style="font-size:.9rem;">
                                Rp {{ number_format($po->total_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @switch($po->status)
                                    @case('draft')
                                        <span class="badge badge-modern badge-draft"><i class="fas fa-pencil-alt fa-xs"></i> Draft</span>
                                        @break
                                    @case('sent')
                                        <span class="badge badge-modern badge-sent"><i class="fas fa-paper-plane fa-xs"></i> Terkirim</span>
                                        @break
                                    @case('partial')
                                        <span class="badge badge-modern badge-partial"><i class="fas fa-adjust fa-xs"></i> Partial</span>
                                        @break
                                    @case('received')
                                        <span class="badge badge-modern badge-received"><i class="fas fa-check fa-xs"></i> Diterima</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-modern badge-cancelled"><i class="fas fa-times fa-xs"></i> Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge badge-modern badge-draft">{{ ucfirst($po->status) }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-info btn-action" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($po->status, ['draft']))
                                    <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-1">Belum ada Purchase Order.</p>
                                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary-apms btn-sm mt-2">
                                        <i class="fas fa-plus mr-1"></i> Buat PO Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $orders->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
