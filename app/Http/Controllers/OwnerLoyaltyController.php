<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WholesaleCreditLog;
use App\Models\WholesaleRedemption;
use App\Models\WholesaleCustomerRedemption;
use App\Services\WholesaleLoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerLoyaltyController extends Controller
{
    protected $loyalty;

    public function __construct(WholesaleLoyaltyService $loyalty)
    {
        $this->loyalty = $loyalty;
        $this->middleware('can:owner');
    }

    public function index()
    {
        $customers = Customer::where('type', 'wholesale')
            ->orderByDesc('lifetime_spend')
            ->paginate(20);

        $topRank = \App\Services\WholesaleLoyaltyService::RANK_NAMES[array_key_last(\App\Services\WholesaleLoyaltyService::RANK_NAMES)];

        return view('owner.loyalty.index', compact('customers', 'topRank'));
    }

    public function show(Customer $customer)
    {
        $rankInfo = $this->loyalty->getRankInfo($customer);
        $logs = WholesaleCreditLog::where('customer_id', $customer->id)
            ->latest()
            ->paginate(30);

        return view('owner.loyalty.show', compact('customer', 'rankInfo', 'logs'));
    }

    public function manualAdjust(Request $request, Customer $customer)
    {
        $request->validate([
            'credits' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $customer) {
            $locked = Customer::lockForUpdate()->findOrFail($customer->id);
            $credits = (int) $request->credits;

            if ($credits > 0) {
                $locked->increment('total_credits_earned', $credits);
            } else {
                $locked->increment('total_credits_spent', abs($credits));
            }

            $this->loyalty->checkRankUp($locked);

            WholesaleCreditLog::create([
                'customer_id' => $locked->id,
                'credits' => $credits,
                'gold_points' => 0,
                'type' => 'admin',
                'description' => $request->reason,
                'reference_type' => 'admin',
            ]);
        });

        return back()->with('success', 'Kredit berhasil disesuaikan.');
    }

    public function redemptions()
    {
        $redemptions = WholesaleRedemption::latest()->get();
        return view('owner.loyalty.redemptions', compact('redemptions'));
    }

    public function storeRedemption(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits_required' => 'required|integer|min:1',
            'reward_type' => 'required|in:discount_percent,paket_usaha,free_shipping,product',
            'reward_value' => 'required|numeric|min:0',
            'max_uses_per_customer' => 'required|integer|min:0',
        ]);

        WholesaleRedemption::create($request->only([
            'name', 'description', 'credits_required', 'reward_type', 'reward_value', 'max_uses_per_customer',
        ]));

        return redirect()->route('owner.loyalty.redemptions')
            ->with('success', 'Promo kredit berhasil dibuat.');
    }

    public function updateRedemption(Request $request, WholesaleRedemption $redemption)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'credits_required' => 'required|integer|min:1',
            'reward_type' => 'required|in:discount_percent,paket_usaha,free_shipping,product',
            'reward_value' => 'required|numeric|min:0',
            'max_uses_per_customer' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $redemption->update($request->only([
            'name', 'description', 'credits_required', 'reward_type', 'reward_value', 'max_uses_per_customer', 'is_active',
        ]));

        return redirect()->route('owner.loyalty.redemptions')
            ->with('success', 'Promo kredit diperbarui.');
    }

    public function history()
    {
        $logs = WholesaleCreditLog::with('customer')
            ->latest()
            ->paginate(30);

        return view('owner.loyalty.history', compact('logs'));
    }
}
