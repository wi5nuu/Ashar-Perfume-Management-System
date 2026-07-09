@extends('layouts.app')
@section('title', 'Retur Penjualan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-undo-alt"></i> Retur Penjualan</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Retur</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card card-apms">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Daftar Retur</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="form-inline mb-3">
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                    <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
                    <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
                    <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>No. Retur</th>
                            <th>Invoice Asal</th>
                            <th>Tanggal</th>
                            <th>Total Refund</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $r)
                        <tr>
                            <td><strong>{{ $r->return_number }}</strong></td>
                            <td>{{ $r->transaction->invoice_number ?? '-' }}</td>
                            <td>{{ $r->created_at->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($r->total_refund, 0, ',', '.') }}</td>
                            <td>
                                @switch($r->status)
                                    @case('pending') <span class="badge badge-warning">Pending</span> @break
                                    @case('approved') <span class="badge badge-info">Approved</span> @break
                                    @case('completed') <span class="badge badge-success">Completed</span> @break
                                    @case('rejected') <span class="badge badge-danger">Rejected</span> @break
                                @endswitch
                            </td>
                            <td>{{ $r->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('returns.show', $r) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada retur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection
