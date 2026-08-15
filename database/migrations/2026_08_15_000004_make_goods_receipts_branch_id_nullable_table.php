<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * goods_receipts.branch_id secara desain nullable (stok terpusat / "Pusat"),
     * tapi migrasi awal lupa ->nullable(). Perbaikan: kolom dijadikan nullable
     * agar owner yang memilih "Stok Pusat" tidak gagal insert (BUG-35).
     */
    public function up(): void
    {
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->foreignId('branch_id')->change();
        });
    }
};
