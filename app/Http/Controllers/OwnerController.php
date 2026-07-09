<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Branch;
use App\Models\WholesaleOrder;
use App\Notifications\PasswordResetApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

        $customers = User::where('role', 'wholesale_customer')->get(['id', 'name', 'email', 'phone', 'referral_code', 'created_at']);

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

        Log::info('Wholesale customer password reset', ['user_id' => $user->id, 'resolved_by' => auth()->id()]);

        return response()->json(['success' => true, 'password' => $newPassword]);
    }

    public function updateWholesaleAccount(Request $request, $id)
    {
        Gate::authorize('owner');

        $user = User::where('role', 'wholesale_customer')->findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', new \App\Rules\StrongPassword],
        ]);

        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Akun berhasil diperbarui.']);
    }

    public function wholesalePasswordRequests()
    {
        Gate::authorize('owner');

        $requests = PasswordResetRequest::whereHas('user', function ($q) {
            $q->where('role', 'wholesale_customer');
        })->with(['user', 'resolver'])->latest()->get();

        if (request()->wantsJson()) {
            $pending = $requests->where('status', 'pending')->values()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->user->name ?? '-',
                    'email' => $r->user->email ?? '-',
                    'created_at' => $r->created_at->format('d/m/Y H:i'),
                ];
            });

            $resolved = $requests->where('status', '!=', 'pending')->values()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->user->name ?? '-',
                    'email' => $r->user->email ?? '-',
                    'resolved_by' => $r->resolver->name ?? '-',
                    'resolved_at' => $r->resolved_at ? $r->resolved_at->format('d/m/Y H:i') : '-',
                ];
            });

            return response()->json(['pending' => $pending, 'resolved' => $resolved]);
        }

        return view('owner.wholesale-password-requests', compact('requests'));
    }

    public function resolveWholesalePasswordRequest(Request $request, $id)
    {
        Gate::authorize('owner');

        $resetRequest = PasswordResetRequest::whereHas('user', function ($q) {
            $q->where('role', 'wholesale_customer');
        })->findOrFail($id);

        $user = $resetRequest->user;
        $newPassword = \Illuminate\Support\Str::random(16);
        $user->password = Hash::make($newPassword);
        $user->save();

        $resetRequest->update([
            'status' => 'approved',
            'new_password' => $newPassword,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        Log::info('Wholesale customer password reset via request', [
            'user_id' => $user->id,
            'request_id' => $resetRequest->id,
            'resolved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'password' => $newPassword, 'email' => $user->email, 'name' => $user->name]);
    }

    public function wholesaleCustomerOrders($id)
    {
        Gate::authorize('owner');

        $user = User::where('role', 'wholesale_customer')->findOrFail($id);

        $orders = WholesaleOrder::where(function ($q) use ($user) {
            $q->where('recipient_phone', $user->phone)
              ->orWhereHas('customer', function ($cq) use ($user) {
                  $cq->where('email', $user->email);
              });
        })->with(['details', 'handler'])->withTrashed()->latest()->get();

        return response()->json([
            'success' => true,
            'customer' => ['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone],
            'orders' => $orders->map(function ($o) {
                return [
                    'id' => $o->id,
                    'invoice_number' => $o->invoice_number,
                    'total_amount' => (float) $o->total_amount,
                    'status' => $o->status,
                    'created_at' => $o->created_at->format('d/m/Y H:i'),
                    'items_count' => $o->details->count(),
                    'recipient_name' => $o->recipient_name,
                    'deleted_at' => $o->deleted_at ? $o->deleted_at->format('d/m/Y H:i') : null,
                ];
            }),
        ]);
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
