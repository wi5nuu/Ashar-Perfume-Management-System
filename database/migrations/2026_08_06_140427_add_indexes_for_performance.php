<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // transactions: kolom yang sering di-query/filter
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('created_at', 'idx_transactions_created_at');
            $table->index('payment_status', 'idx_transactions_payment_status');
            $table->index('branch_id', 'idx_transactions_branch_id');
            $table->index('user_id', 'idx_transactions_user_id');
            $table->index('customer_id', 'idx_transactions_customer_id');
        });

        // expenses: filter by date, branch, category
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('date', 'idx_expenses_date');
            $table->index('branch_id', 'idx_expenses_branch_id');
            $table->index('category_id', 'idx_expenses_category_id');
        });

        // wholesale_orders: filter by status, branch, created_at
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->index('status', 'idx_wholesale_orders_status');
            $table->index('created_at', 'idx_wholesale_orders_created_at');
            $table->index('branch_id', 'idx_wholesale_orders_branch_id');
        });

        // inventories: lookup by product + branch
        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['product_id', 'branch_id'], 'idx_inventories_product_branch');
        });

        // transaction_details: lookup by product
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->index('product_id', 'idx_transaction_details_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_created_at');
            $table->dropIndex('idx_transactions_payment_status');
            $table->dropIndex('idx_transactions_branch_id');
            $table->dropIndex('idx_transactions_user_id');
            $table->dropIndex('idx_transactions_customer_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_date');
            $table->dropIndex('idx_expenses_branch_id');
            $table->dropIndex('idx_expenses_category_id');
        });

        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->dropIndex('idx_wholesale_orders_status');
            $table->dropIndex('idx_wholesale_orders_created_at');
            $table->dropIndex('idx_wholesale_orders_branch_id');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_inventories_product_branch');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropIndex('idx_transaction_details_product_id');
        });
    }
};
