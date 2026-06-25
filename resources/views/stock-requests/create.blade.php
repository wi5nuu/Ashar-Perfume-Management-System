@extends('layouts.app')
@section('title', 'Ajukan Permintaan Stok')
@section('content')
<div class="container-fluid pt-3">
    @include('stock-requests._nav')

    <div class="card card-apms shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h4 class="font-weight-bold mb-0"><i class="fas fa-plus-circle text-primary mr-2"></i>Ajukan Permintaan Stok Baru</h4>
            <small class="text-muted">Pilih produk dan jumlah stok yang dibutuhkan untuk cabang</small>
        </div>
        <form method="POST" action="{{ route('stock-requests.store') }}">
            @csrf
            <div class="card-body">
                <div class="row">
                    @can('stock_requests.approve')
                    <div class="col-md-4 form-group">
                        <label class="font-weight-medium">Cabang Tujuan <span class="text-danger">*</span></label>
                        <select name="branch_id" class="form-control" required>
                            <option value="">— Pilih Cabang —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    @endif
                </div>

                <div class="form-group">
                    <label class="font-weight-medium">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <hr>
                <h5 class="font-weight-bold"><i class="fas fa-shopping-cart mr-2"></i>Daftar Produk</h5>
                <p class="text-muted small">Cari produk, tentukan jumlah, lalu klik "Tambah"</p>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <select id="productSelect" class="form-control select2" style="width:100%">
                            <option value="">— Cari Produk —</option>
                            @foreach($products as $p)
                                @php $pStock = $p->track_inventory ? $p->inventories->sum('current_stock') : 0; @endphp
                                <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-stock="{{ $pStock }}">
                                    {{ $p->name }} @if(!$p->track_inventory)(Tanpa Stok) @else ({{ $pStock }} stok) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" id="qtyInput" class="form-control" placeholder="Jumlah" min="1">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary-apms btn-block" onclick="addItem()"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    </div>
                </div>

                <table class="table table-sm table-bordered" id="itemsTable">
                    <thead class="thead-light">
                        <tr><th style="width:60%">Produk</th><th style="width:20%">Jumlah</th><th style="width:20%">Aksi</th></tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                </table>
                <small class="text-muted" id="noItemsMsg">Belum ada produk. Cari dan tambahkan produk di atas.</small>
            </div>
            <div class="card-footer bg-white border-top d-flex justify-content-between py-3">
                <a href="{{ route('stock-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
                <button type="submit" class="btn btn-primary-apms px-4" id="submitBtn" disabled onclick="disableBtn(this,'Mengirim...')"><i class="fas fa-paper-plane mr-1"></i>Ajukan Permintaan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
var items = [];

function addItem() {
    var sel = document.getElementById('productSelect');
    var qty = parseInt(document.getElementById('qtyInput').value);
    if (!sel.value || !qty || qty < 1) { Swal.fire('Periksa Input', 'Pilih produk dan masukkan jumlah minimal 1', 'warning'); return; }
    var id = sel.value, name = sel.options[sel.selectedIndex].getAttribute('data-name');
    if (items.find(function(i){return i.product_id===id})) { Swal.fire('Sudah Ada', 'Produk sudah ditambahkan', 'warning'); return; }
    items.push({product_id:id, name:name, quantity:qty});
    renderItems();
    sel.value = ''; document.getElementById('qtyInput').value = '';
    $('#productSelect').trigger('change');
}

function removeItem(id) {
    items = items.filter(function(i){return i.product_id!==id});
    renderItems();
}

function renderItems() {
    var tbody = document.getElementById('itemsBody');
    var msg = document.getElementById('noItemsMsg');
    tbody.innerHTML = '';
    items.forEach(function(i) {
        tbody.innerHTML += '<tr><td>' + i.name + '<input type="hidden" name="items[' + i.product_id + '][product_id]" value="' + i.product_id + '"></td><td><input type="number" name="items[' + i.product_id + '][quantity]" value="' + i.quantity + '" class="form-control form-control-sm" min="1" onchange="updateQty(\'' + i.product_id + '\',this.value)"></td><td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(\'' + i.product_id + '\')"><i class="fas fa-trash"></i></button></td></tr>';
    });
    msg.style.display = items.length ? 'none' : 'block';
    document.getElementById('submitBtn').disabled = items.length === 0;
}

function updateQty(id, val) {
    var item = items.find(function(i){return i.product_id===id});
    if (item) item.quantity = parseInt(val) || 0;
}

function disableBtn(btn, t) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + t; }
</script>
@endpush
