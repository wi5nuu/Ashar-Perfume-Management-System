@extends('layouts.app')
@section('title', 'Detail Retur')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-undo-alt"></i> Detail Retur</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('returns.index') }}">Retur</a></li>
                    <li class="breadcrumb-item active">{{ $return->return_number }}</li>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-apms border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary-apms">
                        <i class="fas fa-undo mr-2"></i> {{ $return->return_number }}
                    </h5>
                    <span class="badge badge-lg 
                        @switch($return->status)
                            @case('pending') badge-warning @break
                            @case('approved') badge-info @break
                            @case('completed') badge-success @break
                            @case('rejected') badge-danger @break
                        @endswitch p-2">
                        {{ strtoupper($return->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Invoice Asal:</strong> {{ $return->transaction->invoice_number ?? '-' }}</p>
                            <p><strong>Customer:</strong> {{ $return->transaction->customer?->name ?? 'Umum' }}</p>
                            <p><strong>Cabang:</strong> {{ $return->branch->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> {{ $return->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Dibuat Oleh:</strong> {{ $return->user->name ?? '-' }}</p>
                            @if($return->approver)
                                <p><strong>Disetujui:</strong> {{ $return->approver->name }} ({{ $return->approved_at->format('d/m/Y H:i') }})</p>
                            @endif
                            <p><strong>Alasan:</strong><br>{{ $return->reason ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty Retur</th>
                                    <th class="text-center">Harga/Unit</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="h5">
                                    <td colspan="3" class="text-right font-weight-bold">Total Refund:</td>
                                    <td class="text-right text-danger font-weight-bold">Rp {{ number_format($return->total_refund, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-apms border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-primary-apms"><i class="fas fa-tasks mr-2"></i>Alur Kerja</h5>
                </div>
                <div class="card-body">
                    @if($return->status === 'pending')
                        <div class="alert alert-warning border-0 small">
                            Retur menunggu persetujuan manager/owner.
                        </div>
                        @can('manage_employees')
                        <form action="{{ route('returns.approve', $return) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-info btn-block" onclick="return confirm('Approve retur ini?')">
                                <i class="fas fa-check mr-1"></i> APPROVE
                            </button>
                        </form>
                        @endcan

                    @elseif($return->status === 'approved')
                        <div class="alert alert-info border-0 small">
                            Retur sudah diapprove. Selesaikan untuk mengembalikan stok.
                        </div>
                        @can('manage_employees')
                        <form action="{{ route('returns.complete', $return) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block"
                                onclick="return confirm('Selesaikan retur? Stok akan dikembalikan ke inventory.')">
                                <i class="fas fa-boxes mr-1"></i> SELESAIKAN & KEMBALIKAN STOK
                            </button>
                        </form>
                        @endcan

                    @elseif($return->status === 'completed')
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5 class="font-weight-bold">Retur Selesai</h5>
                            <p class="text-muted">Stok telah dikembalikan ke inventory.</p>
                            <p class="text-muted small">Selesai: {{ $return->completed_at ? $return->completed_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('returns.index') }}" class="btn btn-light btn-block"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
        </div>
    </div>
</div>
@endsection
