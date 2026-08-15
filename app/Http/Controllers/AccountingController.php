<?php

namespace App\Http\Controllers;

use App\Models\AccountingPeriod;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\Accounting\FinancialStatementService;
use App\Services\Accounting\JournalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function __construct(private readonly FinancialStatementService $statements) {}

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────────────────────────────
    public function index()
    {
        return view('accounting.index', [
            'periods' => AccountingPeriod::latest()->get(),
            'currentPeriod' => AccountingPeriod::current(),
            'coaCount' => ChartOfAccount::count(),
            'journalCount' => JournalEntry::count(),
            'unpostedCount' => JournalEntry::where('status', JournalEntry::STATUS_DRAFT)->count(),
            'recentJournals' => JournalEntry::with(['period', 'creator'])->latest()->limit(5)->get(),
            'closedPeriodCount' => AccountingPeriod::where('is_closed', true)->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // CHART OF ACCOUNTS
    // ─────────────────────────────────────────────────────────────────────
    public function coaIndex(Request $request)
    {
        $accounts = ChartOfAccount::with('parent')
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->when($request->search, fn ($q, $v) => $q->where(fn ($b) => $b->where('name', 'like', "%{$v}%")->orWhere('code', 'like', "%{$v}%")))
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
            'is_posting' => 'sometimes|boolean',
            'is_cash' => 'sometimes|boolean',
            'is_bank' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);
        $validated['normal_balance'] = ChartOfAccount::NORMAL_BALANCE[$validated['type']];
        $validated['is_active'] = true;
        $validated['level'] = ! empty($validated['parent_id'])
            ? (ChartOfAccount::find($validated['parent_id'])?->level ?? 0) + 1
            : 1;
        ChartOfAccount::create($validated);

        return redirect()->route('accounting.coa.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function coaEdit(ChartOfAccount $account)
    {
        return view('accounting.coa.edit', [
            'account' => $account,
            'accounts' => ChartOfAccount::active()->where('id', '!=', $account->id)->orderBy('code')->get(),
        ]);
    }

    public function coaUpdate(Request $request, ChartOfAccount $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_posting' => 'sometimes|boolean',
            'is_cash' => 'sometimes|boolean',
            'is_bank' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_posting'] = $request->boolean('is_posting');
        $validated['is_cash'] = $request->boolean('is_cash');
        $validated['is_bank'] = $request->boolean('is_bank');
        $validated['is_active'] = $request->boolean('is_active', $account->is_active);
        $validated['level'] = ! empty($validated['parent_id'])
            ? (ChartOfAccount::find($validated['parent_id'])?->level ?? 0) + 1
            : 1;

        if (! $validated['is_active'] && $account->journalDetails()->exists()) {
            return back()->withErrors(['is_active' => 'Akun memiliki riwayat jurnal — tidak dapat dinonaktifkan.']);
        }

        $account->update($validated);

        return redirect()->route('accounting.coa.index')->with('success', "Akun {$account->code} berhasil diperbarui");
    }

    public function coaDestroy(ChartOfAccount $account)
    {
        if ($account->journalDetails()->exists() || $account->children()->exists()) {
            return back()->withErrors(['error' => 'Akun tidak dapat dihapus (memiliki riwayat jurnal atau sub-akun).']);
        }
        $account->delete();

        return redirect()->route('accounting.coa.index')->with('success', 'Akun berhasil dihapus');
    }

    // ─────────────────────────────────────────────────────────────────────
    // PERIODE AKUNTANSI
    // ─────────────────────────────────────────────────────────────────────
    public function periodIndex()
    {
        $periods = AccountingPeriod::withCount('journals')->latest()->paginate(25);

        return view('accounting.periods.index', compact('periods'));
    }

    public function periodStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:accounting_periods,name',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        AccountingPeriod::create($validated + ['is_closed' => false]);

        return redirect()->route('accounting.periods.index')->with('success', 'Periode akuntansi berhasil dibuat');
    }

    public function periodClose(AccountingPeriod $period)
    {
        try {
            $period->close(auth()->id());

            return redirect()->route('accounting.periods.index')->with('success', "Periode {$period->name} ditutup.");
        } catch (\DomainException $e) {
            return back()->withErrors(['close' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // JURNAL
    // ─────────────────────────────────────────────────────────────────────
    public function journalIndex(Request $request)
    {
        $journals = JournalEntry::with(['period', 'creator', 'branch'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->from, fn ($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('date', '<=', $v))
            ->when($request->search, fn ($q, $v) => $q->where(fn ($b) => $b->where('journal_number', 'like', "%{$v}%")->orWhere('description', 'like', "%{$v}%")))
            ->orderBy('created_at', 'desc')->paginate(25);

        return view('accounting.journal.index', compact('journals'));
    }

    public function journalCreate()
    {
        return view('accounting.journal.create', [
            'accounts' => ChartOfAccount::active()->posting()->orderBy('code')->get(),
            'periods' => AccountingPeriod::open()->get(),
        ]);
    }

    public function journalStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.memo' => 'nullable|string',
        ]);

        $lines = collect($validated['entries'])->map(fn ($line) => [
            'account_id' => $line['account_id'],
            'debit' => $line['debit'] ?? 0,
            'credit' => $line['credit'] ?? 0,
            'memo' => $line['memo'] ?? null,
        ])->all();

        try {
            $entry = JournalService::create([
                'date' => $validated['date'],
                'description' => $validated['description'],
                'source_type' => 'manual',
                'created_by' => auth()->id(),
                'status' => JournalEntry::STATUS_DRAFT,
            ], $lines, auth()->user()?->branch_id);

            return redirect()->route('accounting.journal.show', $entry->id)->with('success', 'Jurnal berhasil dibuat');
        } catch (\DomainException $e) {
            return back()->withErrors(['entries' => $e->getMessage()])->withInput();
        }
    }

    public function journalShow(JournalEntry $journal)
    {
        $journal->load(['details.account', 'period', 'creator', 'branch']);

        return view('accounting.journal.show', compact('journal'));
    }

    public function journalPost(JournalEntry $journal)
    {
        try {
            $journal->post();

            return redirect()->route('accounting.journal.show', $journal->id)->with('success', 'Jurnal berhasil diposting');
        } catch (\DomainException $e) {
            return back()->withErrors(['post' => $e->getMessage()]);
        }
    }

    public function journalReverse(JournalEntry $journal)
    {
        try {
            $reversal = $journal->reverse(auth()->id());

            return redirect()->route('accounting.journal.show', $reversal->id)
                ->with('success', 'Jurnal pembalik berhasil dibuat. Jurnal asli ditandai reversed.');
        } catch (\DomainException $e) {
            return back()->withErrors(['reverse' => $e->getMessage()]);
        }
    }

    public function journalDestroy(JournalEntry $journal)
    {
        try {
            $journal->deleteDraft();

            return redirect()->route('accounting.journal.index')->with('success', 'Jurnal draft dihapus');
        } catch (\DomainException $e) {
            return back()->withErrors(['delete' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // LAPORAN KEUANGAN (via FinancialStatementService)
    // ─────────────────────────────────────────────────────────────────────
    public function ledger(Request $request)
    {
        $accounts = ChartOfAccount::active()->orderBy('code')->get();
        $accountId = $request->account_id;
        $data = null;

        if ($request->account_id) {
            $account = ChartOfAccount::findOrFail($request->account_id);
            $data = $this->statements->generalLedger(
                $account,
                $request->from,
                $request->to
            );

            if ($request->export === 'pdf') {
                return $this->exportPdf('accounting.exports.ledger-pdf', $data, 'ledger');
            }
        }

        return view('accounting.ledger.index', compact('accounts', 'data', 'accountId', 'request'));
    }

    public function trialBalance(Request $request)
    {
        $data = $this->statements->trialBalance($request->from, $request->to);

        if ($request->export === 'pdf') {
            return $this->exportPdf('accounting.exports.trial-balance-pdf', $data, 'trial-balance');
        }
        if ($request->export === 'csv') {
            return $this->exportCsv('trial-balance', ['Kode', 'Akun', 'Debit', 'Kredit'], $data['rows'], function ($r) {
                return [$r['code'], $r['name'], $r['debit'], $r['credit']];
            }, $data['from'], $data['to']);
        }

        return view('accounting.trial-balance.index', compact('data'));
    }

    public function incomeStatement(Request $request)
    {
        $data = $this->statements->incomeStatement($request->from, $request->to);

        if ($request->export === 'pdf') {
            return $this->exportPdf('accounting.exports.income-statement-pdf', $data, 'income-statement');
        }
        if ($request->export === 'csv') {
            $rows = $data['revenue']->concat($data['expenses']);

            return $this->exportCsv('income-statement', ['Kode', 'Akun', 'Jumlah'], $rows, function ($r) {
                return [$r['code'], $r['name'], $r['balance']];
            }, $data['from'], $data['to']);
        }

        return view('accounting.income-statement.index', compact('data'));
    }

    public function balanceSheet(Request $request)
    {
        $data = $this->statements->balanceSheet($request->as_of);

        if ($request->export === 'pdf') {
            return $this->exportPdf('accounting.exports.balance-sheet-pdf', $data, 'balance-sheet');
        }

        return view('accounting.balance-sheet.index', compact('data'));
    }

    public function cashFlow(Request $request)
    {
        $data = $this->statements->cashFlow($request->from, $request->to);

        if ($request->export === 'pdf') {
            return $this->exportPdf('accounting.exports.cash-flow-pdf', $data, 'cash-flow');
        }

        return view('accounting.cash-flow.index', compact('data'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // EXPORT HELPERS
    // ─────────────────────────────────────────────────────────────────────
    private function exportPdf(string $view, array $data, string $slug)
    {
        $pdf = Pdf::loadView($view, ['data' => $data])
            ->setPaper('a4', 'landscape');

        return $pdf->download($slug.'-'.date('Ymd').'.pdf');
    }

    private function exportCsv(string $slug, array $header, $rows, callable $mapper, ?string $from, ?string $to)
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $header);
        foreach ($rows as $row) {
            fputcsv($output, $mapper($row));
        }
        if ($from || $to) {
            fputcsv($output, []);
            fputcsv($output, ['Periode', ($from ?? '-').' s/d '.($to ?? '-')]);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$slug.'-'.date('Ymd').'.csv"',
        ]);
    }
}
