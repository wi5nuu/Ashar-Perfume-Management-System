<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - {{ $user->name }} - AL'ASHAR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --p: #FF6B35; --pd: #e55a2b; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f5f7; padding-bottom: 70px;
        }
        /* ── Top Bar ── */
        .topbar {
            background: linear-gradient(135deg, #FF6B35 0%, #e55a2b 100%);
            padding: 16px 16px 20px; color: #fff;
        }
        .topbar-row { display: flex; justify-content: space-between; align-items: center; }
        .topbar h1 { font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .topbar h1 i { margin-right: 0; }
        .topbar-logo { height: 32px; border-radius: 6px; }
        .topbar-user { display: flex; align-items: center; gap: 8px; }
        .topbar-user .avatar {
            width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }
        .topbar-user .name { font-size: 0.8rem; font-weight: 600; }
        .topbar-user .logout-btn {
            color: rgba(255,255,255,0.7); font-size: 0.75rem; text-decoration: none;
            display: flex; align-items: center; gap: 4px; margin-left: 4px;
        }
        .topbar-greeting { font-size: 0.85rem; opacity: 0.85; margin-top: 10px; }
        .topbar-greeting strong { opacity: 1; }
        /* ── Quick Stats ── */
        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: -14px 16px 14px; }
        .stat-card {
            background: #fff; border-radius: 10px; padding: 14px; text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .stat-card i { font-size: 1.3rem; color: var(--p); margin-bottom: 4px; }
        .stat-card .num { font-size: 1.4rem; font-weight: 800; color: #222; }
        .stat-card .lbl { font-size: 0.7rem; color: #888; }
        /* ── Section ── */
        .section { margin: 0 16px 14px; }
        .section-title { font-size: 0.85rem; font-weight: 700; color: #555; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .section-title a { margin-left: auto; font-size: 0.72rem; color: var(--p); text-decoration: none; font-weight: 600; }
        /* ── Tier Card ── */
        .tier-card { margin: -8px 16px 14px; border-radius:10px; padding:14px; color:#fff; }
        .tier-card .tier-icon { font-size:1.5rem; margin-bottom:2px; }
        .tier-card .tier-name { font-size:1rem; font-weight:800; }
        .tier-card .tier-total { font-size:0.72rem; opacity:0.85; }
        .tier-card .tier-promo { background:rgba(255,255,255,0.2); padding:6px 10px; border-radius:6px; margin-top:6px; font-size:0.75rem; font-weight:600; }
        /* ── Track Search ── */
        .track-box {
            background: #fff; border-radius: 12px; padding: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin: 0 16px 14px;
        }
        .track-box form { display: flex; gap: 10px; }
        .track-box input {
            flex: 1; border: 1.5px solid #ddd; border-radius: 10px; padding: 14px 14px;
            font-size: 1rem; outline: none; background: #fafafa;
        }
        .track-box input:focus { border-color: var(--p); }
        .track-box button {
            background: var(--p); color: #fff; border: none; border-radius: 10px;
            padding: 0 20px; font-size: 0.95rem; font-weight: 700;
        }
        /* ── Order Card ── */
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
        .order-body { padding: 0 16px 12px; }
        .order-info { display: flex; gap: 16px; font-size: 0.82rem; color: #888; margin-bottom: 8px; }
        .order-info span { display: flex; align-items: center; gap: 4px; }
        .order-items { font-size: 0.8rem; color: #999; margin-top: 6px; line-height: 1.4; }
        .order-total { font-size: 0.9rem; font-weight: 700; color: #222; text-align: right; margin-top: 6px; }
        /* ── Badge ── */
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
        /* ── Stepper ── */
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
        .step.active:not(:last-child)::after { background: linear-gradient(90deg, var(--p), #e0e0e0); }
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
        /* ── Notifications ── */
        .notif-item {
            background: #fff; border-radius: 10px; padding: 12px 14px; margin-bottom: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 10px;
        }
        .notif-item .notif-icon {
            width: 36px; height: 36px; border-radius: 50%; background: #fff3e0;
            display: flex; align-items: center; justify-content: center; color: var(--p); flex-shrink: 0;
        }
        .notif-item .notif-body { flex: 1; }
        .notif-item .notif-body strong { font-size: 0.8rem; display: block; }
        .notif-item .notif-body small { font-size: 0.7rem; color: #999; }
        .notif-item .notif-time { font-size: 0.65rem; color: #bbb; }
        /* ── Empty ── */
        .empty { text-align: center; padding: 30px 16px; color: #999; font-size: 0.85rem; }
        .empty i { font-size: 2.5rem; color: #ddd; margin-bottom: 10px; }
        /* ── Bottom Nav ── */
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
        /* ── Tracked Result ── */
        .track-result {
            background: #fff; border-radius: 12px; padding: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin: -6px 16px 14px;
        }
        .track-result .head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .track-result .head strong { font-size: 1rem; color: var(--p); }
        .track-result .badge { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
        .track-resi { font-size: 0.95rem; color: #1565c0; font-weight: 700; margin: 8px 0; padding: 10px 12px; background: #e3f2fd; border-radius: 8px; }
        .track-qr { text-align: center; margin-top: 12px; padding: 12px; background: #fafafa; border-radius: 10px; }
        .track-qr img { max-width: 100px; border-radius: 6px; }
        .track-qr div { font-size: 0.72rem; color: #888; margin-top: 4px; font-weight: 600; }
    </style>
</head>
<body>
    {{-- Top Bar --}}
    <div class="topbar">
        <div class="topbar-row">
            <h1><img src="{{ asset('logotoko.png') }}" alt="AL'ASHAR" class="topbar-logo"> AL'ASHAR</h1>
            <div class="topbar-user">
                <div class="avatar"><i class="fas fa-user"></i></div>
                <span class="name">{{ $user->name }}</span>
                <a class="logout-btn" href="#" onclick="event.preventDefault();document.getElementById('lf').submit()">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <form id="lf" action="{{ route('wholesale.customer.logout') }}" method="POST" style="display:none">@csrf</form>
            </div>
        </div>
        <div class="topbar-greeting">Halo, <strong>{{ explode(' ', $user->name)[0] }}</strong>! Pantau status pesanan grosir Anda di sini.</div>
    </div>

    {{-- Stats + Tier --}}
    <div class="stats">
        <div class="stat-card">
            <i class="fas fa-box"></i>
            <div class="num">{{ $totalOrders ?? 0 }}</div>
            <div class="lbl">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-spinner"></i>
            <div class="num">{{ $activeOrders ?? 0 }}</div>
            <div class="lbl">Aktif</div>
        </div>
    </div>

    {{-- VIP Tier Card --}}
    <div class="tier-card" style="background:linear-gradient(135deg,{{ $tier['color'] }} 0%,#1a1a2e 100%)">
        <div class="tier-icon"><i class="fas {{ $tier['icon'] }}"></i></div>
        <div class="tier-name">{{ $tier['label'] }}</div>
        <div class="tier-total">Total Belanja: Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</div>
        @if(($tier['discount'] ?? 0) > 0)
        <div class="tier-promo"><i class="fas fa-tag mr-1"></i> Diskon {{ $tier['discount'] }}% — Kode: <strong>{{ strtoupper($tier['label']) }}-{{ $user->id }}-{{ date('Y') }}</strong></div>
        @endif
    </div>

    {{-- Loyalty Credits --}}
    <div class="ref-card" style="background:#fff;border-radius:12px;margin:12px 16px;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,.08);display:flex;align-items:center;gap:12px">
        <div style="width:40px;height:40px;border-radius:50%;background:#fff3cd;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-star" style="color:#ffc107;font-size:1.1rem"></i>
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-size:0.75rem;color:#999;margin-bottom:2px">Kredit & Poin Emas</div>
            <div style="display:flex;gap:16px;align-items:center">
                <span style="font-size:1rem;font-weight:700;color:#1a1a2e">{{ number_format($rankInfo['available_credits'] ?? 0, 0, ',', '.') }} <small style="font-weight:400;color:#888;font-size:0.7rem;">kredit</small></span>
                @if(($rankInfo['gold_points'] ?? 0) > 0)
                <span style="font-size:0.9rem;font-weight:700;color:#b8860b">{{ number_format($rankInfo['gold_points'] ?? 0, 0, ',', '.') }} <small style="font-weight:400;color:#888;font-size:0.7rem;">emas</small></span>
                @endif
            </div>
        </div>
        <a href="{{ route('wholesale.customer.loyalty') }}" style="background:#ffc107;color:#1a1a2e;border:none;border-radius:8px;padding:8px 14px;font-size:0.75rem;font-weight:600;text-decoration:none;white-space:nowrap">
            <i class="fas fa-gift mr-1"></i> Tukar
        </a>
    </div>

    {{-- Referral Code --}}
    <div class="ref-card" style="background:#fff;border-radius:12px;margin:12px 16px;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,.08);display:flex;align-items:center;gap:12px">
        <div style="width:40px;height:40px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-share-alt" style="color:#FF6B35;font-size:1.1rem"></i>
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-size:0.75rem;color:#999;margin-bottom:2px">Kode Referral Anda</div>
            <div style="font-size:1rem;font-weight:700;letter-spacing:1px;color:#1a1a2e">{{ $user->referral_code ?? '-' }}</div>
        </div>
        <a href="{{ route('wholesale.customer.leaderboard') }}" style="background:#FF6B35;color:#fff;border:none;border-radius:8px;padding:8px 14px;font-size:0.75rem;font-weight:600;text-decoration:none;white-space:nowrap">
            <i class="fas fa-trophy mr-1"></i> Peringkat
        </a>
    </div>

    {{-- Track Search --}}
    <div class="track-box">
        <form action="{{ route('wholesale.customer.track') }}" method="GET">
            <input type="text" name="invoice_number" placeholder="Cari no. invoice..." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    {{-- Tracked Result --}}
    @if(isset($trackedOrder))
    <div class="track-result">
        <div class="head">
            <div><strong>{{ $trackedOrder->invoice_number }}</strong></div>
            <span class="badge badge-{{ $trackedOrder->status }}">{{ $trackedOrder->status }}</span>
        </div>
        @if($trackedOrder->tracking_number)
        <div class="track-resi"><i class="fas fa-truck mr-1"></i> No. Resi: {{ $trackedOrder->tracking_number }}</div>
        @endif
        @include('wholesale.customer.timeline', ['order' => $trackedOrder])
        @php $tu = url('/wholesale-customer/track?invoice_number='.urlencode($trackedOrder->invoice_number)); @endphp
        <div class="track-qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($tu) }}" alt="QR">
            <div>Scan untuk lacak</div>
        </div>
    </div>
    @endif

    {{-- Notifications --}}
    @php $unread = $user->notifications()->whereNull('read_at')->latest()->get(); @endphp
    @if($unread->count() > 0)
    <div class="section">
        <div class="section-title">
            <i class="fas fa-bell" style="color:var(--p)"></i> Notifikasi
            <form action="{{ route('wholesale.customer.notifications.read-all') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;color:var(--p);font-size:0.72rem;font-weight:600;cursor:pointer">Tandai Dibaca</button>
            </form>
        </div>
        @foreach($unread->take(3) as $n)
        <div class="notif-item">
            <div class="notif-icon"><i class="fas fa-truck"></i></div>
            <div class="notif-body">
                <strong>{{ $n->data['title'] ?? 'Update Pesanan' }}</strong>
                <small>{{ $n->data['message'] ?? '' }}</small>
            </div>
            <div class="notif-time">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Orders --}}
    <div class="section">
        <div class="section-title">
            <i class="fas fa-history" style="color:var(--p)"></i> Pesanan Terbaru
            <a href="{{ route('wholesale.customer.orders') }}">Lihat Semua <i class="fas fa-chevron-right" style="font-size:0.6rem"></i></a>
        </div>
        @forelse($orders as $order)
        <a href="{{ route('wholesale.customer.orders.show', $order) }}" style="text-decoration:none;color:inherit;display:block">
        <div class="order-card">
            <div class="order-head">
                <span class="order-invoice"><i class="fas fa-file-invoice mr-1"></i>{{ $order->invoice_number }}</span>
                <span class="badge badge-{{ $order->status }}">{{ ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$order->status] ?? $order->status }}</span>
            </div>
            <div class="order-body">
                @include('wholesale.customer.timeline', ['order' => $order])
                <div class="order-info">
                    <span><i class="fas fa-box"></i> {{ $order->details->count() }} item</span>
                    @if($order->tracking_number)
                    <span><i class="fas fa-truck"></i> {{ $order->tracking_number }}</span>
                    @endif
                </div>
                @if($order->details->count() > 0)
                <div class="order-items">
                    @foreach($order->details->take(3) as $d)
                        {{ $d->product_name }} ({{ $d->quantity }})@if(!$loop->last), @endif
                    @endforeach
                    @if($order->details->count() > 3), <em>+{{ $order->details->count()-3 }} lainnya</em>@endif
                </div>
                @endif
                <div class="order-total">Total: Rp {{ number_format($order->total_amount + ($order->shipping_cost??0), 0, ',', '.') }}</div>
            </div>
        </div>
        </a>
        @empty
        <div class="empty">
            <i class="fas fa-box-open"></i>
            <p>Belum ada pesanan grosir.</p>
        </div>
        @endforelse
    </div>

    {{-- Bottom Nav --}}
    <div class="bottom-nav">
        <a href="{{ route('wholesale.customer.dashboard') }}" class="active">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="{{ route('wholesale.customer.orders') }}">
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

    <script>
        // auto-refresh setiap 30 detik
        setTimeout(function(){ location.reload(); }, 30000);
    </script>
</body>
</html>
