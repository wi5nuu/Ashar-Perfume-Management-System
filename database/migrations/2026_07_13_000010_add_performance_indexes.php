<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            $t->index(['branch_id','created_at']);
            $t->index(['status','created_at']);
        });
        Schema::table('transaction_details', function (Blueprint $t) {
            $t->index(['product_id','transaction_id']);
        });
        Schema::table('inventories', function (Blueprint $t) {
            $t->index(['product_id','branch_id']);
        });
        Schema::table('products', function (Blueprint $t) {
            $t->index(['category_id','is_active']);
        });
        Schema::table('inventory_movements', function (Blueprint $t) {
            $t->index(['product_id','branch_id','created_at']);
        });
        Schema::table('wholesale_orders', function (Blueprint $t) {
            $t->index(['branch_id','status','created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            $t->dropIndex(['branch_id','created_at']);
            $t->dropIndex(['status','created_at']);
        });
        Schema::table('transaction_details', function (Blueprint $t) {
            $t->dropIndex(['product_id','transaction_id']);
        });
        Schema::table('inventories', function (Blueprint $t) {
            $t->dropIndex(['product_id','branch_id']);
        });
        Schema::table('products', function (Blueprint $t) {
            $t->dropIndex(['category_id','is_active']);
        });
        Schema::table('inventory_movements', function (Blueprint $t) {
            $t->dropIndex(['product_id','branch_id','created_at']);
        });
        Schema::table('wholesale_orders', function (Blueprint $t) {
            $t->dropIndex(['branch_id','status','created_at']);
        });
    }
};
