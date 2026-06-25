<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode')->nullable();
            
            // Menghubungkan ke tabel product_categories
            $table->foreignId('product_category_id')->constrained('product_categories')->onDelete('cascade');
            
            $table->string('brand')->nullable();
            $table->string('size')->nullable(); // misal: 10ml, 30ml
            $table->string('unit')->default('ml'); // ml, pcs, liter
            
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('wholesale_price', 15, 2)->nullable();
            
            $table->integer('initial_stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('track_inventory')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};