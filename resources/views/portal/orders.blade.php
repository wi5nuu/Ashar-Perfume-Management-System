@extends('portal.layout')

@section('title', 'Pesanan Saya')

@section('content')
<div class="card card-portal">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-box mr-2"></i> Riwayat Pesanan Grosir</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="font-weight-bold">{{ $order->invoice_number }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>{{ $order->details->count() }} produk</td>
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
                    @if($order->details->count() > 0)
                    <tr class="bg-light">
                        <td colspan="5">
                            <small class="text-muted">
                                @foreach($order->details->take(3) as $d)
                                    {{ $d->product->name ?? 'N/A' }} ({{ $d->quantity }})@if(!$loop->last), @endif
                                @endforeach
                                @if($order->details->count() > 3)
                                    <em>+{{ $order->details->count() - 3 }} lainnya</em>
                                @endif
                            </small>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
