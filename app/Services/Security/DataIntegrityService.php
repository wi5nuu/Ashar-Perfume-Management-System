<?php

namespace App\Services\Security;

use App\Models\Transaction;
use App\Models\WholesaleOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataIntegrityService
{
    public function checksumTransaction(Transaction $transaction): string
    {
        $data = $transaction->only(['id', 'total_amount', 'discount', 'final_amount', 'payment_status', 'branch_id']);
        $details = $transaction->details()->orderBy('id')->get()->map(fn($d) => [
            $d->product_id, $d->quantity, $d->price, $d->subtotal,
        ])->toArray();

        return hash_hmac('sha256', json_encode(['transaction' => $data, 'details' => $details]), config('app.key'));
    }

    public function verifyTransactionIntegrity(Transaction $transaction): bool
    {
        $storedChecksum = $transaction->integrity_hash;
        if (!$storedChecksum) {
            Log::warning("Transaction #{$transaction->id} has no integrity hash");
            return false;
        }

        $calculatedChecksum = $this->checksumTransaction($transaction);

        if (!hash_equals($storedChecksum, $calculatedChecksum)) {
            Log::critical("INTEGRITY VIOLATION: Transaction #{$transaction->id} has been tampered with!");
            return false;
        }

        return true;
    }

    public function verifyAllTransactions(): array
    {
        $violations = [];
        Transaction::chunk(100, function ($transactions) use (&$violations) {
            foreach ($transactions as $transaction) {
                if (!$this->verifyTransactionIntegrity($transaction)) {
                    $violations[] = $transaction->id;
                }
            }
        });
        return $violations;
    }

    public function scanForAnomalies(): array
    {
        $anomalies = [];

        $negativeStock = DB::table('inventories')
            ->where('current_stock', '<', 0)
            ->count();
        if ($negativeStock > 0) {
            $anomalies[] = "{$negativeStock} product(s) have negative stock";
        }

        $orphanDetails = DB::table('transaction_details')
            ->leftJoin('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereNull('transactions.id')
            ->count();
        if ($orphanDetails > 0) {
            $anomalies[] = "{$orphanDetails} orphan transaction detail(s)";
        }

        $unmatchedTotals = Transaction::whereColumn('total_amount', '!=', DB::raw('COALESCE((SELECT SUM(subtotal) FROM transaction_details WHERE transaction_id = transactions.id), 0)'))
            ->count();
        if ($unmatchedTotals > 0) {
            $anomalies[] = "{$unmatchedTotals} transaction(s) with mismatched totals";
        }

        return $anomalies;
    }

    public function getIntegrityScore(): int
    {
        return Cache::remember('integrity_score', 300, function () {
            $score = 100;

            try {
                $negativeStock = DB::table('inventories')->where('current_stock', '<', 0)->count();
                $score -= $negativeStock * 5;

                $orphanDetails = DB::table('transaction_details')
                    ->leftJoin('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                    ->whereNull('transactions.id')->count();
                $score -= $orphanDetails * 10;

                $unmatched = Transaction::whereColumn('total_amount', '!=', DB::raw('COALESCE((SELECT SUM(subtotal) FROM transaction_details WHERE transaction_id = transactions.id), 0)'))->count();
                $score -= $unmatched * 5;
            } catch (\Throwable $e) {
                $score = 0;
            }

            return max(0, min(100, $score));
        });
    }
}
