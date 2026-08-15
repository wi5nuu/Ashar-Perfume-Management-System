<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id')->unique();
            $table->index('uuid');
        });

        // Generate UUID untuk semua transaksi yang sudah ada
        \Illuminate\Support\Facades\DB::table('transactions')
            ->whereNull('uuid')
            ->orderBy('id')
            ->chunk(100, function ($transactions) {
                foreach ($transactions as $transaction) {
                    \Illuminate\Support\Facades\DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update(['uuid' => (string) Str::uuid()]);
                }
            });

        // Setelah semua transaksi punya UUID, buat column menjadi NOT NULL
        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
