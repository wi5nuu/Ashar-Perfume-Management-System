<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            // Jumlah botol bonus 20ml Sedang yang diberikan untuk item ini
            $table->integer('bonus_quantity')->default(0)->after('subtotal');
            // Catatan bonus (opsional: nama produk yg dibonus-kan)
            $table->string('bonus_note')->nullable()->after('bonus_quantity');
        });
    }

    public function down()
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['bonus_quantity', 'bonus_note']);
        });
    }
};
