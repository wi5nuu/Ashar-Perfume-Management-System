<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CustomerPortalController extends Controller
{
    /**
     * Resolve customer from portal token.
     */
    private function resolveCustomer(string $token): Customer
    {
        return Customer::where('portal_token', $token)->firstOrFail();
    }

    /**
     * Check token expiry — uses portal_token_created_at if available,
     * falls back to updated_at. Tokens expire after 30 days.
     */
    private function isTokenExpired(Customer $customer): bool
    {
        $tokenCreated = $customer->portal_token_created_at
            ?? $customer->updated_at
            ?? $customer->created_at;

        return $tokenCreated->diffInDays(now()) > 30;
    }

    /**
     * Generate or regenerate portal token for a customer.
     */
    public function generateToken(Request $request, Customer $customer)
    {
        Gate::authorize('manage_customers');
        $customer->update([
            'portal_token' => Str::random(48),
        ]);

        return back()->with('success', 'Portal link generated successfully.');
    }

    /**
     * Portal dashboard — orders + debt summary.
     */
    public function dashboard(string $token)
    {
        $customer = $this->resolveCustomer($token);

        if ($this->isTokenExpired($customer)) {
            abort(410, 'Token ini sudah kedaluwarsa. Hubungi toko untuk token baru.');
        }

        $ordersQuery = $customer->wholesaleOrders();
        $totalOrders = (clone $ordersQuery)->where('status', '!=', 'cancelled')->count();
        $pendingOrders = (clone $ordersQuery)->whereIn('status', ['pending', 'processing'])->count();

        // Debt summary: sum of debt_amount - sum of payments
        $totalDebt = (float) $customer->transactions()->where('debt_amount', '>', 0)->sum('debt_amount');
        $totalPaid = (float) DebtPayment::whereIn('transaction_id', $customer->transactions()->pluck('id'))->sum('amount');
        $remainingDebt = max(0, $totalDebt - $totalPaid);

        $recentOrders = (clone $ordersQuery)
            ->with('details.product')
            ->latest()
            ->take(5)
            ->get();

        return view('portal.dashboard', compact(
            'customer', 'token', 'totalOrders', 'pendingOrders',
            'remainingDebt', 'recentOrders'
        ));
    }

    /**
     * Wholesale order history.
     */
    public function orders(string $token)
    {
        $customer = $this->resolveCustomer($token);

        if ($this->isTokenExpired($customer)) {
            abort(410, 'Token ini sudah kedaluwarsa. Hubungi toko untuk token baru.');
        }

        $orders = $customer->wholesaleOrders()
            ->with('details.product')
            ->latest()
            ->paginate(15);

        return view('portal.orders', compact('customer', 'token', 'orders'));
    }

    /**
     * Transaction + payment history with running balance.
     */
    public function statement(string $token)
    {
        $customer = $this->resolveCustomer($token);

        if ($this->isTokenExpired($customer)) {
            abort(410, 'Token ini sudah kedaluwarsa. Hubungi toko untuk token baru.');
        }

        $transactions = $customer->transactions()
            ->with('debtPayments')
            ->latest()
            ->get();

        return view('portal.statement', compact('customer', 'token', 'transactions'));
    }
}
