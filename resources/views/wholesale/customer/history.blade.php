<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat Belanja - {{ $user->name }} - AL'ASHAR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root { --p:#FF6B35; }
        body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background:#f4f5f7; padding-bottom:70px; }
        .topbar { background:linear-gradient(135deg,#FF6B35 0%,#e55a2b 100%); padding:14px 16px; color:#fff; display:flex; align-items:center; gap:12px; }
        .topbar a { color:#fff; font-size:1.1rem; text-decoration:none; }
        .topbar h1 { font-size:1.05rem; font-weight:700; flex:1; display:flex; align-items:center; gap:8px; }
        .topbar h1 i { margin-right:0; }
        .topbar-logo { height:32px; border-radius:6px; }
        .section { margin:14px 16px; }
        .card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:16px; margin-bottom:12px; }
        .tier-card { background:linear-gradient(135deg,{{ $tier['color'] }} 0%,#fff 100%); border-radius:10px; padding:16px; margin-bottom:12px; color:#fff; }
        .tier-card .tier-icon { font-size:2rem; margin-bottom:4px; }
        .tier-card .tier-name { font-size:1.1rem; font-weight:800; }
        .tier-card .tier-total { font-size:0.78rem; opacity:0.85; }
        .tier-card .tier-promo { background:rgba(255,255,255,0.2); padding:8px; border-radius:6px; margin-top:8px; font-size:0.82rem; font-weight:600; }
        .stat-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px; }
        .stat-box { background:#fff; border-radius:10px; padding:14px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        .stat-box .num { font-size:1.3rem; font-weight:800; color:#222; }
        .stat-box .lbl { font-size:0.7rem; color:#888; }
        .next-tier { background:#f0fdf4; border-radius:10px; padding:12px; margin-bottom:12px; border-left:3px solid #22c55e; font-size:0.8rem; color:#166534; }
        .next-tier strong { display:block; }
        .order-item { border-bottom:1px solid #f0f0f0; padding:10px 0; }
        .order-item:last-child { border-bottom:none; }
        .order-head { display:flex; justify-content:space-between; align-items:center; }
        .order-inv { font-size:0.8rem; font-weight:700; color:var(--p); }
        .order-date { font-size:0.68rem; color:#999; }
        .order-body { font-size:0.75rem; color:#555; margin-top:4px; display:flex; gap:12px; flex-wrap:wrap; }
        .order-body span { display:flex; align-items:center; gap:4px; }
        .order-total { font-size:0.82rem; font-weight:700; color:#222; text-align:right; margin-top:4px; }
        .badge-status { display:inline-block; padding:2px 8px; border-radius:20px; font-size:0.62rem; font-weight:700; text-transform:uppercase; }
        .empty { text-align:center; padding:40px 16px; color:#999; }
        .empty i { font-size:2.5rem; color:#ddd; margin-bottom:10px; }
        .bottom-nav { position:fixed; bottom:0; left:0; right:0; z-index:100; background:#fff; border-top:1px solid #eee; display:flex; box-shadow:0 -2px 10px rgba(0,0,0,0.06); }
        .bottom-nav a { flex:1; text-align:center; padding:8px 0 6px; text-decoration:none; color:#999; font-size:0.62rem; transition:color .2s; }
        .bottom-nav a i { display:block; font-size:1.15rem; margin-bottom:2px; }
        .bottom-nav a.active { color:var(--p); }
        .pagination-wrap { display:flex; justify-content:center; gap:6px; margin:14px 0; }
        .pagination-wrap a,.pagination-wrap span { padding:6px 12px; border-radius:6px; font-size:0.78rem; text-decoration:none; background:#fff; color:#555; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
        .pagination-wrap .active { background:var(--p); color:#fff; }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-arrow-left"></i></a>
        <h1><i class="fas fa-history mr-1"></i>Riwayat Belanja</h1>
    </div>

    <div class="section">
        {{-- VIP Tier Card --}}
        <div class="tier-card" style="background:linear-gradient(135deg,{{ $tier['color'] }} 0%,#1a1a2e 100%)">
            <div class="tier-icon"><i class="fas {{ $tier['icon'] }}"></i></div>
            <div class="tier-name">{{ $tier['label'] }}</div>
            <div class="tier-total">Total Belanja: Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
            @if($tier['discount'] > 0)
            <div class="tier-promo">
                <i class="fas fa-tag mr-1"></i> Promo: Diskon {{ $tier['discount'] }}% — Kode: <strong>{{ strtoupper($tier['label']) }}-{{ $user->id }}-{{ date('Y') }}</strong>
            </div>
            @endif
        </div>

        {{-- Next Tier --}}
        @if($nextTier)
        <div class="next-tier">
            <strong><i class="fas fa-arrow-up mr-1"></i> Tier Berikutnya: {{ $nextTier['label'] }}</strong>
            Belanja Rp {{ number_format($nextTier['min'] - $totalSpent, 0, ',', '.') }} lagi untuk naik ke {{ $nextTier['label'] }} dan dapatkan diskon {{ $nextTier['discount'] }}%!
        </div>
        @endif

        {{-- Stats --}}
        <div class="stat-row">
            <div class="stat-box">
                <div class="num">{{ $totalOrders }}</div>
                <div class="lbl">Total Pesanan</div>
            </div>
            <div class="stat-box">
                <div class="num">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
                <div class="lbl">Total Belanja</div>
            </div>
        </div>

        {{-- Order History --}}
        @forelse($orders as $order)
        <div class="card" style="padding:12px 14px">
            <div class="order-head">
                <span class="order-inv">{{ $order->invoice_number }}</span>
                <span class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div style="margin:4px 0">
                <span class="badge-status" style="background:{{ ['pending'=>'#fff3e0','reviewed'=>'#e3f2fd','on_progress'=>'#e0f7fa','packed'=>'#f3e5f5','shipped'=>'#e8eaf6','delivered'=>'#e8f5e9','completed'=>'#e8f5e9','cancelled'=>'#fce4ec'][$order->status] ?? '#eee' }};color:{{ ['pending'=>'#e65100','reviewed'=>'#1565c0','on_progress'=>'#00838f','packed'=>'#6a1b9a','shipped'=>'#283593','delivered'=>'#1b5e20','completed'=>'#1b5e20','cancelled'=>'#b71c1c'][$order->status] ?? '#555' }}">
                    {{ ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$order->status] ?? $order->status }}
                </span>
            </div>
            <div class="order-body">
                <span><i class="fas fa-box"></i>{{ $order->details->count() }} item</span>
                @if($order->tracking_number)<span><i class="fas fa-truck"></i>{{ $order->tracking_number }}</span>@endif
            </div>
            <div class="order-total">Rp {{ number_format($order->total_amount + ($order->shipping_cost ?? 0), 0, ',', '.') }}</div>
            @if($order->details->count() > 0)
            <div style="font-size:0.68rem;color:#999;margin-top:4px">
                @foreach($order->details->take(3) as $d)
                    {{ $d->product_name }} ({{ $d->quantity }})@if(!$loop->last), @endif
                @endforeach
                @if($order->details->count() > 3), <em>+{{ $order->details->count()-3 }} lainnya</em>@endif
            </div>
            @endif
        </div>
        @empty
        <div class="empty">
            <i class="fas fa-box-open"></i>
            <p>Belum ada riwayat belanja.</p>
        </div>
        @endforelse

        @if(method_exists($orders,'hasPages') && $orders->hasPages())
        <div class="pagination-wrap">{{ $orders->links('vendor.pagination.simple-bootstrap-4') }}</div>
        @endif
    </div>

    <div class="bottom-nav">
        <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-home"></i> Beranda</a>
        <a href="{{ route('wholesale.customer.orders') }}"><i class="fas fa-box"></i> Pesanan</a>
        <a href="{{ route('wholesale.customer.history') }}" class="active"><i class="fas fa-chart-line"></i> Riwayat</a>
        <a href="{{ route('wholesale.customer.loyalty') }}"><i class="fas fa-star"></i> Loyalty</a>
        <a href="{{ route('wholesale.customer.leaderboard') }}"><i class="fas fa-trophy"></i> Peringkat</a>
        <a href="#" onclick="event.preventDefault();document.getElementById('lf').submit()"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
    <form id="lf" action="{{ route('wholesale.customer.logout') }}" method="POST" style="display:none">@csrf</form>
    <style>
        .btt { position:fixed; bottom:80px; right:16px; z-index:200; width:44px; height:44px; border-radius:50%; background:var(--p); color:#fff; border:none; box-shadow:0 3px 12px rgba(0,0,0,0.25); display:none; align-items:center; justify-content:center; font-size:1.1rem; cursor:pointer; }
    </style>
    <button class="btt" id="btt" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-chevron-up"></i></button>
    <script>
        window.addEventListener('scroll', function() {
            document.getElementById('btt').style.display = window.scrollY > 400 ? 'flex' : 'none';
        });
    </script>
</body>
</html>
