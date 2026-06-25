<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: 'Segoe UI',Arial,sans-serif; background: #f4f4f4; padding: 40px;">
<div style="max-width: 600px; margin: auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.1);">
    <div style="background: linear-gradient(135deg, #FF6B35, #E55A2B); padding: 30px; text-align: center;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">📊 Laporan Harian Penjualan</h1>
        <p style="color: rgba(255,255,255,.8); margin: 5px 0 0;">{{ $data['date'] }}</p>
    </div>
    <div style="padding: 30px;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Penjualan Retail</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right;">Rp {{ number_format($data['retail_sales'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Jumlah Transaksi</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right;">{{ $data['retail_count'] }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Penjualan Grosir</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right;">Rp {{ number_format($data['wholesale_sales'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Total Revenue</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; color: #28a745;">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee; color: #666;">Pengeluaran</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold; text-align: right; color: #dc3545;">Rp {{ number_format($data['expenses'], 0, ',', '.') }}</td>
            </tr>
        </table>

        @if(count($data['top_products']) > 0)
        <h3 style="margin-top: 20px; color: #2D3047;">🏆 Produk Terlaris</h3>
        <table style="width: 100%; border-collapse: collapse;">
            @foreach($data['top_products'] as $i => $p)
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $i + 1 }}. {{ $p->name }}</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">{{ $p->total_sold }} pcs</td>
            </tr>
            @endforeach
        </table>
        @endif

        @if(count($data['discrepancies']) > 0)
        <h3 style="margin-top: 20px; color: #dc3545;">⚠️ Selisih Kas</h3>
        <table style="width: 100%; border-collapse: collapse;">
            @foreach($data['discrepancies'] as $d)
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">Shift {{ $d->user_id }} — {{ $d->branch_id }}</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; color: #dc3545;">
                    Rp {{ number_format($d->cash_breakdown['discrepancy'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #999; font-size: 12px; text-align: center;">APMS — Ashar Parfum Management System</p>
    </div>
</div>
</body>
</html>
