<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create product categories if not exists
        $categories = [
            ['name' => 'Premium', 'color' => '#FF6B6B', 'description' => 'Produk premium berkualitas tinggi'],
            ['name' => 'Regular', 'color' => '#4ECDC4', 'description' => 'Produk regular standar'],
            ['name' => 'Refill', 'color' => '#95E1D3', 'description' => 'Produk refill'],
            ['name' => 'Bundle', 'color' => '#FFD93D', 'description' => 'Paket bundle hemat'],
        ];

        foreach ($categories as $category) {
            if (!\App\Models\ProductCategory::where('name', $category['name'])->exists()) {
                \App\Models\ProductCategory::create($category);
            }
        }

        // 1. Create Owner Account
        User::updateOrCreate(
            ['email' => env('SEED_OWNER_EMAIL', 'owner@example.com')],
            [
                'name' => 'Owner APMS',
                'role' => 'owner',
                'phone' => env('SEED_OWNER_PHONE', '081234567890'),
                'password' => bcrypt(env('SEED_OWNER_PASSWORD', 'password')),
            ]
        );

        // 2. Create Admin/Contributor Account
        User::updateOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'Admin APMS',
                'role' => 'admin',
                'phone' => env('SEED_ADMIN_PHONE', '081234567891'),
                'password' => bcrypt(env('SEED_ADMIN_PASSWORD', 'password')),
            ]
        );

        // 3. Create Cashier Account
        User::updateOrCreate(
            ['email' => env('SEED_CASHIER_EMAIL', 'cashier@example.com')],
            [
                'name' => 'Cashier APMS',
                'role' => 'cashier',
                'phone' => env('SEED_CASHIER_PHONE', '081234567892'),
                'password' => bcrypt(env('SEED_CASHIER_PASSWORD', 'password')),
            ]
        );

        // Create sample customers if not exist
        if (!\App\Models\Customer::where('customer_code', 'CUST001')->exists()) {
            \App\Models\Customer::create([
                'customer_code' => 'CUST001',
                'name' => 'PT Mitra Sejaya',
                'phone' => '02156789012',
                'email' => 'contact@mitrajaya.com',
                'type' => 'wholesale',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'is_active' => true,
            ]);
        }

        if (!\App\Models\Customer::where('customer_code', 'CUST002')->exists()) {
            \App\Models\Customer::create([
                'customer_code' => 'CUST002',
                'name' => 'Toko Roti Abadi',
                'phone' => '08567890123',
                'email' => 'toko.abadi@email.com',
                'type' => 'retail',
                'address' => 'Jl. Sudirman No. 45, Bandung',
                'is_active' => true,
            ]);
        }

        // 4. Import Data from SQL Files (Products, etc.)
        $this->call(SqlDataSeeder::class);

        // 5. 100 Branches + Employees
        $this->call(BranchAndEmployeeSeeder::class);
    }
}
