<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->json('cash_breakdown')->nullable()->after('actual_cash');
            $table->json('denominations')->nullable()->after('cash_breakdown');
            $table->timestamp('reviewed_at')->nullable()->after('photo_reviewed_by');
            $table->text('manager_notes')->nullable()->after('reviewed_at');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['cash_breakdown', 'denominations', 'reviewed_at', 'reviewed_by', 'manager_notes']);
        });
    }
};
