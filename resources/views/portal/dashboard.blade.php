@extends('portal.layout')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card card-portal">
            <div class="card-body text-center">
                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                <h5 class="text-muted">Total Pesanan</h5>
                <h2 class="font-weight-bold">{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-portal">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h5 class="text-muted">Pesanan Proses</h5>
                <h2 class="font-weight-bold">{{ $pendingOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card card-portal">
            <div class="card-body text-center">
                <i class="fas fa-hand-holding-usd fa-2x text-danger mb-2"></i>
                <h5 class="text-muted">Sisa Hutang</h5>
                <h2 class="font-weight-bold">Rp {{ number_format($remainingDebt, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card card-portal">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history mr-2"></i> Pesanan Terbaru</h5>
        <a href="{{ route('portal.orders', $token) }}" class="btn btn-sm btn-portal">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>{{ $order->invoice_number }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge badge-warning badge-status">Pending</span>
                                    @break
                                @case('processing')
                                    <span class="badge badge-info badge-status">Diproses</span>
                                    @break
                                @case('completed')
                                    <span class="badge badge-success badge-status">Selesai</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger badge-status">Dibatalkan</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary badge-status">{{ ucfirst($order->status) }}</span>
                            @endswitch
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
