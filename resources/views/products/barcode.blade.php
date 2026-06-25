<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barcode - {{ $product->barcode }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #fff;
        }
        .barcode-container {
            text-align: center;
            padding: 20px;
            border: 1px dashed #ccc;
            width: fit-content;
        }
        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .barcode-wrapper {
            margin: 10px auto;
            display: flex;
            justify-content: center;
        }
        .barcode-text {
            font-size: 16px;
            letter-spacing: 5px;
            font-weight: bold;
            margin-top: 2px;
        }
        .product-price {
            font-size: 14px;
            margin-top: 5px;
        }
        @media print {
            .no-print { display: none; }
            body { background: none; }
            .barcode-container { border: none; }
        }
    </style>
</head>
<body>
    <div class="barcode-container">
        <div class="product-name">{{ $product->name }}</div>
        <div class="barcode-wrapper">
            <img src="{{ route('products.barcode-image', $product) }}" alt="{{ $product->barcode }}" style="height:60px;width:auto;">
        </div>
        <div class="barcode-text">{{ $product->barcode }}</div>
        <div class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
        
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.print()">Cetak Barcode</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>
