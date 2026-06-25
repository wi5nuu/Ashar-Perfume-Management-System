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
            $table->decimal('subtotal', 15, 2)->after('customer_type')->default(0);
            $table->decimal('paid_amount', 15, 2)->after('final_amount')->default(0);
            $table->decimal('change_amount', 15, 2)->after('paid_amount')->default(0);
            
            // Renaming final_amount to total_amount logic might be needed,
            // but for simplicity and backward compatibility, we add subtotal, paid, change.
            // Also transaction controller assumes 'total_amount'.
            // Let's check if total_amount already exists in the transactions table. The previous schema has total_amount AND final_amount.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'paid_amount', 'change_amount']);
        });
    }
};
