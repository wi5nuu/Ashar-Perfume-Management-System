<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('chart_of_accounts', 'is_posting')) {
                $table->boolean('is_posting')->default(true)->after('level');
            }
            if (! Schema::hasColumn('chart_of_accounts', 'is_cash')) {
                $table->boolean('is_cash')->default(false)->after('is_posting');
            }
            if (! Schema::hasColumn('chart_of_accounts', 'is_bank')) {
                $table->boolean('is_bank')->default(false)->after('is_cash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropColumn(['is_bank', 'is_cash', 'is_posting']);
        });
    }
};
