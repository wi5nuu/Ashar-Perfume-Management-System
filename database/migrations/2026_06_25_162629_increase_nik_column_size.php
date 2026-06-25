<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 255)->nullable()->change();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nik', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 16)->nullable(false)->change();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nik', 20)->nullable(false)->change();
        });
    }
};
