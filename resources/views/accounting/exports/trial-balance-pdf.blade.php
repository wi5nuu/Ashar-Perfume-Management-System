<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Trial Balance</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 14px; }
        .header h2 { margin: 0 0 3px; font-size: 16px; }
        .header p { margin: 1px 0; color: #555; }
        .title { font-weight: bold; font-size: 13px; margin: 8px 0 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f0f2f8; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .bold { font-weight: bold; }
        .total td { border-top: 2px solid #333; border-bottom: 3px double #333; font-weight: bold; background: #fafafa; }
        .status { text-align: center; font-weight: bold; padding: 5px; margin-top: 10px; border-radius: 4px; }
        .ok { color: #2e7d32; border: 1px solid #2e7d32; }
        .err { color: #c62828; border: 1px solid #c62828; }
        .footer { margin-top: 16px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ASHAR GROSIR PARFUME</h2>
        <p>Neraca Saldo (Trial Balance)</p>
        <p>Periode: {{ $data['from'] ?? 'Awal' }} s/d {{ $data['to'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:90px">Kode</th>
                <th>Nama Akun</th>
                <th class="num" style="width:130px">Debit</th>
                <th class="num" style="width:130px">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $r)
            <tr>
                <td>{{ $r['code'] }}</td>
                <td>{{ $r['name'] }}</td>
                <td class="num">{{ $r['debit'] > 0 ? number_format($r['debit'], 0, ',', '.') : '-' }}</td>
                <td class="num">{{ $r['credit'] > 0 ? number_format($r['credit'], 0, ',', '.') : '-' }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="2">TOTAL</td>
                <td class="num">{{ number_format($data['total_debit'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($data['total_credit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="status {{ $data['is_balanced'] ? 'ok' : 'err' }}">
        {{ $data['is_balanced'] ? 'SEIMBANG — Total Debit = Total Kredit' : 'TIDAK SEIMBANG — Selisih ' . number_format(abs($data['total_debit'] - $data['total_credit']), 0, ',', '.') }}
    </div>

    <div class="footer">Digenerate: {{ now()->format('d M Y, H:i') }} WIB</div>
</body>
</html>
