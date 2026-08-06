<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Bonus20mlProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua produk tier Biasa yang belum ada versi 20ml-nya
        $biasaProducts = DB::select("
            SELECT p.*
            FROM products p
            JOIN product_categories c ON c.id = p.product_category_id
            WHERE p.is_active = 1
              AND p.is_refill = 0
              AND c.tier = 'biasa'
              AND LOWER(p.size) NOT LIKE '%20ml%'
        ");

        $created = 0;
        $skipped = 0;

        foreach ($biasaProducts as $p) {
            // Buat nama produk 20ml
            $name20 = $p->name . ' (20ml Bonus)';

            // Cek apakah sudah ada
            $exists = DB::table('products')->where('name', $name20)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            // Duplikat produk dengan ukuran 20ml
            $newId = DB::table('products')->insertGetId([
                'name'                => $name20,
                'barcode'             => null,
                'product_category_id' => $p->product_category_id,
                'brand'               => $p->brand ?? null,
                'size'                => '20ml',
                'unit'                => $p->unit ?? 'pcs',
                'purchase_price'      => 0,
                'selling_price'       => 0, // gratis
                'wholesale_price'     => 0,
                'initial_stock'       => 0,
                'image'               => $p->image ?? null,
                'description'         => 'Produk bonus gratis 20ml (duplikat dari ' . $p->name . ')',
                'is_active'           => true,
                'track_inventory'     => true,
                'supplier_id'         => $p->supplier_id ?? null,
                'minimum_stock'       => 0,
                'is_refill'           => false,
                'refill_price_per_ml' => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Buat inventory awal (stok 0) untuk setiap branch yang ada
            $branches = DB::table('branches')->pluck('id');
            foreach ($branches as $branchId) {
                // Cek apakah kolom bulk_stock_ml ada
                $hasBulk = DB::getSchemaBuilder()->hasColumn('inventories', 'bulk_stock_ml');

                $inventoryData = [
                    'product_id'    => $newId,
                    'branch_id'     => $branchId,
                    'current_stock' => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
                if ($hasBulk) {
                    $inventoryData['bulk_stock_ml'] = 0;
                }

                DB::table('inventories')->insert($inventoryData);
            }

            $created++;
        }

        $this->command->info("Selesai! Dibuat: {$created} produk 20ml bonus. Dilewati (sudah ada): {$skipped}.");
    }
}
