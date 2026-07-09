<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Grosir — {{ $order->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #c9a84c;
            --gold-light: #f5e6b8;
            --dark: #1a1a2e;
            --dark-blue: #16213e;
            --accent: #0f3460;
            --text: #2d2d2d;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg-light: #fafaf8;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            font-size: 13px;
            color: var(--text);
            margin: 0;
            padding: 40px;
            background: #f0f0f0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print { display: none !important; }
            .invoice-box { box-shadow: none !important; border: 1px solid #ddd !important; }
            .print-break { page-break-inside: avoid; }
        }

        .no-print {
            text-align: center;
            margin-bottom: 24px;
        }
        .no-print button {
            padding: 12px 32px;
            background: linear-gradient(135deg, var(--dark), var(--accent));
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
            transition: transform 0.2s;
        }
        .no-print button:hover { transform: translateY(-1px); }
        .no-print p { font-size: 11px; color: #888; margin-top: 6px; }

        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d0d0d0;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 0;
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px;
            font-weight: 800;
            color: rgba(201, 168, 76, 0.04);
            pointer-events: none;
            white-space: nowrap;
            font-family: 'Playfair Display', serif;
            letter-spacing: 12px;
            text-transform: uppercase;
        }

        .invoice-inner {
            padding: 48px 44px 36px;
            position: relative;
            z-index: 1;
        }

        /* ── TOP GOLD BAR ── */
        .gold-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--gold), #d4a74a, var(--gold-light), var(--gold));
            margin: 0;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--dark);
            margin-bottom: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid var(--gold);
        }
        .brand-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }
        .brand-text .tagline {
            font-size: 10px;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .brand-text .address-line {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .invoice-meta {
            text-align: right;
        }
        .invoice-meta .doc-type {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--gold);
            margin: 0;
            letter-spacing: 1px;
        }
        .invoice-meta .doc-type span {
            display: inline-block;
            border: 2px solid var(--gold);
            padding: 2px 14px;
            border-radius: 3px;
        }
        .invoice-meta p {
            margin: 2px 0;
            font-size: 12px;
            color: var(--muted);
        }
        .invoice-meta strong {
            color: var(--text);
        }

        /* ── INFO GRID ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
            padding: 20px;
            background: var(--bg-light);
            border: 1px solid var(--border);
            border-radius: 6px;
        }
        .info-block h3 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gold);
            margin: 0 0 8px;
            font-weight: 700;
        }
        .info-block .name {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            margin: 0 0 4px;
        }
        .info-block p {
            margin: 2px 0;
            font-size: 12px;
            color: var(--text);
        }
        .shipping-address {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.4;
            margin-top: 4px;
        }
        .logistics-badge {
            display: inline-block;
            background: #fff;
            border: 1px solid var(--gold);
            color: var(--gold);
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tracking-number {
            display: inline-block;
            background: #f0fdf4;
            border: 1px dashed #22c55e;
            color: #166534;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
            margin-top: 4px;
        }

        /* ── TABLE ── */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        table.items thead th {
            background: var(--dark);
            color: #fff;
            padding: 10px 12px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            border: none;
        }
        table.items thead th:first-child { border-radius: 4px 0 0 0; }
        table.items thead th:last-child { border-radius: 0 4px 0 0; }
        table.items tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }
        table.items tbody tr:hover { background: #f9f9f9; }
        table.items tbody td {
            padding: 10px 12px;
            vertical-align: top;
        }
        table.items .item-name {
            font-weight: 600;
            color: var(--text);
        }
        table.items .item-desc {
            font-size: 11px;
            color: var(--muted);
        }
        .text-right { text-align: right; }

        /* ── TOTALS ── */
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 4px;
        }
        .totals table {
            width: 280px;
            border-collapse: collapse;
        }
        .totals td {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
        }
        .totals .label { color: var(--muted); }
        .totals .value { text-align: right; font-weight: 500; }
        .totals .separator td { padding: 0; }
        .totals .separator div {
            border-top: 1px dashed var(--border);
            margin: 2px 12px;
        }
        .totals .grand td {
            padding: 10px 12px;
            background: var(--dark);
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            border-radius: 0 0 4px 4px;
        }
        .totals .grand .value { font-size: 16px; }

        /* ── QR + FOOTER ── */
        .footer-section {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            margin-top: 10px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            align-items: start;
        }
        .terms {
            font-size: 10px;
            color: var(--muted);
            line-height: 1.6;
        }
        .terms strong {
            font-size: 11px;
            color: var(--text);
            display: block;
            margin-bottom: 6px;
        }
        .terms ol {
            margin: 0;
            padding-left: 14px;
        }
        .terms li { margin-bottom: 3px; }

        .qr-area {
            text-align: center;
            min-width: 140px;
        }
        .qr-area img {
            border-radius: 6px;
            border: 2px solid var(--border);
        }
        .qr-area .qr-label {
            font-size: 9px;
            color: var(--muted);
            margin-top: 4px;
        }
        .qr-area .invoice-ref {
            font-size: 10px;
            color: var(--muted);
            margin-top: 4px;
            word-break: break-all;
        }

        /* ── SIGNATURE ── */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        .signature-block {
            text-align: center;
            font-size: 11px;
            min-height: 140px;
        }
        .signature-block .sig-top {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signature-block .line {
            width: 180px;
            border-top: 1px solid var(--text);
            margin: 0 auto 6px;
        }
        .signature-block .role {
            font-weight: 700;
            color: var(--dark);
        }
        .signature-block .date {
            font-size: 10px;
            color: var(--muted);
        }
        .sig-stamp {
            width: 70px;
            height: 70px;
            border: 2px dashed var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.4;
        }

        .print-meta {
            text-align: center;
            font-size: 9px;
            color: #aaa;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 600px) {
            body { padding: 12px; }
            .invoice-inner { padding: 24px 16px; }
            .header { flex-direction: column; gap: 12px; }
            .invoice-meta { text-align: left; }
            .info-grid { grid-template-columns: 1fr; }
            .footer-section { grid-template-columns: 1fr; }
            .signatures { grid-template-columns: 1fr; gap: 20px; }
            .totals table { width: 100%; }
        }
    </style>
</head>
<body>

    <!-- PRINT BUTTON -->
    <div class="no-print">
        <button onclick="window.print()">🖨️ CETAK / PDF</button>
        <p>Gunakan browser desktop untuk hasil terbaik</p>
    </div>

    <div class="invoice-box">
        <div class="gold-bar"></div>
        <div class="watermark">INVOICE</div>
        <div class="invoice-inner">

            <!-- HEADER -->
            <div class="header">
                <div class="brand">
                    <img src="{{ asset('logotoko.png') }}" alt="AL'ASHAR PARFUM" class="brand-logo">
                    <div class="brand-text">
                        <h1>AL'ASHAR PARFUM</h1>
                        <div class="tagline">Grosir Parfum Refill &bull; Sejak 2018</div>
                        <div class="address-line">Bekasi, West Java &mdash; Indonesia</div>
                    </div>
                </div>
                <div class="invoice-meta">
                    <div class="doc-type"><span>I N V O I C E</span></div>
                    <p>No. <strong>{{ $order->invoice_number }}</strong></p>
                    <p>Tanggal: <strong>{{ $order->created_at->format('d/m/Y') }}</strong></p>
                </div>
            </div>

            <!-- INFO -->
            <div class="info-grid">
                <div class="info-block">
                    <h3>Penerima / Ship To</h3>
                    <div class="name">{{ $order->recipient_name }}</div>
                    <p>{{ $order->recipient_phone }}</p>
                    <div class="shipping-address">{{ $order->shipping_address }}</div>
                </div>
                <div class="info-block">
                    <h3>Pengiriman</h3>
                    <p>Kurir: <strong>{{ $order->shipping_courier ?? 'Internal' }}</strong></p>
                    <p>Biaya Kirim: <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong></p>
                    <p>P. Jawab: {{ $order->handler->name ?? $order->delivery_handler ?? '-' }}</p>
                    <div class="logistics-badge">Estimasi Packing: {{ $order->packing_days ?? 1 }} Hari</div>
                    @if($order->tracking_number)
                    <div class="tracking-number">{{ $order->tracking_number }}</div>
                    @endif
                    @if($order->notes)
                    <p style="margin-top:8px;font-size:11px;color:var(--muted)"><strong>Catatan:</strong><br>{{ $order->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- ITEMS TABLE -->
            <table class="items">
                <thead>
                    <tr>
                        <th style="width:45%">Item / Deskripsi</th>
                        <th class="text-right" style="width:20%">Harga</th>
                        <th class="text-right" style="width:10%">Qty</th>
                        <th class="text-right" style="width:25%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->details as $detail)
                    <tr>
                        <td>
                            <div class="item-name">{{ $detail->product_name }}</div>
                            @if($detail->volume_ml)
                            <div class="item-desc">{{ $detail->volume_ml }} ml</div>
                            @endif
                        </td>
                        <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $detail->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-right" style="color:var(--muted);font-style:italic">Tidak ada item</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- TOTALS -->
            <div class="totals">
                <table>
                    <tr><td class="label">Subtotal</td><td class="value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td></tr>
                    <tr><td class="label">Biaya Kirim</td><td class="value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td></tr>
                    <tr><td class="label">Target Paket</td><td class="value">Rp {{ number_format($order->package_target_amount, 0, ',', '.') }}</td></tr>
                    <tr class="separator"><td colspan="2"><div></div></td></tr>
                    <tr class="grand">
                        <td>TOTAL</td>
                        <td class="value">Rp {{ number_format($order->total_amount + $order->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <!-- QR + TERMS -->
            <div class="footer-section print-break">
                <div class="terms">
                    <strong>KETENTUAN &amp; KESEPAKATAN GROSIR</strong>
                    <ol>
                        <li>Pesanan ini merupakan <strong>kesepakatan grosir</strong> yang mengikat secara hukum antara <strong>AL'ASHAR PARFUM</strong> dan Pembeli.</li>
                        <li>Pembayaran dilakukan sesuai ketentuan yang disepakati <em>sebelum</em> barang dikirimkan.</li>
                        <li>Resiko pengiriman ditanggung pembeli setelah barang diserahkan ke kurir, kecuali ada kesepakatan lain secara tertulis.</li>
                        <li>Barang grosir yang sudah dibeli <strong>tidak dapat ditukar/dikembalikan</strong> kecuali terdapat cacat produksi (klaim maksimal 1×24 jam setelah diterima).</li>
                        <li>Ketidaksesuaian barang harus dilaporkan maksimal 2×24 jam disertai foto/video sebagai bukti.</li>
                        <li>Pembatalan pesanan hanya dapat dilakukan sebelum barang diproses dan dikenakan biaya administrasi 5% dari total pesanan.</li>
                        <li>Dengan melanjutkan pesanan, Pembeli menyetujui seluruh ketentuan yang berlaku di AL'ASHAR PARFUM.</li>
                    </ol>
                </div>
                <div class="qr-area">
                    @php
                        $trackUrl = url('/wholesale-customer/track?invoice_number=' . urlencode($order->invoice_number));
                    @endphp
                    @if($order->barcode)
                    <div style="font-size:30px;font-family:monospace;letter-spacing:2px;margin-bottom:6px;color:var(--muted)">{{ $order->barcode }}</div>
                    @endif
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($trackUrl) }}"
                         alt="QR Track" style="border-radius:4px">
                    <div class="qr-label">Scan untuk lacak pesanan</div>
                    <div class="invoice-ref">{{ $order->invoice_number }}</div>
                </div>
            </div>

            <!-- SIGNATURES -->
            <div class="signatures print-break">
                <div class="signature-block">
                    <div class="sig-top"><div class="sig-stamp">Segel</div></div>
                    <div class="line"></div>
                    <div class="role">{{ $order->handler->name ?? 'Admin Pusat' }}</div>
                    <div class="date">Pihak Penjual &bull; {{ now()->format('d/m/Y') }}</div>
                </div>
                <div class="signature-block">
                    <div class="sig-top"></div>
                    <div class="line"></div>
                    <div class="role">{{ $order->recipient_name }}</div>
                    <div class="date">Pihak Pembeli &bull; {{ $order->created_at->format('d/m/Y') }}</div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="print-meta">
                Dokumen ini dicetak secara elektronik melalui <strong>APMS</strong> pada {{ now()->format('d/m/Y H:i') }}
                &bull; Berlaku tanpa tanda tangan basah &bull; @ashargrosirparfum
            </div>

        </div>
    </div>

</body>
</html>
