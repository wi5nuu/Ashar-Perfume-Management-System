<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class SampleProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $premiumCategory = ProductCategory::where('name', 'Premium')->first();
        $regularCategory = ProductCategory::where('name', 'Regular')->first();
        $refillCategory = ProductCategory::where('name', 'Refill')->first();
        $bundleCategory = ProductCategory::where('name', 'Bundle')->first();

        $products = [
            // Premium Products
            [
                'name' => 'Parfum Premium - Rose Gold',
                'barcode' => 'PRD-001',
                'product_category_id' => $premiumCategory->id,
                'brand' => 'Luxury Fragrance',
                'size' => '100ml',
                'unit' => 'ml',
                'purchase_price' => 500000,
                'selling_price' => 750000,
                'wholesale_price' => 700000,
                'initial_stock' => 20,
                'description' => 'Premium parfum dengan aroma rose gold yang elegan dan tahan lama',
            ],
            [
                'name' => 'Parfum Premium - Vanilla Sky',
                'barcode' => 'PRD-002',
                'product_category_id' => $premiumCategory->id,
                'brand' => 'Luxury Fragrance',
                'size' => '100ml',
                'unit' => 'ml',
                'purchase_price' => 500000,
                'selling_price' => 750000,
                'wholesale_price' => 700000,
                'initial_stock' => 15,
                'description' => 'Aroma vanilla yang hangat dan mewah untuk kesempatan istimewa',
            ],
            // Regular Products
            [
                'name' => 'Parfum Regular - Fresh Citrus',
                'barcode' => 'PRD-003',
                'product_category_id' => $regularCategory->id,
                'brand' => 'Daily Fresh',
                'size' => '50ml',
                'unit' => 'ml',
                'purchase_price' => 150000,
                'selling_price' => 225000,
                'wholesale_price' => 200000,
                'initial_stock' => 50,
                'description' => 'Aroma segar dengan citrus yang membangkitkan semangat',
            ],
            [
                'name' => 'Parfum Regular - Ocean Breeze',
                'barcode' => 'PRD-004',
                'product_category_id' => $regularCategory->id,
                'brand' => 'Daily Fresh',
                'size' => '50ml',
                'unit' => 'ml',
                'purchase_price' => 150000,
                'selling_price' => 225000,
                'wholesale_price' => 200000,
                'initial_stock' => 45,
                'description' => 'Aroma laut yang menyegarkan dan cocok untuk aktivitas sehari-hari',
            ],
            [
                'name' => 'Parfum Regular - Floral Dream',
                'barcode' => 'PRD-005',
                'product_category_id' => $regularCategory->id,
                'brand' => 'Daily Fresh',
                'size' => '50ml',
                'unit' => 'ml',
                'purchase_price' => 150000,
                'selling_price' => 225000,
                'wholesale_price' => 200000,
                'initial_stock' => 40,
                'description' => 'Aroma bunga yang indah dan romantis',
            ],
            // Refill Products
            [
                'name' => 'Refill Parfum - Rose Gold (30ml)',
                'barcode' => 'PRD-006',
                'product_category_id' => $refillCategory->id,
                'brand' => 'Luxury Fragrance',
                'size' => '30ml',
                'unit' => 'ml',
                'purchase_price' => 200000,
                'selling_price' => 300000,
                'wholesale_price' => 280000,
                'initial_stock' => 30,
                'description' => 'Refill untuk parfum premium Rose Gold',
            ],
            [
                'name' => 'Refill Parfum - Fresh Citrus (30ml)',
                'barcode' => 'PRD-007',
                'product_category_id' => $refillCategory->id,
                'brand' => 'Daily Fresh',
                'size' => '30ml',
                'unit' => 'ml',
                'purchase_price' => 70000,
                'selling_price' => 105000,
                'wholesale_price' => 95000,
                'initial_stock' => 60,
                'description' => 'Refill untuk parfum regular Fresh Citrus',
            ],
            // Bundle Products
            [
                'name' => 'Bundle - Premium Duo Pack',
                'barcode' => 'PRD-008',
                'product_category_id' => $bundleCategory->id,
                'brand' => 'Luxury Fragrance',
                'size' => '2x100ml',
                'unit' => 'set',
                'purchase_price' => 900000,
                'selling_price' => 1350000,
                'wholesale_price' => 1250000,
                'initial_stock' => 10,
                'description' => 'Paket hemat berisi 2 botol parfum premium pilihan',
            ],
            [
                'name' => 'Bundle - Regular Mix Pack',
                'barcode' => 'PRD-009',
                'product_category_id' => $bundleCategory->id,
                'brand' => 'Daily Fresh',
                'size' => '3x50ml',
                'unit' => 'set',
                'purchase_price' => 350000,
                'selling_price' => 525000,
                'wholesale_price' => 480000,
                'initial_stock' => 25,
                'description' => 'Paket hemat berisi 3 botol parfum regular berbeda',
            ],
        ];

        foreach ($products as $productData) {
            if (!Product::where('barcode', $productData['barcode'])->exists()) {
                $product = Product::create($productData);

                // Create inventory record
                Inventory::create([
                    'product_id' => $product->id,
                    'current_stock' => $productData['initial_stock'],
                    'minimum_stock' => 5,
                    'cost_per_unit' => $productData['purchase_price'],
                ]);
            }
        }

        $this->command->info('Sample products created successfully!');
    }
}
