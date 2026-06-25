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
            if (!Schema::hasColumn('transactions', 'payment_status')) {
                $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid')->after('payment_method');
            }
            if (!Schema::hasColumn('transactions', 'debt_amount')) {
                $table->decimal('debt_amount', 15, 2)->default(0)->after('payment_status');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'aroma_preferences')) {
                $table->text('aroma_preferences')->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'debt_amount']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('aroma_preferences');
        });
    }
};
