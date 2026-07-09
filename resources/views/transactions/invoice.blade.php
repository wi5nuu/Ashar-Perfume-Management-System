<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
            background: #fff;
            margin: 0;
            padding: 10px;
            width: 80mm; /* Standard Thermal Receipt Width */
        }
        .receipt-container {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .store-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .store-info {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-table {
            width: 100%;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .items-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .totals-container {
            margin-top: 5px;
            font-size: 11px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 13px;
            margin: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 0;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        .qrcode-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 15px 0;
        }
        #qrcode img {
            margin: 0 auto;
            border: 4px solid #fff;
        }
        .qr-placeholder {
            text-align: center;
            margin: 5px 0;
            font-size: 8px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-2" style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #FF6B35; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak Struk
        </button>
    </div>
    <div class="receipt-container">
        <!-- Store Header -->
        <div class="header">
            <div class="store-name">ASHAR GROSIR PARFUM</div>
            <div class="store-info">
                {{ $transaction->branch->address ?? 'Bekasi, Indonesia' }}<br>
                Telp: 081251026345<br>
                www.ashargrosirparfum.com
            </div>
        </div>

        <div class="separator"></div>

        <!-- Transaction Details -->
        <table class="info-table">
            <tr>
                <td>Tgl : {{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-right">No : {{ substr($transaction->invoice_number, -8) }}</td>
            </tr>
            <tr>
                <td>Kasir : {{ $transaction->user?->nickname ?? $transaction->user?->name ?? '-' }}</td>
                <td class="text-right">Plgn: {{ $transaction->customer?->name ?? 'Umum' }}</td>
            </tr>
        </table>

        <div class="separator"></div>

        <!-- Items -->
        <table class="items-table">
            @foreach($transaction->details as $detail)
            <tr>
                <td colspan="3">
                    {{ $detail->product->name }} 
                    @if($detail->price == 0)
                        (20ml Bonus)
                    @elseif(stripos($detail->product->name, 'ml') === false && $detail->product->size)
                        ({{ $detail->product->size }})
                    @endif
                </td>
            </tr>
            <tr>
                <td width="45%">
                    {{ $detail->quantity }} x 
                    @if($detail->price == 0)
                        <del style="font-size: 8px;">Rp 35.000</del> Rp 0
                    @else
                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                    @endif
                </td>
                <td width="15%">
                    @if($detail->price == 0)
                        <span style="font-size: 8px; font-weight: bold;">(FREE)</span>
                    @endif
                </td>
                <td class="text-right">
                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @if($detail->bonus_quantity > 0)
            <tr>
                <td colspan="3" style="font-size: 9px; padding-left: 10px;">
                    * Extra Bonus: {{ $detail->bonus_quantity }} botol 20ml <br>
                    &nbsp;&nbsp;(Harga: <del>Rp 35.000</del> -> Rp 0)
                </td>
            </tr>
            @endif
            @endforeach
        </table>

        <div class="separator"></div>

        <!-- Totals -->
        <div class="totals-container">
            <div class="total-row">
                <span>Subtotal</span>
                <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->discount > 0)
            <div class="total-row">
                <span>Diskon</span>
                <span>-{{ number_format($transaction->discount, 0, ',', '.') }}</span>
            </div>
            @endif

            @if($transaction->tax_amount > 0)
            <div class="total-row">
                <span>PPN (10%)</span>
                <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="total-row">
                <span>Bayar ({{ strtoupper($transaction->payment_method) }})</span>
                <span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>

            <div class="total-row">
                <span>Kembali</span>
                <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="separator"></div>

        <!-- QR Code Section -->
        <div class="qrcode-wrapper">
            <div id="qrcode"></div>
            <div style="font-size: 9px; font-weight: bold; margin-top: 5px;">SCAN UNTUK STRUK DIGITAL</div>
            <div style="font-size: 8px;">{{ $transaction->invoice_number }}</div>
        </div>

        <div class="footer">
            --- TERIMA KASIH ---<br>
            Barang yang sudah dibeli<br>
            tidak dapat ditukar/dikembalikan<br>
            <br>
            Layanan Konsumen:<br>
            WA: 081251026345
        </div>

        <div class="qr-placeholder">
            * Simpan struk ini sebagai bukti pembelian sah *
        </div>
    </div>

    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            function generateQRCode() {
                const qrContainer = document.getElementById("qrcode");
                qrContainer.innerHTML = "";
                new QRCode(qrContainer, {
                    text: "{{ route('transactions.public_invoice', $transaction->invoice_number) }}",
                    width: 100,
                    height: 100,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            }
            generateQRCode();
            if (window.location.search.includes('print')) {
                window.print();
            }
        });
    </script>
</body>
</html>
