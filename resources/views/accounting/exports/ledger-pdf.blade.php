<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Buku Besar</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h2 { margin: 0 0 3px; font-size: 15px; }
        .header p { margin: 1px 0; color: #555; }
        .acct-line { margin: 6px 0 2px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th { background: #f0f2f8; text-align: left; padding: 5px 6px; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; }
        td { padding: 4px 6px; border-bottom: 1px solid #eee; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .bold { font-weight: bold; }
        .opening td { background: #fafafa; font-weight: bold; border-top: 1px solid #ccc; }
        .closing td { background: #f8f9ff; font-weight: bold; border-top: 2px solid #333; border-bottom: 3px double #333; }
        .footer { margin-top: 14px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ASHAR GROSIR PARFUME</h2>
        <p>Buku Besar</p>
        <p>Periode: {{ \Carbon\Carbon::parse($data['from'])->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($data['to'])->format('d/m/Y') }}</p>
    </div>

    <div class="acct-line">
        {{ $data['account']->code }} — {{ $data['account']->name }}
        (Normal Balance: {{ strtoupper($data['account']->normal_balance) }})
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:70px">Tanggal</th>
                <th style="width:120px">No. Jurnal</th>
                <th>Keterangan</th>
                <th class="num" style="width:105px">Debit</th>
                <th class="num" style="width:105px">Kredit</th>
                <th class="num" style="width:115px">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr class="opening">
                <td colspan="3">Saldo Awal</td>
                <td></td>
                <td></td>
                <td class="num">{{ number_format($data['opening_balance'], 0, ',', '.') }} {{ $data['opening_balance'] >= 0 ? 'Dr' : 'Cr' }}</td>
            </tr>
            @foreach($data['rows'] as $r)
            <tr>
                <td>{{ \Carbon\Carbon::parse($r['date'])->format('d/m/Y') }}</td>
                <td>{{ $r['journal_number'] }}</td>
                <td>{{ $r['description'] }}</td>
                <td class="num">{{ $r['debit'] > 0 ? number_format($r['debit'], 0, ',', '.') : '' }}</td>
                <td class="num">{{ $r['credit'] > 0 ? number_format($r['credit'], 0, ',', '.') : '' }}</td>
                <td class="num">{{ number_format(abs($r['running_balance']), 0, ',', '.') }} {{ $r['running_balance'] >= 0 ? 'Dr' : 'Cr' }}</td>
            </tr>
            @endforeach
            <tr class="closing">
                <td colspan="3">Saldo Akhir</td>
                <td></td>
                <td></td>
                <td class="num">{{ number_format(abs($data['closing_balance']), 0, ',', '.') }} {{ $data['closing_balance'] >= 0 ? 'Dr' : 'Cr' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">Digenerate: {{ now()->format('d M Y, H:i') }} WIB</div>
</body>
</html>
