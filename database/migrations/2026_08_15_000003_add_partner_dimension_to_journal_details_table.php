<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_details', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_details', 'contact_type')) {
                $table->string('contact_type', 50)->nullable()->after('account_id');
            }
            if (! Schema::hasColumn('journal_details', 'contact_id')) {
                $table->unsignedBigInteger('contact_id')->nullable()->after('contact_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journal_details', function (Blueprint $table) {
            $table->dropColumn(['contact_id', 'contact_type']);
        });
    }
};
