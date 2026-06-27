@extends('layouts.app')
@section('title', 'Permintaan Stok')
@section('content')
<div class="container-fluid pt-3">
    @include('stock-requests._nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="row mb-3">
        <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $stats['pending'] }}</h3><p>Pending</p></div><div class="icon"><i class="fas fa-clock"></i></div></div></div>
        <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>{{ $stats['shipped'] }}</h3><p>Dikirim</p></div><div class="icon"><i class="fas fa-truck"></i></div></div></div>
        <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>{{ $stats['received'] }}</h3><p>Diterima</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div></div>
        <div class="col-md-3"><a href="{{ route('stock-requests.create') }}" class="btn btn-primary-apms btn-block" style="height:100%;display:flex;align-items:center;justify-content:center;font-size:1.1rem"><i class="fas fa-plus mr-2"></i>Ajukan Permintaan</a></div>
    </div>

    <div class="card card-apms shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light"><tr>
                        <th>No. Request</th><th>Cabang</th><th>Pemohon</th><th class="text-center">Item</th><th>Status</th><th>Pengiriman</th><th>Tgl Diajukan</th><th class="text-center">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse($requests as $r)
                        <tr>
                            <td class="font-weight-bold">{{ $r->request_number }}</td>
                            <td>{{ $r->branch->name ?? '-' }}</td>
                            <td>{{ $r->requester->name ?? '-' }}</td>
                            <td class="text-center">{{ $r->items->count() }}</td>
                            <td>@include('stock-requests._status', ['stockRequest' => $r])</td>
                            <td>
                                @if($r->delivery_date)
                                    <small>{{ $r->delivery_method ?? '-' }}<br>{{ $r->delivery_date->format('d/m/Y') }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td><small>{{ $r->created_at->format('d/m/Y H:i') }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('stock-requests.show', $r) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada permintaan stok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
