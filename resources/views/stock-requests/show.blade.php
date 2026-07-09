@extends('layouts.app')
@section('title', 'Detail Permintaan Stok')
@section('content')
<div class="container-fluid pt-3">
    @include('stock-requests._nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h4 class="font-weight-bold mb-0">{{ $stockRequest->request_number }}</h4>
                    @include('stock-requests._status')
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4"><small class="text-muted d-block">Cabang</small><strong>{{ $stockRequest->branch->name ?? '-' }}</strong></div>
                        <div class="col-md-4"><small class="text-muted d-block">Pemohon</small><strong>{{ $stockRequest->requester->name ?? '-' }}</strong></div>
                        <div class="col-md-4"><small class="text-muted d-block">Tanggal Diajukan</small><strong>{{ $stockRequest->created_at->format('d/m/Y H:i') }}</strong></div>
                        @if($stockRequest->approved_by)
                        <div class="col-md-4 mt-2"><small class="text-muted d-block">Disetujui Oleh</small><strong>{{ $stockRequest->approver->name ?? '-' }}</strong></div>
                        @endif
                        @if($stockRequest->received_date)
                        <div class="col-md-4 mt-2"><small class="text-muted d-block">Tanggal Diterima</small><strong>{{ \Carbon\Carbon::parse($stockRequest->received_date)->format('d/m/Y') }}</strong></div>
                        @endif
                    </div>

                    @if($stockRequest->notes)
                    <div class="alert alert-light border mb-4">
                        <small class="text-muted d-block">Catatan</small>
                        <p class="mb-0">{{ $stockRequest->notes }}</p>
                    </div>
                    @endif

                    <hr>
                    <h5 class="font-weight-bold"><i class="fas fa-boxes mr-2"></i>Daftar Produk</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Diminta</th>
                                    @if($stockRequest->status !== 'pending')
                                    <th class="text-center">Disiapkan</th>
                                    @endif
                                    @if(in_array($stockRequest->status, ['received']))
                                    <th class="text-center">Diterima</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockRequest->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity_requested }}</td>
                                    @if($stockRequest->status !== 'pending')
                                    <td class="text-center">{{ $item->quantity_prepared ?: 0 }}</td>
                                    @endif
                                    @if(in_array($stockRequest->status, ['received']))
                                    <td class="text-center">{{ $item->quantity_received ?: 0 }}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($stockRequest->delivery_method || $stockRequest->receipt_notes)
                    <hr>
                    <div class="row">
                        @if($stockRequest->delivery_method)
                        <div class="col-md-4"><small class="text-muted d-block">Metode Pengiriman</small><strong>{{ $stockRequest->delivery_method }}</strong></div>
                        @endif
                        @if($stockRequest->delivery_date)
                        <div class="col-md-4"><small class="text-muted d-block">Tanggal Kirim</small><strong>{{ $stockRequest->delivery_date->format('d/m/Y') }}</strong></div>
                        @endif
                        @if($stockRequest->receipt_notes)
                        <div class="col-md-4"><small class="text-muted d-block">Catatan Pengiriman</small><strong>{{ $stockRequest->receipt_notes }}</strong></div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="{{ route('stock-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-apms shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="font-weight-bold mb-0"><i class="fas fa-tasks mr-2"></i>Aksi</h5>
                </div>
                <div class="card-body">
                    @php $u = auth()->user(); $isBranch = $stockRequest->branch_id === $u->branch_id; @endphp

                    {{-- PENDING: Approve (pusat) or Cancel --}}
                    @if($stockRequest->status === 'pending')
                        @can('stock_requests.approve')
                            @if($u->isAdminPusat() || $u->isOwner())
                            <form method="POST" action="{{ route('stock-requests.approve', $stockRequest) }}" class="mb-2">
                                @csrf @method('PATCH')
                                <div class="form-group"><textarea name="notes" class="form-control form-control-sm" placeholder="Catatan persetujuan..." rows="2"></textarea></div>
                                <button type="submit" class="btn btn-success btn-block" onclick="disableBtn(this,'Menyetujui...')"><i class="fas fa-check mr-1"></i>Setujui</button>
                            </form>
                            @endif
                        @endcan
                        @can('stock_requests.create')
                            @if($isBranch || $u->isOwner())
                            <form method="POST" action="{{ route('stock-requests.cancel', $stockRequest) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-block" onclick="return confirm('Batalkan permintaan?')"><i class="fas fa-times mr-1"></i>Batalkan</button>
                            </form>
                            @endif
                        @endcan
                    @endif

                    {{-- APPROVED: Prepare / Set Delivery --}}
                    @if($stockRequest->status === 'approved')
                        @can('stock_requests.approve')
                            @if($u->isAdminPusat() || $u->isOwner())
                            <form method="POST" action="{{ route('stock-requests.prepare', $stockRequest) }}">
                                @csrf @method('PATCH')
                                <h6 class="font-weight-bold">Siapkan Pengiriman</h6>
                                @foreach($stockRequest->items as $item)
                                <div class="form-group row align-items-center mb-1">
                                    <label class="col-6 col-form-label col-form-label-sm">{{ $item->product->name }}</label>
                                    <div class="col-6">
                                        <input type="number" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" hidden>
                                        <input type="number" name="items[{{ $loop->index }}][quantity_prepared]" class="form-control form-control-sm" value="{{ $item->quantity_requested }}" min="0" max="{{ $item->quantity_requested }}">
                                    </div>
                                </div>
                                @endforeach
                                <div class="form-group">
                                    <label class="small font-weight-bold">Metode Pengiriman</label>
                                    <input type="text" name="delivery_method" class="form-control form-control-sm" placeholder="Kurir internal / ekspedisi" required>
                                </div>
                                <div class="form-group">
                                    <label class="small font-weight-bold">Tanggal Kirim</label>
                                    <input type="date" name="delivery_date" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group">
                                    <textarea name="receipt_notes" class="form-control form-control-sm" placeholder="Catatan pengiriman..." rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary-apms btn-block" onclick="disableBtn(this,'Menyiapkan...')"><i class="fas fa-boxes mr-1"></i>Siapkan & Kirim</button>
                            </form>
                            @endif
                        @endcan
                    @endif

                    {{-- PREPARING: Ship (pusat) --}}
                    @if($stockRequest->status === 'preparing')
                        @can('stock_requests.approve')
                            @if($u->isAdminPusat() || $u->isOwner())
                            <form method="POST" action="{{ route('stock-requests.ship', $stockRequest) }}" class="mb-2">
                                @csrf @method('PATCH')
                                <p class="text-muted small">Stok akan dikurangi dari gudang pusat dan barang ditandai sudah dikirim.</p>
                                <button type="submit" class="btn btn-warning btn-block" onclick="disableBtn(this,'Mengirim...')"><i class="fas fa-truck mr-1"></i>Konfirmasi Dikirim</button>
                            </form>
                            @endif
                        @endcan
                    @endif

                    {{-- SHIPPED: Receive (branch) --}}
                    @if($stockRequest->status === 'shipped')
                        @can('stock_requests.receive')
                            @if($isBranch || $u->isAdminPusat() || $u->isOwner())
                            <form method="POST" action="{{ route('stock-requests.receive', $stockRequest) }}">
                                @csrf @method('PATCH')
                                <h6 class="font-weight-bold">Terima Barang</h6>
                                @foreach($stockRequest->items as $item)
                                <div class="form-group row align-items-center mb-1">
                                    <label class="col-6 col-form-label col-form-label-sm">{{ $item->product->name }}</label>
                                    <div class="col-6">
                                        <input type="number" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" hidden>
                                        <input type="number" name="items[{{ $loop->index }}][quantity_received]" class="form-control form-control-sm" value="{{ $item->quantity_prepared }}" min="0">
                                    </div>
                                </div>
                                @endforeach
                                <div class="form-group">
                                    <label class="small font-weight-bold">Tanggal Diterima</label>
                                    <input type="date" name="received_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-block" onclick="disableBtn(this,'Menerima...')"><i class="fas fa-check-circle mr-1"></i>Konfirmasi Diterima</button>
                            </form>
                            @endif
                        @endcan
                    @endif

                    @if(in_array($stockRequest->status, ['received', 'cancelled']))
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-check-circle fa-2x d-block mb-1"></i>
                            <span>Proses selesai</span>
                        </div>
                    @endif
                </div>
            </div>

            @can('stock_requests.approve')
            @if($stockRequest->status === 'pending' && ($u->isAdminPusat() || $u->isOwner()))
            <div class="card card-apms shadow-sm border-0 mt-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="font-weight-bold mb-0"><i class="fas fa-times-circle mr-2 text-danger"></i>Tolak</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stock-requests.cancel', $stockRequest) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Tolak permintaan ini?')"><i class="fas fa-ban mr-1"></i>Tolak Permintaan</button>
                    </form>
                </div>
            </div>
            @endif
            @endcan
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function disableBtn(btn, t) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + t; }
</script>
@endpush
