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
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->boolean('is_bonus_item')->default(false)->after('refill_volume_ml');
            $table->decimal('bonus_ml', 8, 2)->nullable()->after('is_bonus_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['is_bonus_item', 'bonus_ml']);
        });
    }
};
