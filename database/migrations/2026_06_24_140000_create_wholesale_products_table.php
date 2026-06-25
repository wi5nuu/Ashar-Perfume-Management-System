<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wholesale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('botol')->comment('botol, sarung, methanol, aroma, aksesoris, lainnya');
            $table->string('unit')->default('pcs');
            $table->decimal('price_per_unit', 15, 2)->default(0);
            $table->decimal('price_per_ml', 15, 2)->nullable()->comment('Harga per mililiter untuk produk cair');
            $table->integer('stock')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesale_products');
    }
};
