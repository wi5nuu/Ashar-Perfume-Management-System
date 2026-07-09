<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Customer;
use App\Models\WholesaleOrder;
use App\Models\WholesaleOrderDetail;
use App\Models\WholesaleProduct;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedWholesaleOrders extends Command
{
    protected $signature = 'leads:seed-orders';
    protected $description = 'Create starter wholesale orders for new leads';

    public function handle()
    {
        $users = User::where('role', 'wholesale_customer')
            ->where('created_at', '>=', Carbon::now()->subHours(2))
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No new wholesale customers found in the last 2 hours.');
            return;
        }

        $fragranceOils = WholesaleProduct::where('type', 'aroma')->where('is_active', true)->pluck('id');
        $ethanol = WholesaleProduct::where('name', 'like', '%etanol%')->where('is_active', true)->first();
        $bottles = WholesaleProduct::where('type', 'botol')->where('is_active', true)->get();
        $accessories = WholesaleProduct::where('type', 'aksesoris')->where('is_active', true)->get();

        if ($fragranceOils->isEmpty() || $bottles->isEmpty()) {
            $this->error('Missing wholesale products (fragrance oils or bottles). Run SetupRealPaketUsaha first.');
            return;
        }

        $couriers = ['JNE', 'J&T', 'SiCepat', 'AnterAja', 'Gosend Same Day'];
        $created = 0;
        $errors = 0;

        foreach ($users as $u) {
            $customer = Customer::where('phone', $u->phone)->first();
            if (!$customer) {
                $this->warn("No customer record for {$u->name}");
                $errors++;
                continue;
            }

            $branchId = $u->branch_id ?? $customer->branch_id ?? 1;
            $name = $u->name;
            $phone = $u->phone;
            $address = $customer->address ?? 'Jl. ' . $name;

            $items = $this->buildStarterItems($fragranceOils, $ethanol, $bottles, $accessories);
            $totalAmount = collect($items)->sum('subtotal');

            $invoiceNumber = 'GROSIR-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(8));

            try {
                $order = WholesaleOrder::create([
                    'invoice_number'        => $invoiceNumber,
                    'user_id'               => 1,
                    'branch_id'             => $branchId,
                    'customer_id'           => $customer->id,
                    'package_target_amount' => $totalAmount,
                    'total_amount'          => $totalAmount,
                    'shipping_cost'         => rand(50000, 100000),
                    'recipient_name'        => $name,
                    'recipient_phone'       => $phone,
                    'shipping_address'      => $address,
                    'shipping_courier'      => $couriers[array_rand($couriers)],
                    'packing_days'          => rand(1, 3),
                    'status'                => 'pending',
                ]);

                foreach ($items as $item) {
                    $item['wholesale_order_id'] = $order->id;
                    WholesaleOrderDetail::create($item);
                }

                $branchName = $order->branch->name ?? 'N/A';
                $this->line("  {$name} -> {$branchName} (Rp " . number_format($totalAmount, 0, ',', '.') . ')');
                $created++;
            } catch (\Exception $e) {
                $this->error("Failed order for {$name}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->table(
            ['Total Leads', 'Orders Created', 'Errors'],
            [[$users->count(), $created, $errors]]
        );
    }

    private function buildStarterItems($fragranceOils, $ethanol, $bottles, $accessories): array
    {
        $items = [];

        $oilIds = $fragranceOils->random(min(3, $fragranceOils->count()))->toArray();
        foreach ($oilIds as $oilId) {
            $oil = WholesaleProduct::find($oilId);
            $qty = [1, 1, 1, 1, 2, 2][array_rand([1, 1, 1, 1, 2, 2])];
            $items[] = [
                'wholesale_product_id' => $oilId,
                'product_name'         => $oil->name . ' - 250ml',
                'quantity'             => $qty,
                'volume_ml'            => 250 * $qty,
                'unit'                 => 'ml',
                'price'                => $oil->price_per_ml * 250,
                'price_per_ml'         => $oil->price_per_ml,
                'subtotal'             => $oil->price_per_ml * 250 * $qty,
            ];
        }

        if ($ethanol) {
            $qty = [1, 1, 2, 2, 3][array_rand([1, 1, 2, 2, 3])];
            $price = $ethanol->price_per_unit;
            $items[] = [
                'wholesale_product_id' => $ethanol->id,
                'product_name'         => $ethanol->name . ' - ' . $qty . ' liter',
                'quantity'             => $qty,
                'volume_ml'            => null,
                'unit'                 => 'liter',
                'price'                => $price,
                'price_per_ml'         => $ethanol->price_per_ml,
                'subtotal'             => $price * $qty,
            ];
        }

        if ($bottles->isNotEmpty()) {
            $bottle30 = $bottles->where('name', 'like', '%30%')->first() ?? $bottles->first();
            $bottle50 = $bottles->where('name', 'like', '%50%')->first() ?? $bottles->skip(1)->first() ?? $bottles->first();
            $bottle100 = $bottles->where('name', 'like', '%100%')->first() ?? $bottles->last();

            $bottleConfigs = [
                [$bottle30, rand(10, 30)],
                [$bottle50, rand(10, 20)],
                [$bottle100, rand(5, 15)],
            ];

            foreach ($bottleConfigs as [$bottle, $qty]) {
                if ($bottle) {
                    $items[] = [
                        'wholesale_product_id' => $bottle->id,
                        'product_name'         => $bottle->name . ' x' . $qty,
                        'quantity'             => $qty,
                        'volume_ml'            => null,
                        'unit'                 => 'pcs',
                        'price'                => $bottle->price_per_unit,
                        'price_per_ml'         => null,
                        'subtotal'             => $bottle->price_per_unit * $qty,
                    ];
                }
            }
        }

        if ($accessories->isNotEmpty()) {
            $capType = $accessories->filter(fn($a) => str_contains(strtolower($a->name), 'tutup') || str_contains(strtolower($a->name), 'cap'))->first();
            if ($capType) {
                $qty = rand(10, 25);
                $items[] = [
                    'wholesale_product_id' => $capType->id,
                    'product_name'         => $capType->name . ' x' . $qty,
                    'quantity'             => $qty,
                    'volume_ml'            => null,
                    'unit'                 => 'pcs',
                    'price'                => $capType->price_per_unit,
                    'price_per_ml'         => null,
                    'subtotal'             => $capType->price_per_unit * $qty,
                ];
            }
        }

        return $items;
    }
}
