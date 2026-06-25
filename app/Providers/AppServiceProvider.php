<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\WholesaleOrder;
use App\Policies\TransactionPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\WholesaleOrderPolicy;
use App\Services\Contracts\CopilotEngineInterface;
use App\Services\RuleBasedCopilotEngine;
use App\Services\AiCopilotService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Events\Login;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CopilotEngineInterface::class, function ($app) {
            $engine = env('AI_ENGINE', 'rule_based');
            return $engine === 'claude' ? $app->make(AiCopilotService::class) : $app->make(RuleBasedCopilotEngine::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        // Register policies for model authorization
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(WholesaleOrder::class, WholesaleOrderPolicy::class);

        // Register event listeners for Login events
        Event::listen(Login::class, [\App\Listeners\RecordLoginAttendance::class, 'handle']);
        Event::listen(Login::class, [\App\Listeners\RecordLoginActivity::class, 'handle']);

        // Share urgent wholesale notifications (cached per-user branch for 60s)
        // BEFORE: Queried DB on every page load for every user.
        // AFTER: Cached per branch, only counts (not full collection).
        view()->composer('layouts.app', function ($view) {
            $urgentCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $branchId = $user->branch_id ?? 0;

                $urgentCount = Cache::remember(
                    "urgent_orders_count_branch_{$branchId}",
                    60,
                    function () use ($branchId) {
                        $query = \App\Models\WholesaleOrder::where('status', 'pending');

                        if ($branchId > 0) {
                            $query->where('branch_id', $branchId);
                        }

                        return $query->where('packing_days', 1)->count();
                    }
                );
            }

            $view->with('urgentWholesaleCount', $urgentCount);
        });

        // Share settings globally (cached for 5 minutes — already optimized)
        view()->composer('*', function ($view) {
            $settings = Cache::remember('app_settings', 300, function () {
                return \App\Models\Setting::pluck('value', 'key');
            });
            $view->with('app_settings', $settings);
        });

        // Role-based gates
        Gate::define('manage_products', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('manage_inventory', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('manage_transactions', fn($user) => in_array($user->role, ['admin_pusat', 'admin', 'cashier', 'manager', 'owner']));
        Gate::define('manage_customers', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'cashier', 'owner']));
        Gate::define('manage_coupons', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('manage_expenses', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('view_reports', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('manage_employees', fn($user) => $user->role === 'owner');
        Gate::define('manage_settings', fn($user) => $user->role === 'owner');
        Gate::define('manage_attendance', fn($user) => in_array($user->role, ['admin_pusat', 'admin', 'manager', 'owner', 'supervisor', 'cashier']));
        Gate::define('manage_payroll', fn($user) => $user->role === 'owner');
        Gate::define('audit.view', fn($user) => $user->role === 'owner');
        Gate::define('roles.manage', fn($user) => $user->role === 'owner');
        Gate::define('owner', fn($user) => $user->role === 'owner');
    }
}
