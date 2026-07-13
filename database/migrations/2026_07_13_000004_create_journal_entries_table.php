<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number', 50)->unique();
            $table->foreignId('period_id')->constrained('accounting_periods');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('transaction_type', 50)->nullable();
            $table->date('date');
            $table->string('description');
            $table->decimal('total_debit', 20, 2)->default(0);
            $table->decimal('total_credit', 20, 2)->default(0);
            $table->enum('status', ['draft','posted','reversed'])->default('draft');
            $table->timestamp('posted_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['period_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('journal_entries'); }
};
