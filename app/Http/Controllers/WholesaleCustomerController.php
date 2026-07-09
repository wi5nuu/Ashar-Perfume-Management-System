<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WholesaleOrder;
use App\Models\Customer;
use App\Models\PasswordResetRequest;
use App\Services\WholesaleLoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WholesaleCustomerController extends Controller
{
    private function safeProfile(User $user): array
    {
        return $user->only(['id', 'name', 'email', 'phone', 'role', 'referral_code']);
    }

    private function userOrdersQuery(User $user)
    {
        return WholesaleOrder::where(function ($q) use ($user) {
            $q->where('recipient_phone', $user->phone)
              ->orWhereHas('customer', function ($cq) use ($user) {
                  $cq->where('email', $user->email);
              });
        });
    }

    private function computeTier($totalSpent): array
    {
        $tiers = [
            ['label' => 'Platinum', 'min' => 50000000, 'discount' => 25, 'icon' => 'fa-crown', 'color' => '#8B5CF6'],
            ['label' => 'Gold', 'min' => 30000000, 'discount' => 20, 'icon' => 'fa-star', 'color' => '#F59E0B'],
            ['label' => 'Silver', 'min' => 20000000, 'discount' => 15, 'icon' => 'fa-gem', 'color' => '#6B7280'],
            ['label' => 'VIP', 'min' => 10000000, 'discount' => 10, 'icon' => 'fa-certificate', 'color' => '#FF6B35'],
        ];
        foreach ($tiers as $t) {
            if ($totalSpent >= $t['min']) return $t;
        }
        return ['label' => 'Regular', 'min' => 0, 'discount' => 0, 'icon' => 'fa-user', 'color' => '#999'];
    }

    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'wholesale_customer') {
            return redirect()->route('wholesale.customer.dashboard');
        }
        return view('wholesale.customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'wholesale_login_' . $request->ip();
        $attempts = (int) \Illuminate\Support\Facades\Cache::get($key, 0);
        if ($attempts >= 5) {
            return back()->withErrors(['email' => 'Terlalu banyak percobaan. Silakan coba lagi dalam 15 menit.'])->onlyInput('email');
        }

        if (Auth::attempt(array_merge($credentials, ['role' => 'wholesale_customer', 'can_login' => true, 'is_active' => true]))) {
            $request->session()->regenerate();
            \Illuminate\Support\Facades\Cache::forget($key);
            return redirect()->intended(route('wholesale.customer.dashboard'));
        }

        \Illuminate\Support\Facades\Cache::put($key, $attempts + 1, now()->addMinutes(15));
        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function dashboard()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return redirect()->route('wholesale.customer.login');
        }
        $qq = $this->userOrdersQuery($user);
        $orders = (clone $qq)->with(['details', 'handler'])->latest()->paginate(10);
        $totalOrders = (clone $qq)->count();
        $activeOrders = (clone $qq)->whereNotIn('status', ['completed', 'cancelled'])->count();
        $totalSpent = (clone $qq)->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');
        $tier = $this->computeTier($totalSpent);

        $customer = Customer::where('type', 'wholesale')->where(function($q) use ($user) {
            $q->where('phone', $user->phone)->orWhere('email', $user->email);
        })->first();
        $rankInfo = $customer ? app(WholesaleLoyaltyService::class)->getRankInfo($customer) : [];

        $user = $this->safeProfile($user);
        return view('wholesale.customer.dashboard', compact('user', 'orders', 'totalOrders', 'activeOrders', 'totalSpent', 'tier', 'rankInfo', 'customer'));
    }

    public function orders()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return redirect()->route('wholesale.customer.login');
        }
        $orders = $this->userOrdersQuery($user)->with(['details', 'handler'])->latest()->paginate(15);
        $totalSpent = $this->userOrdersQuery($user)->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');
        $tier = $this->computeTier($totalSpent);

        $user = $this->safeProfile($user);
        return view('wholesale.customer.orders', compact('user', 'orders', 'totalSpent', 'tier'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return redirect()->route('wholesale.customer.login');
        }
        $qq = $this->userOrdersQuery($user);
        $totalSpent = (clone $qq)->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');
        $totalOrders = (clone $qq)->count();
        $tier = $this->computeTier($totalSpent);

        $orders = (clone $qq)->with(['details'])->latest()->paginate(20);
        $nextTier = null;
        $tiers = [
            ['label' => 'VIP', 'min' => 10000000, 'discount' => 10],
            ['label' => 'Silver', 'min' => 20000000, 'discount' => 15],
            ['label' => 'Gold', 'min' => 30000000, 'discount' => 20],
            ['label' => 'Platinum', 'min' => 50000000, 'discount' => 25],
        ];
        foreach ($tiers as $t) {
            if ($totalSpent < $t['min']) { $nextTier = $t; break; }
        }

        $user = $this->safeProfile($user);
        return view('wholesale.customer.history', compact('user', 'orders', 'totalSpent', 'totalOrders', 'tier', 'nextTier'));
    }

    public function leaderboard()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return redirect()->route('wholesale.customer.login');
        }

        // Top referrers — only name + count, no sensitive data
        $referrerRanks = User::where('role', 'wholesale_customer')
            ->where('id', '!=', $user->id)
            ->withCount('referrals')
            ->orderByDesc('referrals_count')
            ->take(50)
            ->get(['id', 'name', 'referrals_count']);

        $topReferrers = $referrerRanks->where('referrals_count', '>', 0)->take(20);

        // Current user position
        $myReferralsCount = $user->referrals()->count();
        $myPosition = $referrerRanks->where('referrals_count', '>', $myReferralsCount)->count() + 1;

        $totalReferrals = $user->referrals()->count();

        $user = $this->safeProfile($user);
        return view('wholesale.customer.leaderboard', compact(
            'user', 'topReferrers', 'myReferralsCount', 'myPosition',
            'totalReferrals'
        ));
    }

    public function loyalty()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return redirect()->route('wholesale.customer.login');
        }
        $customer = Customer::where('type', 'wholesale')->where(function($q) use ($user) {
            $q->where('phone', $user->phone)->orWhere('email', $user->email);
        })->first();
        $rankInfo = $customer ? app(WholesaleLoyaltyService::class)->getRankInfo($customer) : [];
        $redemptions = \App\Models\WholesaleRedemption::where('is_active', true)->get();

        $totalsQuery = $this->userOrdersQuery($user);
        $totalSpent = (clone $totalsQuery)->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');
        $totalOrders = (clone $totalsQuery)->count();

        $user = $this->safeProfile($user);
        return view('wholesale.customer.loyalty', compact('user', 'rankInfo', 'redemptions', 'totalSpent', 'totalOrders'));
    }

    public function redeem(Request $request, $redemptionId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $customer = Customer::where('type', 'wholesale')->where(function($q) use ($user) {
            $q->where('phone', $user->phone)->orWhere('email', $user->email);
        })->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan.']);
        }

        $result = DB::transaction(function () use ($redemptionId, $customer) {
            $redemption = \App\Models\WholesaleRedemption::lockForUpdate()->find($redemptionId);
            if (!$redemption || !$redemption->is_active) {
                return ['success' => false, 'message' => 'Promo tidak ditemukan.'];
            }
            return app(WholesaleLoyaltyService::class)->redeemCredits($customer, $redemption);
        });
        return response()->json($result);
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'wholesale_customer') {
            return redirect()->route('wholesale.customer.login');
        }
        $order = $this->userOrdersQuery($user)->with(['details', 'handler', 'customer'])->findOrFail($id);
        $user = $this->safeProfile($user);
        return view('wholesale.customer.show', compact('user', 'order'));
    }

    public function trackOrder(Request $request)
    {
        $request->validate(['invoice_number' => 'required|string']);
        $order = WholesaleOrder::where('invoice_number', $request->invoice_number)->with(['details', 'handler'])->first();
        if (!$order) return back()->with('error', 'Pesanan dengan kode tersebut tidak ditemukan.');

        $user = Auth::user();
        $qq = $this->userOrdersQuery($user);
        $totalOrders = (clone $qq)->count();
        $activeOrders = (clone $qq)->whereNotIn('status', ['completed', 'cancelled'])->count();
        $totalSpent = (clone $qq)->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');
        $tier = $this->computeTier($totalSpent);
        $orders = (clone $qq)->with(['details', 'handler'])->latest()->paginate(10);
        $user = $this->safeProfile($user);

        return view('wholesale.customer.dashboard', compact('user', 'orders', 'totalOrders', 'activeOrders', 'totalSpent', 'tier'))
            ->with('trackedOrder', $order);
    }

    public function markAllRead()
    {
        $user = Auth::user();
        if ($user) $user->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function showForgotPasswordForm()
    {
        if (Auth::check() && Auth::user()->role === 'wholesale_customer') {
            return redirect()->route('wholesale.customer.dashboard');
        }
        return view('wholesale.customer.forgot-password');
    }

    public function sendForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('role', 'wholesale_customer')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->onlyInput('email');
        }

        $existing = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'Permintaan reset password sudah dikirim. Silakan tunggu diproses Owner.');
        }

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'notes' => 'Permintaan reset password dari portal pelanggan grosir.',
        ]);

        Log::info('Wholesale customer forgot password request', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return back()->with('success', 'Permintaan reset password telah dikirim ke Owner. Anda akan mendapatkan password baru setelah diproses.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('wholesale.customer.login');
    }
}
