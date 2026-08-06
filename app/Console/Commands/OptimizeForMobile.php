<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class OptimizeForMobile extends Command
{
    protected $signature = 'mobile:optimize {--clear : Clear all caches before optimizing}';
    protected $description = 'Optimize application for mobile performance';

    public function handle()
    {
        $this->info('🚀 Starting mobile optimization...');
        $this->newLine();

        // Clear caches if requested
        if ($this->option('clear')) {
            $this->clearCaches();
        }

        // Optimize Laravel
        $this->optimizeLaravel();

        // Build frontend assets
        $this->buildAssets();

        // Generate favicons if missing
        $this->checkFavicons();

        // Check service worker
        $this->checkServiceWorker();

        $this->newLine();
        $this->info('✅ Mobile optimization completed!');
        $this->newLine();
        $this->displayMetrics();
    }

    protected function clearCaches()
    {
        $this->info('🧹 Clearing caches...');
        
        $commands = [
            'config:clear',
            'route:clear',
            'view:clear',
            'cache:clear',
            'clear-compiled',
        ];

        foreach ($commands as $command) {
            Artisan::call($command);
            $this->line("  ✓ {$command}");
        }

        $this->newLine();
    }

    protected function optimizeLaravel()
    {
        $this->info('⚡ Optimizing Laravel...');
        
        $commands = [
            'config:cache' => 'Configuration cached',
            'route:cache' => 'Routes cached',
            'view:cache' => 'Views cached',
            'event:cache' => 'Events cached',
        ];

        foreach ($commands as $command => $message) {
            Artisan::call($command);
            $this->line("  ✓ {$message}");
        }

        $this->newLine();
    }

    protected function buildAssets()
    {
        $this->info('🎨 Building frontend assets...');
        
        if (File::exists(base_path('package.json'))) {
            $this->line('  Running npm run build...');
            exec('npm run build 2>&1', $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->line('  ✓ Assets built successfully');
            } else {
                $this->error('  ✗ Asset build failed');
                $this->line('  ' . implode("\n  ", $output));
            }
        } else {
            $this->warn('  ⚠ package.json not found, skipping asset build');
        }

        $this->newLine();
    }

    protected function checkFavicons()
    {
        $this->info('🎯 Checking favicons...');
        
        $favicons = [
            'public/favicon-32x32.png',
            'public/favicon-512x512.png',
            'public/apple-touch-icon.png',
        ];

        foreach ($favicons as $favicon) {
            if (File::exists(base_path($favicon))) {
                $this->line("  ✓ {$favicon} exists");
            } else {
                $this->warn("  ⚠ {$favicon} missing");
            }
        }

        $this->newLine();
    }

    protected function checkServiceWorker()
    {
        $this->info('📱 Checking PWA files...');
        
        $pwaFiles = [
            'public/sw.js' => 'Service Worker',
            'public/site.webmanifest' => 'Web Manifest',
        ];

        foreach ($pwaFiles as $file => $name) {
            if (File::exists(base_path($file))) {
                $this->line("  ✓ {$name} exists");
            } else {
                $this->warn("  ⚠ {$name} missing");
            }
        }

        $this->newLine();
    }

    protected function displayMetrics()
    {
        $this->info('📊 System Metrics:');
        
        $metrics = [
            'Memory Usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'PHP Version' => PHP_VERSION,
            'Laravel Version' => app()->version(),
            'Environment' => app()->environment(),
        ];

        foreach ($metrics as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
    }
}
