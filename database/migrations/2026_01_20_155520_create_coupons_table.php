<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->enum('type', ['discount', 'bonus', 'cashback', 'other']);
        $table->decimal('value', 15, 2); // bisa persen atau nominal, tergantung type
        $table->boolean('is_percentage')->default(false);
        $table->date('expiration_date');
        $table->foreignId('customer_id')->nullable()->constrained();
        $table->integer('max_usage')->default(1);
        $table->integer('used_count')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
};
