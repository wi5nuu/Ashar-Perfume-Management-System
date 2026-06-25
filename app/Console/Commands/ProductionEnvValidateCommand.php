<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProductionEnvValidateCommand extends Command
{
    protected $signature = 'env:validate-production';
    protected $description = 'Validate .env configuration for production readiness';

    public function handle(): int
    {
        $this->info('=== PRODUCTION ENV VALIDATION ===');
        $this->newLine();
        $errors = 0;
        $warnings = 0;

        $checks = [
            'APP_ENV' => ['required' => 'production', 'critical' => true],
            'APP_DEBUG' => ['required' => 'false', 'critical' => true],
            'APP_KEY' => ['required' => true, 'critical' => true],
            'DB_HOST' => ['required' => true, 'critical' => true],
            'DB_DATABASE' => ['required' => true, 'critical' => true],
            'DB_USERNAME' => ['required' => true, 'critical' => true],
        ];

        foreach ($checks as $key => $config) {
            $value = env($key);
            if (!$value) {
                $this->error("   ❌ {$key} is not set!");
                $config['critical'] ? $errors++ : $warnings++;
            } elseif (isset($config['required']) && $config['required'] !== true && $value !== $config['required']) {
                $this->error("   ❌ {$key} should be '{$config['required']}' (currently '{$value}')");
                $config['critical'] ? $errors++ : $warnings++;
            } else {
                $displayValue = in_array($key, ['APP_KEY', 'DB_PASSWORD', 'DB_USERNAME']) ? '***' : $value;
                $this->info("   ✅ {$key} = {$displayValue}");
            }
        }

        $url = env('APP_URL');
        if ($url) {
            if (!str_starts_with($url, 'https://')) {
                $this->warn("   ⚠️  APP_URL should use HTTPS in production (currently: {$url})");
                $warnings++;
            }
        }

        $sessionSecure = env('SESSION_SECURE_COOKIE');
        if ($sessionSecure !== 'true') {
            $this->warn('   ⚠️  SESSION_SECURE_COOKIE should be set to true');
            $warnings++;
        }

        $this->newLine();
        if ($errors === 0 && $warnings === 0) {
            $this->info('✅ Environment is fully configured for production.');
        } else {
            $this->warn("⚠️  {$errors} error(s), {$warnings} warning(s) found.");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
