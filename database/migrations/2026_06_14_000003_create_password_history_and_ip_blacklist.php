<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('password_histories')) {
            Schema::create('password_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('password');
                $table->timestamp('created_at')->useCurrent();
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('ip_blacklist')) {
            Schema::create('ip_blacklist', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45);
                $table->string('reason', 100)->nullable();
                $table->integer('attempts')->default(0);
                $table->timestamp('blocked_until')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index('ip_address');
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email', 191)->index();
                $table->string('token', 191);
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('password_histories');
        Schema::dropIfExists('ip_blacklist');
        Schema::dropIfExists('password_resets');
    }
};
