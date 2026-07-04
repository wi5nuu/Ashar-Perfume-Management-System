<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE wholesale_orders MODIFY COLUMN status ENUM('pending','reviewed','on_progress','packed','shipped','delivered','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE wholesale_orders MODIFY COLUMN status ENUM('pending','on_progress','ready_to_ship','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
