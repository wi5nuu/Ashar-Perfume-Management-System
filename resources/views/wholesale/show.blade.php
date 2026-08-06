@extends('layouts.app')
@section('title', 'Detail Pesanan Grosir')

@section('content')
<style>
    :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #2D3047; }

    /* PAGE HEADER */
    .page-header-apms { background: linear-gradient(135deg, var(--secondary) 0%, #3d4266 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; color: #fff; }
    .page-header-apms h1 { font-size: 1.4rem; font-weight: 700; margin: 0 0 4px; color: #fff; }
    .page-header-apms .breadcrumb { background: transparent; margin: 0; padding: 0; font-size: 0.8rem; }
    .page-header-apms .breadcrumb-item a { color: rgba(255,255,255,0.7); text-decoration: none; }
    .page-header-apms .breadcrumb-item.active { color: rgba(255,255,255,0.5); }
    .page-header-apms .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

    /* TIMELINE */
    .order-timeline { display: flex; align-items: flex-start; position: relative; padding: 1.5rem 1rem; overflow-x: auto; }
    .order-timeline::before { content: ''; position: absolute; top: 2.15rem; left: 2rem; right: 2rem; height: 2px; background: #e4e8f0; z-index: 0; }
    .tl-step { flex: 1; text-align: center; position: relative; z-index: 1; min-width: 80px; }
    .tl-step .tl-dot {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 8px;
        font-size: 0.88rem;
        border: 2px solid #e4e8f0;
        background: #fff;
        color: #c0c8d8;
        transition: all 0.2s;
    }
    .tl-step.done .tl-dot { background: #43a047; border-color: #43a047; color: #fff; }
    .tl-step.active .tl-dot { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 0 0 4px rgba(255,107,53,0.2); }
    .tl-step.cancelled .tl-dot { background: #ef5350; border-color: #ef5350; color: #fff; }
    .tl-step .tl-label { font-size: 0.72rem; font-weight: 600; color: #b0b8c9; line-height: 1.3; }
    .tl-step.done .tl-label { color: #43a047; }
    .tl-step.active .tl-label { color: var(--primary); }
    .tl-step.cancelled .tl-label { color: #ef5350; }
    .tl-step .tl-date { font-size: 0.65rem; color: #b0b8c9; margin-top: 2px; }
    .tl-connector { flex: 1; height: 2px; background: #e4e8f0; margin-top: 17px; min-width: 20px; }
    .tl-connector.done { background: #43a047; }

    /* CARDS */
    .detail-card { background: #fff; border-radius: 14px; border: 1px solid #eef0f8; box-shadow: 0 2px 12px rgba(45,48,71,0.06); margin-bottom: 1.25rem; overflow: hidden; }
    .detail-card .dc-header { padding: 0.9rem 1.25rem; border-bottom: 1px solid #f0f2f8; display: flex; align-items: center; gap: 8px; }
    .detail-card .dc-header h5 { font-size: 0.88rem; font-weight: 700; color: var(--secondary); margin: 0; }
    .detail-card .dc-header .dc-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; }
    .detail-card .dc-body { padding: 1.25rem; }

    /* INFO GRID */
    .info-dl { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
    .info-dl .dl-item { padding: 0.65rem 0; border-bottom: 1px solid #f5f6fb; }
    .info-dl .dl-item:nth-last-child(-n+2) { border-bottom: none; }
    .info-dl .dl-item.full { grid-column: 1 / -1; }
    .info-dl .dl-label { font-size: 0.7rem; color: #8892a4; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; font-weight: 600; }
    .info-dl .dl-value { font-size: 0.87rem; color: var(--secondary); font-weight: 500; }

    /* PRODUCT TABLE */
    .prod-table { width: 100%; font-size: 0.84rem; }
    .prod-table thead th { background: #f8f9ff; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #8892a4; padding: 0.7rem 1rem; border-bottom: 2px solid #eef0f8; border-top: none; }
    .prod-table tbody td { padding: 0.85rem 1rem; border-top: 1px solid #f5f6fb; vertical-align: middle; }
    .prod-table tbody tr:last-child td { border-bottom: none; }
    .prod-table tbody tr:hover { background: #fafbff; }

    /* SUMMARY */
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 0.55rem 0; border-bottom: 1px solid #f5f6fb; font-size: 0.85rem; color: #667; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total { padding-top: 0.8rem; font-size: 1rem; font-weight: 800; color: var(--secondary); }
    .summary-row .label { color: #8892a4; }
    .summary-row.discount .amount { color: #d32f2f; }
    .summary-row.total .amount { color: var(--primary); font-size: 1.1rem; }

    /* ACTION BUTTONS */
    .action-bar { display: flex; flex-wrap: wrap; gap: 0.6rem; }
    .btn-action { display: inline-flex; align-items: center; gap: 6px; padding: 0.55rem 1.2rem; border-radius: 9px; font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all 0.15s; border: none; text-decoration: none; }
    .btn-action:hover { transform: translateY(-1px); text-decoration: none; }
    .btn-action.approve { background: linear-gradient(135deg,#43a047,#388e3c); color: #fff; box-shadow: 0 3px 10px rgba(67,160,71,0.3); }
    .btn-action.approve:hover { box-shadow: 0 5px 16px rgba(67,160,71,0.4); color:#fff; }
    .btn-action.process { background: linear-gradient(135deg,#1976d2,#1565c0); color: #fff; box-shadow: 0 3px 10px rgba(25,118,210,0.3); }
    .btn-action.process:hover { box-shadow: 0 5px 16px rgba(25,118,210,0.4); color:#fff; }
    .btn-action.ship { background: linear-gradient(135deg,#00897b,#00695c); color: #fff; box-shadow: 0 3px 10px rgba(0,137,123,0.3); }
    .btn-action.ship:hover { box-shadow: 0 5px 16px rgba(0,137,123,0.4); color:#fff; }
    .btn-action.deliver { background: linear-gradient(135deg,var(--primary),var(--primary-dark)); color: #fff; box-shadow: 0 3px 10px rgba(255,107,53,0.3); }
    .btn-action.deliver:hover { box-shadow: 0 5px 16px rgba(255,107,53,0.4); color:#fff; }
    .btn-action.cancel { background: #fff; color: #d32f2f; border: 1.5px solid #ef9a9a; }
    .btn-action.cancel:hover { background: #fce4ec; color: #d32f2f; }
    .btn-action.back { background: #f5f6fb; color: var(--secondary); border: 1.5px solid #e4e8f0; }
    .btn-action.back:hover { background: #eef0f8; color:var(--secondary); }
    .btn-action.confirm { background: linear-gradient(135deg,#7b1fa2,#6a1b9a); color: #fff; box-shadow: 0 3px 10px rgba(123,31,162,0.3); }
    .btn-action.confirm:hover { box-shadow: 0 5px 16px rgba(123,31,162,0.4); color:#fff; }

    /* STATUS BADGE */
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .status-badge.pending    { background:#fff3e0; color:#e65100; }
    .status-badge.reviewed   { background:#e3f2fd; color:#1565c0; }
    .status-badge.on_progress { background:#e8eaf6; color:#283593; }
    .status-badge.packed     { background:#f3e5f5; color:#6a1b9a; }
    .status-badge.shipped    { background:#e0f2f1; color:#00695c; }
    .status-badge.delivered  { background:#e8f5e9; color:#2e7d32; }
    .status-badge.completed  { background:#e8f5e9; color:#1b5e20; }
    .status-badge.cancelled  { background:#fce4ec; color:#880e4f; }

    /* MODAL */
    .modal-apms .modal-content { border: none; border-radius: 16px; box-shadow: 0 16px 48px rgba(45,48,71,0.2); }
    .modal-apms .modal-header { border-bottom: 1px solid #f0f2f8; padding: 1rem 1.5rem; }
    .modal-apms .modal-body { padding: 1.5rem; }
    .modal-apms .modal-footer { border-top: 1px solid #f0f2f8; padding: 1rem 1.5rem; }

    @media(max-width:575px) {
        .info-dl { grid-template-columns: 1fr; }
        .info-dl .dl-item.full { grid-column: 1; }
        .info-dl .dl-item:nth-last-child(-n+2) { border-bottom: 1px solid #f5f6fb; }
        .info-dl .dl-item:last-child { border-bottom: none; }
        .order-timeline { padding: 1rem 0.5rem; }
        .tl-step { min-width: 60px; }
    }

    /* compat keep old class */
    .status-timeline-modern { display: flex; gap: 0; border-radius: 10px; overflow: hidden; }
    .btn-primary-apms { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff !important; border: none; border-radius: 9px; font-weight: 600; font-size: 0.85rem; padding: 0.55rem 1.2rem; box-shadow: 0 3px 10px rgba(255,107,53,0.3); transition: transform 0.15s, box-shadow 0.15s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary-apms:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(255,107,53,0.4); }
    .form-control { border-radius: 8px; border: 1.5px solid #e4e8f0; font-size: 0.85rem; color: var(--secondary); }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .form-control-sm { border-radius: 7px; }
</style>
<div class="container-fluid pb-4">

    @php
        $statusFlow = ['pending','reviewed','on_progress','packed','shipped','delivered','completed'];
        $flowIndex = array_search($order->status, $statusFlow);
        $labelMap = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Dikemas','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
        $flowLabels = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Dikemas','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai'];
        $flowIcons = ['pending'=>'fa-clock','reviewed'=>'fa-check-double','on_progress'=>'fa-cog','packed'=>'fa-box','shipped'=>'fa-truck','delivered'=>'fa-handshake','completed'=>'fa-check-circle'];
    @endphp

    {{-- Page Header + Status Timeline side by side --}}
    <div class="page-header-apms mb-4">
        <div class="row align-items-center" style="gap:0">

            {{-- Left: Title & Breadcrumb & Buttons --}}
            <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
                <h1 style="font-size:1.25rem;font-weight:700;margin:0 0 4px;color:#fff;">
                    <i class="fas fa-file-invoice mr-2" style="color:var(--primary)"></i>Detail Pesanan Grosir
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background:transparent;margin:0;padding:0;font-size:0.78rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,0.65);text-decoration:none;"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('wholesale.index') }}" style="color:rgba(255,255,255,0.65);text-decoration:none;">Grosir</a></li>
                        <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.45);">{{ $order->invoice_number }}</li>
                    </ol>
                </nav>
                <div class="d-flex flex-wrap mt-3" style="gap:0.4rem;">
                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm" style="background:#25d366;color:#fff;border-radius:8px;font-weight:600;font-size:0.8rem;padding:0.4rem 0.85rem;">
                        <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                    </a>
                    <a href="{{ route('wholesale.print', $order->id) }}" target="_blank" class="btn btn-outline-light btn-sm" style="border-radius:8px;font-weight:600;font-size:0.8rem;">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </a>
                    <a href="{{ route('wholesale.index') }}" class="btn btn-outline-light btn-sm" style="border-radius:8px;font-weight:600;font-size:0.8rem;">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- Right: Status Timeline --}}
            <div class="col-lg-8 col-md-12">
                <div style="background:rgba(255,255,255,0.07);border-radius:12px;padding:0.75rem 1rem 0.5rem;">
                    <div class="d-flex align-items-center justify-content-between mb-2" style="gap:8px;">
                        <span style="font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:0.5px;">
                            <i class="fas fa-route mr-1"></i>Status Pesanan
                        </span>
                        <span class="status-badge {{ $order->status }}">
                            {{ $labelMap[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </div>

                    @if($order->status === 'cancelled')
                        <div style="background:rgba(239,83,80,0.15);border:1px solid rgba(239,83,80,0.3);border-radius:8px;padding:0.5rem 0.85rem;font-size:0.8rem;color:#ffcdd2;font-weight:600;">
                            <i class="fas fa-ban mr-2"></i>Pesanan dibatalkan
                            @if($order->cancellation_reason) — {{ $order->cancellation_reason }} @endif
                        </div>
                    @else
                        {{-- Timeline steps --}}
                        <div class="order-timeline px-2" style="padding-top:0.5rem;padding-bottom:0.25rem;">
                            @foreach($statusFlow as $i => $step)
                                @php
                                    $cls = 'upcoming';
                                    if ($flowIndex !== false) {
                                        if ($i < $flowIndex) $cls = 'done';
                                        elseif ($i === $flowIndex) $cls = 'active';
                                    }
                                @endphp
                                @if($i > 0)
                                <div class="tl-connector {{ $i <= $flowIndex ? 'done' : '' }}"></div>
                                @endif
                                <div class="tl-step {{ $cls }}">
                                    <div class="tl-dot">
                                        @if($cls === 'done')<i class="fas fa-check"></i>
                                        @else<i class="fas {{ $flowIcons[$step] }}"></i>@endif
                                    </div>
                                    <div class="tl-label">{{ $flowLabels[$step] }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Timestamp strip --}}
                        <div class="d-flex flex-wrap mt-1" style="gap:5px;">
                            @if($order->confirmed_at)<span style="background:rgba(255,255,255,0.1);border-radius:5px;padding:2px 8px;font-size:0.67rem;color:rgba(255,255,255,0.65);display:inline-flex;align-items:center;gap:3px;"><i class="fas fa-check" style="font-size:0.6rem;color:var(--primary)"></i>Konfirmasi {{ $order->confirmed_at->format('d/m H:i') }}</span>@endif
                            @if($order->packed_at)<span style="background:rgba(255,255,255,0.1);border-radius:5px;padding:2px 8px;font-size:0.67rem;color:rgba(255,255,255,0.65);display:inline-flex;align-items:center;gap:3px;"><i class="fas fa-box" style="font-size:0.6rem;color:#ce93d8;"></i>Kemas {{ $order->packed_at->format('d/m H:i') }}</span>@endif
                            @if($order->shipped_at)<span style="background:rgba(255,255,255,0.1);border-radius:5px;padding:2px 8px;font-size:0.67rem;color:rgba(255,255,255,0.65);display:inline-flex;align-items:center;gap:3px;"><i class="fas fa-truck" style="font-size:0.6rem;color:#80cbc4;"></i>Kirim {{ $order->shipped_at->format('d/m H:i') }}</span>@endif
                            @if($order->delivered_at)<span style="background:rgba(255,255,255,0.1);border-radius:5px;padding:2px 8px;font-size:0.67rem;color:rgba(255,255,255,0.65);display:inline-flex;align-items:center;gap:3px;"><i class="fas fa-handshake" style="font-size:0.6rem;color:#a5d6a7;"></i>Terima {{ $order->delivered_at->format('d/m H:i') }}</span>@endif
                            @if($order->completed_at)<span style="background:rgba(67,160,71,0.2);border-radius:5px;padding:2px 8px;font-size:0.67rem;color:#a5d6a7;display:inline-flex;align-items:center;gap:3px;"><i class="fas fa-check-circle" style="font-size:0.6rem;"></i>Selesai {{ $order->completed_at->format('d/m H:i') }}</span>@endif
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        {{-- LEFT: Items + Summary --}}
        <div class="col-lg-8">
            {{-- Order Items --}}
            <div class="detail-card">
                <div class="dc-header">
                    <div class="dc-icon" style="background:rgba(25,118,210,0.1);color:#1976d2"><i class="fas fa-shopping-cart"></i></div>
                    <h5>Item Pesanan</h5>
                    <span class="ml-auto" style="font-size:0.78rem;color:#8892a4;background:#f5f6fb;border-radius:6px;padding:2px 8px;">{{ $order->details->count() }} produk</span>
                </div>
                <div class="table-responsive">
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th style="width:40%">Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Volume</th>
                                <th class="text-right">Harga/pcs</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->details as $detail)
                            <tr>
                                <td>
                                    <div style="font-weight:600;color:var(--secondary)">{{ $detail->product_name }}</div>
                                    @if($detail->unit && $detail->unit !== 'pcs')
                                    <div style="font-size:0.72rem;color:#8892a4">{{ $detail->unit }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span style="font-weight:700;color:var(--secondary)">{{ $detail->quantity }}</span>
                                </td>
                                <td class="text-center" style="color:#8892a4">
                                    {{ $detail->volume_ml ? $detail->volume_ml . ' ml' : '-' }}
                                </td>
                                <td class="text-right">
                                    <div style="font-weight:500">Rp {{ number_format($detail->price, 0, ',', '.') }}</div>
                                    @if($detail->price_per_ml)
                                    <div style="font-size:0.7rem;color:#8892a4">{{ number_format($detail->price_per_ml,0) }}/ml</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span style="font-weight:700;color:var(--secondary)">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Summary --}}
                <div class="dc-body" style="border-top:2px solid #f0f2f8">
                    <div class="row">
                        <div class="col-md-5 offset-md-7">
                            @php
                                $subtotal = $order->details->sum('subtotal');
                                $discount = $order->discount_amount ?? 0;
                                $shipping = $order->shipping_cost ?? 0;
                                $total = $order->total_amount ?? ($subtotal - $discount + $shipping);
                            @endphp
                            <div class="summary-row">
                                <span class="label">Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($discount > 0)
                            <div class="summary-row discount">
                                <span class="label">Diskon Grosir</span>
                                <span class="amount">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($shipping > 0)
                            <div class="summary-row">
                                <span class="label">Ongkos Kirim</span>
                                <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="summary-row total">
                                <span>Total</span>
                                <span class="amount">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Info + Actions --}}
        <div class="col-lg-4">

            {{-- Customer Info --}}
            <div class="detail-card">
                <div class="dc-header">
                    <div class="dc-icon" style="background:rgba(0,137,123,0.1);color:#00897b"><i class="fas fa-user"></i></div>
                    <h5>Informasi Pelanggan</h5>
                </div>
                <div class="dc-body">
                    <div class="info-dl">
                        <div class="dl-item full">
                            <div class="dl-label">Penerima</div>
                            <div class="dl-value" style="font-size:1rem;font-weight:700">{{ $order->recipient_name }}</div>
                        </div>
                        <div class="dl-item">
                            <div class="dl-label">Telepon</div>
                            <div class="dl-value">{{ $order->recipient_phone }}</div>
                        </div>
                        <div class="dl-item">
                            <div class="dl-label">Kurir</div>
                            <div class="dl-value">{{ $order->shipping_courier ?? '-' }}</div>
                        </div>
                        <div class="dl-item full">
                            <div class="dl-label">Alamat Pengiriman</div>
                            <div class="dl-value" style="font-size:0.82rem;line-height:1.5">{{ $order->shipping_address }}</div>
                        </div>
                        @if($order->tracking_number)
                        <div class="dl-item full">
                            <div class="dl-label">No. Resi</div>
                            <div class="dl-value" style="font-weight:700;color:var(--primary);font-size:0.95rem">{{ $order->tracking_number }}</div>
                        </div>
                        @endif
                        <div class="dl-item">
                            <div class="dl-label">Dibuat</div>
                            <div class="dl-value">{{ $order->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="dl-item">
                            <div class="dl-label">Oleh</div>
                            <div class="dl-value">{{ $order->user->name ?? 'System' }}</div>
                        </div>
                        @if($order->notes)
                        <div class="dl-item full">
                            <div class="dl-label">Catatan</div>
                            <div class="dl-value" style="font-size:0.82rem;color:#667">{{ $order->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Card --}}
            <div class="detail-card">
                <div class="dc-header">
                    <div class="dc-icon" style="background:rgba(255,107,53,0.1);color:var(--primary)"><i class="fas fa-tasks"></i></div>
                    <h5>Tindakan</h5>
                </div>
                <div class="dc-body">
                    @if($order->status === 'pending')
                        <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.82rem;color:#e65100;margin-bottom:1rem;">
                            <i class="fas fa-clock mr-1"></i> Menunggu konfirmasi admin
                        </div>
                        <div class="action-bar">
                            <button type="button" class="btn-action confirm" data-toggle="modal" data-target="#confirmModal">
                                <i class="fas fa-check-double"></i> Konfirmasi Pesanan
                            </button>
                            @can('wholesale.manage')
                            <a href="{{ route('wholesale.edit', $order->id) }}" class="btn-action" style="background:#fff8e1;color:#f57f17;border-color:#ffe082;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @endcan
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('wholesale.cancel', $order->id) }}" method="POST">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" name="cancellation_reason" class="form-control" placeholder="Alasan pembatalan..." required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan pesanan ini?')">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    @elseif($order->status === 'reviewed')
                        <div style="background:#e3f2fd;border:1px solid #90caf9;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.82rem;color:#1565c0;margin-bottom:1rem;">
                            <i class="fas fa-check-circle mr-1"></i> Pesanan sudah dikonfirmasi, siap diproses
                        </div>
                        <form action="{{ route('wholesale.process', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action process w-100">
                                <i class="fas fa-play"></i> Mulai Proses
                            </button>
                        </form>

                    @elseif($order->status === 'on_progress')
                        <div style="background:#e8eaf6;border:1px solid #9fa8da;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.82rem;color:#283593;margin-bottom:1rem;">
                            <i class="fas fa-cog fa-spin mr-1"></i> Sedang diproses
                        </div>
                        <form action="{{ route('wholesale.pack', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase">Penanggung Jawab</label>
                                <select name="handler_id" class="form-control form-control-sm">
                                    <option value="">Pilih Staff</option>
                                    @foreach($handlers as $h)
                                    <option value="{{ $h->id }}" {{ $order->handler_id == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-action w-100" style="background:linear-gradient(135deg,#7b1fa2,#6a1b9a);color:#fff;justify-content:center;">
                                <i class="fas fa-box"></i> Selesai Packing
                            </button>
                        </form>

                    @elseif($order->status === 'packed')
                        <div style="background:#f3e5f5;border:1px solid #ce93d8;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.82rem;color:#6a1b9a;margin-bottom:1rem;">
                            <i class="fas fa-box mr-1"></i> Barang sudah dikemas, siap dikirim
                        </div>
                        <form action="{{ route('wholesale.ship', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase">Kurir</label>
                                <input type="text" name="shipping_courier" class="form-control form-control-sm" value="{{ $order->shipping_courier }}" placeholder="J&T, Sicepat, dll">
                            </div>
                            <div class="form-group">
                                <label style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase">Biaya Kirim (Rp)</label>
                                <input type="number" name="shipping_cost" class="form-control form-control-sm" value="{{ $order->shipping_cost }}" min="0">
                            </div>
                            <div class="form-group">
                                <label style="font-size:0.75rem;font-weight:600;color:#8892a4;text-transform:uppercase">No. Resi</label>
                                <input type="text" name="tracking_number" class="form-control form-control-sm" value="{{ $order->tracking_number }}" placeholder="JP0000123456">
                            </div>
                            <button type="submit" class="btn-action ship w-100" style="justify-content:center;">
                                <i class="fas fa-truck"></i> Tandai Dikirim
                            </button>
                        </form>

                    @elseif($order->status === 'shipped')
                        <div style="background:#e0f2f1;border:1px solid #80cbc4;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.82rem;color:#00695c;margin-bottom:1rem;">
                            <i class="fas fa-truck mr-1"></i> Dalam perjalanan{{ $order->shipping_courier ? ' via ' . $order->shipping_courier : '' }}
                        </div>
                        <form action="{{ route('wholesale.deliver', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action deliver w-100" style="justify-content:center;">
                                <i class="fas fa-handshake"></i> Tandai Diterima
                            </button>
                        </form>

                    @elseif($order->status === 'delivered')
                        <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.82rem;color:#2e7d32;margin-bottom:1rem;">
                            <i class="fas fa-check-circle mr-1"></i> Sudah diterima pelanggan
                        </div>
                        <form action="{{ route('wholesale.complete', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action approve w-100" style="justify-content:center;">
                                <i class="fas fa-check-double"></i> Selesaikan Pesanan
                            </button>
                        </form>

                    @elseif($order->status === 'completed')
                        <div class="text-center py-3">
                            <div style="width:56px;height:56px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.5rem;color:#43a047">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="font-weight:700;color:#2e7d32;font-size:1rem">Pesanan Selesai</div>
                            <div style="font-size:0.78rem;color:#8892a4;margin-top:4px">
                                {{ $order->completed_at ? $order->completed_at->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>

                    @elseif($order->status === 'cancelled')
                        <div class="text-center py-3">
                            <div style="width:56px;height:56px;border-radius:50%;background:#fce4ec;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.5rem;color:#e53935">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div style="font-weight:700;color:#c62828;font-size:1rem">Dibatalkan</div>
                            @if($order->cancellation_reason)
                            <div style="font-size:0.78rem;color:#8892a4;margin-top:4px">{{ $order->cancellation_reason }}</div>
                            @endif
                        </div>
                    @endif

                    {{-- Cancel for in-progress orders --}}
                    @if(in_array($order->status, ['reviewed','on_progress','packed']))
                    <div class="mt-3 pt-3" style="border-top:1px dashed #f0f2f8">
                        <form action="{{ route('wholesale.cancel', $order->id) }}" method="POST">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="text" name="cancellation_reason" class="form-control" placeholder="Alasan pembatalan..." required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin batalkan pesanan?')">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

        </div>{{-- /RIGHT --}}
    </div>{{-- /row --}}
</div>

{{-- Confirm Modal --}}
<div class="modal fade modal-apms" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('wholesale.confirm', $order->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-double mr-2" style="color:var(--primary)"></i>Konfirmasi Pesanan
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,107,53,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:var(--primary)">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6 style="font-weight:700;color:var(--secondary);margin-bottom:6px">Konfirmasi pesanan ini?</h6>
                <p style="font-size:0.84rem;color:#8892a4;margin:0">Stok gudang akan terpotong sesuai jumlah pesanan.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn-action back" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn-action confirm">
                    <i class="fas fa-check"></i> Ya, Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection