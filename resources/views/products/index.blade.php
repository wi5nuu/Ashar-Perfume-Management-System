@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-header-apms">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1><i class="fas fa-box mr-2"></i>Manajemen Produk</h1>
                    <ol class="breadcrumb mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Produk</li>
                    </ol>
                </div>
                <div class="mt-2 mt-md-0 d-flex align-items-center flex-wrap gap-1">
                    {{-- View toggle (parfum tab only) --}}
                    <div class="btn-group btn-group-sm mr-2 d-none d-sm-inline-flex parfum-only-control" id="viewToggle">
                        <button class="btn btn-outline-light active" id="tableViewBtn" onclick="switchProductView('table')" title="Tampilan Tabel">
                            <i class="fas fa-table"></i>
                        </button>
                        <button class="btn btn-outline-light" id="cardViewBtn" onclick="switchProductView('card')" title="Tampilan Kartu">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                    {{-- Tambah Parfum --}}
                    <a href="{{ route('products.create') }}" class="btn btn-primary-apms btn-sm mr-1 parfum-only-control">
                        <i class="fas fa-plus mr-1"></i> Tambah Parfum
                    </a>
                    {{-- Tambah Aksesori --}}
                    <button type="button" class="btn btn-secondary-apms btn-sm mr-1 acc-only-control" style="display:none;"
                            data-toggle="modal" data-target="#modalTambahAksesori">
                        <i class="fas fa-plus mr-1"></i> Tambah Aksesori
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <x-alert />
                    {{-- Export (parfum only) --}}
                    <div class="btn-group btn-group-sm parfum-only-control">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('products.export.pdf') }}" target="_blank" onclick="loadingExport(this)">
                                <i class="fas fa-file-pdf text-danger mr-2"></i> Export PDF
                            </a>
                            <a class="dropdown-item" href="{{ route('products.export.csv') }}" target="_blank" onclick="loadingExport(this)">
                                <i class="fas fa-file-csv text-success mr-2"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Strip --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-modern h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                             style="width:46px;height:46px;background:rgba(255,107,53,0.1);">
                            <i class="fas fa-box fa-lg" style="color:var(--primary);"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $products->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-modern h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                             style="width:46px;height:46px;background:rgba(255,193,7,0.15);">
                            <i class="fas fa-exclamation-triangle fa-lg text-warning"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Stok Rendah</div>
                            <div class="h5 mb-0 font-weight-bold text-warning">
                                {{ $products->getCollection()->filter(function($p){ $inv=$p->inventories->first(); return $inv && !$p->is_refill && $inv->current_stock > 0 && $inv->current_stock < 10; })->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-modern h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                             style="width:46px;height:46px;background:rgba(52,152,219,0.1);">
                            <i class="fas fa-tags fa-lg text-info"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Kategori</div>
                            <div class="h5 mb-0 font-weight-bold text-info">{{ $categories->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card card-modern h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                             style="width:46px;height:46px;background:rgba(40,199,111,0.1);">
                            <i class="fas fa-warehouse fa-lg text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Nilai Inventory</div>
                            <div class="h5 mb-0 font-weight-bold text-success" style="font-size:0.95rem!important;">
                                Rp {{ number_format($products->getCollection()->sum(function($p){ $inv=$p->inventories->first(); $s=$inv?$inv->current_stock:0; return $s*$p->selling_price; }), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="row mb-0">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productMainTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab !== 'accessories' ? 'active' : '' }}" id="tab-parfum"
                       data-toggle="tab" href="#tabParfum" role="tab" onclick="switchMainTab('parfum')">
                        <i class="fas fa-spray-can mr-1"></i> Produk Parfum
                        <span class="badge badge-secondary ml-1">{{ $products->total() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'accessories' ? 'active' : '' }}" id="tab-accessories"
                       data-toggle="tab" href="#tabAccessories" role="tab" onclick="switchMainTab('accessories')">
                        <i class="fas fa-box-open mr-1"></i> Aksesori & Perlengkapan
                        <span class="badge badge-secondary ml-1">{{ $accessories->total() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
    {{-- ══ TAB 1: PARFUM ══ --}}
    <div class="tab-pane fade {{ $activeTab !== 'accessories' ? 'show active' : '' }}" id="tabParfum">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Daftar Produk</h3>
                </div>
                {{-- Filter Bar --}}
                <div class="card-body border-bottom pb-3">
                    <div class="row">
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Cari Produk</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="searchInput" class="form-control" placeholder="Nama, SKU, barcode..." autofocus>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Kategori</label>
                            <select id="categoryFilter" class="form-control form-control-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Tipe Produk</label>
                            <div class="btn-group btn-group-toggle btn-group-sm w-100" data-toggle="buttons">
                                <label class="btn btn-outline-secondary active" id="typeAll">
                                    <input type="radio" name="typeFilter" value="" checked> Semua
                                </label>
                                <label class="btn btn-outline-secondary" id="typeRegular">
                                    <input type="radio" name="typeFilter" value="regular"> Produk
                                </label>
                                <label class="btn btn-outline-info" id="typeRefill">
                                    <input type="radio" name="typeFilter" value="refill"> Isi Ulang
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                            <label class="small text-muted mb-1">Status Stok</label>
                            <select id="stockFilter" class="form-control form-control-sm">
                                <option value="">Semua Stok</option>
                                <option value="available">Tersedia</option>
                                <option value="low">Stok Rendah</option>
                                <option value="out">Stok Habis</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-1 mb-2 mb-md-0 d-flex align-items-end">
                            <button class="btn btn-secondary btn-sm btn-block" onclick="resetFilters()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                    
                    {{-- Mobile View Toggle --}}
                    <div class="d-sm-none my-2">
                        <div class="btn-group btn-group-toggle btn-group-sm w-100" data-toggle="buttons">
                            <label class="btn btn-outline-secondary active" id="mobileTableViewBtn">
                                <input type="radio" name="productView" value="table" checked onchange="switchProductView('table')">
                                <i class="fas fa-list"></i> Tabel
                            </label>
                            <label class="btn btn-outline-secondary" id="mobileCardViewBtn">
                                <input type="radio" name="productView" value="card" onchange="switchProductView('card')">
                                <i class="fas fa-th-large"></i> Kartu
                            </label>
                        </div>
                    </div>

                    {{-- Products Table --}}
                    <div class="table-responsive p-0" id="productTableView">
                        <table class="table table-hover table-modern mb-0" id="productsTable">
                            <thead>
                                <tr>
                                    <th width="36"><input type="checkbox" id="selectAll"></th>
                                    <th class="d-none d-md-table-cell">Kode</th>
                                    <th>Produk</th>
                                    <th class="d-none d-sm-table-cell">Kategori</th>
                                    <th class="d-none d-lg-table-cell">Sisa</th>
                                    <th class="d-none d-md-table-cell">Tipe</th>
                                    <th>Harga Jual</th>
                                    <th class="d-none d-sm-table-cell">Harga Grosir</th>
                                    <th>Stok</th>
                                    <th class="d-none d-md-table-cell">Terjual</th>
                                    <th class="d-none d-lg-table-cell">Free 20ml</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                @php
                                    $inventory     = $product->inventories->first();
                                    $currentStock  = $inventory ? $inventory->current_stock : 0;
                                    $sales         = $salesData[$product->id] ?? null;
                                    $totalTerjual  = $sales ? (int)$sales->total_qty : 0;
                                    $totalRevenue  = $sales ? (float)$sales->total_revenue : 0;
                                    $totalBonus    = $bonusUsageData[$product->id]->total_free ?? 0;
                                    // Sisa ml = current_stock (sudah dalam ml)
                                    $sisaMl        = $currentStock;
                                    preg_match('/(\d+)/', $product->size ?? '', $sizeMatch);
                                    $mlPerBottle   = isset($sizeMatch[1]) ? (int)$sizeMatch[1] : 30;
                                    $terjualMl     = $totalTerjual * $mlPerBottle;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                    </td>
                                    <td>
                                        <span class="badge badge-light">PRD-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span><br>
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
                                                 class="img-circle img-size-32 mr-2" loading="lazy">
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
                                    <td class="d-none d-lg-table-cell">
                                        @if($product->track_inventory)
                                            <span class="text-{{ $sisaMl > 0 ? 'success' : 'danger' }} font-weight-bold">{{ \App\Helpers\PerformanceHelper::formatMl($sisaMl) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->is_refill)
                                        <span class="badge badge-info"><i class="fas fa-fill-drip mr-1"></i> Isi Ulang</span>
                                        @else
                                        <span class="badge badge-secondary">Reguler</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-success font-weight-bold">
                                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        </div>
                                        @if($product->is_refill && $product->refill_price_per_ml)
                                        <div class="text-info small">
                                            <i class="fas fa-fill-drip"></i> Rp {{ number_format($product->refill_price_per_ml, 0, ',', '.') }}/ml
                                        </div>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @php
                                            // Harga grosir per tier untuk 100ml
                                            $grPremium = 250000;
                                            $grSedang  = 200000;
                                            $grBiasa   = 140000;
                                        @endphp
                                        <div style="font-size:11px;line-height:1.6;">
                                            <span class="badge badge-warning text-dark">Premium</span> Rp {{ number_format($grPremium, 0, ',', '.') }}<br>
                                            <span class="badge badge-info">Sedang</span> Rp {{ number_format($grSedang, 0, ',', '.') }}<br>
                                            <span class="badge badge-secondary">Biasa</span> Rp {{ number_format($grBiasa, 0, ',', '.') }}
                                        </div>
                                        <small class="text-muted" style="font-size:10px;">per 100ml</small>
                                    </td>
                                    <td>
                                        @if(!$product->track_inventory)
                                            <span class="badge badge-secondary">Tanpa Stok</span>
                                        @elseif($product->is_refill)
                                            @if($bulkStock == 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @else
                                                <span class="badge badge-info">{{ \App\Helpers\PerformanceHelper::formatMl($bulkStock) }}</span>
                                            @endif
                                        @else
                                            @if($currentStock == 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @elseif($currentStock < 10)
                                                <span class="badge badge-warning">{{ $currentStock }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $currentStock }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    {{-- Kolom Terjual --}}
                                    <td class="d-none d-md-table-cell">
                                        @if($totalTerjual > 0)
                                            <span class="badge badge-primary">{{ $totalTerjual }} botol</span>
                                            @if($mlPerBottle > 0)
                                                <br><small class="text-muted">{{ \App\Helpers\PerformanceHelper::formatMl($terjualMl) }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    {{-- Kolom Free 20ml --}}
                                    <td class="d-none d-lg-table-cell">
                                        @if($totalBonus > 0)
                                            <span class="badge badge-warning">{{ $totalBonus }}x gratis</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @if($product->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
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

                    <!-- Product Card Grid View (mobile friendly) -->
                    <div class="row" id="productCardView" style="display: none;">
                        @foreach($products as $product)
                        @php
                            $inventory = $product->inventories->first();
                            $currentStock = $inventory ? $inventory->current_stock : 0;
                            $catColor = $product->category ? (preg_match('/^#[0-9a-fA-F]{6}$/', $product->category->color) ? $product->category->color : '#FF6B35') : '#FF6B35';
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3 mb-2 product-card-item" 
                             data-name="{{ strtolower($product->name) }}"
                             data-category="{{ $product->category?->name ?? '' }}"
                             data-type="{{ $product->is_refill ? 'refill' : 'regular' }}"
                             data-stock="{{ $currentStock }}">
                            <div class="card card-apms h-100">
                                <div class="card-body p-2">
                                    <div class="text-center mb-2">
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded" style="width:60px;height:60px;object-fit:cover;" loading="lazy">
                                        @else
                                        <div class="bg-light rounded d-inline-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                                            <i class="fas fa-wine-bottle fa-2x text-muted"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <h6 class="mb-1 text-center" style="font-size:0.78rem;min-height:2.2rem;">{{ $product->name }}</h6>
                                    <div class="text-center mb-1">
                                        <span class="badge" style="background-color:{{ $catColor }};color:white;font-size:0.6rem;">
                                            {{ $product->category?->name ?? '-' }}
                                        </span>
                                        @if($product->is_refill)
                                        <span class="badge badge-info" style="font-size:0.6rem;">Isi Ulang</span>
                                        @endif
                                    </div>
                                    <div class="text-center font-weight-bold text-success" style="font-size:0.8rem;">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </div>
                                    <div class="text-center mt-1">
                                        @if($product->is_refill)
                                            @if($bulkStock <= 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @else
                                                <span class="badge badge-info">{{ \App\Helpers\PerformanceHelper::formatMl($bulkStock) }}</span>
                                            @endif
                                        @else
                                            @if($currentStock == 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @elseif($currentStock < 10)
                                                <span class="badge badge-warning">{{ $currentStock }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $currentStock }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="btn-group btn-group-sm w-100 mt-2">
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduct(@json($product->id))">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>{{-- end #productCardView --}}

                </div>{{-- end card-body --}}
            </div>{{-- end card --}}

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $products->appends(request()->query())->links() }}
            </div>

        </div>{{-- end col-12 --}}
    </div>{{-- end row --}}
    </div>{{-- end #tabParfum --}}

    {{-- ══ TAB 2: AKSESORI ══ --}}
    <div class="tab-pane fade {{ $activeTab === 'accessories' ? 'show active' : '' }}" id="tabAccessories">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0"><i class="fas fa-box-open mr-2"></i>Daftar Aksesori & Perlengkapan</h3>
                </div>
                {{-- Filter Bar Aksesori --}}
                <div class="card-body border-bottom pb-3">
                    <form method="GET" action="{{ route('products.index') }}" id="accFilterForm">
                        <input type="hidden" name="tab" value="accessories">
                        <div class="row">
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Cari Aksesori</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="acc_search" class="form-control"
                                           placeholder="Nama, SKU..." value="{{ request('acc_search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2 mb-md-0">
                                <label class="small text-muted mb-1">Kategori</label>
                                <select name="acc_category" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach($accessoryCategories as $key => $label)
                                    <option value="{{ $key }}" {{ request('acc_category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-2 mb-md-0 d-flex align-items-end">
                                <a href="{{ route('products.index', ['tab' => 'accessories']) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- Tabel Aksesori --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:50px">#</th>
                                    <th>Nama Aksesori</th>
                                    <th>Kategori</th>
                                    <th>SKU</th>
                                    <th>Satuan</th>
                                    <th class="text-right">Harga Jual</th>
                                    <th class="text-right">Harga Grosir</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accessories as $acc)
                                <tr>
                                    <td class="text-muted small">{{ $loop->iteration + ($accessories->currentPage()-1)*$accessories->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($acc->image)
                                            <img src="{{ asset('storage/'.$acc->image) }}" alt="{{ $acc->name }}"
                                                 class="img-circle img-size-32 mr-2" loading="lazy">
                                            @else
                                            <div class="img-circle img-size-32 bg-light d-flex align-items-center justify-content-center mr-2">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <strong>{{ $acc->name }}</strong>
                                                @if($acc->brand)<br><small class="text-muted">{{ $acc->brand }}</small>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $accessoryCategories[$acc->category] ?? $acc->category }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $acc->sku ?? '-' }}</small></td>
                                    <td>{{ $acc->unit }}</td>
                                    <td class="text-right">Rp {{ number_format($acc->selling_price, 0, ',', '.') }}</td>
                                    <td class="text-right">
                                        @if($acc->wholesale_price)
                                            Rp {{ number_format($acc->wholesale_price, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($acc->current_stock <= 0)
                                            <span class="badge badge-danger">Habis</span>
                                        @elseif($acc->current_stock <= $acc->minimum_stock)
                                            <span class="badge badge-warning">{{ $acc->current_stock }} {{ $acc->unit }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $acc->current_stock }} {{ $acc->unit }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($acc->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    onclick="editAksesori(this)"
                                                    data-acc="{{ e(json_encode($acc)) }}"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteAksesori({{ $acc->id }}, '{{ addslashes($acc->name) }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                        Belum ada aksesori. Klik "Tambah Aksesori" untuk menambahkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination Aksesori --}}
                @if($accessories->hasPages())
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <small class="text-muted">
                                Menampilkan {{ $accessories->firstItem() ?? 0 }} s/d {{ $accessories->lastItem() ?? 0 }}
                                dari {{ $accessories->total() }} aksesori
                            </small>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-center justify-content-md-end">
                                {{ $accessories->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>{{-- end #tabAccessories --}}
    </div>{{-- end .tab-content --}}

<!-- Delete Parfum Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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

<!-- Modal Tambah / Edit Aksesori -->
<div class="modal fade" id="modalTambahAksesori" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formAksesori" method="POST" enctype="multipart/form-data">
                @csrf
                <span id="aksesoriMethodField"></span>
                <div class="modal-header" style="background:#2D3047;color:#fff;">
                    <h5 class="modal-title" id="modalAksesoriTitle"><i class="fas fa-plus mr-2"></i>Tambah Aksesori</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Aksesori <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required maxlength="255" id="acc_name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" name="sku" class="form-control" maxlength="100" id="acc_sku">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Barcode</label>
                                <input type="text" name="barcode" class="form-control" maxlength="100" id="acc_barcode">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-control" required id="acc_category">
                                    @foreach($accessoryCategories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Brand / Merk</label>
                                <input type="text" name="brand" class="form-control" maxlength="100" id="acc_brand">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Satuan <span class="text-danger">*</span></label>
                                <select name="unit" class="form-control" required id="acc_unit">
                                    <option value="pcs">pcs</option>
                                    <option value="set">set</option>
                                    <option value="buah">buah</option>
                                    <option value="lusin">lusin</option>
                                    <option value="kodi">kodi</option>
                                    <option value="pak">pak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Harga Beli (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="purchase_price" class="form-control" required min="0" step="100" id="acc_purchase_price">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="selling_price" class="form-control" required min="0" step="100" id="acc_selling_price">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Harga Grosir (Rp)</label>
                                <input type="number" name="wholesale_price" class="form-control" min="0" step="100" id="acc_wholesale_price">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="current_stock" class="form-control" required min="0" id="acc_current_stock">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" name="minimum_stock" class="form-control" required min="0" value="5" id="acc_minimum_stock">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supplier</label>
                                {{-- Mode: pilih existing atau ketik baru --}}
                                <div id="supplierPickerWrap">
                                    <div class="input-group">
                                        <input type="text" id="acc_supplier_input" class="form-control"
                                               placeholder="Ketik nama supplier..."
                                               autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="saveNewSupplierInline()"
                                                    title="Simpan supplier baru">
                                                <i class="fas fa-plus"></i> Baru
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Dropdown saran --}}
                                    <div id="supplierSuggestions" class="list-group" style="position:absolute;z-index:9999;width:100%;display:none;max-height:180px;overflow-y:auto;"></div>
                                    {{-- Hidden: menyimpan supplier_id yang terpilih --}}
                                    <input type="hidden" name="supplier_id" id="acc_supplier_id">
                                    <small id="supplierSelectedLabel" class="text-success" style="display:none;">
                                        <i class="fas fa-check-circle"></i> <span></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control" rows="2" maxlength="1000" id="acc_description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Foto Aksesori</label>
                                <input type="file" name="image" class="form-control-file" accept="image/*" id="acc_image">
                                <small class="text-muted">Maks. 2MB</small>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="acc_is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="acc_is_active">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanAksesori">
                        <i class="fas fa-save mr-1"></i> Simpan Aksesori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Aksesori -->
<div class="modal fade" id="modalHapusAksesori" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus Aksesori</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Hapus aksesori <strong id="namaAksesoriHapus"></strong>?</p>
                <p class="text-danger small">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="formHapusAksesori" method="POST" style="display:inline;">
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
/* ── Stats cards ── */
.card-modern {
    border: none;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    border-radius: 10px;
    transition: box-shadow 0.2s, transform 0.2s;
}
.card-modern:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

/* ── Table ── */
.table-modern thead th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.82rem;
    padding: 10px 12px;
    white-space: nowrap;
    vertical-align: middle;
}
.table-modern tbody tr {
    transition: background 0.15s;
}
.table-modern tbody tr:hover {
    background: #fff8f5;
}
.table-modern td {
    vertical-align: middle;
    padding: 9px 12px;
    font-size: 0.85rem;
}

/* ── Product image avatar ── */
.prod-avatar {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}
.prod-avatar-placeholder {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* ── Stock badges ── */
.badge-stock-ok      { background: #e8f8f0; color: #1a7a4a; border: 1px solid #b7ebd4; }
.badge-stock-low     { background: #fff8e1; color: #8a6200; border: 1px solid #ffe082; }
.badge-stock-out     { background: #ffeaea; color: #c0392b; border: 1px solid #ffb3b3; }
.badge-stock-ok, .badge-stock-low, .badge-stock-out {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

/* ── Product card grid ── */
.product-card-item .card {
    border: 1px solid #eee;
    border-radius: 10px;
    transition: box-shadow 0.2s, transform 0.2s;
}
.product-card-item .card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}
.product-card-item .prod-card-img {
    height: 130px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    width: 100%;
}
.product-card-item .prod-card-placeholder {
    height: 130px;
    background: linear-gradient(135deg, #f5f5f5, #ebebeb);
    border-radius: 10px 10px 0 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ── Filter bar ── */
.card-body.border-bottom {
    background: #fafafa;
}

@media (max-width: 767.98px) {
    .table-modern thead th { font-size: 0.65rem; padding: 8px; }
    .table-modern td { font-size: 0.8rem; padding: 8px; }
    .table-modern .btn-group .btn { padding: 3px 7px; font-size: 0.7rem; }
    .product-card-item .card-body { padding: 8px !important; }
    .product-card-item h6 { font-size: 0.75rem !important; }
}
</style>
@endpush

@push('scripts')
<script>
let selectedProducts = [];
let currentView = window.innerWidth < 768 ? 'card' : 'table';

function switchProductView(view) {
    currentView = view;
    if (view === 'card') {
        $('#productTableView').hide();
        $('#productCardView').show();
        $('#tableViewBtn').removeClass('active');
        $('#cardViewBtn').addClass('active');
        $('#mobileTableViewBtn').removeClass('active');
        $('#mobileCardViewBtn').addClass('active');
    } else {
        $('#productTableView').show();
        $('#productCardView').hide();
        $('#cardViewBtn').removeClass('active');
        $('#tableViewBtn').addClass('active');
        $('#mobileCardViewBtn').removeClass('active');
        $('#mobileTableViewBtn').addClass('active');
    }
}

$(window).on('resize', function() {
    if (window.innerWidth < 768 && currentView === 'table') {
        switchProductView('card');
    } else if (window.innerWidth >= 768 && currentView === 'card') {
        switchProductView('table');
    }
});

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
                    url: @json(route('products.bulk-delete')),
                    method: 'POST',
                    data: { ids: ids, _token: '{{ csrf_token() }}' },
                    success: function() {
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({icon:'error', title:'Gagal!', text: xhr.responseJSON?.message || 'Gagal menghapus produk.'});
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
            window.open(@json(route('products.export.csv')) + '?ids=' + ids.join(','), '_blank');
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

function filterProducts() {
    const searchVal = $('#searchInput').val().toLowerCase();
    const catVal = $('#categoryFilter').val().toLowerCase();
    const typeVal = $('input[name="typeFilter"]:checked').val();
    const stockVal = $('#stockFilter').val();

    // Filter table rows
    $('#productsTable tbody tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const category = row.find('td:eq(3)').text().toLowerCase().trim();
        const type = row.find('td:eq(5)').text().toLowerCase().trim();
        const badge = row.find('td:eq(7)').find('.badge');

        let show = true;
        if (searchVal && text.indexOf(searchVal) === -1) show = false;
        if (catVal && category.indexOf(catVal) === -1) show = false;
        if (typeVal === 'refill' && type.indexOf('isi ulang') === -1) show = false;
        if (typeVal === 'regular' && type.indexOf('reguler') === -1) show = false;
        if (stockVal === 'available') show = show && (badge.hasClass('badge-success') || badge.hasClass('badge-info'));
        if (stockVal === 'low') show = show && badge.hasClass('badge-warning');
        if (stockVal === 'out') show = show && badge.hasClass('badge-danger');
        row.toggle(show);
    });

    // Filter card items using same logic with data attributes
    $('.product-card-item').each(function() {
        const card = $(this);
        const name = card.data('name');
        const category = card.data('category').toLowerCase();
        const type = card.data('type');
        const stock = parseInt(card.data('stock'));

        let show = true;
        if (searchVal && name.indexOf(searchVal) === -1) show = false;
        if (catVal && category.indexOf(catVal) === -1) show = false;
        if (typeVal === 'refill' && type !== 'refill') show = false;
        if (typeVal === 'regular' && type !== 'regular') show = false;
        if (stockVal === 'available') show = show && stock > 0;
        if (stockVal === 'low') show = show && stock > 0 && stock < 10;
        if (stockVal === 'out') show = show && stock <= 0;
        card.toggle(show);
    });
}

$(function() {
    // Set default view based on screen size
    if (window.innerWidth < 768) {
        switchProductView('card');
    }

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
    $('#searchInput').on('keyup', filterProducts);
    
    // Category filter
    $('#categoryFilter').change(filterProducts);
    
    // Type filter buttons (Produk / Isi Ulang)
    $('input[name="typeFilter"]').change(filterProducts);
    
    // Stock filter
    $('#stockFilter').change(filterProducts);
});

function updateSelectedCount() {
    const count = $('.product-checkbox:checked').length;
    $('#selectedCount').text(`${count} produk terpilih`);
}

// ── Tab switching: Parfum ↔ Aksesori ──
function switchMainTab(tab) {
    if (tab === 'accessories') {
        $('.parfum-only-control').hide();
        $('.acc-only-control').show();
    } else {
        $('.parfum-only-control').show();
        $('.acc-only-control').hide();
    }
    // Update URL param tanpa reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
}

// Init tab on load
$(function() {
    const initTab = @json($activeTab);
    if (initTab === 'accessories') {
        switchMainTab('accessories');
        $('#tab-accessories').tab('show');
    }
});

// ── Aksesori CRUD ──
const accStoreUrl   = @json(route('accessories.store'));
const accUpdateBase = '{{ url("/accessories") }}';
const supplierSearchUrl   = @json(route('suppliers.search'));
const supplierQuickStore  = @json(route('suppliers.quick-store'));

// ── Supplier Autocomplete ──
let supplierSearchTimer = null;

$(document).on('input', '#acc_supplier_input', function () {
    const q = $(this).val().trim();
    clearTimeout(supplierSearchTimer);
    $('#acc_supplier_id').val('');
    $('#supplierSelectedLabel').hide();

    if (q.length < 1) { $('#supplierSuggestions').hide().empty(); return; }

    supplierSearchTimer = setTimeout(function () {
        $.getJSON(supplierSearchUrl, { q: q }, function (data) {
            const box = $('#supplierSuggestions').empty();
            if (data.length === 0) {
                box.append('<div class="list-group-item list-group-item-action text-muted small">Tidak ditemukan — klik "+ Baru" untuk tambah</div>');
            } else {
                data.forEach(function (s) {
                    box.append(
                        $('<button type="button" class="list-group-item list-group-item-action py-1 small">')
                            .text(s.name)
                            .on('click', function () {
                                $('#acc_supplier_id').val(s.id);
                                $('#acc_supplier_input').val(s.name);
                                $('#supplierSelectedLabel span').text(s.name);
                                $('#supplierSelectedLabel').show();
                                box.hide().empty();
                            })
                    );
                });
            }
            box.show();
        });
    }, 300);
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('#supplierPickerWrap').length) {
        $('#supplierSuggestions').hide().empty();
    }
});

function saveNewSupplierInline() {
    const name = $('#acc_supplier_input').val().trim();
    if (!name) { alert('Masukkan nama supplier terlebih dahulu.'); return; }

    $.ajax({
        url: supplierQuickStore,
        method: 'POST',
        data: { name: name, _token: '{{ csrf_token() }}' },
        success: function (res) {
            $('#acc_supplier_id').val(res.id);
            $('#acc_supplier_input').val(res.name);
            $('#supplierSelectedLabel span').text(res.name);
            $('#supplierSelectedLabel').show();
            $('#supplierSuggestions').hide().empty();
            toastr.success('Supplier "' + res.name + '" berhasil disimpan.');
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.errors?.name?.[0] || xhr.responseJSON?.message || 'Gagal menyimpan supplier.';
            toastr.error(msg);
        }
    });
}

// Reset supplier field saat modal ditutup
$('#modalTambahAksesori').on('hidden.bs.modal', function () {
    $('#acc_supplier_input').val('');
    $('#acc_supplier_id').val('');
    $('#supplierSelectedLabel').hide();
    $('#supplierSuggestions').hide().empty();
});

function editAksesori(btn) {
    const data = JSON.parse(btn.getAttribute('data-acc'));
    const id   = data.id;
    $('#modalAksesoriTitle').html('<i class="fas fa-edit mr-2"></i>Edit Aksesori');
    $('#formAksesori').attr('action', accUpdateBase + '/' + id);
    $('#aksesoriMethodField').html('<input type="hidden" name="_method" value="PUT">');

    $('#acc_name').val(data.name);
    $('#acc_sku').val(data.sku || '');
    $('#acc_barcode').val(data.barcode || '');
    $('#acc_category').val(data.category);
    $('#acc_brand').val(data.brand || '');
    $('#acc_purchase_price').val(data.purchase_price);
    $('#acc_selling_price').val(data.selling_price);
    $('#acc_wholesale_price').val(data.wholesale_price || '');
    $('#acc_current_stock').val(data.current_stock);
    $('#acc_minimum_stock').val(data.minimum_stock);
    $('#acc_unit').val(data.unit);
    $('#acc_supplier_id').val(data.supplier_id || '');
    // Set supplier input text
    if (data.supplier_id && data.supplier) {
        $('#acc_supplier_input').val(data.supplier.name || '');
        $('#supplierSelectedLabel span').text(data.supplier.name || '');
        $('#supplierSelectedLabel').show();
    } else {
        $('#acc_supplier_input').val('');
        $('#supplierSelectedLabel').hide();
    }
    $('#acc_description').val(data.description || '');
    $('#acc_is_active').prop('checked', data.is_active == 1 || data.is_active === true);

    $('#modalTambahAksesori').modal('show');
}

// Reset form saat modal tambah dibuka
$('#modalTambahAksesori').on('show.bs.modal', function(e) {
    // Hanya reset jika bukan dari editAksesori
    if (!e.relatedTarget && $('#aksesoriMethodField').html() !== '') return;
    if (e.relatedTarget) {
        // Dibuka via data-target (tombol Tambah Aksesori)
        $('#modalAksesoriTitle').html('<i class="fas fa-plus mr-2"></i>Tambah Aksesori');
        $('#formAksesori').attr('action', accStoreUrl);
        $('#aksesoriMethodField').html('');
        $('#formAksesori')[0].reset();
        $('#acc_minimum_stock').val(5);
        $('#acc_is_active').prop('checked', true);
    }
});

// Submit aksesori via AJAX
$('#formAksesori').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const formData = new FormData(this);
    const btn = $('#btnSimpanAksesori');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        success: function() {
            $('#modalTambahAksesori').modal('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Aksesori berhasil disimpan.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1600);
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Aksesori');
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                const msg = Object.values(errors).flat().join('\n');
                Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: msg });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message || 'Terjadi kesalahan.' });
            }
        }
    });
});

function deleteAksesori(id, nama) {
    $('#namaAksesoriHapus').text(nama);
    $('#formHapusAksesori').attr('action', accUpdateBase + '/' + id);
    $('#modalHapusAksesori').modal('show');
}
</script>
@endpush