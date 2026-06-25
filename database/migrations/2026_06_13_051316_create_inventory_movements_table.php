<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create inventory_movements table for full stock audit trail.
     *
     * Every stock change (sale, purchase, adjustment, bonus, return) is recorded here.
     * This replaces the old "stock_out" column-only approach with a full ledger.
     */
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            // What moved
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('inventory_id')->nullable();

            // Movement details
            $table->enum('type', [
                'sale',         // Out: regular sale
                'bonus',        // Out: bonus product given
                'return',       // In: customer return
                'purchase',     // In: supplier purchase
                'adjustment',   // In/Out: manual stock count correction
                'transfer_in',  // In: received from another branch
                'transfer_out', // Out: sent to another branch
                'void',         // In: voided transaction restores stock
            ]);
            $table->integer('quantity'); // positive = in, negative = out
            $table->integer('stock_before');
            $table->integer('stock_after');

            // Traceability
            $table->string('reference_type')->nullable(); // 'transaction', 'purchase', 'adjustment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('notes')->nullable();

            // Who did it
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for common queries
            $table->index(['product_id', 'branch_id'], 'idx_movement_product_branch');
            $table->index('type', 'idx_movement_type');
            $table->index('created_at', 'idx_movement_date');
            $table->index(['reference_type', 'reference_id'], 'idx_movement_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
