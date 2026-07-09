@extends('layouts.app')

@section('title', 'Manajemen Inventory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h3 class="card-title mb-2 mb-md-0">Inventory Management</h3>
                    @if(isset($warehouses) && $warehouses->count() > 0)
                    <form action="{{ route('inventory.index') }}" method="GET" class="ml-3 mb-1">
                        <select name="warehouse_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Semua Gudang</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ $wh->code }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                    <div class="card-tools ml-auto d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-primary-apms btn-sm mb-1" data-toggle="modal" data-target="#adjustModal">
                            <i class="fas fa-exchange-alt"></i> Adjustment
                        </button>
                        <button type="button" class="btn btn-success btn-sm mb-1" data-toggle="modal" data-target="#stockInModal">
                            <i class="fas fa-arrow-down"></i> Stok Masuk
                        </button>
                        <button type="button" class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#auditModal">
                            <i class="fas fa-clipboard-check"></i> Audit
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs flex-nowrap overflow-auto" id="inventoryTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <li class="nav-item">
                            <a class="nav-link active text-nowrap" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                <i class="fas fa-boxes"></i> Semua
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap" id="low-tab" data-toggle="tab" href="#low" role="tab">
                                <i class="fas fa-exclamation-triangle text-warning"></i> Stok Rendah
                                <span class="badge badge-warning">{{ $lowStock->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap" id="out-tab" data-toggle="tab" href="#out" role="tab">
                                <i class="fas fa-times-circle text-danger"></i> Habis
                                <span class="badge badge-danger">{{ $outOfStock->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap" id="expiring-tab" data-toggle="tab" href="#expiring" role="tab">
                                <i class="fas fa-calendar-times text-danger"></i> Kadaluarsa
                                <span class="badge badge-danger">{{ $expiringSoon->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="batch-tab" data-toggle="tab" href="#batch" role="tab">
                                <i class="fas fa-layer-group"></i> Batch Management
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tabs Content -->
                    <div class="tab-content mt-3" id="inventoryTabsContent">
                        <!-- All Inventory Tab -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="inventoryTable">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Batch</th>
                                            <th>Stok Awal</th>
                                            <th>Masuk</th>
                                            <th>Keluar</th>
                                            <th>Stok Sekarang</th>
                                            <th>Minimal</th>
                                            <th>Harga Beli</th>
                                            <th>Expired</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventories as $inventory)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($inventory->product->image)
                                                    <img src="{{ asset('storage/' . $inventory->product->image) }}" 
                                                         class="img-circle img-size-32 mr-2">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $inventory->product->name }}</strong><br>
                                                        <small class="text-muted">{{ $inventory->product->size }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($inventory->batch_number)
                                                <span class="badge badge-info">{{ $inventory->batch_number }}</span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $inventory->stock_in }}</td>
                                            <td class="text-success">+{{ $inventory->stock_in }}</td>
                                            <td class="text-danger">-{{ $inventory->stock_out }}</td>
                                            <td>
                                                <span class="badge badge-{{ $inventory->current_stock == 0 ? 'danger' : ($inventory->current_stock < $inventory->minimum_stock ? 'warning' : 'success') }}">
                                                    {{ $inventory->current_stock }}
                                                </span>
                                                @if($inventory->bulk_stock_ml > 0 || ($inventory->product && $inventory->product->is_refill))
                                                <br><small class="text-info font-weight-bold">{{ number_format($inventory->bulk_stock_ml ?? 0) }} ml</small>
                                                @endif
                                            </td>
                                            <td>{{ $inventory->minimum_stock }}</td>
                                            <td>Rp {{ number_format($inventory->cost_per_unit, 0, ',', '.') }}</td>
                                            <td>
                                                @if($inventory->expiration_date)
                                                    @php
                                                        $expDate = \Carbon\Carbon::parse($inventory->expiration_date);
                                                        $now = \Carbon\Carbon::now();
                                                        $diffDays = $now->diffInDays($expDate, false);
                                                    @endphp
                                                    
                                                    @if($diffDays < 0)
                                                        <span class="badge badge-danger">
                                                            {{ $expDate->format('d/m/Y') }}
                                                            <i class="fas fa-exclamation ml-1"></i>
                                                        </span>
                                                    @elseif($diffDays <= 30)
                                                        <span class="badge badge-warning">
                                                            {{ $expDate->format('d/m/Y') }}
                                                            ({{ $diffDays }} hari)
                                                        </span>
                                                    @else
                                                        <span class="badge badge-success">
                                                            {{ $expDate->format('d/m/Y') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $invStatus = $inventory->current_stock == 0 ? 'out_of_stock' : ($inventory->current_stock <= $inventory->minimum_stock ? 'low_stock' : 'in_stock'); @endphp
                                                @if($invStatus == 'in_stock')
                                                    <span class="badge badge-success">Tersedia</span>
                                                @elseif($invStatus == 'low_stock')
                                                    <span class="badge badge-warning">Rendah</span>
                                                @elseif($invStatus == 'out_of_stock')
                                                    <span class="badge badge-danger">Habis</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info" 
                                                            onclick="viewInventory(@json($inventory->id))" 
                                                            title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning" 
                                                            onclick="editInventory(@json($inventory->id))" 
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary-apms" 
                                                            onclick="adjustStock(@json($inventory->id))" 
                                                            title="Adjust">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Low Stock Tab -->
                        <div class="tab-pane fade" id="low" role="tabpanel">
                            @if($lowStock->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Terdapat {{ $lowStock->count() }} produk dengan stok rendah
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Stok Sekarang</th>
                                            <th>Minimal</th>
                                            <th>Kekurangan</th>
                                            <th>Estimasi Habis</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStock as $item)
                                        <tr>
                                            <td>{{ $item->product?->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-warning">{{ $item->current_stock }}</span>
                                            </td>
                                            <td>{{ $item->minimum_stock }}</td>
                                            <td>{{ $item->minimum_stock - $item->current_stock }}</td>
                                            <td>
                                                @php
                                                    $daysLeft = $item->minimum_stock > 0 ? floor($item->current_stock / max($item->stock_out, 1)) : 0;
                                                @endphp
                                                @if($daysLeft <= 3)
                                                    <span class="badge badge-danger">{{ $daysLeft }} hari</span>
                                                @elseif($daysLeft <= 7)
                                                    <span class="badge badge-warning">{{ $daysLeft }} hari</span>
                                                @else
                                                    <span class="badge badge-success">{{ $daysLeft }} hari</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary-apms btn-sm" 
                                                        onclick="quickReorder(@json($item->product_id))">
                                                    <i class="fas fa-shopping-cart"></i> Pesan Ulang
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Tidak ada produk dengan stok rendah
                            </div>
                            @endif
                        </div>
                        
                        <!-- Out of Stock Tab -->
                        <div class="tab-pane fade" id="out" role="tabpanel">
                            @if($outOfStock->count() > 0)
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle"></i>
                                Terdapat {{ $outOfStock->count() }} produk yang stoknya habis
                            </div>
                            <div class="row">
                                @foreach($outOfStock as $item)
                                <div class="col-md-4 mb-3">
                                    <div class="card card-outline card-danger">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $item->product?->name ?? '-' }}</h5>
                                            <p class="card-text">
                                                <i class="fas fa-box text-danger"></i> Stok: <strong>0</strong><br>
                                                <i class="fas fa-tag text-muted"></i> Kategori: {{ $item->product?->category?->name ?? '-' }}<br>
                                                <i class="fas fa-money-bill-wave text-success"></i> Harga: Rp {{ number_format($item->product->selling_price, 0, ',', '.') }}
                                            </p>
                                            <button class="btn btn-primary-apms btn-block" 
                                                    onclick="quickReorder(@json($item->product_id))">
                                                <i class="fas fa-redo"></i> Restock
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Tidak ada produk yang stoknya habis
                            </div>
                            @endif
                        </div>
                        
                        <!-- Expiring Soon Tab -->
                        <div class="tab-pane fade" id="expiring" role="tabpanel">
                            @if($expiringSoon->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-calendar-times"></i>
                                Terdapat {{ $expiringSoon->count() }} produk yang akan kadaluarsa dalam 30 hari
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Batch</th>
                                            <th>Stok</th>
                                            <th>Tanggal Expired</th>
                                            <th>Sisa Hari</th>
                                            <th>Nilai Stok</th>
                                            <th>Rekomendasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiringSoon as $item)
                                        @php
                                            $expDate = \Carbon\Carbon::parse($item->expiration_date);
                                            $now = \Carbon\Carbon::now();
                                            $diffDays = $now->diffInDays($expDate, false);
                                            $stockValue = $item->current_stock * $item->cost_per_unit;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->product?->name ?? '-' }}</td>
                                            <td>{{ $item->batch_number ?? '-' }}</td>
                                            <td>{{ $item->current_stock }}</td>
                                            <td>{{ $expDate->format('d/m/Y') }}</td>
                                            <td>
                                                @if($diffDays < 0)
                                                    <span class="badge badge-danger">EXPIRED</span>
                                                @else
                                                    <span class="badge badge-warning">{{ $diffDays }} hari</span>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($stockValue, 0, ',', '.') }}</td>
                                            <td>
                                                @if($diffDays <= 7)
                                                    <span class="badge badge-danger">Diskon Cepat!</span>
                                                @elseif($diffDays <= 15)
                                                    <span class="badge badge-warning">Promo</span>
                                                @else
                                                    <span class="badge badge-info">Monitor</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Tidak ada produk yang akan kadaluarsa dalam 30 hari
                            </div>
                            @endif
                        </div>
                        
                        <!-- Batch Management Tab -->
                        <div class="tab-pane fade" id="batch" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Kelola Batch Produk</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pilih Produk</label>
                                                <select class="form-control select2" id="batchProductSelect">
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nomor Batch</label>
                                                <input type="text" class="form-control" id="batchNumber" 
                                                       placeholder="Contoh: BATCH-2024-001">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="batchDetails" class="mt-3" style="display: none;">
                                        <!-- Batch details will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted">
                                Total Nilai Inventory: <strong>Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="small">
                                Update Terakhir: {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('inventory.modals.adjust')
@include('inventory.modals.stock-in')
@include('inventory.modals.audit')

@endsection

@push('styles')
<style>
.card-outline {
    border-top: 3px solid;
}
.img-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
}
.img-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.nav-tabs .nav-link.active {
    border-bottom: 3px solid #FF6B35;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
$(function() {
    // Initialize DataTable
    $('#inventoryTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 25
    });
    
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });
    
    // Product select change for batch management
    $('#batchProductSelect').change(function() {
        const productId = $(this).val();
        if (productId) {
            loadBatchDetails(productId);
        } else {
            $('#batchDetails').hide();
        }
    });
});

function loadBatchDetails(productId) {
    $.ajax({
        url: `/api/inventory/batch/${productId}`,
        method: 'GET',
        success: function(response) {
            $('#batchDetails').html(response.html).show();
        }
    });
}

function adjustStock(inventoryId) {
    $('#adjustInventoryId').val(inventoryId);
    $('#adjustModal').modal('show');
}

function quickReorder(productId) {
    Swal.fire({
        title: 'Pesan Ulang Produk',
        html: `
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" id="reorderQuantity" class="form-control" min="1" value="10">
            </div>
            <div class="form-group">
                <label>Supplier</label>
                <select id="reorderSupplier" class="form-control">
                    <option value="">Pilih Supplier</option>
                    <option value="1">Supplier Utama</option>
                    <option value="2">Supplier Alternatif</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Pesan',
        preConfirm: () => {
            const quantity = $('#reorderQuantity').val();
            const supplier = $('#reorderSupplier').val();
            
            if (!quantity || quantity < 1) {
                Swal.showValidationMessage('Masukkan jumlah yang valid');
                return false;
            }
            
            return { quantity, supplier };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX request to create purchase order
            Swal.fire('Berhasil', 'Pesanan telah dibuat', 'success');
        }
    });
}

function viewInventory(id) {
    adjustStock(id);
}

function editInventory(id) {
    adjustStock(id);
}
</script>
@endpush