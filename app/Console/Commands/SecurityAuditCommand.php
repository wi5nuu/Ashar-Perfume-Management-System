<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SecurityAuditCommand extends Command
{
    protected $signature = 'security:audit {--fix : Attempt to automatically fix issues}';
    protected $description = 'Run comprehensive security audit on the application';

    public function handle(): int
    {
        $this->info('=== APMS SECURITY AUDIT ===');
        $this->newLine();
        $issues = 0;

        $issues += $this->checkEnvSecurity();
        $issues += $this->checkDebugMode();
        $issues += $this->checkAppKey();
        $issues += $this->checkUserSecurity();
        $issues += $this->checkDatabaseSecurity();
        $issues += $this->checkCacheSecurity();
        $issues += $this->checkFilePermissions();
        $issues += $this->checkPhpSettings();

        $this->newLine();
        if ($issues === 0) {
            $this->info('✅ No security issues found. System is secure.');
        } else {
            $this->warn("⚠️  Found {$issues} security issue(s). Review the details above.");
        }

        return Command::SUCCESS;
    }

    private function checkEnvSecurity(): int
    {
        $issues = 0;
        $this->info('[1] Environment File Security');

        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            $this->warn('   ⚠️  .env file not found!');
            $issues++;
        } else {
            $perms = substr(sprintf('%o', fileperms($envPath)), -4);
            $this->line("   Permissions: {$perms}");
            if ($perms !== '0600' && $perms !== '0640') {
                $this->warn("   ⚠️  .env file permissions should be 600 or 640 (currently {$perms})");
                $issues++;
            }
        }

        $envContent = File::exists($envPath) ? File::get($envPath) : '';
        if (preg_match('/DB_PASSWORD="?"?/', $envContent) && !preg_match('/DB_PASSWORD=.{6,}/', $envContent)) {
            $this->warn('   ⚠️  Database password appears to be empty or too short');
            $issues++;
        }

        return $issues;
    }

    private function checkDebugMode(): int
    {
        $issues = 0;
        $this->info('[2] Debug Mode Check');

        if (env('APP_DEBUG', true)) {
            if (app()->environment('production')) {
                $this->error('   ❌ APP_DEBUG is enabled in production!');
                $issues++;
            } else {
                $this->warn('   ⚠️  APP_DEBUG is enabled (acceptable for non-production)');
            }
        } else {
            $this->info('   ✅ APP_DEBUG is disabled');
        }

        return $issues;
    }

    private function checkAppKey(): int
    {
        $issues = 0;
        $this->info('[3] Application Key');

        $key = config('app.key');
        if (!$key || $key === 'base64:' . base64_encode(str_repeat('x', 32))) {
            $this->error('   ❌ APP_KEY is not set or is the default value!');
            $issues++;
        } elseif (strlen($key) < 20) {
            $this->warn('   ⚠️  APP_KEY seems too short');
            $issues++;
        } else {
            $this->info('   ✅ APP_KEY is set');
        }

        return $issues;
    }

    private function checkUserSecurity(): int
    {
        $issues = 0;
        $this->info('[4] User Account Security');

        try {
            $userCount = User::count();
            $this->line("   Total users: {$userCount}");

            $ownerCount = User::where('role', 'owner')->count();
            if ($ownerCount === 0) {
                $this->warn('   ⚠️  No owner account exists in the system');
                $issues++;
            }

            $weakPasswords = User::where('password', 'like', '$2y$10$%')
                ->whereRaw('LENGTH(password) < 60')
                ->count();
            if ($weakPasswords > 0) {
                $this->warn("   ⚠️  {$weakPasswords} user(s) may have weak password hashes");
            }

            $expiredPasswords = User::whereNotNull('password_changed_at')
                ->where('password_changed_at', '<', now()->subDays(90))
                ->count();
            if ($expiredPasswords > 0) {
                $this->warn("   ⚠️  {$expiredPasswords} user(s) have passwords older than 90 days");
            }

            $lockedUsers = User::where('is_locked', true)->count();
            if ($lockedUsers > 0) {
                $this->warn("   ⚠️  {$lockedUsers} user(s) are currently locked");
            }
        } catch (\Throwable $e) {
            $this->warn('   ⚠️  Could not check user security: ' . $e->getMessage());
        }

        return $issues;
    }

    private function checkDatabaseSecurity(): int
    {
        $issues = 0;
        $this->info('[5] Database Security');

        try {
            $connection = config('database.default');
            $this->line("   Connection: {$connection}");

            $host = config("database.connections.{$connection}.host");
            if ($host === 'localhost' || $host === '127.0.0.1') {
                $this->info('   ✅ Database is on localhost');
            } else {
                $this->warn("   ⚠️  Remote database host: {$host} - ensure firewall rules are strict");
            }

            if (config("database.connections.{$connection}.password") === '') {
                $this->warn('   ⚠️  Database password is empty');
                $issues++;
            }
        } catch (\Throwable $e) {
            $this->warn('   ⚠️  Could not check database: ' . $e->getMessage());
        }

        return $issues;
    }

    private function checkCacheSecurity(): int
    {
        $issues = 0;
        $this->info('[6] Cache & Session Security');

        $sessionDriver = config('session.driver');
        $this->line("   Session driver: {$sessionDriver}");

        if ($sessionDriver === 'file') {
            $this->line('   Session path: ' . storage_path('framework/sessions'));
        }

        $cacheDriver = config('cache.default');
        $this->line("   Cache driver: {$cacheDriver}");

        return $issues;
    }

    private function checkFilePermissions(): int
    {
        $issues = 0;
        $this->info('[7] Critical File Permissions');

        $checks = [
            storage_path() => '0755',
            base_path('bootstrap/cache') => '0755',
        ];

        foreach ($checks as $path => $expected) {
            if (is_dir($path)) {
                $actual = substr(sprintf('%o', fileperms($path)), -4);
                if ($actual !== $expected) {
                    $this->warn("   ⚠️  {$path} permissions: {$actual} (expected {$expected})");
                } else {
                    $this->info("   ✅ {$path} is secure ({$actual})");
                }
            }
        }

        return $issues;
    }

    private function checkPhpSettings(): int
    {
        $issues = 0;
        $this->info('[8] PHP Security Settings');

        $checks = [
            'expose_php' => ['expected' => false, 'label' => 'expose_php should be Off'],
            'display_errors' => ['expected' => false, 'label' => 'display_errors should be Off in production'],
            'allow_url_fopen' => ['expected' => false, 'label' => 'allow_url_fopen should be Off if not needed'],
        ];

        foreach ($checks as $setting => $config) {
            $value = ini_get($setting);
            $intValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($intValue === true) {
                $this->warn("   ⚠️  {$setting} = On ({$config['label']})");
            } else {
                $this->info("   ✅ {$setting} = " . ($value ?: 'Off'));
            }
        }

        return $issues;
    }
}
