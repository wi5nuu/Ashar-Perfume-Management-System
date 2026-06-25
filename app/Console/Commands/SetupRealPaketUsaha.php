<?php

namespace App\Console\Commands;

use App\Models\WholesaleOrder;
use App\Models\User;
use App\Notifications\WholesaleOrderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupRealPaketUsaha extends Command
{
    protected $signature = 'wholesale:setup-real';
    protected $description = 'Setup real customer & update Paket Usaha order with proper data';

    public function handle()
    {
        // 1. Create or find wholesale customer
        $customer = User::where('email', 'rafa.paketusaha@gmail.com')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Rafa Ahmad Fauzi',
                'email' => 'rafa.paketusaha@gmail.com',
                'phone' => '081219091234',
                'password' => Hash::make('password123'),
                'role' => 'wholesale_customer',
                'can_login' => true,
                'is_active' => true,
            ]);
            $this->info('✓ Akun pelanggan dibuat: rafa.paketusaha@gmail.com / password123');
        } else {
            $this->info('✓ Akun pelanggan sudah ada');
        }

        // 2. Update the Paket Usaha order
        $order = WholesaleOrder::where('invoice_number', 'like', 'PAKET-%')->latest()->first();
        if (!$order) {
            $this->error('Pesanan PAKET tidak ditemukan. Jalankan wholesale:seed-paket-usaha dulu.');
            return;
        }

        $order->update([
            'recipient_name' => $customer->name,
            'recipient_phone' => $customer->phone,
            'shipping_address' => 'Perumahan Grand Wisata, Blok A5 No. 12, Kec. Tambun Selatan, Kab. Bekasi, Jawa Barat 17510',
            'notes' => 'PAKET USAHA PARFUM LENGKAP' . "\n" .
                       '100 Aroma Fragrance Oil (40 Pria + 40 Wanita + 20 Unisex) @250ml' . "\n" .
                       'Ethanol 96% 150L + Fixative + Solvent' . "\n" .
                       'Botol 2000 pcs (30ml:500, 50ml:700, 100ml:800)' . "\n" .
                       'Peralatan Lab + Mesin Filling/Crimping/Capping' . "\n" .
                       'Alat QC + Perlengkapan Pendukung',
        ]);

        $this->info('✓ Data pesanan diperbarui dengan data real');
        $this->info('  Penerima: ' . $customer->name);
        $this->info('  Telepon: ' . $customer->phone);
        $this->info('  Alamat: Perumahan Grand Wisata, Blok A5 No. 12, Tambun Selatan, Bekasi');

        // 3. Send notification to customer
        $customer->notify(new WholesaleOrderNotification($order, 'pending'));
        $this->info('✓ Notifikasi "Pesanan Dibuat" terkirim ke pelanggan');

        // 4. Show result
        $this->newLine();
        $this->table(['Item', 'Detail'], [
            ['Invoice', $order->invoice_number],
            ['Pelanggan', $customer->name . ' (' . $customer->email . ')'],
            ['Total Item', $order->details()->count() . ' item'],
            ['Total', 'Rp ' . number_format($order->total_amount, 0, ',', '.')],
            ['Status', $order->status],
            ['Login URL', route('wholesale.customer.login')],
            ['Email', $customer->email],
            ['Password', 'password123'],
        ]);
    }
}
