@extends('layouts.app')
@section('title', 'Manajemen Supplier')

@section('content')
{{-- All styles loaded globally from resources/sass/_apms.scss --}}

{{-- Page Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-truck mr-2"></i>Manajemen Supplier</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Supplier</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('suppliers.create')
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Tambah Supplier
                    </a>
                    @endcan
                    <a href="{{ route('suppliers.index', array_merge(request()->query(), ['export' => 'xlsx'])) }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px; border:none; box-shadow:0 2px 8px rgba(40,167,69,.2);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- KPI Row --}}
    <div class="row mb-4">
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon orange"><i class="fas fa-truck"></i></div>
                <div>
                    <div class="kpi-label">Total Supplier</div>
                    <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
                    <div class="kpi-sub">Terdaftar di sistem</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="kpi-label">Supplier Aktif</div>
                    <div class="kpi-value">{{ $stats['active'] ?? 0 }}</div>
                    <div class="kpi-sub">Siap bertransaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon red"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <div class="kpi-label">Total Hutang</div>
                    <div class="kpi-value" style="font-size:1.1rem;">Rp {{ number_format($stats['total_debt'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-sub">Ke seluruh supplier</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <div class="kpi-label">Pembelian Bulan Ini</div>
                    <div class="kpi-value" style="font-size:1.1rem;">Rp {{ number_format($stats['this_month_purchase'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-sub">{{ now()->format('F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card card-apms">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title mb-0 font-weight-700" style="font-size:1rem; color: var(--secondary);">
                <i class="fas fa-list mr-2" style="color:var(--primary);"></i>Daftar Supplier
            </h3>
            <small class="text-muted">{{ $suppliers->total() ?? 0 }} supplier ditemukan</small>
        </div>
        <div class="card-body">

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('suppliers.index') }}" id="filterForm">
                <div class="filter-bar">
                    <div class="row align-items-end g-2">
                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Cari Supplier</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span></div>
                                <input type="text" name="search" class="form-control" placeholder="Nama, kode, email, telepon..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                            <label class="small font-weight-600 text-muted mb-1">Urutkan</label>
                            <select name="sort" class="form-control form-control-sm">
                                <option value="name"       {{ request('sort') === 'name'       ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="latest"     {{ request('sort') === 'latest'     ? 'selected' : '' }}>Terbaru</option>
                                <option value="most_po"    {{ request('sort') === 'most_po'    ? 'selected' : '' }}>Terbanyak PO</option>
                                <option value="most_debt"  {{ request('sort') === 'most_debt'  ? 'selected' : '' }}>Hutang Terbesar</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary-apms btn-sm w-100">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Supplier</th>
                            <th class="d-none d-md-table-cell">Kontak</th>
                            <th class="d-none d-lg-table-cell">Alamat</th>
                            <th class="text-center d-none d-md-table-cell">Total PO</th>
                            <th class="text-right d-none d-lg-table-cell">Total Hutang</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="supplier-avatar">
                                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-600" style="color:var(--secondary);">{{ $supplier->name }}</div>
                                        @if($supplier->code)
                                        <small class="text-muted"><i class="fas fa-tag mr-1"></i>{{ $supplier->code }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($supplier->email)
                                <div><i class="fas fa-envelope fa-xs mr-1 text-muted"></i><small>{{ $supplier->email }}</small></div>
                                @endif
                                @if($supplier->phone)
                                <div><i class="fas fa-phone fa-xs mr-1 text-muted"></i><small>{{ $supplier->phone }}</small></div>
                                @endif
                                @if($supplier->contact_person)
                                <div><i class="fas fa-user fa-xs mr-1 text-muted"></i><small>{{ $supplier->contact_person }}</small></div>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small class="text-muted" style="max-width:180px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $supplier->address ?? '-' }}
                                </small>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge badge-modern" style="background:rgba(0,123,255,.1);color:#0056b3;">
                                    {{ $supplier->purchase_orders_count ?? 0 }} PO
                                </span>
                            </td>
                            <td class="text-right d-none d-lg-table-cell">
                                @php $debt = $supplier->total_debt ?? 0; @endphp
                                <span class="{{ $debt > 0 ? 'text-danger font-weight-600' : 'text-muted' }}">
                                    Rp {{ number_format($debt, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if(($supplier->status ?? 'active') === 'active')
                                    <span class="badge badge-modern badge-active"><i class="fas fa-circle fa-xs mr-1"></i>Aktif</span>
                                @else
                                    <span class="badge badge-modern badge-inactive"><i class="fas fa-circle fa-xs mr-1"></i>Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @can('suppliers.view')
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-info btn-action" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('suppliers.edit')
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    <a href="{{ route('purchase-orders.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-outline-primary btn-action" title="Lihat PO">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                    @can('suppliers.delete')
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline form-delete">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="text-center py-5">
                                    <i class="fas fa-truck fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-1">Belum ada supplier terdaftar.</p>
                                    @can('suppliers.create')
                                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary-apms btn-sm mt-2">
                                        <i class="fas fa-plus mr-1"></i> Tambah Supplier Pertama
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $suppliers->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Confirm delete
    $(document).on('submit', '.form-delete', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Supplier?',
            text: 'Data supplier dan seluruh riwayat terkait akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
