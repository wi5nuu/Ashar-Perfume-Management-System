<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','cashier','manager','packing','supervisor','employee') DEFAULT 'cashier'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','cashier','manager','packing','supervisor') DEFAULT 'cashier'");
    }
};
