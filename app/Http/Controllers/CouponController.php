<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CouponController extends Controller
{
    public function index()
    {
        Gate::authorize('manage_coupons');
        $coupons = Coupon::latest()->paginate(15);
        return view('coupons.index', compact('coupons'));
    }

    public function create()
    {
        Gate::authorize('manage_coupons');
        return view('coupons.create');
    }

    public function store(\App\Http\Requests\StoreCouponRequest $request)
    {
        Gate::authorize('manage_coupons');
        $validated = $request->validated();

        Coupon::create($validated);
        return redirect()->route('coupons.index')->with('success', 'Kupon berhasil dibuat');
    }

    public function show(Coupon $coupon)
    {
        Gate::authorize('manage_coupons');
        return view('coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        Gate::authorize('manage_coupons');
        return view('coupons.edit', compact('coupon'));
    }

    public function update(\App\Http\Requests\StoreCouponRequest $request, Coupon $coupon)
    {
        Gate::authorize('manage_coupons');
        $validated = $request->validated();

        $coupon->update($validated);
        return redirect()->route('coupons.index')->with('success', 'Kupon berhasil diperbarui');
    }

    public function destroy(Coupon $coupon)
    {
        Gate::authorize('manage_coupons');
        $coupon->delete();
        return redirect()->route('coupons.index')->with('success', 'Kupon berhasil dihapus');
    }

    public function redeem(Request $request, Coupon $coupon)
    {
        Gate::authorize('manage_coupons');

        try {
            DB::transaction(function () use ($coupon) {
                // Lock row to prevent concurrent over-redemption
                $locked = Coupon::lockForUpdate()->findOrFail($coupon->id);

                if ($locked->expiration_date && $locked->expiration_date < now()) {
                    throw new \RuntimeException('Kupon sudah kadaluarsa');
                }

                if ($locked->max_usage && $locked->used_count >= $locked->max_usage) {
                    throw new \RuntimeException('Kupon sudah mencapai batas penggunaan');
                }

                $locked->increment('used_count');
            });

            return response()->json(['message' => 'Kupon berhasil digunakan']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
