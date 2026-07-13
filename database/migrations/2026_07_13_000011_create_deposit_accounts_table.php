<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 20, 2)->default(0);
            $table->enum('status', ['active','frozen','closed'])->default('active');
            $table->timestamps();
        });

        Schema::create('deposit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_account_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['deposit','withdrawal','transfer']);
            $table->decimal('amount', 20, 2);
            $table->decimal('balance_before', 20, 2);
            $table->decimal('balance_after', 20, 2);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_transactions');
        Schema::dropIfExists('deposit_accounts');
    }
};
