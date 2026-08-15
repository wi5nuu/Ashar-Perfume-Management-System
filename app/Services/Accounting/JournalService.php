<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public const SOURCE_PREFIX = [
        'sale' => 'JNL',
        'expense' => 'JNL-EXP',
        'wholesale' => 'JNL-GRS',
        'debt_payment' => 'JNL-BYR',
        'sales_return' => 'JNL-RTR',
        'payroll' => 'JNL-GJI',
        'goods_receipt' => 'JNL-PEM',
        'manual' => 'JNL',
    ];

    /**
     * Enterprise journal number: prefix + YYYYMMDD + sequential per day.
     * Format: JNL-20260815-000123
     */
    public static function generateNumber(string $type = 'manual'): string
    {
        $prefix = self::SOURCE_PREFIX[$type] ?? 'JNL';
        $date = now()->format('Ymd');
        $base = $prefix.'-'.$date.'-';

        $last = JournalEntry::where('journal_number', 'like', $base.'%')
            ->orderByDesc('journal_number')
            ->value('journal_number');

        $seq = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $base.str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a journal entry from a structured collection of lines.
     * Each line: ['account_id'|'account_code', 'debit', 'credit', 'memo', 'contact_type', 'contact_id']
     */
    public static function create(array $params, array $lines, ?int $branchId = null): JournalEntry
    {
        if (count($lines) < 2) {
            throw new \DomainException('Jurnal minimal membutuhkan 2 baris (debit & kredit).');
        }

        $totalDebit = 0.0;
        $totalCredit = 0.0;

        $normalized = collect($lines)->map(function (array $line) use (&$totalDebit, &$totalCredit) {
            $account = isset($line['account_id'])
                ? ChartOfAccount::findOrFail($line['account_id'])
                : ChartOfAccount::where('code', $line['account_code'] ?? '')->firstOrFail();

            if (! $account->is_posting) {
                throw new \DomainException("Akun {$account->code} ({$account->name}) bukan akun posting.");
            }
            if (! $account->is_active) {
                throw new \DomainException("Akun {$account->code} ({$account->name}) tidak aktif.");
            }

            $debit = round((float) ($line['debit'] ?? 0), 2);
            $credit = round((float) ($line['credit'] ?? 0), 2);

            if ($debit < 0 || $credit < 0) {
                throw new \DomainException('Nilai debit/kredit tidak boleh negatif.');
            }
            if ($debit > 0 && $credit > 0) {
                throw new \DomainException('Satu baris tidak boleh berisi debit dan kredit sekaligus.');
            }
            if ($debit == 0 && $credit == 0) {
                throw new \DomainException('Baris jurnal harus memiliki nilai.');
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            return [
                'account_id' => $account->id,
                'contact_type' => $line['contact_type'] ?? null,
                'contact_id' => $line['contact_id'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
                'memo' => $line['memo'] ?? null,
            ];
        });

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \DomainException(
                'Jurnal tidak seimbang: debit '.number_format($totalDebit, 2).
                ' vs kredit '.number_format($totalCredit, 2).'.'
            );
        }

        return DB::transaction(function () use ($params, $normalized, $totalDebit, $totalCredit, $branchId) {
            $period = AccountingPeriodService::resolveForDate($params['date']);

            $entry = JournalEntry::create([
                'journal_number' => $params['journal_number'] ?? self::generateNumber($params['source_type'] ?? 'manual'),
                'period_id' => $period->id,
                'branch_id' => $branchId,
                'transaction_id' => $params['transaction_id'] ?? null,
                'transaction_type' => $params['source_type'] ?? 'manual',
                'date' => $params['date'],
                'description' => $params['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => $params['status'] ?? JournalEntry::STATUS_DRAFT,
                'created_by' => $params['created_by'] ?? auth()->id(),
            ]);

            foreach ($normalized as $line) {
                JournalDetail::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'contact_type' => $line['contact_type'],
                    'contact_id' => $line['contact_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'memo' => $line['memo'],
                ]);
            }

            return $entry;
        });
    }

    /**
     * Idempotency guard: a source transaction may only ever produce one
     * journal entry. Prevents duplicate postings on retry.
     */
    public static function existsForSource(string $sourceType, int $sourceId): bool
    {
        return JournalEntry::where('transaction_type', $sourceType)
            ->where('transaction_id', $sourceId)
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->exists();
    }
}
