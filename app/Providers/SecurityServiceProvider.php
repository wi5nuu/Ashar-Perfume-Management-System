<?php

namespace App\Providers;

use App\Services\Security\ActivityMonitor;
use App\Services\Security\EncryptionService;
use App\Services\Security\FileUploadSecurityService;
use App\Services\Security\PosAntiTamperingService;
use App\Services\Security\RbacService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RbacService::class);
        $this->app->singleton(ActivityMonitor::class);
        $this->app->singleton(EncryptionService::class);
        $this->app->singleton(FileUploadSecurityService::class);
        $this->app->singleton(PosAntiTamperingService::class);
    }

    public function boot(): void
    {
        try {
            $rbac = $this->app->make(RbacService::class);
            $rbac->registerGates();
        } catch (\Throwable $e) {
            Log::warning('RBAC gates not registered (database not available yet): ' . $e->getMessage());
        }

        Blade::directive('permission', function ($slug) {
            return "<?php if(auth()->check() && app(App\\Services\\Security\\RbacService::class)->userHasPermission(auth()->user(), {$slug})): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('role', function ($roles) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$roles})): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Route::pattern('id', '[0-9]+');
    }
}
