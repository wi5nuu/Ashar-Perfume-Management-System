<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('audit_logs', 'tags')) {
                $table->string('tags')->nullable()->after('user_agent');
            }
        });

        Schema::table('login_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('login_activities', 'city')) {
                $table->string('city')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('login_activities', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
            if (!Schema::hasColumn('login_activities', 'is_suspicious')) {
                $table->boolean('is_suspicious')->default(false)->after('country');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'requires_password_change')) {
                $table->boolean('requires_password_change')->default(false)->after('password_changed_at');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('requires_password_change');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('two_factor_confirmed_at');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'login_attempts')) {
                $table->integer('login_attempts')->default(0)->after('last_login_ip');
            }
            if (!Schema::hasColumn('users', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('login_attempts');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('is_locked');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'tags']);
        });

        Schema::table('login_activities', function (Blueprint $table) {
            $table->dropColumn(['city', 'country', 'is_suspicious']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'password_changed_at',
                'requires_password_change',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'last_login_at',
                'last_login_ip',
                'login_attempts',
                'is_locked',
                'locked_until',
            ]);
        });
    }
};
