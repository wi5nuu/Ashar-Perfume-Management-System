<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use App\Models\AccountingPeriod;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AutoPostingService
{
    public function postSale(Transaction $transaction): JournalEntry
    {
        return DB::transaction(function () use ($transaction) {
            $period = AccountingPeriod::current();
            if (!$period) throw new \Exception('Tidak ada periode aktif');

            $kas = ChartOfAccount::where('code', '1-101')->firstOrFail();
            $penjualan = ChartOfAccount::where('code', '4-101')->firstOrFail();

            $entry = JournalEntry::create([
                'journal_number' => 'JNL-' . $transaction->created_at->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'period_id' => $period->id,
                'transaction_id' => $transaction->id,
                'transaction_type' => 'sale',
                'date' => $transaction->created_at,
                'description' => 'Penjualan #' . $transaction->invoice_number,
                'total_debit' => $transaction->total_amount,
                'total_credit' => $transaction->total_amount,
                'status' => JournalEntry::STATUS_DRAFT,
                'created_by' => $transaction->user_id,
            ]);

            JournalDetail::create(['journal_entry_id' => $entry->id, 'account_id' => $kas->id, 'debit' => $transaction->total_amount, 'credit' => 0, 'memo' => 'Penerimaan kas']);
            JournalDetail::create(['journal_entry_id' => $entry->id, 'account_id' => $penjualan->id, 'debit' => 0, 'credit' => $transaction->total_amount, 'memo' => 'Pendapatan penjualan']);

            $hpp = ChartOfAccount::where('code', '5-101')->first();
            $persediaan = ChartOfAccount::where('code', '1-105')->first();
            if ($hpp && $persediaan && ($transaction->total_cogs ?? 0) > 0) {
                JournalDetail::create(['journal_entry_id' => $entry->id, 'account_id' => $hpp->id, 'debit' => $transaction->total_cogs, 'credit' => 0, 'memo' => 'HPP']);
                JournalDetail::create(['journal_entry_id' => $entry->id, 'account_id' => $persediaan->id, 'debit' => 0, 'credit' => $transaction->total_cogs, 'memo' => 'Pengurangan persediaan']);
            }

            $entry->post();
            return $entry;
        });
    }

    public function postExpense(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $period = AccountingPeriod::current();
            if (!$period) throw new \Exception('Tidak ada periode aktif');

            $expense = ChartOfAccount::findOrFail($data['account_id']);
            $kas = ChartOfAccount::where('code', '1-101')->firstOrFail();

            $entry = JournalEntry::create([
                'journal_number' => 'JNL-EXP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'period_id' => $period->id,
                'transaction_type' => 'expense',
                'date' => $data['date'],
                'description' => $data['description'],
                'total_debit' => $data['amount'],
                'total_credit' => $data['amount'],
                'status' => JournalEntry::STATUS_DRAFT,
                'created_by' => $data['user_id'],
            ]);

            JournalDetail::create(['journal_entry_id' => $entry->id, 'account_id' => $expense->id, 'debit' => $data['amount'], 'credit' => 0, 'memo' => $data['description']]);
            JournalDetail::create(['journal_entry_id' => $entry->id, 'account_id' => $kas->id, 'debit' => 0, 'credit' => $data['amount'], 'memo' => 'Pembayaran ' . $data['description']]);

            $entry->post();
            return $entry;
        });
    }
}
