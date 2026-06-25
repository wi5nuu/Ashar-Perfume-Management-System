<?php

namespace App\Console\Commands;

use App\Models\WholesaleProduct;
use App\Models\WholesaleOrder;
use App\Models\WholesaleOrderDetail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeedPaketUsaha extends Command
{
    protected $signature = 'wholesale:seed-paket-usaha';
    protected $description = 'Seed Wholesale Products & create Paket Usaha Parfum order';

    public function handle()
    {
        $this->info('Memasukkan data Paket Usaha Parfum...');

        DB::beginTransaction();
        try {
            // ── 1. CREATE ALL WHOLESALE PRODUCTS ──
            $products = [];

            // Helper
            $add = function ($name, $type, $unit, $price, $pieces = 1, $priceMl = null) use (&$products) {
                $products[] = [
                    'name' => $name,
                    'type' => $type,
                    'unit' => $unit,
                    'pieces_per_unit' => $pieces,
                    'price_per_unit' => $price,
                    'price_per_ml' => $priceMl,
                    'stock' => 999,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            };

            // ── FRAGRANCE OILS (100 Aroma) ──
            // Pria (40)
            $pria = [
                'Creed Aventus','Dior Sauvage EDT','Dior Sauvage Elixir','Bleu de Chanel EDP','Bleu de Chanel Parfum',
                'Acqua di Gio Profumo','Acqua di Gio Profondo','Versace Eros','Versace Dylan Blue','Paco Rabanne Invictus',
                'Paco Rabanne One Million','JPG Ultra Male','Le Male Le Parfum','YSL Y EDP','YSL MYSLF',
                'Stronger With You Absolutely','Stronger With You Intensely','Terre d\'Hermès','Tom Ford Oud Wood','Tom Ford Tobacco Vanille',
                'Tom Ford Ombre Leather','Azzaro Wanted','Azzaro Most Wanted','Boss Bottled','Bvlgari Man In Black',
                'Bvlgari Aqva','Mont Blanc Explorer','Mont Blanc Legend','Davidoff Cool Water','Dior Homme Intense',
                'Givenchy Gentleman','Prada L\'Homme','Prada Luna Rossa Carbon','Armani Code','Bentley Intense',
                'Nautica Voyage','CK One','CK Be','Issey Miyake Pour Homme','Lacoste Blanc',
            ];
            // Wanita (40)
            $wanita = [
                'Good Girl','Libre EDP','Libre Intense','Black Opium','Coco Mademoiselle',
                'Chance Eau Tendre','Chance EDP','Miss Dior Blooming Bouquet','Miss Dior EDP','J\'adore',
                'La Vie Est Belle','Idôle','Si','My Way','Bright Crystal',
                'Crystal Noir','Dylan Purple','Burberry Her','Her Elixir','Delina',
                'Delina Exclusif','Flowerbomb','Mon Paris','Alien','Angel',
                'Gucci Bloom','Flora Gorgeous Gardenia','Daisy','Perfect','Olympéa',
                'Scandal','Narciso Rodriguez For Her','Twilly d\'Hermès','Pure XS For Her','Good Fortune',
                'Bombshell','Love Don\'t Be Shy','Baccarat Rouge 540','L\'Interdit','English Pear & Freesia',
            ];
            // Unisex (20)
            $unisex = [
                'Baccarat Rouge 540','Oud Satin Mood','Oud for Greatness','Erba Pura','Naxos',
                'Another 13','Santal 33','Imagination','Afternoon Swim','L\'immensité',
                'Ombre Nomade','Meteore','Layton','Pegasus','Herod',
                'Carlisle','XJ 1861 Naxos','Alexandria II','Ani','Hacivat',
            ];

            $pricePerMl = 3500; // Rp 3,500/ml untuk fragrance oil
            foreach (array_merge($pria, $wanita, $unisex) as $aroma) {
                $add("Fragrance Oil - $aroma", 'aroma', 'ml', $pricePerMl * 250, 1, $pricePerMl);
            }

            // ── ALCOHOL ──
            $add('Ethanol Cosmetic 96%', 'methanol', 'liter', 120000, 1);
            // ── FIXATIVES ──
            $add('Iso E Super', 'methanol', 'liter', 350000, 1);
            $add('Ambroxan', 'methanol', 'liter', 450000, 1);
            $add('Hedione', 'methanol', 'liter', 250000, 1);
            $add('Galaxolide', 'methanol', 'liter', 200000, 1);
            $add('Cashmeran', 'methanol', 'liter', 400000, 1);
            // ── SOLVENTS ──
            $add('DPG (Dipropylene Glycol)', 'methanol', 'liter', 85000, 1);
            $add('TEC (Triethyl Citrate)', 'methanol', 'liter', 95000, 1);
            $add('IPM (Isopropyl Myristate)', 'methanol', 'liter', 90000, 1);
            $add('Distilled Water', 'methanol', 'liter', 15000, 1);
            // ── BOTTLES ──
            $add('Botol Parfum 30 ml (Kosong)', 'botol', 'pcs', 4500, 1);
            $add('Botol Parfum 50 ml (Kosong)', 'botol', 'pcs', 5500, 1);
            $add('Botol Parfum 100 ml (Kosong)', 'botol', 'pcs', 7500, 1);
            // ── CAPS & ACCESSORIES ──
            $add('Magnetic Cap (Tutup Botol)', 'aksesoris', 'pcs', 1500, 1);
            $add('Spray Pump', 'aksesoris', 'pcs', 1000, 1);
            $add('Collar (Ring Botol)', 'aksesoris', 'pcs', 500, 1);
            $add('Box Parfum Premium', 'aksesoris', 'pcs', 3500, 1);
            $add('Kaleng Premium (Box Besi)', 'aksesoris', 'pcs', 8500, 1);
            // ── PACKAGING ──
            $add('Bubble Wrap (Roll Besar)', 'aksesoris', 'roll', 45000, 1);
            $add('Shrink Wrap (Roll)', 'aksesoris', 'roll', 35000, 1);
            $add('Segel Plastik (Shrink Seal)', 'aksesoris', 'pcs', 200, 1);
            $add('Label Stiker Waterproof (Cetak)', 'aksesoris', 'pcs', 500, 1);
            // ── LAB EQUIPMENT ──
            $add('Timbangan Digital 0,001 g', 'aksesoris', 'unit', 450000, 1);
            $add('Timbangan Digital 0,01 g', 'aksesoris', 'unit', 350000, 1);
            $add('Gelas Ukur 10 ml', 'aksesoris', 'pcs', 25000, 1);
            $add('Gelas Ukur 50 ml', 'aksesoris', 'pcs', 35000, 1);
            $add('Gelas Ukur 100 ml', 'aksesoris', 'pcs', 45000, 1);
            $add('Gelas Ukur 250 ml', 'aksesoris', 'pcs', 55000, 1);
            $add('Gelas Ukur 500 ml', 'aksesoris', 'pcs', 65000, 1);
            $add('Gelas Ukur 1000 ml', 'aksesoris', 'pcs', 80000, 1);
            $add('Beaker Glass 100 ml', 'aksesoris', 'pcs', 30000, 1);
            $add('Beaker Glass 250 ml', 'aksesoris', 'pcs', 40000, 1);
            $add('Beaker Glass 500 ml', 'aksesoris', 'pcs', 55000, 1);
            $add('Beaker Glass 1000 ml', 'aksesoris', 'pcs', 70000, 1);
            $add('Erlenmeyer 250 ml', 'aksesoris', 'pcs', 50000, 1);
            $add('Erlenmeyer 500 ml', 'aksesoris', 'pcs', 65000, 1);
            $add('Erlenmeyer 1000 ml', 'aksesoris', 'pcs', 85000, 1);
            $add('Pipet Pasteur (Pack isi 500)', 'aksesoris', 'pack', 75000, 1);
            $add('Mikropipet 100-1000 µL (Set)', 'aksesoris', 'set', 850000, 1);
            $add('Corong Kaca', 'aksesoris', 'pcs', 20000, 1);
            $add('Corong Stainless', 'aksesoris', 'pcs', 45000, 1);
            $add('Batang Pengaduk Kaca', 'aksesoris', 'pcs', 15000, 1);
            $add('Magnetic Stirrer', 'aksesoris', 'unit', 1500000, 1);
            $add('Hot Plate', 'aksesoris', 'unit', 1200000, 1);
            $add('Magnetic Stir Bar', 'aksesoris', 'pcs', 35000, 1);
            $add('Kertas Saring (100 lembar)', 'aksesoris', 'pack', 25000, 1);
            $add('Botol Amber 250 ml', 'aksesoris', 'pcs', 15000, 1);
            $add('Botol Amber 500 ml', 'aksesoris', 'pcs', 20000, 1);
            $add('Jerigen HDPE 5 L', 'aksesoris', 'pcs', 35000, 1);
            $add('Jerigen HDPE 20 L', 'aksesoris', 'pcs', 75000, 1);
            // ── MACHINES ──
            $add('Mesin Filling Semi Otomatis', 'aksesoris', 'unit', 4500000, 1);
            $add('Mesin Crimping Parfum', 'aksesoris', 'unit', 3500000, 1);
            $add('Mesin Capping Manual', 'aksesoris', 'unit', 1500000, 1);
            // ── QC TOOLS ──
            $add('Alcohol Meter', 'aksesoris', 'unit', 250000, 1);
            $add('Hydrometer', 'aksesoris', 'unit', 200000, 1);
            $add('Refractometer', 'aksesoris', 'unit', 350000, 1);
            $add('pH Meter Digital', 'aksesoris', 'unit', 150000, 1);
            $add('Thermometer Digital', 'aksesoris', 'unit', 75000, 1);
            $add('Hygrometer', 'aksesoris', 'unit', 50000, 1);
            // ── SUPPORTING ──
            $add('Sarung Tangan Nitrile (Box)', 'aksesoris', 'box', 85000, 1);
            $add('Masker Medis (Box)', 'aksesoris', 'box', 35000, 1);
            $add('Hair Cap (Pack)', 'aksesoris', 'pack', 15000, 1);
            $add('Apron Lab', 'aksesoris', 'pcs', 45000, 1);
            $add('Safety Goggles', 'aksesoris', 'pcs', 25000, 1);
            $add('Tisu Bebas Serat (Pack)', 'aksesoris', 'pack', 12000, 1);
            $add('Alkohol Pembersih 70%', 'methanol', 'liter', 25000, 1);

            // Bulk insert all products
            WholesaleProduct::insert($products);
            $this->info('✓ ' . count($products) . ' produk grosir berhasil ditambahkan.');

            // ── 2. CREATE THE ORDER ──
            $allProducts = WholesaleProduct::all()->keyBy('name');

            $order = WholesaleOrder::create([
                'invoice_number' => 'PAKET-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id' => User::where('role', 'owner')->first()?->id ?? 1,
                'package_target_amount' => 50000000,
                'total_amount' => 0,
                'recipient_name' => 'Customer Paket Usaha',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Contoh No. 1, Bekasi',
                'shipping_courier' => 'J&T',
                'packing_days' => 14,
                'status' => 'pending',
                'notes' => 'PAKET USAHA PARFUM LENGKAP - 100 Aroma + Peralatan + Mesin',
            ]);

            $orderItems = [];
            $totalAmount = 0;
            $idx = 0;

            // Helper
            $addItem = function ($name, $qty, $volume = null, $unit = null) use ($allProducts, &$orderItems, &$totalAmount, &$idx) {
                $wp = $allProducts[$name] ?? null;
                if (!$wp) return;
                $price = $wp->price_per_unit;
                $subtotal = $price * $qty;
                $totalAmount += $subtotal;
                $orderItems[] = [
                    'wholesale_order_id' => 0, // will set after save
                    'wholesale_product_id' => $wp->id,
                    'product_name' => $name,
                    'quantity' => $qty,
                    'volume_ml' => $volume,
                    'unit' => $unit ?? $wp->unit,
                    'price' => $price,
                    'price_per_ml' => $wp->price_per_ml,
                    'subtotal' => $subtotal,
                ];
                $idx++;
            };

            // Fragrance Oils (100 x 250ml)
            foreach (array_merge($pria, $wanita, $unisex) as $aroma) {
                $addItem("Fragrance Oil - $aroma", 1, 250, 'ml');
            }

            // Alcohol
            $addItem('Ethanol Cosmetic 96%', 150, null, 'liter');
            // Fixatives
            $addItem('Iso E Super', 2);  $addItem('Ambroxan', 1);
            $addItem('Hedione', 2);       $addItem('Galaxolide', 2);
            $addItem('Cashmeran', 1);
            // Solvents
            $addItem('DPG (Dipropylene Glycol)', 20);
            $addItem('TEC (Triethyl Citrate)', 10);
            $addItem('IPM (Isopropyl Myristate)', 10);
            $addItem('Distilled Water', 20);
            // Bottles
            $addItem('Botol Parfum 30 ml (Kosong)', 500);
            $addItem('Botol Parfum 50 ml (Kosong)', 700);
            $addItem('Botol Parfum 100 ml (Kosong)', 800);
            // Caps & Accessories
            $addItem('Magnetic Cap (Tutup Botol)', 2000);
            $addItem('Spray Pump', 2000);
            $addItem('Collar (Ring Botol)', 2000);
            $addItem('Box Parfum Premium', 2000);
            $addItem('Kaleng Premium (Box Besi)', 500);
            // Packaging
            $addItem('Bubble Wrap (Roll Besar)', 2);
            $addItem('Shrink Wrap (Roll)', 5);
            $addItem('Segel Plastik (Shrink Seal)', 2000);
            $addItem('Label Stiker Waterproof (Cetak)', 2000);
            // Lab Equipment
            $addItem('Timbangan Digital 0,001 g', 1);
            $addItem('Timbangan Digital 0,01 g', 1);
            $addItem('Gelas Ukur 10 ml', 2);   $addItem('Gelas Ukur 50 ml', 2);
            $addItem('Gelas Ukur 100 ml', 2);  $addItem('Gelas Ukur 250 ml', 2);
            $addItem('Gelas Ukur 500 ml', 2);  $addItem('Gelas Ukur 1000 ml', 2);
            $addItem('Beaker Glass 100 ml', 5);  $addItem('Beaker Glass 250 ml', 5);
            $addItem('Beaker Glass 500 ml', 5);  $addItem('Beaker Glass 1000 ml', 5);
            $addItem('Erlenmeyer 250 ml', 3); $addItem('Erlenmeyer 500 ml', 3);
            $addItem('Erlenmeyer 1000 ml', 3);
            $addItem('Pipet Pasteur (Pack isi 500)', 1);
            $addItem('Mikropipet 100-1000 µL (Set)', 1);
            $addItem('Corong Kaca', 5);         $addItem('Corong Stainless', 3);
            $addItem('Batang Pengaduk Kaca', 5);
            $addItem('Magnetic Stirrer', 1);    $addItem('Hot Plate', 1);
            $addItem('Magnetic Stir Bar', 5);
            $addItem('Kertas Saring (100 lembar)', 1);
            $addItem('Botol Amber 250 ml', 50); $addItem('Botol Amber 500 ml', 30);
            $addItem('Jerigen HDPE 5 L', 10);   $addItem('Jerigen HDPE 20 L', 5);
            // Machines
            $addItem('Mesin Filling Semi Otomatis', 1);
            $addItem('Mesin Crimping Parfum', 1);
            $addItem('Mesin Capping Manual', 1);
            // QC
            $addItem('Alcohol Meter', 1);       $addItem('Hydrometer', 1);
            $addItem('Refractometer', 1);       $addItem('pH Meter Digital', 1);
            $addItem('Thermometer Digital', 1); $addItem('Hygrometer', 1);
            // Supporting
            $addItem('Sarung Tangan Nitrile (Box)', 10);
            $addItem('Masker Medis (Box)', 5);  $addItem('Hair Cap (Pack)', 5);
            $addItem('Apron Lab', 3);           $addItem('Safety Goggles', 2);
            $addItem('Tisu Bebas Serat (Pack)', 20);
            $addItem('Alkohol Pembersih 70%', 10);

            // Save order items
            $order->update(['total_amount' => $totalAmount]);

            foreach ($orderItems as &$item) {
                $item['wholesale_order_id'] = $order->id;
            }
            WholesaleOrderDetail::insert($orderItems);

            DB::commit();

            $this->info('✓ Pesanan berhasil dibuat!');
            $this->newLine();
            $this->table(['Item', 'Detail'], [
                ['Invoice', $order->invoice_number],
                ['Jumlah Item', count($orderItems) . ' item'],
                ['Total', 'Rp ' . number_format($totalAmount, 0, ',', '.')],
                ['Status', strtoupper($order->status)],
                ['Link', route('wholesale.show', $order->id)],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('GAGAL: ' . $e->getMessage());
            $this->error('Line: ' . $e->getLine() . ' - File: ' . $e->getFile());
        }
    }
}
