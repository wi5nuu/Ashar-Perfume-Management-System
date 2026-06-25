<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['branch_id', 'created_at']);
            $table->index('payment_status');
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['product_id', 'branch_id']);
            $table->index('current_stock');
        });
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['date', 'status']);
            $table->index('user_id');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['branch_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'created_at']);
            $table->dropIndex(['payment_status']);
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'branch_id']);
            $table->dropIndex(['current_stock']);
        });
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['date', 'status']);
            $table->dropIndex(['user_id']);
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'date']);
        });
    }
};
