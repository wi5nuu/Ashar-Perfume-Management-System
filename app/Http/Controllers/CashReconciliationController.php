<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaction;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CashReconciliationController extends Controller
{
    /**
     * Show reconciliation page for a closed shift.
     */
    public function show(Shift $shift)
    {
        Gate::authorize('manage_transactions');

        if ($shift->status !== 'closed') {
            return redirect()->route('shifts.show', $shift)
                ->with('error', 'Shift harus ditutup terlebih dahulu sebelum rekonsiliasi.');
        }

        $shift->load(['user']);

        // Calculate expected cash breakdown from transactions
        $cashSales = Transaction::where('user_id', $shift->user_id)
            ->where('payment_method', 'cash')
            ->whereBetween('created_at', [$shift->start_time, $shift->end_time ?? now()])
            ->selectRaw('SUM(paid_amount - change_amount) as net_cash')
            ->value('net_cash') ?? 0;

        $startFmt = $shift->start_time?->format('Y-m-d H:i:s') ?? '1970-01-01 00:00:00';
        $endFmt   = ($shift->end_time ?? now())->format('Y-m-d H:i:s');
        $cashExpenses = Expense::where('user_id', $shift->user_id)
            ->whereBetween('date', [$startFmt, $endFmt])
            ->sum('amount') ?? 0;

        $expectedCash = $shift->initial_cash + $cashSales - $cashExpenses;

        // Denomination data
        $denominationValues = [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100];

        return view('shifts.reconciliation', compact('shift', 'expectedCash', 'cashSales', 'cashExpenses', 'denominationValues'));
    }

    /**
     * Store reconciliation data (actual cash count with denominations).
     */
    public function store(Request $request, Shift $shift)
    {
        Gate::authorize('manage_transactions');

        if ($shift->status !== 'closed') {
            return back()->with('error', 'Shift harus sudah ditutup.');
        }

        $validated = $request->validate([
            'denominations' => 'required|array',
            'denominations.*' => 'integer|min:0',
            'cash_breakdown' => 'nullable|array',
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($shift, $validated) {
                // Calculate actual cash from denominations
                $actualCash = 0;
                foreach ($validated['denominations'] as $value => $count) {
                    $actualCash += ((int) $value) * ((int) $count);
                }

                // Add coins if provided
                if (!empty($validated['cash_breakdown'])) {
                    foreach ($validated['cash_breakdown'] as $value => $count) {
                        $actualCash += ((int) $value) * ((int) $count);
                    }
                }

                $discrepancy = $actualCash - ($shift->expected_cash ?? 0);

                $shift->update([
                    'denominations'  => $validated['denominations'],
                    'cash_breakdown' => $validated['cash_breakdown'] ?? [],
                    'actual_cash'    => $actualCash,
                    'discrepancy'    => $discrepancy,
                ]);
            });

            Log::info('Cash reconciliation saved', [
                'shift_id' => $shift->id,
                'user_id'  => auth()->id(),
            ]);

            return redirect()->route('shifts.reconciliation', $shift)
                ->with('success', 'Rekonsiliasi kas berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Reconciliation failed', ['shift_id' => $shift->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menyimpan rekonsiliasi: ' . $e->getMessage());
        }
    }

    /**
     * Manager review and approve the reconciliation.
     */
    public function review(Request $request, Shift $shift)
    {
        Gate::authorize('manage_employees');

        if ($shift->reviewed_at) {
            return back()->with('error', 'Rekonsiliasi sudah direview.');
        }

        $validated = $request->validate([
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        $shift->update([
            'reviewed_at'   => now(),
            'reviewed_by'   => auth()->id(),
            'manager_notes' => $validated['manager_notes'] ?? null,
        ]);

        Log::info('Reconciliation reviewed', [
            'shift_id'   => $shift->id,
            'reviewer_id' => auth()->id(),
        ]);

        return redirect()->route('shifts.reconciliation', $shift)
            ->with('success', 'Rekonsiliasi telah direview dan disetujui.');
    }
}
