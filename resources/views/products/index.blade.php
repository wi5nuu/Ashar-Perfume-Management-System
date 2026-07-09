@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Daftar Produk</h3>
                    <div class="ml-auto">
                        <a href="{{ route('products.create') }}" class="btn btn-primary-apms">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </a>
                        <div class="btn-group ml-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('products.export.pdf') }}" target="_blank" onclick="loadingExport(this)">
                                    <i class="fas fa-file-pdf text-danger mr-2"></i> PDF
                                </a>
                                <a class="dropdown-item" href="{{ route('products.export.csv') }}" target="_blank" onclick="loadingExport(this)">
                                    <i class="fas fa-file-csv text-success mr-2"></i> CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari produk atau scan barcode..." autofocus>
                                <div class="input-group-append">
                                    <button class="btn btn-primary-apms" onclick="$('#searchInput').focus()" title="Scan Barcode">
                                        <i class="fas fa-barcode"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                            <select id="categoryFilter" class="form-control">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 mb-2 mb-md-0">
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary active" id="typeAll">
                                    <input type="radio" name="typeFilter" value="" checked> Semua
                                </label>
                                <label class="btn btn-outline-primary" id="typeRegular">
                                    <input type="radio" name="typeFilter" value="regular"> Produk
                                </label>
                                <label class="btn btn-outline-info" id="typeRefill">
                                    <input type="radio" name="typeFilter" value="refill"> Isi Ulang
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                            <select id="stockFilter" class="form-control">
                                <option value="">Semua Stok</option>
                                <option value="available">Tersedia</option>
                                <option value="low">Stok Rendah</option>
                                <option value="out">Stok Habis</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-secondary btn-block" onclick="resetFilters()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                    
                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Kode</th>
                                    <th>Produk</th>
                                    <th class="d-none d-sm-table-cell">Kategori</th>
                                    <th class="d-none d-md-table-cell">Ukuran</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="d-none d-sm-table-cell">Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                @php
                                    $inventory = $product->inventories->first();
                                    $currentStock = $inventory ? $inventory->current_stock : 0;
                                    $bulkStock = $inventory ? $inventory->bulk_stock_ml : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $product->internal_id }}</span><br>
                                        @if($product->barcode)
                                        <img src="{{ route('products.barcode-image', $product) }}"
                                             alt="{{ $product->barcode }}"
                                             style="height:24px;width:auto;display:block;margin-top:2px;">
                                        <small class="text-muted" style="font-size:10px;">{{ $product->barcode }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="img-circle img-size-32 mr-2">
                                            @else
                                            <div class="img-circle img-size-32 bg-light d-flex align-items-center justify-content-center mr-2">
                                                <i class="fas fa-wine-bottle text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @php $catColor = $product->category ? (preg_match('/^#[0-9a-fA-F]{6}$/', $product->category->color) ? $product->category->color : '#FF6B35') : '#FF6B35'; @endphp
                                        <span class="badge" style="background-color: {{ $catColor }}; color: white;">
                                            {{ $product->category?->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $product->size }} {{ $product->unit }}</td>
                                    <td>
                                        @if($product->is_refill)
                                        <span class="badge badge-info"><i class="fas fa-fill-drip mr-1"></i> Isi Ulang</span>
                                        @else
                                        <span class="badge badge-secondary">Reguler</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <div class="text-success font-weight-bold">
                                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                            </div>
                                            @if($product->is_refill && $product->refill_price_per_ml)
                                            <div class="text-info small">
                                                <i class="fas fa-fill-drip"></i> Refill: Rp {{ number_format($product->refill_price_per_ml, 0, ',', '.') }}/ml
                                            </div>
                                            @endif
                                            @if($product->wholesale_price)
                                            <div class="text-primary small">
                                                Grosir: Rp {{ number_format($product->wholesale_price, 0, ',', '.') }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->is_refill)
                                            @if(!$product->track_inventory)
                                                <span class="badge badge-secondary">Tanpa Stok</span>
                                            @elseif($bulkStock <= 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @else
                                                <span class="badge badge-info">{{ number_format($bulkStock) }} ml</span>
                                            @endif
                                        @else
                                            @if(!$product->track_inventory)
                                                <span class="badge badge-secondary">Tanpa Stok</span>
                                            @elseif($currentStock == 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @elseif($currentStock < 10)
                                                <span class="badge badge-warning">{{ $currentStock }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $currentStock }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @if($product->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('products.show', $product->id) }}" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product->id) }}" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.barcode', $product->id) }}" 
                                               class="btn btn-primary-apms" title="Barcode" target="_blank">
                                                <i class="fas fa-barcode"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="deleteProduct(@json($product->id))" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="row mt-3">
                        <div class="col-12 col-md-6 text-center text-md-left mb-2 mb-md-0">
                            <small class="text-muted">
                                Menampilkan {{ $products->firstItem() }} s/d {{ $products->lastItem() }} dari {{ $products->total() }} produk
                            </small>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-center justify-content-md-end">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bulk Actions -->
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-8 mb-2 mb-md-0">
                            <div class="btn-group flex-wrap">
                                <button type="button" class="btn btn-default" onclick="bulkAction('activate')">
                                    <i class="fas fa-check-circle"></i> Aktifkan
                                </button>
                                <button type="button" class="btn btn-default" onclick="bulkAction('deactivate')">
                                    <i class="fas fa-ban"></i> Nonaktifkan
                                </button>
                                <button type="button" class="btn btn-default" onclick="bulkAction('export')">
                                    <i class="fas fa-file-export"></i> Export
                                </button>
                                <button type="button" class="btn btn-danger" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-md-right">
                            <span id="selectedCount" class="d-block d-md-inline">0 produk terpilih</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus produk ini?</p>
                <p class="text-danger">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.price-info {
    min-width: 120px;
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
@media (max-width: 767.98px) {
    #productsTable .btn-group .btn {
        padding: 4px 6px;
        font-size: 0.65rem;
    }
    #productsTable td {
        vertical-align: middle;
        font-size: 0.78rem;
    }
    .card-footer .btn-group .btn {
        font-size: 0.75rem;
        padding: 6px 10px;
        margin-bottom: 4px;
    }
    #productsTable td:nth-child(2) { /* Kode column - smaller barcode image */
        max-width: 80px;
        overflow: hidden;
    }
}
</style>
@endpush

@push('scripts')
<script>
let selectedProducts = [];

function deleteProduct(id) {
    $('#deleteForm').attr('action', '{{ url('/products') }}/' + id);
    $('#deleteModal').modal('show');
}

function loadingExport(el) {
    Swal.fire({title:'Mengexport...', text:'Memproses export produk', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
    setTimeout(() => Swal.close(), 5000);
}

function bulkAction(action) {
    const selected = $('.product-checkbox:checked');
    if (selected.length === 0) {
        Swal.fire('Peringatan', 'Pilih produk terlebih dahulu', 'warning');
        return;
    }
    
    const ids = selected.map(function() {
        return $(this).val();
    }).get();
    
    switch(action) {
        case 'delete':
            if (confirm(`Hapus ${ids.length} produk?`)) {
                // AJAX delete
                $.ajax({
                    url: '{{ route('products.bulk-delete') }}',
                    method: 'POST',
                    data: { ids: ids, _token: '{{ csrf_token() }}' },
                    success: function() {
                        location.reload();
                    }
                });
            }
            break;
        case 'activate':
            // AJAX activate
            break;
        case 'deactivate':
            // AJAX deactivate
            break;
        case 'export':
            Swal.fire({title:'Mengexport...', text:`Mengexport ${ids.length} produk`, allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
            window.open('{{ route('products.export.csv') }}?ids=' + ids.join(','), '_blank');
            setTimeout(() => Swal.close(), 2000);
            break;
    }
}

function resetFilters() {
    $('#searchInput').val('');
    $('#categoryFilter').val('');
    $('.btn-group-toggle label').removeClass('active');
    $('#typeAll').addClass('active');
    $('#typeAll input').prop('checked', true);
    $('#stockFilter').val('');
    $('#productsTable tbody tr').show();
}

$(function() {
    // Select All
    $('#selectAll').change(function() {
        const isChecked = $(this).prop('checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });
    
    // Update selected count
    $('.product-checkbox').change(function() {
        updateSelectedCount();
    });
    
    // Search filter
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#productsTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Category filter
    $('#categoryFilter').change(function() {
        const value = $(this).val().toLowerCase();
        $('#productsTable tbody tr').each(function() {
            const category = $(this).find('td:eq(3)').text().toLowerCase().trim();
            $(this).toggle(!value || category.indexOf(value) > -1);
        });
    });
    
    // Type filter buttons (Produk / Isi Ulang)
    $('input[name="typeFilter"]').change(function() {
        const value = $(this).val();
        $('#productsTable tbody tr').each(function() {
            const type = $(this).find('td:eq(5)').text().toLowerCase().trim();
            if (!value) { $(this).show(); return; }
            if (value === 'refill') {
                $(this).toggle(type.indexOf('isi ulang') > -1);
            } else {
                $(this).toggle(type.indexOf('reguler') > -1);
            }
        });
    });
    
    // Stock filter
    $('#stockFilter').change(function() {
        const value = $(this).val();
        $('#productsTable tbody tr').each(function() {
            const cell = $(this).find('td:eq(7)');
            const badge = cell.find('.badge');
            let shouldShow = false;
            
            if (!value) {
                shouldShow = true;
            } else if (value === 'available') {
                shouldShow = badge.hasClass('badge-success') || badge.hasClass('badge-info');
            } else if (value === 'low') {
                shouldShow = badge.hasClass('badge-warning');
            } else if (value === 'out') {
                shouldShow = badge.hasClass('badge-danger');
            }
            
            $(this).toggle(shouldShow);
        });
    });
});

function updateSelectedCount() {
    const count = $('.product-checkbox:checked').length;
    $('#selectedCount').text(`${count} produk terpilih`);
}
</script>
@endpush