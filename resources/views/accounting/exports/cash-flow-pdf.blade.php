<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Arus Kas</title>
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
        .subtotal td { font-weight: bold; background: #fafafa; border-top: 2px solid #333; }
        .positive { color: #2e7d32; }
        .negative { color: #c62828; }
        .indent { padding-left: 22px !important; }
        .footer { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
        .grand td { border-top: 3px double #333; font-weight: bold; background: #fafafa; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ASHAR GROSIR PARFUME</h2>
        <p>Laporan Arus Kas (Metode Langsung)</p>
        <p>Periode: {{ \Carbon\Carbon::parse($data['from'])->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($data['to'])->format('d/m/Y') }}</p>
    </div>

    @foreach($data['sections'] as $key => $section)
    <table>
        <tr class="section"><td colspan="3">{{ $section['label'] }}</td></tr>
        @foreach($section['rows'] as $row)
        <tr>
            <td class="indent" style="width:55%">
                {{ $row['date'] }} — {{ $row['description'] }}
                <div style="font-size:9px;color:#888">{{ $row['journal'] }} · {{ $row['account'] }}</div>
            </td>
            <td class="num positive" style="width:22%">{{ $row['inflow'] > 0 ? number_format($row['inflow'], 0, ',', '.') : '' }}</td>
            <td class="num negative" style="width:23%">{{ $row['outflow'] > 0 ? '(' . number_format($row['outflow'], 0, ',', '.') . ')' : '' }}</td>
        </tr>
        @endforeach
        <tr class="subtotal">
            <td>Kas Bersih {{ $key === 'operating' ? 'Operasi' : ($key === 'investing' ? 'Investasi' : 'Pendanaan') }}</td>
            <td class="num {{ $section['net'] >= 0 ? 'positive' : 'negative' }}">{{ number_format(abs($section['net']), 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </table>
    @endforeach

    <table style="margin-top:12px">
        <tr class="grand">
            <td>KENAIKAN / PENURUNAN BERSIH KAS</td>
            <td class="num {{ $data['net_change'] >= 0 ? 'positive' : 'negative' }}">
                {{ $data['net_change'] >= 0 ? '+' : '-' }} {{ number_format(abs($data['net_change']), 0, ',', '.') }}
            </td>
            <td></td>
        </tr>
    </table>

    <div class="footer">Digenerate: {{ now()->format('d M Y, H:i') }} WIB</div>
</body>
</html>
