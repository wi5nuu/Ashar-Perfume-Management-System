<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wholesale_order_details', function (Blueprint $table) {
            $table->foreignId('accessory_id')->nullable()->after('wholesale_product_id')
                  ->constrained('accessories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wholesale_order_details', function (Blueprint $table) {
            $table->dropForeign(['accessory_id']);
            $table->dropColumn('accessory_id');
        });
    }
};
