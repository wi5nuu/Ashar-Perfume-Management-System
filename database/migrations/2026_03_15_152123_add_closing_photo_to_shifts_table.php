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
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('closing_photo_path')->nullable()->after('notes');
            $table->enum('photo_status', ['pending', 'approved', 'rejected'])->default('pending')->after('closing_photo_path');
            $table->foreignId('photo_reviewed_by')->nullable()->constrained('users')->after('photo_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['photo_reviewed_by']);
            $table->dropColumn(['closing_photo_path', 'photo_status', 'photo_reviewed_by']);
        });
    }
};
