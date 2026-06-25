@extends('layouts.app')

@section('title', 'Manajemen Grosir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center flex-wrap">
                    <h3 class="card-title font-weight-bold text-primary-apms mb-0">
                        <i class="fas fa-boxes-packing mr-2"></i> Daftar Pesanan Grosir
                    </h3>
                    <div class="ml-auto d-flex">
                        <a href="{{ route('wholesale.products.index') }}" class="btn btn-outline-info mr-2">
                            <i class="fas fa-box mr-1"></i> Produk Grosir
                        </a>
                        <a href="{{ route('wholesale.create') }}" class="btn btn-primary-apms">
                            <i class="fas fa-plus-circle mr-1"></i> Buat Pesanan Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('wholesale.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari Invoice/Penerima/No HP..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    @foreach($statuses as $s)
                                        @php $label = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$s] ?? ucfirst($s); @endphp
                                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari tanggal" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Sampai tanggal" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-1">
                                @if(request()->anyFilled(['search','status','date_from','date_to']))
                                <a href="{{ route('wholesale.index') }}" class="btn btn-outline-danger">Reset</a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr class="text-nowrap">
                                    <th>Invoice</th>
                                    <th>Penerima</th>
                                    <th>Penanggung Jawab</th>
                                    <th class="d-none d-sm-table-cell">Total</th>
                                    <th>Status</th>
                                    <th class="d-none d-lg-table-cell">Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="font-weight-bold text-nowrap">{{ $order->invoice_number }}</td>
                                    <td>
                                        <div class="font-weight-bold truncate-text">{{ $order->recipient_name }}</div>
                                        <small class="text-muted d-none d-sm-block">{{ $order->recipient_phone }}</small>
                                    </td>
                                    <td>
                                        @if($order->handler)
                                            <span class="badge badge-info">{{ $order->handler->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell text-nowrap">
                                        <span class="font-weight-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                        @if($order->shipping_cost > 0)
                                        <br><small class="text-muted">+ Ongkir: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</small>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @php
                                            $badgeMap = ['pending'=>'warning','reviewed'=>'primary','on_progress'=>'info','packed'=>'dark','shipped'=>'secondary','delivered'=>'success','completed'=>'success','cancelled'=>'danger'];
                                            $labelMap = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
                                        @endphp
                                        <span class="badge badge-{{ $badgeMap[$order->status] ?? 'secondary' }} px-2 py-1">{{ $labelMap[$order->status] ?? strtoupper($order->status) }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell text-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('wholesale.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada pesanan grosir ditemukan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $orders->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
