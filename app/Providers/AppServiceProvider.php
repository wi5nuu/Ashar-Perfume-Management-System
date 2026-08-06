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
                'ownerPendingResetCount' => 0,
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
                // owner = semua data, admin = semua data, manager/lainnya = data cabang saja
                $isOwnerOrAdmin = in_array($user->role, ['owner', 'admin']);
                $isSenior       = in_array($user->role, ['owner', 'admin', 'manager']);

                // Urgent & pending wholesale orders
                $urgentQuery   = \App\Models\WholesaleOrder::where('status', 'pending');
                $pendingQuery  = \App\Models\WholesaleOrder::where('status', 'pending');
                if ($branchId > 0 && !$isOwnerOrAdmin) {
                    $urgentQuery->where('branch_id', $branchId);
                    $pendingQuery->where('branch_id', $branchId);
                }
                $d['urgentWholesaleCount'] = (clone $urgentQuery)->where('packing_days', 1)->count();
                $d['pendingGrosirCount']   = $pendingQuery->count();
                $d['pendingGrosirOrders']  = (clone $pendingQuery)->with('customer')->latest()->take(5)->get();

                // Login activities — hanya owner/admin yang boleh lihat
                if ($isOwnerOrAdmin) {
                    $loginQuery = \Illuminate\Support\Facades\DB::table('login_activities')
                        ->whereDate('login_activities.created_at', $today);
                    $d['loginToday'] = (clone $loginQuery)
                        ->join('users', 'login_activities.user_id', '=', 'users.id')
                        ->select('users.name', 'users.role', 'login_activities.created_at', 'login_activities.ip_address')
                        ->latest('login_activities.created_at')
                        ->take(10)
                        ->get();
                    $d['loginTodayCount'] = $loginQuery->distinct('login_activities.user_id')->count('login_activities.user_id');
                } else {
                    $d['loginToday']      = collect();
                    $d['loginTodayCount'] = 0;
                }

                // Audit logs — hanya owner/admin yang boleh lihat
                if ($isOwnerOrAdmin) {
                    $auditQuery = \Illuminate\Support\Facades\DB::table('audit_logs')
                        ->whereDate('audit_logs.created_at', $today);
                    $d['auditToday'] = (clone $auditQuery)
                        ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
                        ->select('audit_logs.*', 'users.name as user_name', 'users.role as user_role')
                        ->latest('audit_logs.created_at')
                        ->take(5)
                        ->get();
                    $d['auditTodayCount'] = $auditQuery->count();
                } else {
                    $d['auditToday']      = collect();
                    $d['auditTodayCount'] = 0;
                }

                // DB notifications — scope ke notifiable user atau owner/admin lihat semua
                $notifQuery = \Illuminate\Support\Facades\DB::table('notifications')
                    ->whereNull('read_at');
                if (!$isOwnerOrAdmin) {
                    $notifQuery->where('notifiable_id', $user->id);
                }
                $d['dbNotifs']    = (clone $notifQuery)->latest()->take(5)->get();
                $d['dbNotifCount'] = $notifQuery->count();

                // Password reset requests — hanya owner/admin
                $d['pendingResetCount'] = $isOwnerOrAdmin
                    ? \App\Models\PasswordResetRequest::pending()->count()
                    : 0;
                $d['ownerPendingResetCount'] = $d['pendingResetCount'];

                // Active sessions — hanya owner/admin
                $d['activeSessions'] = $isOwnerOrAdmin
                    ? \Illuminate\Support\Facades\DB::table('sessions')
                        ->where('last_activity', '>=', $now - 3600)
                        ->distinct('user_id')->count('user_id')
                    : 0;

                // Total notif yang relevan per role
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
        // Roles di DB: owner, admin, manager, supervisor, cashier, warehouse, employee, wholesale_customer
        Gate::define('manage_products',    fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse']));
        Gate::define('manage_inventory',   fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse']));
        Gate::define('manage_transactions',fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'cashier', 'supervisor']));
        Gate::define('manage_customers',   fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'cashier']));
        Gate::define('manage_coupons',     fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));
        Gate::define('manage_expenses',    fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));
        Gate::define('manage_suppliers',   fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse']));
        Gate::define('view_reports',       fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));
        Gate::define('manage_employees',   fn($u) => in_array($u->role, ['owner', 'admin']));
        Gate::define('manage_settings',    fn($u) => in_array($u->role, ['owner', 'admin']));
        Gate::define('manage_attendance',  fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'supervisor', 'cashier']));
        Gate::define('manage_payroll',     fn($u) => in_array($u->role, ['owner', 'admin']));
        Gate::define('audit.view',         fn($u) => in_array($u->role, ['owner', 'admin']));
        Gate::define('roles.manage',       fn($u) => in_array($u->role, ['owner', 'admin']));
        Gate::define('owner',              fn($u) => $u->role === 'owner');

        // Granular feature gates used by specific controllers
        Gate::define('inventory.view',        fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse']));
        Gate::define('stock_requests.view',   fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse', 'cashier']));
        Gate::define('expenses.view',         fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));
        Gate::define('expenses.manage',       fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));
        Gate::define('goods_receipts.view',   fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse']));
        Gate::define('goods_receipts.create', fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse']));
        Gate::define('wholesale.view',          fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'wholesale_customer']));
        Gate::define('wholesale.manage',        fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));
        Gate::define('products.view',           fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'cashier', 'warehouse', 'supervisor']));
        Gate::define('reports.view',            fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));

        // Transaction gates — dipakai di dashboard, sidebar, TransactionController
        Gate::define('transactions.view',       fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'cashier', 'supervisor']));
        Gate::define('transactions.create',     fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'cashier', 'supervisor']));

        // Stock request gates — dipakai di dashboard, sidebar, StockRequestController
        Gate::define('stock_requests.create',   fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'warehouse', 'cashier']));
        Gate::define('stock_requests.approve',  fn($u) => in_array($u->role, ['owner', 'admin', 'manager']));

        // Attendance gates — dipakai di sidebar dan attendances
        Gate::define('attendances.view',        fn($u) => in_array($u->role, ['owner', 'admin', 'manager', 'supervisor']));
    }
}
