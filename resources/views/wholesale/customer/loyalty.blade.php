<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Loyalty - {{ $user->name }} - AL'ASHAR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --p: #FF6B35; --pd: #e55a2b; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f5f7; padding-bottom: 70px;
        }
        .topbar {
            background: linear-gradient(135deg, #FF6B35 0%, #e55a2b 100%);
            padding: 16px; color: #fff;
        }
        .topbar-row { display: flex; align-items: center; gap: 12px; }
        .topbar-row a { color: #fff; font-size: 1rem; }
        .topbar h1 { font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .topbar h1 i { margin-right: 0; }
        .topbar-logo { height: 32px; border-radius: 6px; }
        .section { margin: 12px 16px; }
        .section-title { font-size: 0.85rem; font-weight: 700; color: #555; margin-bottom: 8px; }
        .credit-summary {
            display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin: 12px 16px;
        }
        .credit-card {
            background: #fff; border-radius: 12px; padding: 14px; text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .credit-card .num { font-size: 1.5rem; font-weight: 800; }
        .credit-card .lbl { font-size: 0.7rem; color: #888; margin-top: 2px; }
        .rank-bar {
            background: #fff; border-radius: 12px; padding: 16px; margin: 0 16px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .rank-bar .rank-row { display: flex; justify-content: space-between; align-items: center; }
        .rank-bar .rank-name { font-size: 0.95rem; font-weight: 700; }
        .rank-bar .rank-name small { font-weight: 400; font-size: 0.7rem; color: #888; }
        .progress { height: 8px; background: #eee; border-radius: 4px; margin-top: 8px; overflow: hidden; }
        .progress div { height: 100%; background: linear-gradient(90deg, #ffc107, #ff9800); border-radius: 4px; }
        .promo-card {
            background: #fff; border-radius: 12px; padding: 14px; margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; gap: 12px; align-items: center;
        }
        .promo-card .icon {
            width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .promo-card .info { flex: 1; }
        .promo-card .info .name { font-size: 0.9rem; font-weight: 700; }
        .promo-card .info .desc { font-size: 0.72rem; color: #888; margin-top: 2px; }
        .promo-card .info .cost { font-size: 0.72rem; color: var(--p); font-weight: 600; margin-top: 4px; }
        .promo-card .btn-redeem {
            background: var(--p); color: #fff; border: none; border-radius: 8px; padding: 8px 14px;
            font-size: 0.75rem; font-weight: 600; text-decoration: none; white-space: nowrap;
        }
        .promo-card .btn-redeem:disabled { background: #ccc; }
        .empty-state { text-align: center; padding: 30px 16px; color: #999; }
        .empty-state i { font-size: 2rem; margin-bottom: 8px; }
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; background: #fff;
            display: flex; justify-content: space-around; padding: 6px 0 10px;
            border-top: 1px solid #eee; z-index: 100;
        }
        .bottom-nav a {
            text-align: center; color: #999; text-decoration: none; font-size: 0.63rem; font-weight: 500;
            flex: 1; padding: 2px 0;
        }
        .bottom-nav a i { font-size: 1.25rem; display: block; margin-bottom: 1px; }
        .bottom-nav a.active { color: var(--p); }
        .notif-dot { width: 8px; height: 8px; background: #e74c3c; border-radius: 50%; display: inline-block; position: relative; top: -8px; left: -4px; }
        .toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #28a745; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; z-index: 999; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .toast.error { background: #dc3545; }
    </style>
</head>
<body>
    <div class="toast" id="toast"></div>

    <div class="topbar">
        <div class="topbar-row">
            <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-arrow-left"></i></a>
            <h1><i class="fas fa-star mr-1"></i> Loyalty Saya</h1>
        </div>
    </div>

    {{-- Credit Summary --}}
    <div class="credit-summary">
        <div class="credit-card">
            <div class="num" style="color:#28a745">{{ number_format($rankInfo['available_credits'] ?? 0, 0, ',', '.') }}</div>
            <div class="lbl">Kredit</div>
        </div>
        <div class="credit-card">
            <div class="num" style="color:#ffc107">{{ number_format($rankInfo['gold_points'] ?? 0, 0, ',', '.') }}</div>
            <div class="lbl">Poin Emas</div>
        </div>
        <div class="credit-card">
            <div class="num" style="color:#6f42c1">{{ $rankInfo['current_rank'] ?? 'Regular' }}</div>
            <div class="lbl">Rank</div>
        </div>
    </div>

    {{-- Rank Progress --}}
    <div class="rank-bar">
        <div class="rank-row">
            <div class="rank-name">
                {{ $rankInfo['current_rank'] ?? 'Regular' }}
                @if($rankInfo['is_top_rank'] ?? false)
                    <i class="fas fa-crown" style="color:#ffc107"></i>
                @endif
                <small>{{ $rankInfo['benefits'] ?? '' }}</small>
            </div>
            @if($rankInfo['next_rank'] ?? false)
                <div style="font-size:0.75rem;color:#888">{{ $rankInfo['next_rank']['name'] }}</div>
            @endif
        </div>
        <div class="progress">
            <div style="width:{{ $rankInfo['progress'] ?? 0 }}%"></div>
        </div>
        @if($rankInfo['next_rank'] ?? false)
            <div style="font-size:0.65rem;color:#999;margin-top:4px">
                Butuh Rp {{ number_format($rankInfo['next_rank']['min_spend'], 0, ',', '.') }} total belanja
            </div>
        @else
            <div style="font-size:0.65rem;color:#b8860b;margin-top:4px">
                <i class="fas fa-crown mr-1"></i> Rank tertinggi! Setiap Rp1M belanja = +1 Poin Emas
            </div>
        @endif
    </div>

    {{-- Available Redemptions --}}
    <div class="section">
        <div class="section-title"><i class="fas fa-gift mr-1" style="color:var(--p)"></i> Tukar Kredit</div>
        @forelse($redemptions as $r)
            @php
                $canRedeem = ($rankInfo['available_credits'] ?? 0) >= $r->credits_required;
                $icons = ['discount_percent' => ['bg'=>'#e3f2fd','color'=>'#1976d2','icon'=>'fa-percent'],
                          'paket_usaha' => ['bg'=>'#e8f5e9','color'=>'#388e3c','icon'=>'fa-box'],
                          'free_shipping' => ['bg'=>'#e0f7fa','color'=>'#00838f','icon'=>'fa-truck'],
                          'product' => ['bg'=>'#fff3e0','color'=>'#f57c00','icon'=>'fa-gift']];
                $ico = $icons[$r->reward_type] ?? $icons['product'];
            @endphp
            <div class="promo-card" style="opacity:{{ $canRedeem ? 1 : 0.5 }}">
                <div class="icon" style="background:{{ $ico['bg'] }};color:{{ $ico['color'] }}">
                    <i class="fas {{ $ico['icon'] }}"></i>
                </div>
                <div class="info">
                    <div class="name">{{ $r->name }}</div>
                    <div class="desc">{{ $r->description }}</div>
                    <div class="cost"><i class="fas fa-coins mr-1"></i>{{ number_format($r->credits_required, 0, ',', '.') }} kredit</div>
                </div>
                <button class="btn-redeem" {{ $canRedeem ? '' : 'disabled' }}
                    onclick="redeem({{ $r->id }}, @js($r->name), {{ $r->credits_required }})">
                    {{ $canRedeem ? 'Tukar' : 'Kurang' }}
                </button>
            </div>
        @empty
            <div class="empty-state"><i class="fas fa-gift"></i><p>Belum ada promo tersedia.</p></div>
        @endforelse
    </div>

    {{-- Bottom Nav --}}
    <div class="bottom-nav">
        <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-home"></i> Beranda</a>
        <a href="{{ route('wholesale.customer.orders') }}"><i class="fas fa-box"></i> Pesanan
            @php $nc = $user->unreadNotifications->count(); @endphp
            @if($nc > 0)<span class="notif-dot"></span>@endif
        </a>
        <a href="{{ route('wholesale.customer.history') }}"><i class="fas fa-chart-line"></i> Riwayat</a>
        <a href="{{ route('wholesale.customer.loyalty') }}" class="active"><i class="fas fa-star"></i> Loyalty</a>
        <a href="{{ route('wholesale.customer.leaderboard') }}"><i class="fas fa-trophy"></i> Peringkat</a>
        <a href="#" onclick="event.preventDefault();document.getElementById('lf').submit()"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
    <form id="lf" action="{{ route('wholesale.customer.logout') }}" method="POST">@csrf</form>

    <form id="redeemForm" method="POST" style="display:none">@csrf</form>

    <script>
        function toast(msg, isError) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast' + (isError ? ' error' : '');
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 3000);
        }

        const redeemUrlBase = '{{ url("/wholesale-customer/redeem") }}';
        function redeem(id, name, cost) {
            if (!confirm('Tukarkan ' + cost.toLocaleString() + ' kredit untuk "' + name + '"?')) return;
            const f = document.getElementById('redeemForm');
            f.action = redeemUrlBase + '/' + id;
            const fd = new FormData(f);
            fd.append('redemption_id', id);
            fetch(f.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    if (d.success) { toast(d.message); setTimeout(() => location.reload(), 1500); }
                    else toast(d.message || 'Gagal menukar kredit.', true);
                })
                .catch(() => toast('Terjadi kesalahan.', true));
        }
        window.addEventListener('scroll', function() {
            document.getElementById('btt').style.display = window.scrollY > 400 ? 'flex' : 'none';
        });
    </script>
    <style>
        .btt { position:fixed; bottom:80px; right:16px; z-index:200; width:44px; height:44px; border-radius:50%; background:var(--p); color:#fff; border:none; box-shadow:0 3px 12px rgba(0,0,0,0.25); display:none; align-items:center; justify-content:center; font-size:1.1rem; cursor:pointer; }
    </style>
    <button class="btt" id="btt" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-chevron-up"></i></button>
</body>
</html>
