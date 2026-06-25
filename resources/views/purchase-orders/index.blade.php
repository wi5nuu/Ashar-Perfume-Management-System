@extends('layouts.app')
@section('title', 'Purchase Orders')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-shopping-cart"></i> Purchase Orders</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Purchase Orders</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card card-apms">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0">Daftar Purchase Order</h3>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary-apms btn-sm ml-auto">
                <i class="fas fa-plus"></i> Buat PO Baru
            </a>
        </div>
        <div class="card-body">
            {{-- Filter --}}
            <form method="GET" class="form-inline mb-3">
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Tanggal Order</th>
                            <th>Expected</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $po)
                        <tr>
                            <td><strong>{{ $po->po_number }}</strong></td>
                            <td>{{ $po->supplier->name ?? '-' }}</td>
                            <td>{{ $po->order_date->format('d/m/Y') }}</td>
                            <td>{{ $po->expected_date ? $po->expected_date->format('d/m/Y') : '-' }}</td>
                            <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @switch($po->status)
                                    @case('draft')
                                        <span class="badge badge-secondary">Draft</span>
                                        @break
                                    @case('sent')
                                        <span class="badge badge-info">Sent</span>
                                        @break
                                    @case('partial')
                                        <span class="badge badge-warning">Partial</span>
                                        @break
                                    @case('received')
                                        <span class="badge badge-success">Received</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $po->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada Purchase Order.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
