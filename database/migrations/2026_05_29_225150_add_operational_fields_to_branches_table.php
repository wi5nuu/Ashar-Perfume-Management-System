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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('shift_start')->nullable()->after('opening_date');
            $table->string('shift_end')->nullable()->after('shift_start');
            $table->text('operational_hours')->nullable()->after('shift_end');
            $table->string('latitude')->nullable()->after('notes');
            $table->string('longitude')->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['shift_start', 'shift_end', 'operational_hours', 'latitude', 'longitude']);
        });
    }
};
