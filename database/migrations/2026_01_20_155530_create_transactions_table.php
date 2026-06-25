<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_number')->unique();
        $table->foreignId('customer_id')->nullable()->constrained();
        $table->enum('customer_type', ['retail', 'wholesale'])->default('retail');
        $table->decimal('total_amount', 15, 2);
        $table->decimal('discount', 15, 2)->default(0);
        $table->decimal('final_amount', 15, 2);
        $table->foreignId('coupon_id')->nullable()->constrained();
        $table->string('payment_method'); // cash, QRIS, transfer, etc. Bisa juga foreign key ke tabel payment_methods
        $table->string('payment_proof_image')->nullable(); // path to image
        $table->text('notes')->nullable();
        $table->foreignId('user_id')->constrained(); // cashier/admin yang melakukan transaksi
        $table->timestamps();
    });
}
};
