<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    /**
     * Monthly commission view.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_employees');

        $month = $request->get('month', now()->format('Y-m'));

        $query = Commission::with(['user', 'transaction'])
            ->where('month', $month);

        $userFilter = $request->get('user_id');
        if ($userFilter) {
            $query->where('user_id', $userFilter);
        }

        $commissions = $query->latest()->paginate(20);
        $users = User::where('is_active', true)->orderBy('name')->get();

        // Summary
        $totalCommission = Commission::where('month', $month)->sum('commission_amount');
        $paidCommission = Commission::where('month', $month)->where('status', 'paid')->sum('commission_amount');
        $pendingCommission = $totalCommission - $paidCommission;

        // Per-user summary
        $perUser = Commission::where('month', $month)
            ->select('user_id', DB::raw('SUM(commission_amount) as total'))
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return view('employees.commissions', compact('commissions', 'users', 'month', 'totalCommission', 'paidCommission', 'pendingCommission', 'perUser', 'userFilter'));
    }

    /**
     * Auto-calculate commissions for a month.
     */
    public function calculate(Request $request)
    {
        Gate::authorize('manage_employees');

        $validated = $request->validate([
            'month'            => 'required|date_format:Y-m',
            'commission_rate'  => 'required|numeric|min:0|max:100',
        ]);

        $month = $validated['month'];
        $rate = (float) $validated['commission_rate'];

        try {
            $count = DB::transaction(function () use ($month, $rate) {
                $startDate = $month . '-01';
                $endDate = now()->parse($startDate)->endOfMonth();

                $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
                    ->where('payment_status', '!=', 'cancelled')
                    ->when(!auth()->user()->isOwner(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
                    ->get();

                $count = 0;
                foreach ($transactions as $trx) {
                    // Skip if commission already exists
                    if (Commission::where('transaction_id', $trx->id)->where('month', $month)->lockForUpdate()->exists()) {
                        continue;
                    }

                    $amount = (float) $trx->total_amount * ($rate / 100);

                    Commission::create([
                        'user_id'           => $trx->user_id,
                        'transaction_id'    => $trx->id,
                        'commission_rate'   => $rate,
                        'commission_amount' => $amount,
                        'month'             => $month,
                        'status'            => 'pending',
                    ]);

                    $count++;
                }

                return $count;
            });

            Log::info('Commissions calculated', [
                'month'   => $month,
                'rate'    => $rate,
                'count'   => $count,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', "Berhasil menghitung komisi {$count} transaksi untuk bulan {$month}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghitung komisi: ' . $e->getMessage());
        }
    }

    /**
     * Mark commissions as paid for a user in a month.
     */
    public function markPaid(Request $request)
    {
        Gate::authorize('manage_employees');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|date_format:Y-m',
        ]);

        $count = Commission::where('user_id', $validated['user_id'])
            ->where('month', $validated['month'])
            ->where('status', 'pending')
            ->update(['status' => 'paid']);

        return back()->with('success', "Berhasil menandai {$count} komisi sebagai paid.");
    }
}
