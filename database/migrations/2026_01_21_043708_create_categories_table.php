<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Tambahan opsional untuk catatan kategori
            $table->timestamps();
        }); // Kurung tutup untuk Schema::create
    } // Kurung tutup untuk function up

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};