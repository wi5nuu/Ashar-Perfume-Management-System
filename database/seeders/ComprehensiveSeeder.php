<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Inventory;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name' => 'Premium Parfume', 'color' => '#FF6B6B', 'description' => 'Parfum premium terbaik'],
            ['name' => 'Casual Parfume', 'color' => '#4ECDC4', 'description' => 'Parfum casual sehari-hari'],
            ['name' => 'Eau de Toilette', 'color' => '#95E1D3', 'description' => 'Eau de toilette berkualitas'],
            ['name' => 'Body Spray', 'color' => '#FFD93D', 'description' => 'Body spray harum'],
            ['name' => 'Perfume Set', 'color' => '#A8D8EA', 'description' => 'Paket perfume lengkap'],
        ];

        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 2. Create 20+ Products
        $products = [
            // Premium Parfume (6 items)
            ['name' => 'Coco Chanel No. 5', 'category' => 'Premium Parfume', 'price' => 850000, 'cost' => 500000],
            ['name' => 'Dior Sauvage', 'category' => 'Premium Parfume', 'price' => 920000, 'cost' => 550000],
            ['name' => 'Guerlain Shalimar', 'category' => 'Premium Parfume', 'price' => 1200000, 'cost' => 700000],
            ['name' => 'Chanel Allure', 'category' => 'Premium Parfume', 'price' => 980000, 'cost' => 600000],
            ['name' => 'Versace Eros', 'category' => 'Premium Parfume', 'price' => 750000, 'cost' => 450000],
            ['name' => 'YSL Black Opium', 'category' => 'Premium Parfume', 'price' => 1100000, 'cost' => 650000],
            
            // Casual Parfume (6 items)
            ['name' => 'Adidas Sport', 'category' => 'Casual Parfume', 'price' => 350000, 'cost' => 180000],
            ['name' => 'Old Spice Classic', 'category' => 'Casual Parfume', 'price' => 280000, 'cost' => 140000],
            ['name' => 'Axe Dark Temptation', 'category' => 'Casual Parfume', 'price' => 220000, 'cost' => 110000],
            ['name' => 'Paco Rabanne 1 Million', 'category' => 'Casual Parfume', 'price' => 520000, 'cost' => 300000],
            ['name' => 'Calvin Klein One', 'category' => 'Casual Parfume', 'price' => 450000, 'cost' => 250000],
            ['name' => 'Lacoste L.12.12', 'category' => 'Casual Parfume', 'price' => 380000, 'cost' => 200000],
            
            // Eau de Toilette (5 items)
            ['name' => 'Giorgio Armani Aqua', 'category' => 'Eau de Toilette', 'price' => 650000, 'cost' => 380000],
            ['name' => 'Burberry Weekend', 'category' => 'Eau de Toilette', 'price' => 520000, 'cost' => 300000],
            ['name' => 'Tommy Hilfiger', 'category' => 'Eau de Toilette', 'price' => 420000, 'cost' => 220000],
            ['name' => 'Acqua di Gioia', 'category' => 'Eau de Toilette', 'price' => 580000, 'cost' => 340000],
            ['name' => 'Light Blue', 'category' => 'Eau de Toilette', 'price' => 610000, 'cost' => 360000],
            
            // Body Spray (4 items)
            ['name' => 'Sensiya Body Spray Rose', 'category' => 'Body Spray', 'price' => 95000, 'cost' => 45000],
            ['name' => 'Sensiya Body Spray Lavender', 'category' => 'Body Spray', 'price' => 95000, 'cost' => 45000],
            ['name' => 'Dove Body Spray', 'category' => 'Body Spray', 'price' => 120000, 'cost' => 60000],
            ['name' => 'Rexona Body Spray Fresh', 'category' => 'Body Spray', 'price' => 85000, 'cost' => 40000],
            
            // Perfume Set (3 items)
            ['name' => 'Perfume Gift Set Deluxe', 'category' => 'Perfume Set', 'price' => 1500000, 'cost' => 900000],
            ['name' => 'Women Parfume Collection', 'category' => 'Perfume Set', 'price' => 2000000, 'cost' => 1200000],
            ['name' => 'Men Parfume Collection', 'category' => 'Perfume Set', 'price' => 1800000, 'cost' => 1000000],
        ];

        foreach ($products as $prod) {
            $category = ProductCategory::where('name', $prod['category'])->first();
            $product = Product::firstOrCreate(
                ['name' => $prod['name']],
                [
                    'product_category_id' => $category->id,
                    'description' => 'Produk parfum berkualitas tinggi - ' . $prod['name'],
                    'purchase_price' => $prod['cost'],
                    'selling_price' => $prod['price'],
                    'wholesale_price' => $prod['price'] * 0.85,
                    'is_active' => true,
                ]
            );

            // Create inventory
            if (!Inventory::where('product_id', $product->id)->exists()) {
                Inventory::create([
                    'product_id' => $product->id,
                    'current_stock' => rand(10, 50),
                    'minimum_stock' => 5,
                    'cost_per_unit' => $prod['cost'],
                    'stock_in' => rand(10, 50),
                    'stock_out' => 0,
                ]);
            }
        }

        // 3. Create Users
        User::firstOrCreate(
            ['email' => 'admin@apms.local'],
            [
                'name' => 'Admin APMS',
                'phone' => '08123456789',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'cashier@apms.local'],
            [
                'name' => 'Cashier User',
                'phone' => '08198765432',
                'password' => bcrypt('password'),
                'role' => 'cashier',
            ]
        );

        // 4. Create 10+ Customers
        $customers = [
            ['name' => 'PT Mitra Sejaya', 'phone' => '0812111111', 'email' => 'mitra@sejaya.com', 'type' => 'wholesale', 'is_active' => true],
            ['name' => 'Toko Roti Abadi', 'phone' => '0812222222', 'email' => 'toko@roti.com', 'type' => 'retail', 'is_active' => true],
            ['name' => 'CV Sempurna Jaya', 'phone' => '0812333333', 'email' => 'cv@sempurna.com', 'type' => 'wholesale', 'is_active' => true],
            ['name' => 'Toko Bunga Indah', 'phone' => '0812444444', 'email' => 'toko@bunga.com', 'type' => 'retail', 'is_active' => true],
            ['name' => 'Supermarket Prima', 'phone' => '0812555555', 'email' => 'super@prima.com', 'type' => 'wholesale', 'is_active' => true],
            ['name' => 'Siti Nurhaliza', 'phone' => '08126666666', 'email' => 'siti@email.com', 'type' => 'vip', 'is_active' => true],
            ['name' => 'Rina Mukherjee', 'phone' => '08127777777', 'email' => 'rina@email.com', 'type' => 'retail', 'is_active' => true],
            ['name' => 'Budi Santoso', 'phone' => '08128888888', 'email' => 'budi@email.com', 'type' => 'retail', 'is_active' => true],
            ['name' => 'Toko Kecantikan Bulan', 'phone' => '0812999999', 'email' => 'toko@bulan.com', 'type' => 'retail', 'is_active' => true],
            ['name' => 'Distributor Wangi Nusantara', 'phone' => '08121010101', 'email' => 'dist@wangi.com', 'type' => 'wholesale', 'is_active' => true],
            ['name' => 'Ahmad Wijaya', 'phone' => '08121111111', 'email' => 'ahmad@email.com', 'type' => 'vip', 'is_active' => true],
        ];

        foreach ($customers as $cust) {
            Customer::firstOrCreate(['phone' => $cust['phone']], $cust);
        }

        // 5. Create 30+ Transactions with details
        $customers = Customer::all();
        $products = Product::all();
        $users = User::all();

        for ($i = 0; $i < 35; $i++) {
            $customer = $customers->random();
            $user = $users->random();
            $numItems = rand(1, 5);
            $totalAmount = 0;

            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'discount' => rand(0, 100000),
                'final_amount' => 0,
                'payment_method' => collect(['cash', 'qris', 'transfer', 'debit_card'])->random(),
                'total_amount' => 0,
                'notes' => 'Transaksi pembelian parfum - ' . ($i + 1),
            ]);

            // Create transaction details
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $price = $product->selling_price;
                $subtotal = $quantity * $price;
                $totalAmount += $subtotal;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update inventory
                $inventory = Inventory::where('product_id', $product->id)->first();
                if ($inventory) {
                    $inventory->update([
                        'current_stock' => max(0, $inventory->current_stock - $quantity),
                        'stock_out' => $inventory->stock_out + $quantity,
                    ]);
                }
            }

            // Update transaction total
            $totalAmount -= $transaction->discount;
            $transaction->update(['total_amount' => max(0, $totalAmount), 'final_amount' => max(0, $totalAmount)]);
        }

        // 6. Create Coupons
        $coupons = [
            ['code' => 'WELCOME10', 'value' => 10, 'is_percentage' => true, 'max_usage' => 50, 'used_count' => 5, 'type' => 'discount', 'expiration_date' => Carbon::now()->addYear()],
            ['code' => 'SUMMER20', 'value' => 20, 'is_percentage' => true, 'max_usage' => 30, 'used_count' => 12, 'type' => 'discount', 'expiration_date' => Carbon::now()->addYear()],
            ['code' => 'PROMO50K', 'value' => 50000, 'is_percentage' => false, 'max_usage' => 25, 'used_count' => 8, 'type' => 'discount', 'expiration_date' => Carbon::now()->addYear()],
            ['code' => 'VIP25', 'value' => 25, 'is_percentage' => true, 'max_usage' => 10, 'used_count' => 3, 'type' => 'discount', 'expiration_date' => Carbon::now()->addYear()],
            ['code' => 'FLASH15', 'value' => 15, 'is_percentage' => true, 'max_usage' => 100, 'used_count' => 45, 'type' => 'discount', 'expiration_date' => Carbon::now()->addYear()],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(['code' => $coupon['code']], $coupon);
        }

        echo "✅ Comprehensive seeding completed successfully!\n";
        echo "✅ Created 24 products\n";
        echo "✅ Created 11 customers\n";
        echo "✅ Created 35 transactions with details\n";
        echo "✅ Created 5 coupons\n";
    }
}
