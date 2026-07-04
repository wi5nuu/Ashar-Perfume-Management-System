<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','cashier','manager','packing','supervisor','employee','warehouse') DEFAULT 'cashier'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','cashier','manager','packing','supervisor','employee') DEFAULT 'cashier'");
    }
};
