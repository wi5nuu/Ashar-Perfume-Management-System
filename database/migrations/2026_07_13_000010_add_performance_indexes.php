<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add indexes that don't already exist
        $this->addIndexSafe('transactions', ['branch_id', 'created_at']);
        $this->addIndexSafe('transactions', ['payment_status', 'created_at']);
        $this->addIndexSafe('transaction_details', ['product_id', 'transaction_id']);
        $this->addIndexSafe('inventories', ['product_id', 'branch_id']);
        $this->addIndexSafe('products', ['category_id', 'is_active']);
        $this->addIndexSafe('inventory_movements', ['product_id', 'branch_id', 'created_at']);
        $this->addIndexSafe('wholesale_orders', ['branch_id', 'status', 'created_at']);
    }

    public function down(): void
    {
        // No-op: existing indexes from other migrations should not be dropped here
    }

    private function addIndexSafe(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        // Check each column exists
        foreach ($columns as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return;
            }
        }
        $indexName = $table . '_' . implode('_', $columns) . '_index';
        // Check if index already exists
        try {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        } catch (\Exception $e) {
            // Silently skip if duplicate
        }
    }
};
