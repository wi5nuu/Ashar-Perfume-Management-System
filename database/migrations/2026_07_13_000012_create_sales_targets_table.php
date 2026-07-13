<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->unsignedTinyInteger('month');
            $table->year('year');
            $table->decimal('target_amount', 20, 2);
            $table->decimal('bonus_percentage', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['branch_id', 'user_id', 'month', 'year']);
        });
    }

    public function down(): void { Schema::dropIfExists('sales_targets'); }
};
