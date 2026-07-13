<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use App\Models\AccountingPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountingController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        return view('accounting.index', [
            'periods' => AccountingPeriod::latest()->get(),
            'currentPeriod' => AccountingPeriod::current(),
            'coaCount' => ChartOfAccount::count(),
            'journalCount' => JournalEntry::count(),
            'unpostedCount' => JournalEntry::where('status', 'draft')->count(),
        ]);
    }

    public function coaIndex(Request $request)
    {
        $accounts = ChartOfAccount::with('parent')
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->when($request->search, fn($q, $v) => $q->where('name', 'like', "%{$v}%")->orWhere('code', 'like', "%{$v}%"))
            ->orderBy('code')->paginate(25);
        return view('accounting.coa.index', compact('accounts'));
    }

    public function coaStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
        ]);
        $validated['normal_balance'] = ChartOfAccount::NORMAL_BALANCE[$validated['type']];
        $validated['is_active'] = true;
        $validated['level'] = $validated['parent_id'] ? (ChartOfAccount::find($validated['parent_id'])?->level ?? 0) + 1 : 1;
        ChartOfAccount::create($validated);
        return redirect()->route('accounting.coa.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function journalIndex(Request $request)
    {
        $journals = JournalEntry::with(['period', 'creator'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->from, fn($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('date', '<=', $v))
            ->orderBy('created_at', 'desc')->paginate(25);
        return view('accounting.journal.index', compact('journals'));
    }

    public function journalCreate()
    {
        return view('accounting.journal.create', [
            'accounts' => ChartOfAccount::active()->orderBy('code')->get(),
            'periods' => AccountingPeriod::open()->get(),
        ]);
    }

    public function journalStore(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:accounting_periods,id',
            'date' => 'required|date',
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
            'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0',
            'entries.*.memo' => 'nullable|string',
        ]);

        $totalDebit = collect($validated['entries'])->sum('debit');
        $totalCredit = collect($validated['entries'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['entries' => "Total debit ({$totalDebit}) != total kredit ({$totalCredit})"])->withInput();
        }

        $entry = DB::transaction(function () use ($validated, $totalDebit, $totalCredit) {
            $journal = JournalEntry::create([
                'journal_number' => 'JNL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'period_id' => $validated['period_id'],
                'date' => $validated['date'],
                'description' => $validated['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => JournalEntry::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);
            foreach ($validated['entries'] as $line) {
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'memo' => $line['memo'] ?? null,
                ]);
            }
            return $journal;
        });

        return redirect()->route('accounting.journal.show', $entry->id)->with('success', 'Jurnal berhasil dibuat');
    }

    public function journalShow(JournalEntry $journal)
    {
        $journal->load(['details.account', 'period', 'creator']);
        return view('accounting.journal.show', compact('journal'));
    }

    public function journalPost(JournalEntry $journal)
    {
        try { $journal->post(); return redirect()->route('accounting.journal.show', $journal->id)->with('success', 'Jurnal berhasil diposting'); }
        catch (\Exception $e) { return back()->withErrors(['post' => $e->getMessage()]); }
    }

    public function ledger(Request $request)
    {
        $accounts = ChartOfAccount::active()->orderBy('code')->get();
        $accountId = $request->account_id;
        $details = collect();
        $balance = 0;

        if ($accountId) {
            $account = ChartOfAccount::findOrFail($accountId);
            $query = JournalDetail::with('journalEntry')->where('account_id', $accountId)
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted'));
            if ($request->from) $query->whereHas('journalEntry', fn($q) => $q->whereDate('date', '>=', $request->from));
            if ($request->to) $query->whereHas('journalEntry', fn($q) => $q->whereDate('date', '<=', $request->to));
            $details = $query->orderBy(JournalEntry::select('date')->whereColumn('id', 'journal_details.journal_entry_id'))->get();
            $normalBalance = $account->normal_balance;
            $details->each(function ($d) use ($normalBalance, &$balance) {
                $balance += $normalBalance === 'debit' ? $d->debit - $d->credit : $d->credit - $d->debit;
                $d->running_balance = $balance;
            });
        }

        return view('accounting.ledger.index', compact('accounts', 'accountId', 'details', 'balance'));
    }

    public function trialBalance(Request $request)
    {
        $endDate = $request->end_date;
        $accounts = ChartOfAccount::active()->orderBy('code')->get()->map(function ($a) use ($endDate) {
            $q = JournalDetail::where('account_id', $a->id)->whereHas('journalEntry', fn($q) => $q->where('status', 'posted'));
            if ($endDate) $q->whereHas('journalEntry', fn($qq) => $qq->whereDate('date', '<=', $endDate));
            $d = (float) $q->sum('debit'); $c = (float) $q->sum('credit');
            $bal = $a->normal_balance === 'debit' ? $d - $c : $c - $d;
            return ['code' => $a->code, 'name' => $a->name, 'type' => $a->type,
                'debit' => $bal > 0 && in_array($a->type, ['asset','expense']) ? $bal : 0,
                'credit' => $bal > 0 && in_array($a->type, ['liability','equity','income']) ? $bal : 0, 'balance' => $bal];
        });
        return view('accounting.trial-balance.index', compact('accounts', 'endDate'));
    }

    public function incomeStatement(Request $request)
    {
        $endDate = $request->end_date ?? now()->toDateString();
        $income = ChartOfAccount::byType('income')->active()->orderBy('code')->get()->map(fn($a) => ['code' => $a->code, 'name' => $a->name, 'balance' => $a->balance()]);
        $expense = ChartOfAccount::byType('expense')->active()->orderBy('code')->get()->map(fn($a) => ['code' => $a->code, 'name' => $a->name, 'balance' => $a->balance()]);
        $ti = $income->sum('balance'); $te = $expense->sum('balance');
        return view('accounting.income-statement.index', compact('income', 'expense', 'ti', 'te', 'endDate'));
    }

    public function balanceSheet(Request $request)
    {
        $endDate = $request->end_date ?? now()->toDateString();
        $assets = ChartOfAccount::byType('asset')->active()->orderBy('code')->get()->map(fn($a) => ['code' => $a->code, 'name' => $a->name, 'balance' => $a->balance()]);
        $liabilities = ChartOfAccount::byType('liability')->active()->orderBy('code')->get()->map(fn($a) => ['code' => $a->code, 'name' => $a->name, 'balance' => $a->balance()]);
        $equities = ChartOfAccount::byType('equity')->active()->orderBy('code')->get()->map(fn($a) => ['code' => $a->code, 'name' => $a->name, 'balance' => $a->balance()]);
        $netIncome = ChartOfAccount::byType('income')->active()->get()->sum(fn($a) => $a->balance()) - ChartOfAccount::byType('expense')->active()->get()->sum(fn($a) => $a->balance());
        return view('accounting.balance-sheet.index', compact('assets', 'liabilities', 'equities', 'netIncome', 'endDate'));
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();
        $revenue = ChartOfAccount::where('code', '4-101')->first();
        $cashIn = $revenue ? $revenue->balance() : 0;
        $cashOut = ChartOfAccount::byType('expense')->active()->get()->sum(fn($a) => $a->balance());
        return view('accounting.cash-flow.index', compact('cashIn', 'cashOut', 'startDate', 'endDate'));
    }
}
