<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Branch;
use App\Models\WholesaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class OwnerController extends Controller
{
    public function monitoring()
    {
        Gate::authorize('owner');

        // Password reset requests
        $pendingResets = PasswordResetRequest::with(['user.branch'])
            ->pending()
            ->latest()
            ->get();

        $recentResolvedResets = PasswordResetRequest::with(['user', 'resolver'])
            ->where('status', '!=', 'pending')
            ->latest()
            ->take(20)
            ->get();

        // Branch activity summary
        $branches = Branch::withCount([
            'transactions as today_transactions' => function ($q) {
                $q->whereDate('created_at', today());
            },
            'users as employee_count' => function ($q) {
                $q->where('can_login', true)->where('role', '!=', 'owner');
            },
            'users as store_employee_count' => function ($q) {
                $q->where('can_login', false);
            },
        ])->get();

        // Recent transactions across all branches
        $recentTransactions = Transaction::with(['user', 'branch', 'customer'])
            ->latest()
            ->take(15)
            ->get();

        // Today's stats
        $todayRevenue = Transaction::whereDate('created_at', today())
            ->sum('total_amount');

        $todayTransactions = Transaction::whereDate('created_at', today())
            ->count();

        $totalUsers = User::count();
        $totalBranches = Branch::count();

        // Notifications
        $notifications = auth()->user()->notifications()
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('owner.monitoring', compact(
            'pendingResets',
            'recentResolvedResets',
            'branches',
            'recentTransactions',
            'todayRevenue',
            'todayTransactions',
            'totalUsers',
            'totalBranches',
            'notifications',
            'unreadCount',
        ));
    }

    public function markNotificationRead(string $id)
    {
        Gate::authorize('owner');

        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back();
    }

    public function markAllNotificationsRead()
    {
        Gate::authorize('owner');

        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah dibaca.');
    }

    public function specialPage()
    {
        Gate::authorize('owner');

        return view('owner.special');
    }

    public function wholesaleCustomers()
    {
        Gate::authorize('owner');

        $customers = User::where('role', 'wholesale_customer')->get();

        $phones = $customers->pluck('phone')->filter();
        $emails = $customers->pluck('email')->filter();

        $orders = WholesaleOrder::whereIn('status', ['completed', 'delivered', 'shipped'])
            ->where(function ($q) use ($phones, $emails) {
                $q->whereIn('recipient_phone', $phones)
                  ->orWhereHas('customer', fn($cq) => $cq->whereIn('email', $emails));
            })
            ->get(['id', 'recipient_phone', 'total_amount', 'customer_id', 'created_at']);

        $grouped = [];
        foreach ($orders as $o) {
            $key = $o->recipient_phone;
            $grouped[$key][] = $o;
        }

        $customers = $customers->map(function ($u) use ($grouped) {
            $key = $u->phone;
            $userOrders = $grouped[$key] ?? collect();
            $total = (float) collect($userOrders)->sum('total_amount');

            $tiers = [
                ['label'=>'Platinum','min'=>50000000],
                ['label'=>'Gold','min'=>30000000],
                ['label'=>'Silver','min'=>20000000],
                ['label'=>'VIP','min'=>10000000],
            ];
            $tierLabel = 'Regular';
            foreach ($tiers as $t) { if ($total >= $t['min']) { $tierLabel = $t['label']; break; } }

            $u->total_spent = $total;
            $u->order_count = count($userOrders);
            $u->tier_label = $tierLabel;
            $u->last_order = collect($userOrders)->sortByDesc('created_at')->first()?->created_at;

            return $u;
        })
            ->sortByDesc('total_spent')
            ->values();

        return view('owner.wholesale-customers', compact('customers'));
    }

    public function resetWholesalePassword(Request $request, $id)
    {
        Gate::authorize('owner');

        $user = User::where('role', 'wholesale_customer')->findOrFail($id);
        $newPassword = \Illuminate\Support\Str::random(16);
        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json(['success' => true, 'password' => $newPassword]);
    }

    public function updateWholesaleAccount(Request $request, $id)
    {
        Gate::authorize('owner');

        $user = User::where('role', 'wholesale_customer')->findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
        ]);

        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Akun berhasil diperbarui.']);
    }

    public function customerAccounts()
    {
        Gate::authorize('owner');

        $accounts = User::where('role', 'wholesale_customer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.customer-accounts', compact('accounts'));
    }
}
