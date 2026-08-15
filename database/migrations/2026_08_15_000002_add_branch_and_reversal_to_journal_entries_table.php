<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_entries', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('period_id')
                    ->constrained('branches')->nullOnDelete();
            }
            if (! Schema::hasColumn('journal_entries', 'reversed_by')) {
                $table->unsignedBigInteger('reversed_by')->nullable()->after('posted_at');
            }
            if (! Schema::hasColumn('journal_entries', 'reversed_at')) {
                $table->timestamp('reversed_at')->nullable()->after('reversed_by');
            }
            if (! Schema::hasColumn('journal_entries', 'reversal_of')) {
                $table->unsignedBigInteger('reversal_of')->nullable()->after('reversed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['reversal_of', 'reversed_at', 'reversed_by', 'branch_id']);
        });
    }
};
