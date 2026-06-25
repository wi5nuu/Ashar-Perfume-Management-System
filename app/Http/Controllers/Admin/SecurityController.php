<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\PasswordHistoryRule;
use App\Rules\StrongPassword;
use App\Services\Security\DataIntegrityService;
use App\Services\Security\SecurityAlertService;
use App\Models\AuditLog;
use App\Models\IpBlacklist;
use App\Models\LoginActivity;
use App\Models\PasswordHistory;
use App\Models\User;
use App\Services\Security\ActivityMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    public function __construct(
        protected ActivityMonitor $monitor,
        protected DataIntegrityService $integrity,
        protected SecurityAlertService $alerts,
    ) {
        $this->middleware('can:audit.view');
    }

    public function securityOverview()
    {
        $userCount = User::count();
        $expiredPasswords = User::whereNotNull('password_changed_at')
            ->where('password_changed_at', '<', now()->subDays(90))
            ->count();

        $stats = [
            'total_audit_logs' => AuditLog::count(),
            'today_audit_logs' => AuditLog::whereDate('created_at', today())->count(),
            'total_login_activities' => LoginActivity::count(),
            'suspicious_logins' => LoginActivity::where('is_suspicious', true)->count(),
            'locked_accounts' => User::where('is_locked', true)->count(),
            'active_today' => LoginActivity::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
            'total_users' => $userCount,
            'expired_passwords' => $expiredPasswords,
            'integrity_score' => $this->integrity->getIntegrityScore(),
            'blocked_ips' => IpBlacklist::active()->count(),
            'two_factor_enabled' => User::whereNotNull('two_factor_confirmed_at')->count(),
        ];

        return view('admin.security.overview', compact('stats'));
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('model')) $query->where('target_model', 'like', "%{$request->model}%");
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->paginate(50);
        $actions = AuditLog::distinct('action')->pluck('action');
        return view('admin.security.audit-logs', compact('logs', 'actions'));
    }

    public function loginActivities(Request $request)
    {
        $query = LoginActivity::with('user')->latest();

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('suspicious')) $query->where('is_suspicious', true);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $activities = $query->paginate(50);
        return view('admin.security.login-activities', compact('activities'));
    }

    public function lockedAccounts()
    {
        $users = User::where('is_locked', true)
            ->where(function ($q) {
                $q->whereNull('locked_until')->orWhere('locked_until', '>', now());
            })
            ->with('branch')
            ->paginate(20);
        return view('admin.security.locked-accounts', compact('users'));
    }

    public function unlockAccount(User $user)
    {
        $user->unlock();
        Log::info("User {$user->name} unlocked by " . auth()->user()->name);
        return redirect()->route('admin.security.locked-accounts')
            ->with('success', "Akun {$user->name} berhasil dibuka.");
    }

    public function forceLogout(User $user)
    {
        $user->lock('1 hour');
        Log::info("User {$user->name} force logged out by " . auth()->user()->name);
        return redirect()->route('admin.security.locked-accounts')
            ->with('success', "Sesi {$user->name} telah diakhiri.");
    }

    public function blockedIps()
    {
        $ips = IpBlacklist::active()->latest('created_at')->paginate(20);
        return view('admin.security.blocked-ips', compact('ips'));
    }

    public function unblockIp(string $ip)
    {
        IpBlacklist::where('ip_address', $ip)->delete();
        Cache::forget("whitelist_ip_{$ip}");
        return redirect()->route('admin.security.blocked-ips')
            ->with('success', "IP {$ip} berhasil dibuka.");
    }

    public function integrityCheck()
    {
        $score = $this->integrity->getIntegrityScore();
        $anomalies = $this->integrity->scanForAnomalies();
        return view('admin.security.integrity', compact('score', 'anomalies'));
    }

    public function passwordChangeForm()
    {
        return view('auth.passwords.change');
    }

    public function passwordChange(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => [
                'required', 'confirmed',
                new StrongPassword,
                new PasswordHistoryRule($user->id),
            ],
        ]);

        DB::transaction(function () use ($request, $user) {
            PasswordHistory::create([
                'user_id' => $user->id,
                'password' => Hash::make($request->password),
            ]);

            $user->update([
                'password' => Hash::make($request->password),
                'password_changed_at' => now(),
                'requires_password_change' => false,
            ]);
        });

        $historyCount = config('security.password_policy.history_count', 5);
        PasswordHistory::where('user_id', $user->id)
            ->latest()
            ->skip($historyCount)
            ->take(100)
            ->delete();

        Log::info("Password changed by user {$user->name}");
        return redirect()->route('home')->with('success', 'Kata sandi berhasil diubah.');
    }
}
