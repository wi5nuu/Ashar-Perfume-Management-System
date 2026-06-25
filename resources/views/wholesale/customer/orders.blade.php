<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesanan Saya - {{ $user->name }} - AL'ASHAR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --p: #FF6B35; --pd: #e55a2b; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; padding-bottom: 70px; }
        .topbar {
            background: linear-gradient(135deg, #FF6B35 0%, #e55a2b 100%);
            padding: 14px 16px; color: #fff; display: flex; align-items: center; gap: 12px;
        }
        .topbar a { color: #fff; font-size: 1.1rem; text-decoration: none; }
        .topbar h1 { font-size: 1.05rem; font-weight: 700; flex: 1; display: flex; align-items: center; gap: 8px; }
        .topbar h1 i { margin-right: 0; }
        .topbar-logo { height: 32px; border-radius: 6px; }
        .section { margin: 14px 16px; }
        .section-title {
            font-size: 0.85rem; font-weight: 700; color: #555; margin-bottom: 10px;
            display: flex; align-items: center; gap: 6px;
        }
        .section-title span { margin-left: auto; font-size: 0.72rem; color: #999; }
        .order-card {
            background: #fff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 12px;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        a:hover .order-card { box-shadow: 0 4px 20px rgba(0,0,0,0.15); transform: translateY(-1px); }
        .order-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px 8px;
        }
        .order-invoice { font-size: 0.95rem; font-weight: 800; color: var(--p); }
        .order-date { font-size: 0.75rem; color: #999; }
        .order-body { padding: 0 16px 14px; }
        .order-info { display: flex; gap: 16px; font-size: 0.82rem; color: #888; margin-bottom: 6px; flex-wrap: wrap; }
        .order-info span { display: flex; align-items: center; gap: 4px; }
        .order-items { font-size: 0.8rem; color: #999; margin-top: 6px; line-height: 1.4; }
        .order-total { font-size: 0.9rem; font-weight: 700; color: #222; text-align: right; margin-top: 6px; }
        .badge {
            display: inline-block; padding: 4px 12px; border-radius: 20px;
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-reviewed { background: #e3f2fd; color: #1565c0; }
        .badge-on_progress { background: #e0f7fa; color: #00838f; }
        .badge-packed { background: #f3e5f5; color: #6a1b9a; }
        .badge-shipped { background: #e8eaf6; color: #283593; }
        .badge-delivered,
        .badge-completed { background: #e8f5e9; color: #1b5e20; }
        .badge-cancelled { background: #fce4ec; color: #b71c1c; }
        .stepper {
            display: flex; gap: 0; overflow-x: auto; padding: 6px 0 2px;
            -webkit-overflow-scrolling: touch; scrollbar-width: none;
        }
        .stepper::-webkit-scrollbar { display: none; }
        .step { flex: 0 0 64px; text-align: center; position: relative; }
        .step:not(:last-child)::after {
            content: ''; position: absolute; top: 14px; left: calc(50% + 17px);
            width: calc(100% - 34px); height: 2.5px; background: #e0e0e0;
        }
        .step.done:not(:last-child)::after { background: #4caf50; }
        .step-icon {
            width: 30px; height: 30px; border-radius: 50%; margin: 0 auto 3px;
            display: flex; align-items: center; justify-content: center; font-size: 0.75rem;
        }
        .step-icon.done { background: #4caf50; color: #fff; }
        .step-icon.active { background: var(--p); color: #fff; box-shadow: 0 0 0 5px rgba(255,107,53,0.25); }
        .step-icon.pending { background: #e0e0e0; color: #999; }
        .step-icon.cancelled { background: #e53935; color: #fff; }
        .step-label { font-size: 0.6rem; color: #999; white-space: nowrap; font-weight: 500; }
        .step-label.done { color: #4caf50; }
        .step-label.active { color: var(--p); font-weight: 700; }
        .empty { text-align: center; padding: 40px 16px; color: #999; }
        .empty i { font-size: 2.5rem; color: #ddd; margin-bottom: 10px; }
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;
            background: #fff; border-top: 1px solid #eee;
            display: flex; box-shadow: 0 -2px 10px rgba(0,0,0,0.06);
        }
        .bottom-nav a {
            flex: 1; text-align: center; padding: 8px 0 6px; text-decoration: none;
            color: #999; font-size: 0.62rem; transition: color 0.2s;
        }
        .bottom-nav a i { display: block; font-size: 1.15rem; margin-bottom: 2px; }
        .bottom-nav a.active { color: var(--p); }
        .bottom-nav a .notif-dot {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%;
            background: #e53935; margin-left: 2px; vertical-align: super;
        }
        .pagination { display: flex; justify-content: center; gap: 6px; margin: 14px 0; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: 6px; font-size: 0.78rem; text-decoration: none;
            background: #fff; color: #555; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .pagination .active { background: var(--p); color: #fff; }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-arrow-left"></i></a>
        <h1><i class="fas fa-box mr-1"></i>Pesanan Saya</h1>
        <span style="font-size:0.78rem;opacity:0.85">{{ $orders->total() ?? 0 }} pesanan</span>
    </div>

    <div class="section">
        @forelse($orders as $order)
        <a href="{{ route('wholesale.customer.orders.show', $order) }}" style="text-decoration:none;color:inherit;display:block">
        <div class="order-card">
            <div class="order-head">
                <span class="order-invoice"><i class="fas fa-file-invoice mr-1"></i>{{ $order->invoice_number }}</span>
                <span class="order-date">{{ $order->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="order-body">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                    <span class="badge badge-{{ $order->status }}">
                        {{ ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$order->status] ?? $order->status }}
                    </span>
                    @if($order->tracking_number)
                    <span style="font-size:0.75rem;color:var(--p);font-weight:600"><i class="fas fa-truck mr-1"></i>{{ $order->tracking_number }}</span>
                    @endif
                </div>
                @include('wholesale.customer.timeline', ['order' => $order])
                <div class="order-info">
                    <span><i class="fas fa-box"></i> {{ $order->details->count() }} item</span>
                    <span><i class="fas fa-tag"></i> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                @if($order->details->count() > 0)
                <div class="order-items">
                    @foreach($order->details->take(3) as $d)
                        {{ $d->product_name }} ({{ $d->quantity }})@if(!$loop->last), @endif
                    @endforeach
                    @if($order->details->count() > 3), <em>+{{ $order->details->count()-3 }} lainnya</em>@endif
                </div>
                @endif
                <div class="order-total" style="font-size:0.82rem">Total: Rp {{ number_format($order->total_amount + ($order->shipping_cost??0), 0, ',', '.') }}</div>
            </div>
        </div>
        </a>
        @empty
        <div class="empty">
            <i class="fas fa-box-open"></i>
            <p>Belum ada pesanan grosir.</p>
        </div>
        @endforelse

        @if(method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="pagination">
            {{ $orders->links('vendor.pagination.simple-bootstrap-4') }}
        </div>
        @endif
    </div>

    <div class="bottom-nav">
        <a href="{{ route('wholesale.customer.dashboard') }}">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="{{ route('wholesale.customer.orders') }}" class="active">
            <i class="fas fa-box"></i> Pesanan
            @php $nc = $user->unreadNotifications->count(); @endphp
            @if($nc > 0)<span class="notif-dot"></span>@endif
        </a>
        <a href="{{ route('wholesale.customer.history') }}">
            <i class="fas fa-chart-line"></i> Riwayat
        </a>
        <a href="{{ route('wholesale.customer.loyalty') }}">
            <i class="fas fa-star"></i> Loyalty
        </a>
        <a href="{{ route('wholesale.customer.leaderboard') }}">
            <i class="fas fa-trophy"></i> Peringkat
        </a>
        <a href="#" onclick="event.preventDefault();document.getElementById('lf').submit()">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
    <form id="lf" action="{{ route('wholesale.customer.logout') }}" method="POST" style="display:none">@csrf</form>
</body>
</html>
