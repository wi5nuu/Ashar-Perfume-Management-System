<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
Schema::create('inventories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->integer('current_stock')->default(0);
    $table->integer('minimum_stock')->default(10);
    $table->string('batch_number')->nullable();
    
    // Opsional: Jika kolom ini hanya untuk log, pastikan diisi saat transaksi
    $table->integer('stock_in')->default(0);
    $table->integer('stock_out')->default(0);
    
    // Gunakan nama 'cost_per_unit' agar sama dengan Controller kamu sebelumnya
    $table->decimal('cost_per_unit', 15, 2)->nullable(); 
    
    $table->date('expiration_date')->nullable();
    $table->foreignId('supplier_id')->nullable()->constrained();
    
    // Tambahkan default useCurrent() atau nullable
    $table->date('date_received')->useCurrent(); 
    $table->date('date_sold')->nullable();
    $table->timestamps();
});
}
};
