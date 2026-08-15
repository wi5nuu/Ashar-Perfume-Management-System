<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Enterprise financial statement engine.
 *
 * All reports are derived exclusively from POSTED journal entries, keeping
 * the general ledger as the single source of truth (double-entry integrity).
 */
class FinancialStatementService
{
    private const CASH_FLOW_CLASSIFICATION = [
        // Operating
        'sale' => 'operating',
        'debt_payment' => 'operating',
        'expense' => 'operating',
        'sales_return' => 'operating',
        'payroll' => 'operating',
        'manual' => 'operating',
        // Investing
        'fixed_asset' => 'investing',
        // Financing
        'capital' => 'financing',
        'drawing' => 'financing',
    ];

    private function postedDetails(array $filters = []): Collection
    {
        return JournalDetail::query()
            ->with(['account', 'journalEntry'])
            ->whereHas('journalEntry', function ($q) use ($filters) {
                $q->where('status', JournalEntry::STATUS_POSTED);
                if (! empty($filters['from'])) {
                    $q->whereDate('date', '>=', $filters['from']);
                }
                if (! empty($filters['to'])) {
                    $q->whereDate('date', '<=', $filters['to']);
                }
            })
            ->get();
    }

    /**
     * Trial Balance — debit/credit columns with a hard balance check.
     */
    public function trialBalance(?string $from, ?string $to): array
    {
        $rows = ChartOfAccount::active()->orderBy('code')->get()->map(function (ChartOfAccount $account) use ($from, $to) {
            $q = JournalDetail::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($e) => $e->where('status', JournalEntry::STATUS_POSTED));
            if ($from) {
                $q->whereHas('journalEntry', fn ($e) => $e->whereDate('date', '>=', $from));
            }
            if ($to) {
                $q->whereHas('journalEntry', fn ($e) => $e->whereDate('date', '<=', $to));
            }

            $debit = (float) $q->sum('debit');
            $credit = (float) $q->sum('credit');
            $net = $debit - $credit;

            return [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'level' => $account->level,
                'debit' => $net > 0 ? $net : 0.0,
                'credit' => $net < 0 ? abs($net) : 0.0,
            ];
        });

        return [
            'rows' => $rows->filter(fn ($r) => $r['debit'] > 0 || $r['credit'] > 0)->values(),
            'total_debit' => round($rows->sum('debit'), 2),
            'total_credit' => round($rows->sum('credit'), 2),
            'is_balanced' => abs($rows->sum('debit') - $rows->sum('credit')) < 0.01,
            'from' => $from,
            'to' => $to ?? now()->toDateString(),
        ];
    }

    /**
     * Income Statement — PSAK-style multi-section layout:
     * Pendapatan → HPP → Laba Kotor → Beban Operasi → Laba Operasi → Laba Bersih.
     */
    public function incomeStatement(?string $from, ?string $to): array
    {
        $to ??= now()->toDateString();
        $from ??= now()->startOfMonth()->toDateString();

        $income = ChartOfAccount::byType('income')->active()->orderBy('code')->get();
        $expense = ChartOfAccount::byType('expense')->active()->orderBy('code')->get();

        $withBalance = fn (Collection $accounts) => $accounts->map(fn (ChartOfAccount $a) => [
            'code' => $a->code,
            'name' => $a->name,
            'balance' => round($a->balanceBetween($from, $to), 2),
        ]);

        $revenue = $withBalance($income);
        $allExpenses = $withBalance($expense);

        $totalRevenue = round($revenue->sum('balance'), 2);
        $totalAllExp = round($allExpenses->sum('balance'), 2);
        $cogs = round($allExpenses->firstWhere('code', config('accounting.accounts.cogs'))['balance'] ?? 0, 2);
        $expenses = $allExpenses->reject(fn ($e) => $e['code'] === config('accounting.accounts.cogs'))
            ->filter(fn ($e) => $e['balance'] != 0)->values();
        $totalOperating = round($totalAllExp - $cogs, 2);

        return [
            'revenue' => $revenue->filter(fn ($r) => $r['balance'] != 0)->values(),
            'total_revenue' => $totalRevenue,
            'cogs' => $cogs,
            'gross_profit' => round($totalRevenue - $cogs, 2),
            'expenses' => $expenses,
            'total_expense' => $totalOperating,
            'operating_expense' => $totalOperating,
            'operating_profit' => round($totalRevenue - $cogs - $totalOperating, 2),
            'net_income' => round($totalRevenue - $totalAllExp, 2),
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * Balance Sheet — Aset (lancar/tetap), Kewajiban (jangka pendek/panjang),
     * Ekuitas termasuk laba berjalan periode berjalan.
     */
    public function balanceSheet(?string $asOf): array
    {
        $asOf ??= now()->toDateString();

        $assets = ChartOfAccount::byType('asset')->active()->orderBy('code')->get();
        $liabilities = ChartOfAccount::byType('liability')->active()->orderBy('code')->get();
        $equities = ChartOfAccount::byType('equity')->active()->orderBy('code')->get();

        $rows = fn (Collection $accounts) => $accounts->map(fn (ChartOfAccount $a) => [
            'code' => $a->code,
            'name' => $a->name,
            'level' => $a->level,
            'balance' => round($a->balanceBetween(null, $asOf), 2),
        ]);

        $assetRows = $rows($assets);
        $liabilityRows = $rows($liabilities);
        $equityRows = $rows($equities);

        $totalAssets = round($assetRows->sum('balance'), 2);
        $totalLiabilities = round($liabilityRows->sum('balance'), 2);

        // Current-year net income flows into equity (retained earnings effect).
        $netIncome = $this->incomeStatement(now()->startOfYear()->toDateString(), $asOf)['net_income'];
        $totalEquity = round($equityRows->sum('balance') + $netIncome, 2);

        return [
            'assets' => $assetRows->filter(fn ($r) => $r['balance'] != 0)->values(),
            'total_assets' => $totalAssets,
            'liabilities' => $liabilityRows->filter(fn ($r) => $r['balance'] != 0)->values(),
            'total_liabilities' => $totalLiabilities,
            'equities' => $equityRows->filter(fn ($r) => $r['balance'] != 0)->values(),
            'equity_rows_subtotal' => round($equityRows->sum('balance'), 2),
            'net_income' => $netIncome,
            'total_equity' => $totalEquity,
            'total_liability_equity' => round($totalLiabilities + $totalEquity, 2),
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 1,
            'as_of' => $asOf,
        ];
    }

    /**
     * Cash Flow Statement — direct method. Movements are classified
     * (operating / investing / financing) by journal source type and shown
     * per cash/bank account.
     */
    public function cashFlow(?string $from, ?string $to): array
    {
        $from ??= now()->startOfMonth()->toDateString();
        $to ??= now()->toDateString();

        $cashAccounts = ChartOfAccount::cashOrBank()->active()->orderBy('code')->get();

        $sections = [
            'operating' => collect(),
            'investing' => collect(),
            'financing' => collect(),
        ];

        foreach ($cashAccounts as $account) {
            $details = JournalDetail::where('account_id', $account->id)
                ->with('journalEntry')
                ->whereHas('journalEntry', function ($q) use ($from, $to) {
                    $q->where('status', JournalEntry::STATUS_POSTED)
                        ->whereDate('date', '>=', $from)
                        ->whereDate('date', '<=', $to);
                })
                ->get();

            foreach ($details as $detail) {
                $entry = $detail->journalEntry;
                $section = self::CASH_FLOW_CLASSIFICATION[$entry->transaction_type] ?? 'operating';
                $amount = round((float) $detail->debit - (float) $detail->credit, 2); // inflow positive

                $sections[$section]->push([
                    'date' => $entry->date->format('Y-m-d'),
                    'journal' => $entry->journal_number,
                    'account' => $account->code.' — '.$account->name,
                    'description' => $entry->description,
                    'inflow' => $amount > 0 ? $amount : 0.0,
                    'outflow' => $amount < 0 ? abs($amount) : 0.0,
                ]);
            }
        }

        $summarized = [];
        $grandNet = 0.0;
        foreach ($sections as $key => $rows) {
            $net = round($rows->sum('inflow') - $rows->sum('outflow'), 2);
            $grandNet += $net;
            $summarized[$key] = [
                'label' => match ($key) {
                    'operating' => 'Arus Kas dari Aktivitas Operasi',
                    'investing' => 'Arus Kas dari Aktivitas Investasi',
                    'financing' => 'Arus Kas dari Aktivitas Pendanaan',
                },
                'rows' => $rows->sortBy('date')->values(),
                'inflow' => round($rows->sum('inflow'), 2),
                'outflow' => round($rows->sum('outflow'), 2),
                'net' => $net,
            ];
        }

        return [
            'sections' => $summarized,
            'net_change' => round($grandNet, 2),
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * General Ledger — opening balance carried forward, running balance per line.
     */
    public function generalLedger(ChartOfAccount $account, ?string $from, ?string $to): array
    {
        $from ??= now()->startOfMonth()->toDateString();
        $to ??= now()->toDateString();

        $opening = JournalDetail::where('account_id', $account->id)
            ->whereHas('journalEntry', fn ($q) => $q
                ->where('status', JournalEntry::STATUS_POSTED)
                ->whereDate('date', '<', $from))
            ->sum(DB::raw('debit - credit'));

        $openingBalance = round($account->normal_balance === 'debit' ? $opening : -$opening, 2);

        $details = JournalDetail::with('journalEntry')
            ->where('account_id', $account->id)
            ->whereHas('journalEntry', fn ($q) => $q
                ->where('status', JournalEntry::STATUS_POSTED)
                ->whereDate('date', '>=', $from)
                ->whereDate('date', '<=', $to))
            ->orderBy(JournalEntry::select('date')->whereColumn('id', 'journal_details.journal_entry_id'))
            ->orderBy(JournalEntry::select('id')->whereColumn('id', 'journal_details.journal_entry_id'))
            ->get();

        $running = $openingBalance;
        $rows = $details->map(function (JournalDetail $detail) use ($account, &$running) {
            $running = round($running + ($account->normal_balance === 'debit'
                ? $detail->debit - $detail->credit
                : $detail->credit - $detail->debit), 2);

            return [
                'date' => $detail->journalEntry->date->format('Y-m-d'),
                'journal_number' => $detail->journalEntry->journal_number,
                'description' => $detail->journalEntry->description,
                'memo' => $detail->memo,
                'debit' => round((float) $detail->debit, 2),
                'credit' => round((float) $detail->credit, 2),
                'running_balance' => $running,
            ];
        });

        return [
            'account' => $account,
            'rows' => $rows,
            'opening_balance' => $openingBalance,
            'closing_balance' => count($rows) ? $rows->last()['running_balance'] : $openingBalance,
            'from' => $from,
            'to' => $to,
        ];
    }
}
