@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-users mr-2"></i>Manajemen Pelanggan</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Pelanggan</li>
                    </ol>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('customers.create') }}" class="btn btn-primary-apms">
                        <i class="fas fa-plus mr-1"></i> Tambah Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background-color: rgba(255, 107, 53, 0.1);">
                                <i class="fas fa-users fa-lg" style="color: var(--primary);"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Total Pelanggan</div>
                            <h4 class="mb-0 font-weight-bold" id="totalCustomers">{{ $customers->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background-color: rgba(40, 167, 69, 0.1);">
                                <i class="fas fa-user-check fa-lg text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Pelanggan Aktif</div>
                            <h4 class="mb-0 font-weight-bold" id="activeCustomers">{{ $customers->where('is_active', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background-color: rgba(23, 162, 184, 0.1);">
                                <i class="fas fa-store fa-lg text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Pelanggan Grosir</div>
                            <h4 class="mb-0 font-weight-bold" id="wholesaleCustomers">{{ $customers->where('type', 'wholesale')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background-color: rgba(255, 193, 7, 0.1);">
                                <i class="fas fa-crown fa-lg text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Pelanggan VIP</div>
                            <h4 class="mb-0 font-weight-bold" id="vipCustomers">{{ $customers->where('type', 'vip')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card card-apms">
        <div class="card-header d-flex flex-wrap align-items-center py-3">
            <h3 class="card-title mb-2 mb-md-0 font-weight-bold">Daftar Pelanggan</h3>
            <div class="ml-auto d-flex flex-wrap gap-2">
                <a href="{{ route('customers.create') }}" class="btn btn-primary-apms">
                    <i class="fas fa-user-plus mr-1"></i> Tambah Pelanggan
                </a>
                <button class="btn btn-success" onclick="exportCustomers()">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card-body border-bottom">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="searchCustomers" class="form-control" 
                               placeholder="Cari nama, kode, telepon...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-6 mb-3 mb-lg-0">
                    <select id="customerTypeFilter" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="retail">Retail</option>
                        <option value="wholesale">Grosir</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-6 mb-3 mb-lg-0">
                    <select id="customerStatusFilter" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-6 mb-3 mb-lg-0">
                    <select id="sortBy" class="form-control">
                        <option value="name_asc">Nama A-Z</option>
                        <option value="name_desc">Nama Z-A</option>
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <button class="btn btn-outline-secondary btn-block" onclick="resetFilters()">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0" id="customersTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th>Telepon</th>
                            <th>Total Pembelian</th>
                            <th>Poin Loyalty</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr class="customer-row" data-id="{{ $customer->id }}" style="cursor: pointer;">
                            <td onclick="event.stopPropagation()">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input customer-checkbox" 
                                           id="check{{ $customer->id }}" value="{{ $customer->id }}">
                                    <label class="custom-control-label" for="check{{ $customer->id }}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $customer->name }}</div>
                                        <small class="text-muted">{{ $customer->customer_code ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($customer->type == 'wholesale')
                                    <span class="badge badge-modern badge-info">
                                        <i class="fas fa-store mr-1"></i> Grosir
                                    </span>
                                @elseif($customer->type == 'vip')
                                    <span class="badge badge-modern badge-warning">
                                        <i class="fas fa-crown mr-1"></i> VIP
                                    </span>
                                @else
                                    <span class="badge badge-modern badge-secondary">
                                        <i class="fas fa-user mr-1"></i> Retail
                                    </span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-phone-alt text-muted mr-1"></i>
                                {{ $customer->phone ?? '-' }}
                            </td>
                            <td>
                                <span class="font-weight-bold text-success">
                                    Rp {{ number_format($customer->transactions->sum('total_amount'), 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-modern badge-primary">
                                    <i class="fas fa-star mr-1"></i>
                                    {{ floor($customer->transactions->sum('total_amount') / 10000) }}
                                </span>
                            </td>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge badge-modern badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-modern badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center" onclick="event.stopPropagation()">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $customer->id) }}" 
                                       class="btn btn-info btn-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deleteCustomer({{ $customer->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada data pelanggan</h5>
                                    <p class="text-muted mb-3">Mulai tambahkan pelanggan pertama Anda</p>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary-apms">
                                        <i class="fas fa-user-plus mr-1"></i> Tambah Pelanggan
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Actions Bar (Hidden by default) -->
        <div id="bulkActionsBar" class="card-footer border-top bg-light" style="display: none;">
            <div class="d-flex align-items-center">
                <span id="selectedCount" class="mr-3 font-weight-bold">0 dipilih</span>
                <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-modern {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    flex-shrink: 0;
}

.table-modern thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem 0.75rem;
}

.table-modern tbody tr {
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.table-modern td {
    vertical-align: middle;
    padding: 0.875rem 0.75rem;
}

.badge-modern {
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 4px;
    font-size: 0.75rem;
}

.btn-group-sm > .btn {
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 4px;
}

.input-group-text {
    border: 1px solid #ced4da;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.15);
}

.empty-state {
    padding: 3rem 1rem;
}

@media (max-width: 768px) {
    .avatar-circle {
        width: 36px;
        height: 36px;
        font-size: 12px;
    }
    
    .table-modern {
        font-size: 0.875rem;
    }
    
    .badge-modern {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedCustomers = [];

    // Row click to view detail
    $('.customer-row').on('click', function() {
        const customerId = $(this).data('id');
        window.location.href = `/customers/${customerId}`;
    });

    // Search functionality
    $('#searchCustomers').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        filterTable();
    });

    // Filter by type
    $('#customerTypeFilter').on('change', function() {
        filterTable();
    });

    // Filter by status
    $('#customerStatusFilter').on('change', function() {
        filterTable();
    });

    // Sort functionality
    $('#sortBy').on('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchValue = $('#searchCustomers').val().toLowerCase();
        const typeValue = $('#customerTypeFilter').val();
        const statusValue = $('#customerStatusFilter').val();

        $('#customersTable tbody tr.customer-row').each(function() {
            const row = $(this);
            const text = row.text().toLowerCase();
            const type = row.find('.badge-modern').text().toLowerCase();
            const status = row.find('td:eq(6) .badge-modern').hasClass('badge-success') ? 'active' : 'inactive';

            let showRow = true;

            // Search filter
            if (searchValue && !text.includes(searchValue)) {
                showRow = false;
            }

            // Type filter
            if (typeValue && !type.includes(typeValue)) {
                showRow = false;
            }

            // Status filter
            if (statusValue && status !== statusValue) {
                showRow = false;
            }

            row.toggle(showRow);
        });

        updateKPIs();
    }

    function updateKPIs() {
        const visibleRows = $('#customersTable tbody tr.customer-row:visible');
        const activeCount = visibleRows.filter(function() {
            return $(this).find('td:eq(6) .badge-success').length > 0;
        }).length;
        const wholesaleCount = visibleRows.filter(function() {
            return $(this).find('.badge-info').length > 0;
        }).length;
        const vipCount = visibleRows.filter(function() {
            return $(this).find('.badge-warning').length > 0;
        }).length;

        $('#totalCustomers').text(visibleRows.length);
        $('#activeCustomers').text(activeCount);
        $('#wholesaleCustomers').text(wholesaleCount);
        $('#vipCustomers').text(vipCount);
    }

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.customer-checkbox:visible').prop('checked', isChecked);
        updateBulkActions();
    });

    // Individual checkbox
    $('.customer-checkbox').on('change', function() {
        updateBulkActions();
    });

    function updateBulkActions() {
        const checkedCount = $('.customer-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkActionsBar').slideDown();
            $('#selectedCount').text(checkedCount + ' dipilih');
        } else {
            $('#bulkActionsBar').slideUp();
            $('#selectAll').prop('checked', false);
        }
    }

    // Reset filters
    window.resetFilters = function() {
        $('#searchCustomers').val('');
        $('#customerTypeFilter').val('');
        $('#customerStatusFilter').val('');
        $('#sortBy').val('name_asc');
        filterTable();
    };

    // Delete customer
    window.deleteCustomer = function(id) {
        Swal.fire({
            title: 'Hapus Pelanggan?',
            text: 'Data pelanggan akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/customers/' + id,
                    method: 'POST',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Berhasil!', 'Pelanggan telah dihapus', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus pelanggan.', 'error');
                    }
                });
            }
        });
    };

    // Bulk delete
    window.bulkDelete = function() {
        const selectedIds = $('.customer-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Hapus ' + selectedIds.length + ' Pelanggan?',
            text: 'Data pelanggan akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("customers.bulk-delete") }}',
                    method: 'POST',
                    data: { ids: selectedIds, _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Berhasil!', selectedIds.length + ' pelanggan telah dihapus', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus pelanggan.', 'error');
                    }
                });
            }
        });
    };

    // Export customers
    window.exportCustomers = function() {
        Swal.fire({
            title: 'Export Data Pelanggan',
            text: 'Pilih format export',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-file-excel"></i> Excel',
            cancelButtonText: '<i class="fas fa-file-pdf"></i> PDF',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("customers.export") }}?format=excel';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = '/customers/export?format=pdf';
            }
        });
    };
});
</script>
@endpush
