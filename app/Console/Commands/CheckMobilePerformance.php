<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CheckMobilePerformance extends Command
{
    protected $signature = 'mobile:check';
    protected $description = 'Check mobile optimization status and performance';

    public function handle()
    {
        $this->info('🔍 Checking Mobile Performance Status...');
        $this->newLine();

        $this->checkAssets();
        $this->checkCaching();
        $this->checkDatabase();
        $this->checkSecurity();
        $this->checkPWA();
        $this->recommendations();

        $this->newLine();
        $this->info('✅ Performance check completed!');
    }

    protected function checkAssets()
    {
        $this->info('📦 Checking Assets:');
        
        $checks = [
            'Build manifest' => File::exists(public_path('build/manifest.json')),
            'Mobile CSS' => File::exists(resource_path('css/mobile-responsive.css')),
            'Mobile JS' => File::exists(resource_path('js/mobile-optimization.js')),
            'Touch gestures' => File::exists(resource_path('js/touch-gestures.js')),
            'Performance JS' => File::exists(resource_path('js/performance.js')),
        ];

        foreach ($checks as $name => $status) {
            $this->displayCheck($name, $status);
        }

        $this->newLine();
    }

    protected function checkCaching()
    {
        $this->info('⚡ Checking Caching:');
        
        $checks = [
            'Config cached' => File::exists(base_path('bootstrap/cache/config.php')),
            'Routes cached' => File::exists(base_path('bootstrap/cache/routes-v7.php')),
            'Events cached' => File::exists(base_path('bootstrap/cache/events.php')),
            '.htaccess optimized' => $this->checkHtaccess(),
        ];

        foreach ($checks as $name => $status) {
            $this->displayCheck($name, $status);
        }

        $this->newLine();
    }

    protected function checkDatabase()
    {
        $this->info('🗄️  Checking Database:');
        
        try {
            $connectionTime = $this->measureDatabaseConnection();
            $this->line("  ✓ Database connection: {$connectionTime}ms");
            
            // Check for slow queries (if query log is enabled)
            if (config('database.log_queries')) {
                $this->line('  ✓ Query logging enabled');
            } else {
                $this->line('  ⚠ Query logging disabled');
            }
        } catch (\Exception $e) {
            $this->error('  ✗ Database connection failed');
        }

        $this->newLine();
    }

    protected function checkSecurity()
    {
        $this->info('🔒 Checking Security:');
        
        $htaccess = File::get(base_path('.htaccess'));
        
        $securityHeaders = [
            'X-Content-Type-Options' => str_contains($htaccess, 'X-Content-Type-Options'),
            'X-Frame-Options' => str_contains($htaccess, 'X-Frame-Options'),
            'X-XSS-Protection' => str_contains($htaccess, 'X-XSS-Protection'),
            'Referrer-Policy' => str_contains($htaccess, 'Referrer-Policy'),
        ];

        foreach ($securityHeaders as $header => $status) {
            $this->displayCheck($header, $status);
        }

        $this->newLine();
    }

    protected function checkPWA()
    {
        $this->info('📱 Checking PWA:');
        
        $checks = [
            'Service Worker' => File::exists(public_path('sw.js')),
            'Web Manifest' => File::exists(public_path('site.webmanifest')),
            'Favicon 32x32' => File::exists(public_path('favicon-32x32.png')),
            'Favicon 512x512' => File::exists(public_path('favicon-512x512.png')),
            'Apple Touch Icon' => File::exists(public_path('apple-touch-icon.png')),
        ];

        foreach ($checks as $name => $status) {
            $this->displayCheck($name, $status);
        }

        $this->newLine();
    }

    protected function recommendations()
    {
        $this->info('💡 Recommendations:');
        
        $recommendations = [];

        // Check if assets need building
        if (!File::exists(public_path('build/manifest.json'))) {
            $recommendations[] = 'Run "npm run build" to build production assets';
        }

        // Check if caches are cleared
        if (!File::exists(base_path('bootstrap/cache/config.php'))) {
            $recommendations[] = 'Run "php artisan mobile:optimize" to cache configurations';
        }

        // Check if query caching is enabled
        if (!config('cache.default') || config('cache.default') === 'file') {
            $recommendations[] = 'Consider using Redis or Memcached for better performance';
        }

        if (empty($recommendations)) {
            $this->line('  ✓ All optimizations are in place!');
        } else {
            foreach ($recommendations as $recommendation) {
                $this->line("  → {$recommendation}");
            }
        }

        $this->newLine();
    }

    protected function displayCheck(string $name, bool $status)
    {
        $icon = $status ? '✓' : '✗';
        $color = $status ? 'green' : 'red';
        $this->line("  <fg={$color}>{$icon}</> {$name}");
    }

    protected function checkHtaccess(): bool
    {
        if (!File::exists(base_path('.htaccess'))) {
            return false;
        }

        $content = File::get(base_path('.htaccess'));
        
        return str_contains($content, 'mod_expires') && 
               str_contains($content, 'mod_deflate');
    }

    protected function measureDatabaseConnection(): float
    {
        $start = microtime(true);
        DB::connection()->getPdo();
        $end = microtime(true);
        
        return round(($end - $start) * 1000, 2);
    }
}
