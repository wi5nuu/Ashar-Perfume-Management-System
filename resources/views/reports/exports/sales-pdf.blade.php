<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - Ashar Parfum</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ed1c24;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #ed1c24;
            margin: 0;
            text-transform: uppercase;
        }
        .company-tagline {
            font-size: 10px;
            color: #777;
            margin: 0;
        }
        .report-title {
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .report-period {
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
        }
        .bg-success { background-color: #28a745; }
        .bg-info { background-color: #17a2b8; }
        .summary-box {
            margin-top: 30px;
            float: right;
            width: 250px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fdfdfd;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">ASHAR PARFUM</h1>
        <p class="company-tagline">Automatic Perfume Management System (APMS)</p>
    </div>

    <div class="report-title">Laporan Penjualan</div>
    <div class="report-period">
        Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }} 
        ({{ ucfirst(str_replace('_', ' ', $period)) }})
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $transaction->invoice_number ?? 'TRX-'.$transaction->id }}</td>
                <td>{{ $transaction->customer->name ?? 'Umum' }}</td>
                <td>{{ $transaction->user->name ?? 'System' }}</td>
                <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                    Tidak ada transaksi dalam periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div style="font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; font-weight: bold;">Ringkasan</div>
        <table style="margin-bottom: 0;">
            <tr>
                <td style="border: none; padding: 2px 0;">Total Transaksi:</td>
                <td style="border: none; padding: 2px 0;" class="text-right">{{ $sales->count() }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0; font-size: 14px; font-weight: bold;">TOTAL PENJUALAN:</td>
                <td style="border: none; padding: 2px 0; font-size: 14px; font-weight: bold; color: #ed1c24;" class="text-right">
                    Rp {{ number_format($totalSales, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
