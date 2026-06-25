<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - Ashar Parfum</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin: 0;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 16px;
            margin-top: 15px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f8f9fa;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #777;
        }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">ASHAR PARFUM</h1>
        <p style="margin: 0; font-size: 9px; color: #777;">Automatic Perfume Management System (APMS)</p>
    </div>

    <div class="report-title">{{ $title }}</div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Stok Saat Ini</th>
                @if($type === 'low_stock')
                    <th>Minimum Stok</th>
                    <th>Status</th>
                @else
                    <th>Tanggal Kadaluarsa</th>
                    <th>Sisa Hari</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td><strong>{{ $item->name }}</strong></td>
                <td>{{ $item->current_stock }}</td>
                @if($type === 'low_stock')
                    <td>{{ $item->minimum_stock }}</td>
                    <td class="text-danger font-weight-bold">SEGERA RESTOCK</td>
                @else
                    <td>{{ \Carbon\Carbon::parse($item->expiration_date)->format('d/m/Y') }}</td>
                    <td class="{{ \Carbon\Carbon::parse($item->expiration_date)->diffInDays(now()) < 7 ? 'text-danger' : 'text-warning' }}">
                        {{ \Carbon\Carbon::parse($item->expiration_date)->diffInDays(now()) }} Hari Lagi
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                    Tidak ada data untuk laporan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
