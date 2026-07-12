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

        // Provide all notification data to layout (cached 60s)
        // MOVED FROM BLADE: Previously raw DB::table() queries ran on every page load.
        view()->composer('layouts.app', function ($view) {
            $data = [
                'pendingGrosirCount' => 0,
                'pendingGrosirOrders' => collect(),
                'loginToday' => collect(),
                'loginTodayCount' => 0,
                'auditToday' => collect(),
                'auditTodayCount' => 0,
                'dbNotifs' => collect(),
                'dbNotifCount' => 0,
                'pendingResetCount' => 0,
                'activeSessions' => 0,
                'urgentWholesaleCount' => 0,
                'totalNotif' => 0,
            ];

            if (!Auth::check()) {
                $view->with($data);
                return;
            }

            $user = Auth::user();
            $branchId = $user->branch_id ?? 0;
            $today = date('Y-m-d');
            $now = time();
            $cacheKey = "notif_data_branch_{$branchId}_role_{$user->role}";

            $cached = Cache::remember($cacheKey, 60, function () use ($branchId, $today, $now, $user) {
                $d = [];
                $ownerOrAdmin = $user->isOwner() || $user->isAdminPusat();

                // Urgent wholesale count (existing)
                $urgentQuery = \App\Models\WholesaleOrder::where('status', 'pending');
                if ($branchId > 0 && !$ownerOrAdmin) {
                    $urgentQuery->where('branch_id', $branchId);
                }
                $d['urgentWholesaleCount'] = $urgentQuery->where('packing_days', 1)->count();

                // Pending wholesale orders
                $pendingQuery = \App\Models\WholesaleOrder::where('status', 'pending');
                if ($branchId > 0 && !$ownerOrAdmin) {
                    $pendingQuery->where('branch_id', $branchId);
                }
                $d['pendingGrosirCount'] = $pendingQuery->count();
                $d['pendingGrosirOrders'] = (clone $pendingQuery)->with('customer')->latest()->take(5)->get();

                // Login activities today
                $loginQuery = \Illuminate\Support\Facades\DB::table('login_activities')
                    ->whereDate('login_activities.created_at', $today);
                $d['loginToday'] = (clone $loginQuery)
                    ->join('users', 'login_activities.user_id', '=', 'users.id')
                    ->select('users.name', 'users.role', 'login_activities.created_at', 'login_activities.ip_address')
                    ->latest('login_activities.created_at')
                    ->take(10)
                    ->get();
                $d['loginTodayCount'] = $loginQuery->distinct('login_activities.user_id')->count('login_activities.user_id');

                // Audit logs today
                $auditQuery = \Illuminate\Support\Facades\DB::table('audit_logs')
                    ->whereDate('audit_logs.created_at', $today);
                $d['auditToday'] = (clone $auditQuery)
                    ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
                    ->select('audit_logs.*', 'users.name as user_name', 'users.role as user_role')
                    ->latest('audit_logs.created_at')
                    ->take(5)
                    ->get();
                $d['auditTodayCount'] = $auditQuery->count();

                // DB notifications
                $notifQuery = \Illuminate\Support\Facades\DB::table('notifications')
                    ->whereNull('read_at');
                $d['dbNotifs'] = (clone $notifQuery)->latest()->take(5)->get();
                $d['dbNotifCount'] = $notifQuery->count();

                // Password reset requests
                $d['pendingResetCount'] = \App\Models\PasswordResetRequest::pending()->count();

                // Active sessions
                $d['activeSessions'] = \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('last_activity', '>=', $now - 3600)
                    ->distinct('user_id')->count('user_id');

                // Total
                $d['totalNotif'] = $d['pendingGrosirCount'] + $d['dbNotifCount'] + $d['pendingResetCount'];

                return $d;
            });

            $view->with($cached);
        });

        // Share settings globally (cached for 5 minutes — skip error views to prevent nested crashes)
        view()->composer('*', function ($view) {
            if (str_starts_with($view->getName(), 'errors.')) {
                $view->with('app_settings', collect());
                return;
            }
            $settings = Cache::remember('app_settings', 300, function () {
                return \App\Models\Setting::pluck('value', 'key');
            });
            $view->with('app_settings', $settings);
        });

        // Role-based gates
        Gate::define('manage_products', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('manage_inventory', fn($user) => in_array($user->role, ['admin_pusat', 'manager', 'owner']));
        Gate::define('manage_transactions', fn($user) => in_array($user->role, ['admin_pusat', 'admin', 'cashier', 'manager', 'owner']));
        Gate::define('manage_customers', fn($user) => in_array($user->role, ['admin', 'admin_pusat', 'manager', 'cashier', 'owner']));
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
