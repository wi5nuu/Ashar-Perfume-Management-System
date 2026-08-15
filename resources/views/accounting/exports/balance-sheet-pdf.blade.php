<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Neraca</title>
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
        .total td { border-top: 2px solid #333; border-bottom: 3px double #333; font-weight: bold; background: #fafafa; }
        .status { text-align: center; font-weight: bold; padding: 5px; margin-top: 10px; border-radius: 4px; }
        .ok { color: #2e7d32; border: 1px solid #2e7d32; }
        .err { color: #c62828; border: 1px solid #c62828; }
        .indent { padding-left: 22px !important; }
        .footer { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ASHAR GROSIR PARFUME</h2>
        <p>Neraca (Balance Sheet)</p>
        <p>Per {{ \Carbon\Carbon::parse($data['as_of'])->format('d/m/Y') }}</p>
    </div>

    <table style="width:48%;float:left;margin-right:2%">
        <tr class="section"><td colspan="2">ASET</td></tr>
        @foreach($data['assets'] as $a)
        <tr><td class="indent">{{ $a['code'] }} — {{ $a['name'] }}</td><td class="num">{{ number_format($a['balance'], 0, ',', '.') }}</td></tr>
        @endforeach
        <tr class="total"><td>TOTAL ASET</td><td class="num">{{ number_format($data['total_assets'], 0, ',', '.') }}</td></tr>
    </table>

    <table style="width:48%;float:left">
        <tr class="section"><td colspan="2">KEWAJIBAN &amp; EKUITAS</td></tr>
        @foreach($data['liabilities'] as $l)
        <tr><td class="indent">{{ $l['code'] }} — {{ $l['name'] }}</td><td class="num">{{ number_format($l['balance'], 0, ',', '.') }}</td></tr>
        @endforeach
        <tr class="subtotal"><td>Total Kewajiban</td><td class="num">{{ number_format($data['total_liabilities'], 0, ',', '.') }}</td></tr>
        @foreach($data['equities'] as $e)
        <tr><td class="indent">{{ $e['code'] }} — {{ $e['name'] }}</td><td class="num">{{ number_format($e['balance'], 0, ',', '.') }}</td></tr>
        @endforeach
        <tr><td class="indent">Laba Berjalan</td><td class="num">{{ number_format($data['net_income'], 0, ',', '.') }}</td></tr>
        <tr class="subtotal"><td>Total Ekuitas</td><td class="num">{{ number_format($data['total_equity'], 0, ',', '.') }}</td></tr>
        <tr class="total"><td>TOTAL KEWAJIBAN + EKUITAS</td><td class="num">{{ number_format($data['total_liability_equity'], 0, ',', '.') }}</td></tr>
    </table>

    <div style="clear:both"></div>
    <div class="status {{ $data['is_balanced'] ? 'ok' : 'err' }}">
        {{ $data['is_balanced'] ? 'SEIMBANG — Total Aset = Kewajiban + Ekuitas' : 'TIDAK SEIMBANG' }}
    </div>

    <div class="footer">Digenerate: {{ now()->format('d M Y, H:i') }} WIB</div>
</body>
</html>
