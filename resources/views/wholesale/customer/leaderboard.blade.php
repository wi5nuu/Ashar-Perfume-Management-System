<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>Peringkat Referral — AL'ASHAR PARFUM</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--p:#FF6B35;--bg:#f5f5f5}
*{margin:0;padding:0;box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
body{background:var(--bg);padding-bottom:70px}
.topbar{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:12px 16px;position:sticky;top:0;z-index:100}
.topbar h1{font-size:1rem;font-weight:700;display:flex;align-items:center;gap:8px}
.topbar h1 i{color:var(--p)}
.topbar-user{display:flex;align-items:center;gap:8px;margin-top:6px}
.topbar-user .avatar{width:28px;height:28px;border-radius:50%;background:var(--p);display:flex;align-items:center;justify-content:center;font-size:0.75rem}
.topbar-user .name{font-size:0.75rem;opacity:.9}
.back-link{color:#fff;font-size:0.75rem;text-decoration:none}
.back-link i{margin-right:4px}
.leader-card{background:#fff;border-radius:12px;margin:12px 16px;padding:16px;box-shadow:0 1px 4px rgba(0,0,0,.08);text-align:center}
.leader-card .pos{font-size:2rem;font-weight:800;color:var(--p)}
.leader-card .pos-label{font-size:0.85rem;color:#666;margin-bottom:8px}
.leader-card .ref-count{font-size:1.1rem;font-weight:700;color:#1a1a2e}
.leader-card .ref-label{font-size:0.75rem;color:#999}
.leader-card .code-box{margin-top:8px;padding:8px 12px;background:#f8f8f8;border-radius:8px;font-size:0.9rem;font-weight:700;letter-spacing:1px;color:#1a1a2e}
.rank-item{display:flex;align-items:center;padding:12px 16px;border-bottom:1px solid #f0f0f0}
.rank-item:last-child{border-bottom:none}
.rank-num{width:28px;font-weight:700;color:var(--p);font-size:0.9rem}
.rank-medal{width:28px;font-size:1.1rem}
.rank-name{flex:1;font-weight:500;font-size:0.9rem;color:#333}
.rank-count{font-size:0.8rem;color:#999;font-weight:500}
.section-title{padding:16px 16px 8px;font-size:0.85rem;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
.order-feed-item{padding:10px 16px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;gap:10px}
.order-feed-item:last-child{border-bottom:none}
.feed-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.feed-info{flex:1;font-size:0.82rem;color:#444}
.feed-info .feed-status{font-weight:600}
.feed-time{font-size:0.7rem;color:#999;white-space:nowrap}
.empty-state{text-align:center;padding:30px 16px;color:#bbb}
.empty-state i{font-size:2rem;margin-bottom:8px;display:block}
.list-card{background:#fff;border-radius:12px;margin:0 16px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.bottom-nav{position:fixed;bottom:0;left:0;right:0;z-index:100;background:#fff;border-top:1px solid #eee;display:flex;box-shadow:0 -2px 10px rgba(0,0,0,0.06)}
.bottom-nav a{flex:1;text-align:center;padding:8px 0 6px;text-decoration:none;color:#999;font-size:0.62rem;transition:color .2s}
.bottom-nav a i{display:block;font-size:1.15rem;margin-bottom:2px}
.bottom-nav a.active{color:var(--p)}
</style>
</head>
<body>

<div class="topbar">
    <div class="d-flex align-items-center justify-content-between">
        <h1><i class="fas fa-trophy"></i>Peringkat Referral</h1>
        <a href="{{ route('wholesale.customer.dashboard') }}" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="topbar-user">
        <div class="avatar"><i class="fas fa-user"></i></div>
        <span class="name">{{ $user->name }}</span>
    </div>
</div>

<div class="leader-card">
    @if($myReferralsCount > 0)
        <div class="pos">#{{ $myPosition }}</div>
    @else
        <div class="pos" style="font-size:1.2rem;color:#999">—</div>
    @endif
    <div class="pos-label">Posisi Anda</div>
    <div class="ref-count">{{ $myReferralsCount }} <span class="ref-label">Referral</span></div>
    @if($myReferralsCount == 0)
        <div style="margin-top:10px;font-size:0.82rem;color:#999">Anda belum memiliki referral. Bagikan kode referral Anda ke teman untuk mulai!</div>
        <div class="code-box">{{ $user->referral_code ?? '-' }}</div>
    @endif
</div>

@if($topReferrers->isNotEmpty())
<div class="section-title"><i class="fas fa-crown mr-1" style="color:#F59E0B"></i> Peringkat Tertinggi</div>
<div class="list-card">
@php $medals = [1=>'🥇',2=>'🥈',3=>'🥉']; @endphp
@foreach($topReferrers as $i => $r)
    <div class="rank-item">
        @if(isset($medals[$i+1]))
            <div class="rank-medal">{{ $medals[$i+1] }}</div>
        @else
            <div class="rank-num">#{{ $i+1 }}</div>
        @endif
        <div class="rank-name">{{ $r->name }}</div>
        <div class="rank-count">{{ $r->referrals_count }} referral</div>
    </div>
@endforeach
</div>
@else
<div class="empty-state">
    <i class="fas fa-users"></i>
    Belum ada referral. Jadilah yang pertama!
</div>
@endif

@if($recentOrders->isNotEmpty())
<div class="section-title" style="margin-top:16px"><i class="fas fa-sync-alt mr-1" style="color:var(--p)"></i> Transaksi Terkini</div>
<div class="list-card">
@foreach($recentOrders as $o)
    @php
        $colors = ['pending'=>'#f59e0b','reviewed'=>'#3b82f6','on_progress'=>'#8b5cf6','packed'=>'#6b7280','shipped'=>'#14b8a6'];
        $labels = ['pending'=>'Menunggu Review','reviewed'=>'Dikonfirmasi','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim'];
    @endphp
    <div class="order-feed-item">
        <div class="feed-dot" style="background:{{ $colors[$o->status] ?? '#999' }}"></div>
        <div class="feed-info">
            <span class="feed-status">{{ $labels[$o->status] ?? $o->status }}</span>
            <span class="text-muted"> — {{ $o->invoice_number }}</span>
        </div>
        <div class="feed-time">{{ $o->created_at->diffForHumans() }}</div>
    </div>
@endforeach
</div>
@endif

<div class="bottom-nav">
    <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-home"></i> Beranda</a>
    <a href="{{ route('wholesale.customer.orders') }}"><i class="fas fa-box"></i> Pesanan</a>
    <a href="{{ route('wholesale.customer.history') }}"><i class="fas fa-chart-line"></i> Riwayat</a>
    <a href="{{ route('wholesale.customer.loyalty') }}"><i class="fas fa-star"></i> Loyalty</a>
    <a href="{{ route('wholesale.customer.leaderboard') }}" class="active"><i class="fas fa-trophy"></i> Peringkat</a>
    <a href="#" onclick="event.preventDefault();document.getElementById('lf').submit()"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>
<form id="lf" action="{{ route('wholesale.customer.logout') }}" method="POST" style="display:none">@csrf</form>

</body>
</html>