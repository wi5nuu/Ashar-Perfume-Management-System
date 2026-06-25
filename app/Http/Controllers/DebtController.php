<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DebtPayment;
use App\Http\Requests\StoreDebtPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DebtController extends Controller
{
    /**
     * Display a listing of unpaid and partially paid transactions.
     * Branch-scoped: non-owners see only their branch.
     */
    public function index()
    {
        Gate::authorize('manage_transactions');

        $query = Transaction::with(['customer', 'user', 'branch', 'debtPayments'])
            ->whereIn('payment_status', ['unpaid', 'partial']);

        $user = auth()->user();
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $transactions = $query->latest()->paginate(20);

        return view('debts.index', compact('transactions'));
    }

    /**
     * Store a payment for a debt.
     *
     * BEFORE (RACE CONDITION):
     *   Read debt_amount → calculate in PHP → update.
     *   Between read and write, a concurrent payment could modify the same row.
     *   paid_amount could double-count if two payments processed simultaneously.
     *
     * AFTER (ATOMIC SQL):
     *   - Re-fetch with lockForUpdate() inside DB::transaction().
     *   - Validate against the locked row's current debt_amount.
     *   - Use DB::raw() with GREATEST() for atomic debt reduction.
     *   - Use DB::raw() increment for paid_amount to avoid double-counting.
     *   - Use CASE expression for payment_status to stay atomic.
     */
    public function storePayment(StoreDebtPaymentRequest $request, Transaction $transaction)
    {
        Gate::authorize('manage_transactions');

        $validated = $request->validated();
        $paymentAmount = (float) $validated['amount'];

        try {
            DB::transaction(function () use ($validated, $transaction, $paymentAmount) {
                // Re-fetch with pessimistic lock — prevents concurrent modification
                $locked = Transaction::lockForUpdate()->findOrFail($transaction->id);

                if ($locked->payment_status === 'paid') {
                    throw new \RuntimeException('This debt is already fully paid.');
                }

                $currentDebt = (float) $locked->debt_amount;

                if ($paymentAmount > $currentDebt) {
                    throw new \RuntimeException(
                        "Payment amount (Rp " . number_format($paymentAmount, 0, ',', '.') .
                        ") exceeds remaining debt (Rp " . number_format($currentDebt, 0, ',', '.') . ")."
                    );
                }

                // Create the payment record
                DebtPayment::create([
                    'transaction_id' => $locked->id,
                    'amount'         => $paymentAmount,
                    'payment_method' => $validated['payment_method'],
                    'payment_date'   => now(),
                    'notes'          => $validated['notes'] ?? null,
                ]);

                // Atomic update — parameterized query prevents SQL injection
                DB::statement(
                    "UPDATE transactions SET
                        debt_amount = GREATEST(0, debt_amount - ?),
                        paid_amount = paid_amount + ?,
                        payment_status = CASE WHEN (debt_amount - ?) <= 0 THEN 'paid' ELSE 'partial' END
                    WHERE id = ?",
                    [$paymentAmount, $paymentAmount, $paymentAmount, $locked->id]
                );

                Log::info('Debt payment recorded', [
                    'transaction_id' => $locked->id,
                    'amount'         => $paymentAmount,
                    'method'         => $validated['payment_method'],
                    'user_id'        => auth()->id(),
                    'branch_id'      => $locked->branch_id,
                ]);
            });

            return back()->with('success', 'Debt payment recorded successfully.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Debt payment failed', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
                'user_id'        => auth()->id(),
            ]);

            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Aging report — group debts by aging buckets.
     */
    public function agingReport(Request $request)
    {
        Gate::authorize('manage_transactions');

        $user = auth()->user();
        $branchFilter = $request->get('branch_id');

        $query = DB::table('transactions')
            ->select(
                'transactions.id',
                'transactions.invoice_number',
                'transactions.customer_id',
                'transactions.debt_amount',
                'transactions.paid_amount',
                'transactions.total_amount',
                'transactions.created_at',
                DB::raw('DATEDIFF(NOW(), transactions.created_at) as days_overdue'),
                DB::raw('CASE
                    WHEN DATEDIFF(NOW(), transactions.created_at) <= 7 THEN "0-7 hari"
                    WHEN DATEDIFF(NOW(), transactions.created_at) <= 30 THEN "8-30 hari"
                    WHEN DATEDIFF(NOW(), transactions.created_at) <= 60 THEN "31-60 hari"
                    ELSE "60+ hari"
                END as aging_bucket')
            )
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->where('debt_amount', '>', 0);

        if (!$user->isOwner()) {
            $query->where('transactions.branch_id', $user->branch_id);
        } elseif ($branchFilter) {
            $query->where('transactions.branch_id', $branchFilter);
        }

        $debts = $query->orderBy('days_overdue', 'desc')->get();

        // Group by bucket
        $grouped = $debts->groupBy('aging_bucket');
        $buckets = ['0-7 hari', '8-30 hari', '31-60 hari', '60+ hari'];

        // Load customer names
        $customerIds = $debts->pluck('customer_id')->filter()->unique();
        $customers = \App\Models\Customer::whereIn('id', $customerIds)->pluck('name', 'id');

        // Branches for filter
        $branches = \App\Models\Branch::all();

        return view('debts.aging', compact('grouped', 'buckets', 'customers', 'branches', 'branchFilter'));
    }
}
