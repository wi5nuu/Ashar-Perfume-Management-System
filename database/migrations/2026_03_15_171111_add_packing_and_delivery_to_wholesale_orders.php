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
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->integer('packing_days')->default(1)->after('package_target_amount');
            $table->string('delivery_handler')->nullable()->after('shipping_courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wholesale_orders', function (Blueprint $table) {
            if (Schema::hasColumn('wholesale_orders', 'packing_days')) {
                $table->dropColumn('packing_days');
            }
            if (Schema::hasColumn('wholesale_orders', 'delivery_handler')) {
                $table->dropColumn('delivery_handler');
            }
        });
    }
};
