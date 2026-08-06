@extends('layouts.app')

@section('title', 'Manajemen Grosir')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-boxes mr-2"></i>Pesanan Grosir</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Grosir</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pb-4">
    <x-alert />
        <div class="d-flex gap-2 flex-wrap" style="gap:0.5rem">
            <a href="{{ route('wholesale.products.index') }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;font-weight:600;font-size:0.85rem;">
                <i class="fas fa-box mr-1"></i> Produk Grosir
            </a>
            <a href="{{ route('wholesale.create') }}" class="btn-primary-apms">
                <i class="fas fa-plus-circle"></i> Buat Pesanan Baru
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row mb-4" style="row-gap:1rem">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="fas fa-file-invoice"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpiTotal }}</div>
                    <div class="kpi-label">Total Pesanan</div>
                    <div class="kpi-trend neutral"><i class="fas fa-minus mr-1"></i>Semua waktu</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-clock"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpiPending }}</div>
                    <div class="kpi-label">Pending Approval</div>
                    <div class="kpi-trend neutral"><i class="fas fa-hourglass-half mr-1"></i>Menunggu aksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon green"><i class="fas fa-truck"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpiProcess }}</div>
                    <div class="kpi-label">Sedang Diproses</div>
                    <div class="kpi-trend up"><i class="fas fa-arrow-up mr-1"></i>Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpiCompleted }}</div>
                    <div class="kpi-label">Selesai</div>
                    <div class="kpi-trend up"><i class="fas fa-check mr-1"></i>Terkirim</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-card">
        <form action="{{ route('wholesale.index') }}" method="GET">
            <div class="row align-items-end" style="row-gap:0.75rem">
                <div class="col-md-4">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Cari Pesanan</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background:#f8f9ff;border-color:#e4e8f0;border-radius:8px 0 0 8px;">
                                <i class="fas fa-search" style="color:#b0b8c9;font-size:0.82rem"></i>
                            </span>
                        </div>
                        <input type="text" name="search" class="form-control" placeholder="Invoice, nama, no HP..."
                               value="{{ request('search') }}" style="border-left:none;border-radius:0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Status</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $s)
                            @php $label = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$s] ?? ucfirst($s); @endphp
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex" style="gap:0.5rem">
                    <button type="submit" class="btn btn-primary flex-fill" style="background:var(--primary);border:none;border-radius:8px;font-weight:600;font-size:0.85rem;">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['search','status','date_from','date_to']))
                    <a href="{{ route('wholesale.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:0.85rem;" title="Reset filter">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;font-size:0.85rem;border:none;background:#e8f5e9;color:#2e7d32;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>No. Order</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="text-center">Items</th>
                        <th class="text-right">Total Nilai</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $i => $order)
                    @php
                        $statusLabel = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$order->status] ?? ucfirst($order->status);
                        $pipeline = ['pending','reviewed','on_progress','packed','shipped','delivered','completed'];
                        $currentIdx = array_search($order->status, $pipeline);
                        $isCancelled = $order->status === 'cancelled';
                    @endphp
                    <tr>
                        <td style="color:#b0b8c9;font-size:0.8rem">{{ $orders->firstItem() + $i }}</td>
                        <td>
                            <span class="invoice-code">{{ $order->invoice_number ?? $order->order_number ?? '#'.$order->id }}</span>
                            <div style="font-size:0.7rem;color:#b0b8c9;margin-top:2px;">ID: {{ $order->id }}</div>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <div class="name">{{ $order->recipient_name ?? ($order->customer->name ?? '-') }}</div>
                                <div class="phone"><i class="fas fa-phone mr-1"></i>{{ $order->recipient_phone ?? ($order->customer->phone ?? '-') }}</div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:0.84rem;font-weight:500;color:var(--secondary)">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</div>
                            <div style="font-size:0.72rem;color:#b0b8c9">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</div>
                        </td>
                        <td class="text-center">
                            <span style="font-weight:700;color:var(--secondary)">{{ $order->items ? $order->items->count() : 0 }}</span>
                            <div style="font-size:0.7rem;color:#8892a4">produk</div>
                        </td>
                        <td class="text-right">
                            <span style="font-weight:700;color:var(--secondary);font-size:0.9rem">
                                Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status {{ $isCancelled ? 'cancelled' : $order->status }}">{{ $statusLabel }}</span>
                            @if(!$isCancelled)
                            <div class="pipeline-mini mt-1">
                                @foreach($pipeline as $k => $step)
                                <div class="p-step {{ $currentIdx !== false && $k < $currentIdx ? 'done' : ($currentIdx !== false && $k == $currentIdx ? 'active' : '') }}"></div>
                                @endforeach
                            </div>
                            @else
                            <div style="font-size:0.68rem;color:#ef5350;margin-top:3px;"><i class="fas fa-ban mr-1"></i>Dibatalkan</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('wholesale.show', $order) }}" class="action-btn view">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-boxes"></i></div>
                                <h6>Belum Ada Pesanan Grosir</h6>
                                <p>Pesanan yang cocok dengan filter tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid #f0f2f8;">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
