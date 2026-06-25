@extends('layouts.app')
@section('title', 'Update Harga Massal')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-tags"></i> Update Harga Massal</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
                    <li class="breadcrumb-item active">Bulk Price</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <form method="POST" action="{{ route('bulk-price.update') }}">
        @csrf
        <div class="row">
            {{-- Settings Panel --}}
            <div class="col-lg-4">
                <div class="card card-apms mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sliders-h mr-2"></i>Pengaturan Update</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Jenis Harga</label>
                            <select name="price_type" id="priceType" class="form-control">
                                <option value="selling_price">Harga Jual</option>
                                <option value="wholesale_price">Harga Grosir</option>
                                <option value="purchase_price">Harga Beli</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mode Perubahan</label>
                            <select name="change_mode" id="changeMode" class="form-control">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Harga Tetap (Rp)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nilai</label>
                            <div class="input-group">
                                <input type="number" name="change_value" id="changeValue" class="form-control" step="0.01" value="0">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="unitLabel">%</span>
                                </div>
                            </div>
                            <small class="text-muted">Negatif untuk menurunkan harga</small>
                        </div>
                        <hr>
                        <div>
                            <p class="font-weight-bold">Terpilih: <span id="selectedCount">0</span> produk</p>
                            <button type="submit" class="btn btn-primary-apms btn-block"
                                onclick="return confirm('Terapkan perubahan harga ke semua produk terpilih?')">
                                <i class="fas fa-save mr-1"></i> Terapkan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Selection --}}
            <div class="col-lg-8">
                <div class="card card-apms mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Pilih Produk</h3>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllBtn">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">Batal Semua</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light" style="position:sticky;top:0;z-index:1">
                                    <tr>
                                        <th style="width:30px"></th>
                                        <th>Produk</th>
                                        <th class="text-right">Harga Jual</th>
                                        <th class="text-right">Harga Grosir</th>
                                        <th class="text-right">Harga Beli</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $p)
                                    <tr>
                                        <td><input type="checkbox" name="product_ids[]" value="{{ $p->id }}" class="product-check"></td>
                                        <td>{{ $p->name }} <small class="text-muted">{{ $p->size ?? '' }} {{ $p->unit ?? '' }}</small></td>
                                        <td class="text-right">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($p->wholesale_price, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Price History --}}
    <div class="card card-apms">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-history mr-2"></i>Riwayat Perubahan Harga</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jenis</th>
                            <th class="text-right">Harga Lama</th>
                            <th class="text-right">Harga Baru</th>
                            <th>Mode</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $h)
                        <tr>
                            <td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $h->product->name ?? '-' }}</td>
                            <td>{{ $h->price_type }}</td>
                            <td class="text-right">Rp {{ number_format($h->old_price, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($h->new_price, 0, ',', '.') }}</td>
                            <td>{{ $h->change_type }}</td>
                            <td>{{ $h->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-2">{{ $history->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    function updateCount() {
        $('#selectedCount').text($('.product-check:checked').length);
    }

    $('.product-check').on('change', updateCount);
    $('#selectAllBtn').on('click', function() { $('.product-check').prop('checked', true); updateCount(); });
    $('#deselectAllBtn').on('click', function() { $('.product-check').prop('checked', false); updateCount(); });

    $('#changeMode').on('change', function() {
        $('#unitLabel').text($(this).val() === 'percentage' ? '%' : 'Rp');
    });
});
</script>
@endpush
