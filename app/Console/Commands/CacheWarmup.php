<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheWarmup extends Command
{
    protected $signature = 'cache:warmup';
    protected $description = 'Warm up common cache keys for better performance';

    public function handle(): int
    {
        $this->info('Warming up cache...');
        Cache::put('product_count', Product::count(), now()->addHour());
        Cache::put('customer_count', Customer::count(), now()->addHour());
        Cache::put('active_products_count', Product::where('is_active', true)->count(), now()->addHour());
        Cache::put('monthly_sales', Transaction::whereMonth('created_at', now()->month)->sum('total_amount'), now()->addHour());
        $this->info('Cache warmed up successfully.');
        return Command::SUCCESS;
    }
}
