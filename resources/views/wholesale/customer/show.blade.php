<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Pesanan - {{ $order->invoice_number }} - AL'ASHAR</title>
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
        .topbar h1 { font-size: 1rem; font-weight: 600; flex: 1; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; display: flex; align-items: center; gap: 8px; }
        .topbar h1 i { margin-right: 0; }
        .topbar-logo { height: 32px; border-radius: 6px; }

        .section { margin: 14px 16px; }
        .section-title { font-size: 0.85rem; font-weight: 700; color: #555; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }

        .status-banner {
            background: #fff; border-radius: 12px; padding: 16px; margin: 14px 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); text-align: center;
        }
        .status-banner .badge { font-size: 0.85rem; padding: 6px 18px; }
        .status-banner .inv { font-size: 1.1rem; font-weight: 800; color: var(--p); margin-top: 6px; }
        .status-banner .date { font-size: 0.78rem; color: #999; margin-top: 4px; }

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
            -webkit-overflow-scrolling: touch; scrollbar-width: none; justify-content: center;
        }
        .stepper::-webkit-scrollbar { display: none; }
        .step { flex: 0 0 64px; text-align: center; position: relative; }
        .step:not(:last-child)::after {
            content: ''; position: absolute; top: 14px; left: calc(50% + 17px);
            width: calc(100% - 34px); height: 2.5px; background: #e0e0e0;
        }
        .step.done:not(:last-child)::after { background: #4caf50; }
        .step.active:not(:last-child)::after { background: linear-gradient(90deg, var(--p), #e0e0e0); }
        .step-icon { width: 30px; height: 30px; border-radius: 50%; margin: 0 auto 3px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; }
        .step-icon.done { background: #4caf50; color: #fff; }
        .step-icon.active { background: var(--p); color: #fff; box-shadow: 0 0 0 5px rgba(255,107,53,0.25); }
        .step-icon.pending { background: #e0e0e0; color: #999; }
        .step-icon.cancelled { background: #e53935; color: #fff; }
        .step-label { font-size: 0.6rem; color: #999; white-space: nowrap; font-weight: 500; }
        .step-label.done { color: #4caf50; }
        .step-label.active { color: var(--p); font-weight: 700; }

        .info-card {
            background: #fff; border-radius: 12px; padding: 16px; margin: 0 16px 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item { }
        .info-item .lbl { font-size: 0.68rem; color: #999; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
        .info-item .val { font-size: 0.88rem; color: #222; font-weight: 600; margin-top: 2px; }

        .items-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
        .items-table th { text-align: left; font-size: 0.68rem; color: #999; font-weight: 600; text-transform: uppercase; padding: 8px 4px; border-bottom: 1.5px solid #eee; }
        .items-table td { padding: 10px 4px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        .items-table tr:last-child td { border-bottom: none; }
        .items-table .p-name { font-weight: 600; color: #222; }
        .items-table .p-qty { text-align: center; color: #888; }
        .items-table .p-price { text-align: right; font-weight: 600; color: #333; }
        .items-table .p-subtotal { text-align: right; font-weight: 700; color: var(--p); }

        .total-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0 4px; border-top: 1.5px solid #eee; margin-top: 4px; }
        .total-row .lbl { font-size: 0.82rem; color: #888; }
        .total-row .val { font-size: 1rem; font-weight: 800; color: #222; }
        .total-row.grand .val { font-size: 1.15rem; color: var(--p); }

        .notes-box { background: #fafafa; border-radius: 8px; padding: 12px; margin-top: 10px; font-size: 0.8rem; color: #666; }
        .notes-box strong { color: #444; display: block; margin-bottom: 2px; }

        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;
            background: #fff; border-top: 1px solid #eee;
            display: flex; box-shadow: 0 -2px 10px rgba(0,0,0,0.06);
        }
        .bottom-nav a { flex: 1; text-align: center; padding: 8px 0 6px; text-decoration: none; color: #999; font-size: 0.62rem; }
        .bottom-nav a i { display: block; font-size: 1.15rem; margin-bottom: 2px; }
        .bottom-nav a.active { color: var(--p); }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ url()->previous() }}"><i class="fas fa-arrow-left"></i></a>
        <h1>Detail Pesanan</h1>
    </div>

    {{-- Status Banner --}}
    <div class="status-banner">
        <span class="badge badge-{{ $order->status }}">{{ ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Di-packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'][$order->status] ?? $order->status }}</span>
        <div class="inv">{{ $order->invoice_number }}</div>
        <div class="date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
    </div>

    {{-- Timeline --}}
    <div class="info-card" style="padding:12px 16px">
        @include('wholesale.customer.timeline', ['order' => $order])
    </div>

    {{-- Shipping Info --}}
    <div class="info-card">
        <div style="font-size:0.8rem;font-weight:700;color:#555;margin-bottom:10px"><i class="fas fa-truck mr-1" style="color:var(--p)"></i> Informasi Pengiriman</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="lbl">Penerima</div>
                <div class="val">{{ $order->recipient_name ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="lbl">No. HP</div>
                <div class="val">{{ $order->recipient_phone ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="lbl">Kurir</div>
                <div class="val">{{ $order->shipping_courier ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="lbl">No. Resi</div>
                <div class="val">{{ $order->tracking_number ?? '-' }}</div>
            </div>
            <div class="info-item" style="grid-column:1/-1">
                <div class="lbl">Alamat</div>
                <div class="val" style="font-weight:400;font-size:0.82rem">{{ $order->shipping_address ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="info-card">
        <div style="font-size:0.8rem;font-weight:700;color:#555;margin-bottom:10px"><i class="fas fa-box mr-1" style="color:var(--p)"></i> Item Pesanan ({{ $order->details->count() }})</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:50%">Produk</th>
                    <th class="p-qty" style="width:15%">Qty</th>
                    <th class="p-price" style="width:20%">Harga</th>
                    <th class="p-subtotal" style="width:15%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->details as $d)
                <tr>
                    <td class="p-name">{{ $d->product_name }}</td>
                    <td class="p-qty">{{ $d->quantity }} {{ $d->unit ?? 'pcs' }}</td>
                    <td class="p-price">Rp {{ number_format($d->price, 0, ',', '.') }}</td>
                    <td class="p-subtotal">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total-row">
            <span class="lbl">Subtotal</span>
            <span class="val">Rp {{ number_format($order->details->sum('subtotal'), 0, ',', '.') }}</span>
        </div>
        @if($order->shipping_cost > 0)
        <div class="total-row">
            <span class="lbl">Ongkos Kirim</span>
            <span class="val">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span class="lbl"><strong>Total</strong></span>
            <span class="val">Rp {{ number_format($order->total_amount + ($order->shipping_cost??0), 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Thank You Banner --}}
    @if(in_array($order->status, ['completed', 'delivered']))
    <div class="info-card" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border:1.5px solid #a5d6a7">
        <div style="text-align:center;padding:6px 0">
            <div style="font-size:2rem;margin-bottom:4px">🙏</div>
            <div style="font-size:1rem;font-weight:800;color:#2e7d32">Terima Kasih {{ explode(' ', $user->name)[0] }}!</div>
            <div style="font-size:0.82rem;color:#388e3c;margin-top:6px;line-height:1.5">
                Semoga produk yang Anda terima sesuai dengan harapan dan nyaman digunakan.<br>
                Kami selalu berusaha memberikan yang terbaik untuk Anda.<br>
                <strong>Percayakan selalu kebutuhan parfum Anda pada AL'ASHAR PARFUM.</strong>
            </div>
            <div style="margin-top:10px;font-size:0.78rem;color:#558b2f">
                <i class="fas fa-star"></i><i class="fas fa-star ml-1"></i><i class="fas fa-star ml-1"></i><i class="fas fa-star ml-1"></i><i class="fas fa-star ml-1"></i>
                &nbsp; Sampai jumpa di pesanan berikutnya!
            </div>
        </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($order->notes)
    <div class="info-card">
        <div style="font-size:0.8rem;font-weight:700;color:#555;margin-bottom:6px"><i class="fas fa-sticky-note mr-1" style="color:var(--p)"></i> Catatan</div>
        <div class="notes-box">{{ $order->notes }}</div>
    </div>
    @endif

    {{-- Bottom Nav --}}
    <div class="bottom-nav">
        <a href="{{ route('wholesale.customer.dashboard') }}"><i class="fas fa-home"></i> Beranda</a>
        <a href="{{ route('wholesale.customer.orders') }}" class="active"><i class="fas fa-box"></i> Pesanan</a>
        <a href="{{ route('wholesale.customer.history') }}"><i class="fas fa-chart-line"></i> Riwayat</a>
        <a href="{{ route('wholesale.customer.loyalty') }}"><i class="fas fa-star"></i> Loyalty</a>
        <a href="{{ route('wholesale.customer.leaderboard') }}"><i class="fas fa-trophy"></i> Peringkat</a>
    </div>
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
