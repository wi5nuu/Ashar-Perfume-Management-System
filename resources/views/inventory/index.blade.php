@extends('layouts.app')

@section('title', 'Manajemen Inventaris')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-warehouse mr-2"></i>Manajemen Inventaris</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Inventaris</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg col-md-6 col-sm-6 mb-3">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon mr-3" style="background:rgba(255,107,53,0.1);">
                            <i class="fas fa-boxes" style="color:var(--primary);"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Total SKU</div>
                            <h4 class="mb-0 font-weight-bold">{{ $inventories->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-3">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon mr-3" style="background:rgba(23,162,184,0.1);">
                            <i class="fas fa-cubes text-info"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Total Stok</div>
                            <h4 class="mb-0 font-weight-bold">{{ \App\Helpers\PerformanceHelper::formatMl($inventories->sum('current_stock')) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-3">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon mr-3" style="background:rgba(255,193,7,0.1);">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Stok Rendah</div>
                            <h4 class="mb-0 font-weight-bold">{{ $inventories->filter(fn($i) => (float)($i->current_stock ?? 0) > 0 && (float)($i->current_stock ?? 0) <= ($i->minimum_stock ?? 500))->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-3">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon mr-3" style="background:rgba(220,53,69,0.1);">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Stok Habis</div>
                            <h4 class="mb-0 font-weight-bold">{{ $inventories->filter(fn($i) => (float)($i->current_stock ?? 0) <= 0)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-3">
            <div class="card card-modern">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon mr-3" style="background:rgba(40,167,69,0.1);">
                            <i class="fas fa-money-bill-wave text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Nilai Inventory</div>
                            <h5 class="mb-0 font-weight-bold" style="font-size:1rem;">
                                Rp {{ number_format($inventories->sum(fn($i) => $i->quantity * ($i->product->cost_price ?? 0)), 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card card-apms">
        <div class="card-header d-flex flex-wrap align-items-center py-3">
            <h3 class="card-title mb-2 mb-md-0 font-weight-bold">Daftar Inventaris</h3>
            <div class="ml-auto d-flex flex-wrap">
                <button type="button" class="btn btn-primary-apms mr-2 mb-1" data-toggle="modal" data-target="#adjustModal">
                    <i class="fas fa-exchange-alt mr-1"></i> Sesuaikan Stok
                </button>
                <button type="button" class="btn btn-warning mr-2 mb-1" data-toggle="modal" data-target="#auditModal">
                    <i class="fas fa-clipboard-check mr-1"></i> Audit Stok
                </button>
                <button type="button" class="btn btn-success mb-1" onclick="exportInventory()">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card-body border-bottom pb-3">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="searchInventory" class="form-control"
                               placeholder="Cari produk, SKU...">
                    </div>
                </div>
                @if(isset($warehouses) && $warehouses->count() > 0)
                <div class="col-lg-2 col-md-6 col-6 mb-2 mb-lg-0">
                    <form action="{{ route('inventory.index') }}" method="GET" id="warehouseForm">
                        <select name="warehouse_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Gudang</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif
                <div class="col-lg-2 col-md-6 col-6 mb-2 mb-lg-0">
                    <select id="categoryFilter" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($inventories->pluck('product.category')->filter()->unique() as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-6 mb-2 mb-lg-0">
                    <select id="stockStatusFilter" class="form-control">
                        <option value="">Semua Status Stok</option>
                        <option value="normal">Normal</option>
                        <option value="low">Stok Rendah</option>
                        <option value="out">Stok Habis</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <button class="btn btn-outline-secondary btn-block" onclick="resetInventoryFilters()">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Gudang</th>
                            <th class="text-center">Stok Saat Ini</th>
                            <th class="text-center">Min. Stok</th>
                            <th class="text-center">Status</th>
                            <th>Terakhir Update</th>
                            <th class="text-center" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                        @php
                            $qty = (float)($inventory->current_stock ?? 0);
                            $min = $inventory->minimum_stock ?? 500;
                            if ($qty <= 0) {
                                $stockStatus = 'out';
                                $statusClass = 'danger';
                                $statusLabel = 'Habis';
                            } elseif ($qty <= $min) {
                                $stockStatus = 'low';
                                $statusClass = 'warning';
                                $statusLabel = 'Rendah';
                            } else {
                                $stockStatus = 'normal';
                                $statusClass = 'success';
                                $statusLabel = 'Normal';
                            }
                        @endphp
                        <tr class="inventory-row" data-stock-status="{{ $stockStatus }}" data-product-id="{{ $inventory->product_id }}" data-inventory-id="{{ $inventory->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="product-thumb mr-3">
                                        @if($inventory->product && $inventory->product->image)
                                            <img src="{{ asset('storage/' . $inventory->product->image) }}"
                                                 alt="{{ $inventory->product->name }}"
                                                 class="rounded" width="40" height="40" style="object-fit:cover;">
                                        @else
                                            <div class="product-thumb-placeholder">
                                                <i class="fas fa-spray-can"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $inventory->product->name ?? 'Produk Tidak Ditemukan' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $inventory->product->sku ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $inventory->product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-warehouse text-muted mr-1"></i>
                                {{ $inventory->warehouse->name ?? 'Utama' }}
                            </td>
                            <td class="text-center">
                                <span class="stock-qty font-weight-bold stock-{{ $stockStatus }}"
                                      data-id="{{ $inventory->id }}"
                                      style="cursor:pointer;"
                                      title="Klik untuk edit stok"
                                      onclick="quickEditStock({{ $inventory->id }}, {{ $qty }})">
                                    {{ number_format($qty, 0) }}
                                </span>
                                <small class="d-block text-muted" style="font-size:0.7rem;">ml</small>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ number_format($min, 0) }}ml</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-modern badge-{{ $statusClass }}">
                                    @if($stockStatus == 'out')
                                        <i class="fas fa-times-circle mr-1"></i>
                                    @elseif($stockStatus == 'low')
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                    @else
                                        <i class="fas fa-check-circle mr-1"></i>
                                    @endif
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $inventory->updated_at ? $inventory->updated_at->diffForHumans() : '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-primary-apms btn-sm"
                                            onclick="adjustStock({{ $inventory->id }})"
                                            title="Sesuaikan Stok">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <button class="btn btn-info btn-sm"
                                            onclick="viewHistory({{ $inventory->id }})"
                                            title="Lihat History">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada data inventaris</h5>
                                    <p class="text-muted mb-0">Data stok produk akan muncul di sini</p>
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

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exchange-alt mr-2" style="color:var(--primary);"></i>Sesuaikan Stok
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="adjustForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="adjustInventoryId" name="inventory_id">
                    <input type="hidden" id="adjustProductId" name="product_id">
                    <div class="form-group">
                        <label class="font-weight-bold small">Produk</label>
                        <input type="text" id="adjustProductName" class="form-control" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Stok Saat Ini</label>
                                <input type="number" id="adjustCurrentStock" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Tipe Penyesuaian</label>
                                <select name="adjustment_type" id="adjustType" class="form-control">
                                    <option value="add">Tambah (+)</option>
                                    <option value="subtract">Kurangi (-)</option>
                                    <option value="set">Atur Langsung</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="adjustQuantity"
                               class="form-control" min="1" placeholder="Masukkan jumlah" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Keterangan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="2"
                                  placeholder="Alasan penyesuaian..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-apms">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Audit Modal -->
<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-history mr-2" style="color:var(--primary);"></i>
                    Riwayat Stok — <span id="historyProductName"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <div id="historyLoading" class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Memuat riwayat...</p>
                </div>
                <div id="historyContent" style="display:none;">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Sebelum</th>
                                <th class="text-center">Sesudah</th>
                                <th>Keterangan</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody"></tbody>
                    </table>
                </div>
                <div id="historyEmpty" class="text-center py-5" style="display:none;">
                    <i class="fas fa-inbox fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Belum ada riwayat pergerakan stok</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="auditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-clipboard-check mr-2" style="color:var(--primary);"></i>Audit Stok
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Audit stok akan membandingkan stok sistem dengan stok fisik di gudang.
                </div>
                <div class="form-group">
                    <label class="font-weight-bold small">Gudang yang Diaudit</label>
                    <select class="form-control">
                        <option value="">Semua Gudang</option>
                        @if(isset($warehouses))
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold small">Catatan Audit</label>
                    <textarea class="form-control" rows="2" placeholder="Catatan audit..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="startAudit()">
                    <i class="fas fa-clipboard-check mr-1"></i> Mulai Audit
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
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.card-modern:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.summary-icon {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.product-thumb-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}
.table-modern thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.72rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 0.9rem 0.75rem;
    white-space: nowrap;
}
.table-modern td {
    vertical-align: middle;
    padding: 0.8rem 0.75rem;
}
.table-modern tbody tr { transition: background 0.2s ease; }
.table-modern tbody tr:hover { background-color: #f8f9fa; }
.stock-normal { color: #28a745; font-size: 1.1rem; }
.stock-low    { color: #ffc107; font-size: 1.1rem; }
.stock-out    { color: #dc3545; font-size: 1.1rem; }
.stock-qty:hover { text-decoration: underline; }
.badge-modern {
    padding: 0.35em 0.65em;
    font-weight: 600;
    border-radius: 4px;
    font-size: 0.75rem;
}
.input-group-text {
    background-color: #fff;
    border-right: none;
}
.input-group .form-control { border-left: none; }
.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(255,107,53,0.15);
}
.empty-state { padding: 3rem 1rem; }
@media (max-width: 768px) {
    .summary-icon { width: 38px; height: 38px; font-size: 1rem; }
    .table-modern { font-size: 0.85rem; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Search
    $('#searchInventory').on('keyup', function() { filterInventory(); });
    $('#categoryFilter, #stockStatusFilter').on('change', function() { filterInventory(); });

    function filterInventory() {
        const search = $('#searchInventory').val().toLowerCase();
        const cat    = $('#categoryFilter').val().toLowerCase();
        const status = $('#stockStatusFilter').val();

        $('#inventoryTable tbody tr.inventory-row').each(function() {
            const row        = $(this);
            const text       = row.text().toLowerCase();
            const rowCat     = row.find('td:eq(1)').text().toLowerCase().trim();
            const rowStatus  = row.data('stock-status');
            let show = true;
            if (search && !text.includes(search))  show = false;
            if (cat    && rowCat !== cat)           show = false;
            if (status && rowStatus !== status)     show = false;
            row.toggle(show);
        });
    }

    window.resetInventoryFilters = function() {
        $('#searchInventory').val('');
        $('#categoryFilter').val('');
        $('#stockStatusFilter').val('');
        filterInventory();
    };

    // Quick inline stock edit
    window.quickEditStock = function(id, currentQty) {
        Swal.fire({
            title: 'Edit Stok Cepat',
            input: 'number',
            inputLabel: 'Stok baru',
            inputValue: currentQty,
            inputAttributes: { min: 0, step: 1 },
            showCancelButton: true,
            confirmButtonColor: 'var(--primary)',
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            preConfirm: (val) => {
                if (val === '' || val < 0) {
                    Swal.showValidationMessage('Masukkan nilai stok yang valid');
                    return false;
                }
                return parseInt(val);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Stok diperbarui', timer: 1500, showConfirmButton: false });
            }
        });
    };

    // Adjust stock modal
    window.adjustStock = function(id) {
        const row = $('tr[data-inventory-id="' + id + '"]');
        const productName = row.find('td:eq(0) .font-weight-bold').text().trim();
        const currentQty  = row.find('.stock-qty').text().trim();
        const productId   = row.data('product-id');
        $('#adjustInventoryId').val(id);
        $('#adjustProductId').val(productId || '');
        $('#adjustProductName').val(productName);
        $('#adjustCurrentStock').val(currentQty);
        $('#adjustModal').modal('show');
    };

    // Adjust form submit — kirim ke server via AJAX
    $('#adjustForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('[type=submit]').prop('disabled', true);
        $.ajax({
            url: '{{ route("inventory.adjust") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#adjustModal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Penyesuaian stok disimpan', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan' });
            }
        });
    });

    // View history
    window.viewHistory = function(id) {
        $('#historyProductName').text('');
        $('#historyLoading').show();
        $('#historyContent').hide();
        $('#historyEmpty').hide();
        $('#historyModal').modal('show');

        $.ajax({
            url: '/inventory/' + id + '/history',
            method: 'GET',
            success: function(response) {
                $('#historyProductName').text(response.product);
                $('#historyLoading').hide();

                if (!response.movements || response.movements.length === 0) {
                    $('#historyEmpty').show();
                    return;
                }

                const typeColors = {
                    'Penjualan'    : 'danger',
                    'Penyesuaian'  : 'warning',
                    'Penerimaan'   : 'success',
                    'Retur'        : 'info',
                    'Transfer'     : 'secondary',
                };

                let rows = '';
                response.movements.forEach(function(m) {
                    const color  = typeColors[m.type_label] || 'secondary';
                    const qtyStr = m.quantity > 0 ? '+' + m.quantity : m.quantity;
                    const qtyColor = m.quantity > 0 ? 'text-success' : 'text-danger';
                    rows += `<tr>
                        <td style="font-size:0.82rem;white-space:nowrap;">${m.date}</td>
                        <td><span class="badge badge-${color}">${m.type_label}</span></td>
                        <td class="text-center font-weight-bold ${qtyColor}">${qtyStr}</td>
                        <td class="text-center">${m.stock_before}</td>
                        <td class="text-center">${m.stock_after}</td>
                        <td style="font-size:0.82rem;">${m.notes || '-'}</td>
                        <td style="font-size:0.82rem;">${m.user}</td>
                    </tr>`;
                });

                $('#historyTableBody').html(rows);
                $('#historyContent').show();
            },
            error: function() {
                $('#historyLoading').hide();
                $('#historyEmpty').show();
                $('#historyProductName').text('Error memuat data');
            }
        });
    };

    // Transfer stock
    window.transferStock = function(id) {
        Swal.fire({ title: 'Transfer Stok', text: 'Fitur transfer stok antar gudang akan segera tersedia', icon: 'info', confirmButtonColor: 'var(--primary)' });
    };

    // Start audit
    window.startAudit = function() {
        $('#auditModal').modal('hide');
        Swal.fire({ icon: 'success', title: 'Audit Dimulai', text: 'Sesi audit stok telah dibuka', confirmButtonColor: 'var(--primary)' });
    };

    // Export
    window.exportInventory = function() {
        Swal.fire({
            title: 'Export Inventaris',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-file-excel"></i> Excel',
            cancelButtonText: '<i class="fas fa-file-pdf"></i> PDF',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed)
                window.location.href = '{{ route("inventory.export", ["format" => "excel"]) }}';
            else if (result.dismiss === Swal.DismissReason.cancel)
                window.location.href = '{{ route("inventory.export", ["format" => "pdf"]) }}';
        });
    };
});
</script>
@endpush
