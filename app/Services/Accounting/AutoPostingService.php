<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\GoodsReceipt;
use App\Models\JournalEntry;
use App\Models\Payroll;
use App\Models\SalesReturn;
use App\Models\Transaction;
use App\Models\WholesaleOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Enterprise double-entry auto-posting engine.
 *
 * Every business event produces exactly one balanced journal entry
 * (idempotent per source), keeping the general ledger in sync with
 * operations. Posting is fail-safe: when accounting is disabled or the
 * chart of accounts is incomplete, the engine logs and skips instead of
 * blocking operational flows.
 */
class AutoPostingService
{
    private function enabled(): bool
    {
        return (bool) config('accounting.enabled', true);
    }

    private function account(string $key): ?ChartOfAccount
    {
        $code = config("accounting.accounts.{$key}");

        return $code ? ChartOfAccount::where('code', $code)->first() : null;
    }

    private function guard(string $sourceType, int $sourceId): void
    {
        if (JournalService::existsForSource($sourceType, $sourceId)) {
            throw new AccountingAlreadyPostedException("Jurnal untuk sumber {$sourceType}#{$sourceId} sudah ada.");
        }
    }

    /**
     * Retur jurnal jika akun utama tidak tersedia — jangan pernah memblokir operasi.
     */
    private function missing(string $what): void
    {
        Log::warning("AutoPosting skipped — akun akuntansi tidak tersedia: {$what}");
    }

    // ─────────────────────────────────────────────────────────────────────
    // 1. PENJUALAN ECERAN (POS)
    // ─────────────────────────────────────────────────────────────────────
    public function postSale(Transaction $transaction): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('sale', $transaction->id)) {
            return null;
        }

        $cash = $this->account('kas');
        $receivable = $this->account('receivable_retail');
        $revenue = $this->account('revenue_retail');
        $cogs = $this->account('cogs');
        $inventory = $this->account('inventory');

        if (! $cash || ! $revenue) {
            $this->missing('Kas / Pendapatan');

            return null;
        }

        $total = (float) $transaction->total_amount;
        $paid = (float) $transaction->paid_amount;
        $debt = (float) $transaction->debt_amount;
        $tax = (float) $transaction->tax_amount;
        $cogsAmt = (float) $transaction->details()->sum(DB::raw('purchase_price * quantity'));

        if ($total <= 0) {
            return null;
        }

        $lines = [];

        // Aset masuk: kas (tunai) atau piutang (kas bon)
        if ($paid > 0) {
            $lines[] = ['account_id' => $cash->id, 'debit' => $paid, 'credit' => 0, 'memo' => 'Pembayaran tunai'];
        }
        if ($debt > 0) {
            $lines[] = [
                'account_id' => ($receivable?->id ?? $cash->id),
                'debit' => $debt,
                'credit' => 0,
                'memo' => 'Piutang kas bon',
                'contact_type' => 'customer',
                'contact_id' => $transaction->customer_id,
            ];
        }

        // Pendapatan (bersih PPN) + utang PPN jika pajak aktif
        $revenueAmount = $total - $tax;
        $lines[] = ['account_id' => $revenue->id, 'debit' => 0, 'credit' => $revenueAmount, 'memo' => 'Pendapatan penjualan eceran'];

        if ($tax > 0) {
            $taxPayable = $this->account('payable_tax');
            $lines[] = ['account_id' => $taxPayable?->id ?? $revenue->id, 'debit' => 0, 'credit' => $tax, 'memo' => 'PPN keluaran'];
        }

        // HPP & persediaan (hanya jika akun tersedia dan nilai > 0)
        if ($cogs && $inventory && $cogsAmt > 0) {
            $lines[] = ['account_id' => $cogs->id, 'debit' => $cogsAmt, 'credit' => 0, 'memo' => 'HPP penjualan'];
            $lines[] = ['account_id' => $inventory->id, 'debit' => 0, 'credit' => $cogsAmt, 'memo' => 'Pengurangan persediaan'];
        }

        return $this->commit('sale', $transaction->id, $transaction->branch_id, [
            'date' => $transaction->created_at->toDateString(),
            'description' => 'Penjualan #'.$transaction->invoice_number,
            'transaction_id' => $transaction->id,
            'source_type' => 'sale',
            'created_by' => $transaction->user_id,
        ], $lines);
    }

    // ─────────────────────────────────────────────────────────────────────
    // 2. BEBAN (EXPENSE)
    // ─────────────────────────────────────────────────────────────────────
    public function postExpense(Expense $expense, ?int $accountId = null): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('expense', $expense->id)) {
            return null;
        }

        $cash = $this->account('kas');
        if (! $cash) {
            $this->missing('Kas');

            return null;
        }

        // Resolusi akun beban: eksplisit → peta kategori → default beban lain-lain
        $expenseAccount = $accountId ? ChartOfAccount::find($accountId) : null;
        if (! $expenseAccount) {
            $expenseAccount = $this->expenseAccountFor($expense);
        }
        if (! $expenseAccount) {
            $this->missing('Akun beban untuk expense #'.$expense->id);

            return null;
        }

        $amount = (float) $expense->amount;
        if ($amount <= 0) {
            return null;
        }

        $lines = [
            ['account_id' => $expenseAccount->id, 'debit' => $amount, 'credit' => 0, 'memo' => $expense->description],
            ['account_id' => $cash->id, 'debit' => 0, 'credit' => $amount, 'memo' => 'Pembayaran '.$expense->description],
        ];

        return $this->commit('expense', $expense->id, $expense->branch_id, [
            'date' => $expense->date->toDateString(),
            'description' => 'Beban: '.$expense->description,
            'transaction_id' => $expense->id,
            'source_type' => 'expense',
            'created_by' => $expense->user_id,
        ], $lines);
    }

    private function expenseAccountFor(Expense $expense): ?ChartOfAccount
    {
        $categoryName = strtolower(trim($expense->category?->name ?? ''));

        // 1. Peta kategori → kode akun (config)
        $map = [
            'employee salary' => 'expense_salary',
            'gaji' => 'expense_salary',
            'salary' => 'expense_salary',
        ];
        foreach ($map as $needle => $key) {
            if (str_contains($categoryName, $needle)) {
                $acc = $this->account($key);
                if ($acc) {
                    return $acc;
                }
            }
        }

        // 2. Kata kunci kategori → pencocokan nama akun COA
        $candidates = ChartOfAccount::byType('expense')->active()->where('is_posting', true)->orderBy('code')->get();
        $keywords = [
            'sewa' => ['sewa', 'rent'],
            'listrik' => ['listrik', 'electric'],
            'air' => ['air', 'water'],
            'transport' => ['transport', 'pengiriman', 'distribusi', 'bbm'],
            'pemasaran' => ['pemasaran', 'marketing', 'promosi', 'iklan'],
            'administrasi' => ['administrasi', 'admin'],
            'telepon' => ['telepon', 'internet'],
            'bpjs' => ['bpjs', 'jamsostek'],
            'perawatan' => ['perawatan', 'perbaikan', 'maintenance'],
            'perlengkapan' => ['perlengkapan'],
            'penyusutan' => ['penyusutan', 'depreciation'],
        ];
        foreach ($keywords as $words) {
            if (! collect($words)->some(fn ($w) => str_contains($categoryName, $w))) {
                continue;
            }
            $acc = $candidates->first(function (ChartOfAccount $a) use ($words) {
                return collect($words)->some(fn ($w) => str_contains(strtolower($a->name), $w));
            });
            if ($acc) {
                return $acc;
            }
        }

        // 3. Pencocokan nama bebas (nama kategori terdapat pada nama akun)
        if ($categoryName) {
            $acc = $candidates->first(fn ($a) => str_contains(strtolower($a->name), $categoryName));
            if ($acc) {
                return $acc;
            }
        }

        // 4. Fallback terakhir: beban lain-lain (jangan pernah blokir)
        return $this->account('expense_other');
    }

    // ─────────────────────────────────────────────────────────────────────
    // 3. PENJUALAN GROSIR (B2B) — diakui saat order dikonfirmasi
    // ─────────────────────────────────────────────────────────────────────
    public function postWholesaleOrder(WholesaleOrder $order): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('wholesale', $order->id)) {
            return null;
        }

        $receivable = $this->account('receivable_wholesale');
        $cash = $this->account('kas');
        $revenue = $this->account('revenue_wholesale');
        $cogs = $this->account('cogs');
        $inventory = $this->account('inventory');

        if (! $revenue || (! $receivable && ! $cash)) {
            $this->missing('Pendapatan Grosir / Piutang Grosir');

            return null;
        }

        $total = (float) $order->total_amount;
        $shipping = (float) $order->shipping_cost;
        $cogsAmt = (float) $order->details()->with('product')->get()->sum(
            fn ($d) => (float) ($d->product?->purchase_price ?? 0) * (int) $d->quantity
        );

        if ($total <= 0) {
            return null;
        }

        $lines = [
            ['account_id' => $receivable?->id ?? $cash->id, 'debit' => $total + $shipping, 'credit' => 0,
                'memo' => 'Piutang order grosir', 'contact_type' => 'customer', 'contact_id' => $order->customer_id],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => $total, 'memo' => 'Pendapatan grosir'],
        ];

        if ($shipping > 0) {
            $lines[] = ['account_id' => $revenue->id, 'debit' => 0, 'credit' => $shipping, 'memo' => 'Ongkos kirim ditagihkan'];
        }

        if ($cogs && $inventory && $cogsAmt > 0) {
            $lines[] = ['account_id' => $cogs->id, 'debit' => $cogsAmt, 'credit' => 0, 'memo' => 'HPP grosir'];
            $lines[] = ['account_id' => $inventory->id, 'debit' => 0, 'credit' => $cogsAmt, 'memo' => 'Pengurangan persediaan grosir'];
        }

        return $this->commit('wholesale', $order->id, $order->branch_id, [
            'date' => $order->confirmed_at?->toDateString() ?? now()->toDateString(),
            'description' => 'Penjualan grosir #'.$order->invoice_number,
            'transaction_id' => $order->id,
            'source_type' => 'wholesale',
            'created_by' => $order->user_id ?? auth()->id(),
        ], $lines);
    }

    // ─────────────────────────────────────────────────────────────────────
    // 4. PEMBAYARAN PIUTANG (KAS BON)
    // ─────────────────────────────────────────────────────────────────────
    public function postDebtPayment(DebtPayment $payment): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('debt_payment', $payment->id)) {
            return null;
        }

        $cash = $this->account('kas');
        $receivable = $this->account('receivable_retail');
        if (! $cash || ! $receivable) {
            $this->missing('Kas / Piutang');

            return null;
        }

        $amount = (float) $payment->amount;
        if ($amount <= 0) {
            return null;
        }

        $lines = [
            ['account_id' => $cash->id, 'debit' => $amount, 'credit' => 0, 'memo' => 'Penerimaan pembayaran kas bon'],
            ['account_id' => $receivable->id, 'debit' => 0, 'credit' => $amount,
                'memo' => 'Pelunasan piutang', 'contact_type' => 'customer', 'contact_id' => $payment->transaction?->customer_id],
        ];

        return $this->commit('debt_payment', $payment->id, $payment->transaction?->branch_id, [
            'date' => $payment->payment_date->toDateString(),
            'description' => 'Pembayaran piutang #'.($payment->transaction?->invoice_number ?? $payment->id),
            'transaction_id' => $payment->id,
            'source_type' => 'debt_payment',
            'created_by' => auth()->id(),
        ], $lines);
    }

    // ─────────────────────────────────────────────────────────────────────
    // 5. RETUR PENJUALAN — saat disetujui (approved)
    // ─────────────────────────────────────────────────────────────────────
    public function postSalesReturn(SalesReturn $return): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('sales_return', $return->id)) {
            return null;
        }

        $cash = $this->account('kas');
        $receivable = $this->account('receivable_retail');
        $revenue = $this->account('revenue_retail');
        $cogs = $this->account('cogs');
        $inventory = $this->account('inventory');

        if (! $revenue || (! $cash && ! $receivable)) {
            $this->missing('Pendapatan / Kas');

            return null;
        }

        $refund = (float) $return->total_refund;
        if ($refund <= 0) {
            return null;
        }

        $lines = [
            // Pendapatan dikoreksi (balik kredit pendapatan → debit)
            ['account_id' => $revenue->id, 'debit' => $refund, 'credit' => 0, 'memo' => 'Koreksi pendapatan retur'],
            // Kas dikembalikan / piutang dikurangi
            ['account_id' => $receivable?->id ?? $cash->id, 'debit' => 0, 'credit' => $refund,
                'memo' => 'Refund retur', 'contact_type' => 'customer', 'contact_id' => $return->transaction?->customer_id],
        ];

        // Balik HPP: persediaan kembali, HPP dikurangi
        $returnCogs = (float) $return->items()->with('transactionDetail')->get()->sum(
            fn ($i) => (float) ($i->transactionDetail?->purchase_price ?? 0) * (int) $i->quantity
        );
        if ($cogs && $inventory && $returnCogs > 0) {
            $lines[] = ['account_id' => $inventory->id, 'debit' => $returnCogs, 'credit' => 0, 'memo' => 'Persediaan kembali dari retur'];
            $lines[] = ['account_id' => $cogs->id, 'debit' => 0, 'credit' => $returnCogs, 'memo' => 'Koreksi HPP retur'];
        }

        return $this->commit('sales_return', $return->id, $return->branch_id, [
            'date' => $return->approved_at?->toDateString() ?? now()->toDateString(),
            'description' => 'Retur penjualan #'.$return->return_number,
            'transaction_id' => $return->id,
            'source_type' => 'sales_return',
            'created_by' => $return->approved_by ?? auth()->id(),
        ], $lines);
    }

    // ─────────────────────────────────────────────────────────────────────
    // 6. PENERIMAAN BARANG (GOODS RECEIPT) — persediaan vs utang usaha
    // ─────────────────────────────────────────────────────────────────────
    public function postGoodsReceipt(GoodsReceipt $receipt): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('goods_receipt', $receipt->id)) {
            return null;
        }

        $inventory = $this->account('inventory');
        $payableTrade = $this->account('payable_trade');
        if (! $inventory || ! $payableTrade) {
            $this->missing('Persediaan / Utang Usaha');

            return null;
        }

        $amount = (float) $receipt->quantity * (float) $receipt->unit_cost;
        if ($amount <= 0) {
            return null;
        }

        $lines = [
            ['account_id' => $inventory->id, 'debit' => $amount, 'credit' => 0, 'memo' => 'Persediaan masuk'],
            ['account_id' => $payableTrade->id, 'debit' => 0, 'credit' => $amount, 'memo' => 'Utang usaha ke supplier'],
        ];

        return $this->commit('goods_receipt', $receipt->id, $receipt->branch_id, [
            'date' => $receipt->created_at->toDateString(),
            'description' => 'Penerimaan barang #'.($receipt->receipt_number ?? $receipt->id),
            'transaction_id' => $receipt->id,
            'source_type' => 'goods_receipt',
            'created_by' => $receipt->recorded_by ?? auth()->id(),
        ], $lines);
    }

    // ─────────────────────────────────────────────────────────────────────
    // 7. PAYROLL — beban gaji, utang potongan, kas keluar
    // ─────────────────────────────────────────────────────────────────────
    public function postPayroll(Payroll $payroll): ?JournalEntry
    {
        if (! $this->enabled() || JournalService::existsForSource('payroll', $payroll->id)) {
            return null;
        }

        $cash = $this->account('kas');
        $expenseSal = $this->account('expense_salary');
        if (! $cash || ! $expenseSal) {
            $this->missing('Kas / Beban Gaji');

            return null;
        }

        $total = (float) $payroll->total_salary;
        if ($total <= 0) {
            return null;
        }

        $lines = [
            ['account_id' => $expenseSal->id, 'debit' => $total, 'credit' => 0,
                'memo' => 'Beban gaji '.$payroll->month, 'contact_type' => 'employee', 'contact_id' => $payroll->user_id],
            ['account_id' => $cash->id, 'debit' => 0, 'credit' => $total, 'memo' => 'Pembayaran gaji '.$payroll->month],
        ];

        return $this->commit('payroll', $payroll->id, $payroll->user?->branch_id, [
            'date' => now()->toDateString(),
            'description' => 'Penggajian '.$payroll->month,
            'transaction_id' => $payroll->id,
            'source_type' => 'payroll',
            'created_by' => auth()->id(),
        ], $lines);
    }

    /**
     * Aggregate payroll posting — satu jurnal untuk seluruh batch bulanan
     * (lebih ringkas dan sesuai best practice GL).
     */
    public function postPayrollBatch(Collection $payrolls, string $month, ?int $branchId = null): ?JournalEntry
    {
        if (! $this->enabled()) {
            return null;
        }
        $payrolls = $payrolls->filter(fn ($p) => (float) $p->total_salary > 0);
        if ($payrolls->isEmpty()) {
            return null;
        }

        $cash = $this->account('kas');
        $expenseSal = $this->account('expense_salary');
        if (! $cash || ! $expenseSal) {
            $this->missing('Kas / Beban Gaji');

            return null;
        }

        $batchKey = 'payroll_batch_'.$month.'_'.($branchId ?? 'all');
        if (! $this->enabled() || JournalService::existsForSource('payroll', crc32($batchKey))) {
            return null;
        }
        $total = round($payrolls->sum('total_salary'), 2);

        $lines = [
            ['account_id' => $expenseSal->id, 'debit' => $total, 'credit' => 0, 'memo' => "Beban gaji agregat {$month}"],
            ['account_id' => $cash->id, 'debit' => 0, 'credit' => $total, 'memo' => "Pembayaran gaji agregat {$month}"],
        ];

        $first = $payrolls->first();

        return $this->commit('payroll', crc32($batchKey), $branchId, [
            'date' => now()->toDateString(),
            'description' => "Penggajian {$month} (agregat ".$payrolls->count().' karyawan)',
            'transaction_id' => $first->id,
            'source_type' => 'payroll',
            'created_by' => auth()->id(),
        ], $lines);
    }

    // ─────────────────────────────────────────────────────────────────────
    // COMMIT — tulis jurnal (draft lalu langsung posting)
    // ─────────────────────────────────────────────────────────────────────
    private function commit(string $sourceType, int $sourceId, ?int $branchId, array $params, array $lines): ?JournalEntry
    {
        $this->guard($sourceType, $sourceId);

        try {
            // Atomic: jika satu baris gagal, seluruh jurnal dibatalkan.
            $entry = DB::transaction(function () use ($params, $lines, $branchId) {
                return JournalService::create(
                    [
                        'date' => $params['date'],
                        'description' => $params['description'],
                        'transaction_id' => $params['transaction_id'],
                        'source_type' => $params['source_type'],
                        'created_by' => $params['created_by'],
                        'status' => JournalEntry::STATUS_POSTED,
                    ],
                    $lines,
                    $branchId
                );
            });

            Log::info('AutoPosting success', [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'journal' => $entry->journal_number,
            ]);

            return $entry;
        } catch (\DomainException $e) {
            Log::warning("AutoPosting skipped ({$sourceType}#{$sourceId}): {$e->getMessage()}");

            return null;
        } catch (\Throwable $e) {
            Log::error("AutoPosting failed ({$sourceType}#{$sourceId}): {$e->getMessage()}", [
                'exception' => get_class($e),
            ]);

            return null;
        }
    }
}
