<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: 'Segoe UI',Arial,sans-serif; background: #f4f4f4; padding: 40px;">
<div style="max-width: 600px; margin: auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.1);">
    <div style="background: linear-gradient(135deg, #FF6B35, #E55A2B); padding: 30px; text-align: center;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">📈 Laporan Mingguan Penjualan</h1>
        <p style="color: rgba(255,255,255,.8); margin: 5px 0 0;">{{ $data['start_date'] }} — {{ $data['end_date'] }}</p>
    </div>
    <div style="padding: 30px;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Total Revenue</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; color: #28a745;">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">HPP (COGS)</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right;">Rp {{ number_format($data['cogs'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Pengeluaran</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; color: #dc3545;">Rp {{ number_format($data['expenses'], 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #f8f9fa;">
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold;">Laba Bersih</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; color: {{ $data['net_profit'] >= 0 ? '#28a745' : '#dc3545' }};">Rp {{ number_format($data['net_profit'], 0, ',', '.') }}</td>
            </tr>
        </table>

        @if(count($data['low_stock']) > 0)
        <h3 style="margin-top: 20px; color: #dc3545;">📦 Peringatan Stok Rendah</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 8px; text-align: left; font-size: 13px;">Produk</th>
                    <th style="padding: 8px; text-align: center; font-size: 13px;">Stok</th>
                    <th style="padding: 8px; text-align: center; font-size: 13px;">Min</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['low_stock'] as $item)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $item->name }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center; color: #dc3545; font-weight: bold;">{{ $item->current_stock }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">{{ $item->minimum_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #999; font-size: 12px; text-align: center;">APMS — Ashar Parfum Management System</p>
    </div>
</div>
</body>
</html>
