<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wholesale_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('wholesale_orders', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            }
            $table->decimal('shipping_cost', 15, 2)->default(0)->after('shipping_address');
            $table->foreignId('handler_id')->nullable()->constrained('users')->nullOnDelete()->after('delivery_handler');
            $table->text('notes')->nullable()->after('estimated_arrival');
            $table->text('cancellation_reason')->nullable()->after('barcode');
            $table->timestamp('reviewed_at')->nullable()->after('barcode');
            $table->timestamp('packed_at')->nullable()->after('confirmed_at');
            $table->timestamp('shipped_at')->nullable()->after('packed_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
        });

        Schema::table('wholesale_order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('wholesale_order_details', 'wholesale_product_id')) {
                $table->foreignId('wholesale_product_id')->nullable()->constrained()->nullOnDelete()->after('product_id');
            }
            if (!Schema::hasColumn('wholesale_order_details', 'unit')) {
                $table->string('unit')->nullable()->after('volume_ml');
            }
            if (!Schema::hasColumn('wholesale_order_details', 'price_per_ml')) {
                $table->decimal('price_per_ml', 15, 2)->nullable()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $columns = ['branch_id', 'shipping_cost', 'handler_id', 'notes', 'cancellation_reason',
                'reviewed_at', 'packed_at', 'shipped_at', 'delivered_at', 'cancelled_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('wholesale_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('wholesale_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('wholesale_order_details', 'wholesale_product_id')) {
                $table->dropForeign(['wholesale_product_id']);
                $table->dropColumn('wholesale_product_id');
            }
            if (Schema::hasColumn('wholesale_order_details', 'unit')) {
                $table->dropColumn('unit');
            }
            if (Schema::hasColumn('wholesale_order_details', 'price_per_ml')) {
                $table->dropColumn('price_per_ml');
            }
        });
    }
};
