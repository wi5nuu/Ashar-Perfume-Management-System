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
        // 1. CRM / Loyalty Insights
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VIP, Dormant, Newbie
            $table->integer('min_transactions')->default(0);
            $table->integer('min_spend')->default(0);
            $table->timestamps();
        });

        // 2. Payroll Settings & Records
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->decimal('allowance', 15, 2)->default(0); // Tunjangan
            $table->decimal('deduction', 15, 2)->default(0); // Potongan
            $table->integer('overtime_rate')->default(0);
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('month', 7); // YYYY-MM
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowance', 15, 2);
            $table->decimal('deduction', 15, 2);
            $table->decimal('total_salary', 15, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });

        // 3. Inventory Forecasting
        Schema::create('stock_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->integer('predicted_demand');
            $table->date('forecast_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('payroll_settings');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('stock_forecasts');
    }
};
