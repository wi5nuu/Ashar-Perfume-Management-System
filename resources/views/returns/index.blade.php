@extends('layouts.app')
@section('title', 'Retur Produk')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-undo-alt mr-2"></i>Retur Produk</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Retur</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pb-4">
    <x-alert />
        <div></div>
    </div>

    {{-- KPI Cards --}}
    <div class="row mb-4" style="row-gap:1rem">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="fas fa-undo"></i></div>
                <div>
                    <div class="kpi-value">{{ $returns->total() }}</div>
                    <div class="kpi-label">Total Retur</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="kpi-value">{{ $kpiPending }}</div>
                    <div class="kpi-label">Pending Review</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon red"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <div class="kpi-value" style="font-size:1.1rem">Rp {{ number_format($kpiTotalRefund ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-label">Nilai Retur</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="kpi-value">{{ $kpiApproved }}</div>
                    <div class="kpi-label">Disetujui</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;font-size:0.85rem;border:none;background:#e8f5e9;color:#2e7d32;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row align-items-end" style="row-gap:0.75rem">
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Status</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending"   {{ request('status')==='pending'   ?'selected':'' }}>Pending</option>
                        <option value="approved"  {{ request('status')==='approved'  ?'selected':'' }}>Disetujui</option>
                        <option value="rejected"  {{ request('status')==='rejected'  ?'selected':'' }}>Ditolak</option>
                        <option value="completed" {{ request('status')==='completed' ?'selected':'' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Sampai</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="mb-1" style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase;letter-spacing:0.4px">Cari</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background:#f8f9ff;border-color:#e4e8f0;border-radius:8px 0 0 8px;border-right:none;">
                                <i class="fas fa-search" style="color:#b0b8c9;font-size:0.8rem"></i>
                            </span>
                        </div>
                        <input type="text" name="search" class="form-control" placeholder="No. retur, invoice..." value="{{ request('search') }}" style="border-left:none;border-radius:0 8px 8px 0;">
                    </div>
                </div>
                <div class="col-md-3 d-flex" style="gap:0.5rem">
                    <button type="submit" class="btn flex-fill" style="background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.85rem;height:calc(1.5em + 0.7rem + 4px)">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['status','date_from','date_to','search']))
                    <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;height:calc(1.5em + 0.7rem + 4px);padding:0 0.75rem" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Retur</th>
                        <th>Referensi</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Pelanggan / Supplier</th>
                        <th class="text-right">Nilai Refund</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $i => $r)
                    <tr>
                        <td style="color:#b0b8c9;font-size:0.8rem">{{ $returns->firstItem() + $i }}</td>
                        <td>
                            <span style="font-weight:700;color:var(--secondary)">{{ $r->return_number ?? 'RTR-'.$r->id }}</span>
                        </td>
                        <td>
                            <span style="font-size:0.82rem;color:#1976d2;font-weight:500">{{ $r->transaction->invoice_number ?? '-' }}</span>
                        </td>
                        <td>
                            <div style="font-size:0.84rem;font-weight:500">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</div>
                            <div style="font-size:0.72rem;color:#b0b8c9">{{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }}</div>
                        </td>
                        <td>
                            @if(isset($r->type) && $r->type === 'supplier')
                                <span class="badge-type supplier"><i class="fas fa-truck mr-1"></i>Ke Supplier</span>
                            @else
                                <span class="badge-type customer"><i class="fas fa-user mr-1"></i>Pelanggan</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--secondary)">{{ $r->transaction->customer->name ?? 'Umum' }}</div>
                        </td>
                        <td class="text-right">
                            <span style="font-weight:700;color:var(--secondary)">Rp {{ number_format($r->total_refund ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @switch($r->status)
                                @case('pending')   <span class="badge-status pending">Pending</span>   @break
                                @case('approved')  <span class="badge-status approved">Disetujui</span> @break
                                @case('rejected')  <span class="badge-status rejected">Ditolak</span>   @break
                                @case('completed') <span class="badge-status completed">Selesai</span>  @break
                                @default           <span class="badge-status pending">{{ ucfirst($r->status) }}</span>
                            @endswitch
                        </td>
                        <td class="text-center">
                            <a href="{{ route('returns.show', $r) }}" class="action-btn view">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-undo-alt"></i></div>
                                <h6>Belum Ada Data Retur</h6>
                                <p>Tidak ada retur yang cocok dengan filter yang dipilih.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid #f0f2f8">
            {{ $returns->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection