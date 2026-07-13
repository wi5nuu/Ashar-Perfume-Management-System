<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\TransactionDetail;
use Illuminate\Console\Command;

class CheckReorderPoint extends Command
{
    protected $signature = 'reorder:check {--branch=}';
    protected $description = 'Check reorder point for all products';

    public function handle(): int
    {
        $branchId = $this->option('branch') ? (int)$this->option('branch') : null;
        $products = Product::where('is_active', true)->get();
        $needsReorder = [];

        foreach ($products as $product) {
            $sold = (int) TransactionDetail::where('product_id', $product->id)
                ->whereHas('transaction', fn($q) => $q->where('created_at', '>=', now()->subDays(90)))
                ->when($branchId, fn($q) => $q->whereHas('transaction', fn($qq) => $qq->where('branch_id', $branchId)))
                ->sum('quantity');
            $avgDaily = $sold / 90;
            $reorderPoint = ($avgDaily * ($product->lead_time_days ?? 7)) + ($avgDaily * 3);
            $stock = Inventory::where('product_id', $product->id)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->value('current_stock') ?? 0;

            if ($stock <= $reorderPoint) {
                $needsReorder[] = [$product->name, $stock, round($reorderPoint), round($reorderPoint-$stock)];
            }
        }

        $this->info("Checked {$products->count()} products.");
        if (!empty($needsReorder)) {
            $this->table(['Product','Stock','Reorder At','Shortage'], $needsReorder);
        } else {
            $this->info('All products have sufficient stock.');
        }

        return Command::SUCCESS;
    }
}
