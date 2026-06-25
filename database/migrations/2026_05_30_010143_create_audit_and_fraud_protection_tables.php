<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Shift Audits: Wajib input uang fisik saat tutup shift
        if (!Schema::hasTable('shift_audits')) {
            Schema::create('shift_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shift_id')->constrained();
                $table->decimal('system_balance', 15, 2); 
                $table->decimal('physical_balance', 15, 2); 
                $table->decimal('discrepancy', 15, 2); 
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Audit Logs: Mencatat setiap perubahan sensitif
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->string('action');
                $table->string('target_model');
                $table->unsignedBigInteger('target_id');
                $table->text('old_data')->nullable();
                $table->text('new_data')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        // 3. Expense Proofs: Wajib upload bukti untuk pengeluaran
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'proof_image')) {
                $table->string('proof_image')->nullable()->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_audits');
        Schema::dropIfExists('audit_logs');
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('proof_image');
        });
    }
};
