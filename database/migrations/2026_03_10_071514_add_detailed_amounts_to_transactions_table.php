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
            $table->decimal('tax_amount', 15, 2)->after('discount')->default(0);
            $table->string('discount_type')->after('discount')->default('fixed'); // fixed, percent
            $table->decimal('discount_percent', 5, 2)->after('discount_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'discount_type', 'discount_percent']);
        });
    }
};
