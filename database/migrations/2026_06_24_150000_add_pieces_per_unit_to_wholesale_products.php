<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wholesale_products', function (Blueprint $table) {
            if (!Schema::hasColumn('wholesale_products', 'pieces_per_unit')) {
                $table->integer('pieces_per_unit')->default(1)->after('unit')->comment('Jumlah buah per satuan, misal 1 pack = 12 buah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wholesale_products', function (Blueprint $table) {
            if (Schema::hasColumn('wholesale_products', 'pieces_per_unit')) {
                $table->dropColumn('pieces_per_unit');
            }
        });
    }
};
