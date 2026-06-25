<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained('expense_categories');
        $table->decimal('amount', 15, 2);
        $table->string('vendor')->nullable();
        $table->text('description')->nullable();
        $table->enum('type', ['one-time', 'recurring'])->default('one-time');
        $table->date('date');
        $table->string('proof_image')->nullable();
        $table->timestamps();
    });
}
};
