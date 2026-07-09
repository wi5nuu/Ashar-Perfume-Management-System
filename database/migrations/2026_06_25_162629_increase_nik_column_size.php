<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Encrypted NIK values can exceed 255 chars, so use TEXT
        DB::statement('ALTER TABLE users MODIFY nik TEXT NULL');
        DB::statement('ALTER TABLE customers MODIFY nik TEXT NULL');
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
