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
        Schema::create('wholesale_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained(); // The cashier/admin who created it
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->decimal('package_target_amount', 15, 2); // e.g., 10,000,000.00
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'on_progress', 'ready_to_ship', 'completed', 'cancelled'])->default('pending');
            
            // Shipping Details
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_courier')->nullable();
            $table->dateTime('estimated_arrival')->nullable();
            $table->string('barcode')->nullable();
            
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_orders');
    }
};
