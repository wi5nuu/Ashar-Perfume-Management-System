<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (! Schema::hasColumn('payrolls', 'attendance_days')) {
                $table->unsignedSmallInteger('attendance_days')->default(0)->after('status')
                    ->comment('Number of days the employee attended during the payroll month');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'attendance_days')) {
                $table->dropColumn('attendance_days');
            }
        });
    }
};
