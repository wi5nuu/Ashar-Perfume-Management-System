<!DOCTYPE html>
<html>
<head>
    <title>Daftar Produk - APMS</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f2f2; color: #333; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8pt; color: #999; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8pt; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ASHAR PARFUM MANAGEMENT SYSTEM (APMS)</h2>
        <p>Laporan Daftar Produk</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-center">Ukuran</th>
                <th class="text-center">Stok</th>
                <th class="text-right">Harga Jual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->internal_id ?? $product->barcode }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td class="text-center">{{ $product->size }} {{ $product->unit }}</td>
                <td class="text-center">{{ $product->inventories->first()->current_stock ?? 0 }}</td>
                <td class="text-right">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis pada {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
