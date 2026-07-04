<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'owner','admin','admin_pusat','cashier','manager',
            'packing','supervisor','employee','warehouse',
            'wholesale_customer'
        ) DEFAULT 'cashier'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'owner','admin','admin_pusat','cashier','manager',
            'packing','supervisor','employee','warehouse'
        ) DEFAULT 'cashier'");
    }
};
