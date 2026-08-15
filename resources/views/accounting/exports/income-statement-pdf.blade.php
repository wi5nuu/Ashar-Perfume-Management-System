<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laba Rugi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 14px; }
        .header h2 { margin: 0 0 3px; font-size: 16px; }
        .header p { margin: 1px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th { background: #f0f2f8; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .section td { background: #f8f9ff; font-weight: bold; text-transform: uppercase; font-size: 10px; letter-spacing: .5px; padding: 7px 8px; border-left: 3px solid #2D3047; }
        .subtotal td { font-weight: bold; background: #fafafa; }
        .gross td { font-weight: bold; background: #e8f5e9; border-top: 2px solid #a5d6a7; }
        .net td { font-weight: bold; background: #e8f5e9; border-top: 3px double #2e7d32; font-size: 13px; }
        .net-neg td { background: #fce4ec; border-top: 3px double #c62828; }
        .indent { padding-left: 22px !important; }
        .footer { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ASHAR GROSIR PARFUME</h2>
        <p>Laporan Laba Rugi</p>
        <p>Periode: {{ \Carbon\Carbon::parse($data['from'])->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($data['to'])->format('d/m/Y') }}</p>
    </div>

    <table>
        <tr class="section"><td colspan="2">PENDAPATAN USAHA</td></tr>
        @foreach($data['revenue'] as $r)
        <tr><td class="indent">{{ $r['code'] }} — {{ $r['name'] }}</td><td class="num">{{ number_format($r['balance'], 0, ',', '.') }}</td></tr>
        @endforeach
        <tr class="subtotal"><td>Total Pendapatan</td><td class="num">{{ number_format($data['total_revenue'], 0, ',', '.') }}</td></tr>

        <tr class="section"><td colspan="2">HARGA POKOK PENJUALAN (HPP)</td></tr>
        <tr><td class="indent">Harga Pokok Penjualan</td><td class="num">{{ number_format($data['cogs'], 0, ',', '.') }}</td></tr>

        <tr class="gross"><td>LABA KOTOR</td><td class="num">{{ number_format($data['gross_profit'], 0, ',', '.') }}</td></tr>

        <tr class="section"><td colspan="2">BEBAN OPERASIONAL</td></tr>
        @foreach($data['expenses'] as $e)
        <tr><td class="indent">{{ $e['code'] }} — {{ $e['name'] }}</td><td class="num">{{ number_format($e['balance'], 0, ',', '.') }}</td></tr>
        @endforeach
        <tr class="subtotal"><td>Total Beban Operasional</td><td class="num">{{ number_format($data['total_expense'], 0, ',', '.') }}</td></tr>

        <tr class="gross"><td>LABA OPERASIONAL</td><td class="num">{{ number_format($data['operating_profit'], 0, ',', '.') }}</td></tr>

        <tr class="{{ $data['net_income'] >= 0 ? 'net' : 'net net-neg' }}">
            <td>{{ $data['net_income'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</td>
            <td class="num">{{ number_format($data['net_income'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">Digenerate: {{ now()->format('d M Y, H:i') }} WIB</div>
</body>
</html>
