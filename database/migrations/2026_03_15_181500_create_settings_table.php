<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('key')->unique();
            $blueprint->text('value')->nullable();
            $blueprint->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'store_name', 'value' => 'Ashar Parfum'],
            ['key' => 'store_address', 'value' => 'Bekasi, Indonesia'],
            ['key' => 'store_phone', 'value' => '08123456789'],
            ['key' => 'receipt_header', 'value' => 'Terima Kasih Telah Berbelanja'],
            ['key' => 'receipt_footer', 'value' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan'],
            ['key' => 'currency_symbol', 'value' => 'Rp'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
