<?php

namespace Tests\Feature;

use App\Models\AccountingPeriod;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\JournalEntry;
use App\Models\User;
use App\Services\Accounting\AccountingPeriodService;
use App\Services\Accounting\AutoPostingService;
use App\Services\Accounting\FinancialStatementService;
use App\Services\Accounting\JournalService;
use Database\Seeders\ChartOfAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingEnterpriseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ChartOfAccountSeeder::class);
    }

    private function period(): AccountingPeriod
    {
        return AccountingPeriodService::currentOrCreate('2026-08-15');
    }

    // ─────────────────────────────────────────────────────────────────────
    // JOURNAL SERVICE — double-entry integrity
    // ─────────────────────────────────────────────────────────────────────
    public function test_journal_service_creates_balanced_entry(): void
    {
        $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();
        $revenue = ChartOfAccount::where('code', '4-101')->first();

        $entry = JournalService::create(
            [
                'date' => '2026-08-15',
                'description' => 'Test penjualan',
                'source_type' => 'manual',
                'status' => JournalEntry::STATUS_POSTED,
                'created_by' => User::factory()->create()->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 100000, 'credit' => 0, 'memo' => 'Kas'],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100000, 'memo' => 'Pendapatan'],
            ]
        );

        $this->assertEquals(100000, $entry->total_debit);
        $this->assertEquals(100000, $entry->total_credit);
        $this->assertTrue($entry->isBalanced());
        $this->assertStringStartsWith('JNL-', $entry->journal_number);
        $this->assertEquals(JournalEntry::STATUS_POSTED, $entry->status);
    }

    public function test_journal_service_rejects_unbalanced_entry(): void
    {
        $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();

        $this->expectException(\DomainException::class);

        JournalService::create(
            ['date' => '2026-08-15', 'description' => 'Tidak seimbang', 'source_type' => 'manual'],
            [
                ['account_id' => $cash->id, 'debit' => 100000, 'credit' => 0],
                ['account_id' => $cash->id, 'debit' => 50000, 'credit' => 0],
            ]
        );
    }

    public function test_journal_service_rejects_non_posting_account(): void
    {
        $this->period();
        $header = ChartOfAccount::where('level', 0)->first();

        $this->expectException(\DomainException::class);

        JournalService::create(
            ['date' => '2026-08-15', 'description' => 'Header akun', 'source_type' => 'manual'],
            [
                ['account_id' => $header->id, 'debit' => 100000, 'credit' => 0],
                ['account_id' => $header->id, 'debit' => 0, 'credit' => 100000],
            ]
        );
    }

    public function test_generate_number_is_unique_sequential(): void
    {
        $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();
        $revenue = ChartOfAccount::where('code', '4-101')->first();
        $user = User::factory()->create();

        $n1 = JournalService::create(
            [
                'date' => '2026-08-15', 'description' => 'Pertama', 'source_type' => 'manual',
                'status' => JournalEntry::STATUS_POSTED, 'created_by' => $user->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 1000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 1000],
            ]
        )->journal_number;

        $n2 = JournalService::create(
            [
                'date' => '2026-08-15', 'description' => 'Kedua', 'source_type' => 'manual',
                'status' => JournalEntry::STATUS_POSTED, 'created_by' => $user->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 2000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 2000],
            ]
        )->journal_number;

        $this->assertNotEquals($n1, $n2);
        $this->assertMatchesRegularExpression('/^JNL-\d{8}-\d{6}$/', $n1);
        $this->assertMatchesRegularExpression('/^JNL-\d{8}-\d{6}$/', $n2);
    }

    // ─────────────────────────────────────────────────────────────────────
    // AUTO-POSTING — operational events produce journals (fail-safe)
    // ─────────────────────────────────────────────────────────────────────
    public function test_post_expense_creates_journal_and_is_idempotent(): void
    {
        $this->period();
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = ExpenseCategory::create(['name' => 'Employee Salary']);
        $expense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Gaji kasir',
            'amount' => 2500000,
            'date' => '2026-08-15',
        ]);

        $service = app(AutoPostingService::class);
        $entry = $service->postExpense($expense);

        $this->assertNotNull($entry);
        $this->assertEquals(2500000, $entry->total_debit);
        $this->assertTrue($entry->isBalanced());
        $this->assertTrue($entry->details()->whereHas('account', fn ($q) => $q->where('code', '5-102'))->exists());

        // Idempotent — second call produces nothing
        $this->assertNull($service->postExpense($expense));
        $this->assertEquals(1, JournalEntry::where('transaction_type', 'expense')->count());
    }

    public function test_post_expense_skips_when_disabled(): void
    {
        config(['accounting.enabled' => false]);
        $this->period();
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = ExpenseCategory::create(['name' => 'Other']);
        $expense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Sewa',
            'amount' => 1000000,
            'date' => '2026-08-15',
        ]);

        $this->assertNull(app(AutoPostingService::class)->postExpense($expense));
        $this->assertEquals(0, JournalEntry::count());
    }

    // ─────────────────────────────────────────────────────────────────────
    // JOURNAL LIFECYCLE — reverse & draft deletion
    // ─────────────────────────────────────────────────────────────────────
    public function test_reverse_creates_mirrored_entry_and_marks_original(): void
    {
        $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();
        $revenue = ChartOfAccount::where('code', '4-101')->first();
        $user = User::factory()->create();

        $entry = JournalService::create(
            [
                'date' => '2026-08-15', 'description' => 'Penjualan', 'source_type' => 'manual',
                'status' => JournalEntry::STATUS_POSTED, 'created_by' => $user->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 50000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 50000],
            ]
        );

        $reversal = $entry->reverse($user->id);

        $this->assertEquals(JournalEntry::STATUS_REVERSED, $entry->fresh()->status);
        $this->assertEquals($entry->total_debit, $reversal->total_credit);
        $this->assertEquals($entry->total_credit, $reversal->total_debit);
        $this->assertStringStartsWith('JRV-', $reversal->journal_number);

        // Cannot reverse twice
        $this->expectException(\DomainException::class);
        $entry->reverse($user->id);
    }

    public function test_delete_draft_removes_entry_and_details(): void
    {
        $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();
        $revenue = ChartOfAccount::where('code', '4-101')->first();
        $user = User::factory()->create();

        $entry = JournalService::create(
            [
                'date' => '2026-08-15', 'description' => 'Draft', 'source_type' => 'manual',
                'status' => JournalEntry::STATUS_DRAFT, 'created_by' => $user->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 10000],
            ]
        );

        $entry->deleteDraft();

        $this->assertDatabaseMissing('journal_entries', ['id' => $entry->id]);
        $this->assertDatabaseMissing('journal_details', ['journal_entry_id' => $entry->id]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PERIOD MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────
    public function test_period_close_rejects_draft_journals(): void
    {
        $period = $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();
        $revenue = ChartOfAccount::where('code', '4-101')->first();
        $user = User::factory()->create();

        JournalService::create(
            [
                'date' => '2026-08-15', 'description' => 'Draft', 'source_type' => 'manual',
                'status' => JournalEntry::STATUS_DRAFT, 'created_by' => $user->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 10000],
            ]
        );

        $this->expectException(\DomainException::class);
        $period->close($user->id);
    }

    public function test_period_close_succeeds_without_drafts(): void
    {
        $period = $this->period();
        $user = User::factory()->create();
        $period->close($user->id);

        $this->assertTrue($period->fresh()->is_closed);
        $this->assertNotNull($period->fresh()->closed_at);
    }

    // ─────────────────────────────────────────────────────────────────────
    // FINANCIAL STATEMENTS
    // ─────────────────────────────────────────────────────────────────────
    public function test_financial_statements_structure(): void
    {
        $this->period();
        $cash = ChartOfAccount::where('code', '1-101')->first();
        $revenue = ChartOfAccount::where('code', '4-101')->first();
        $user = User::factory()->create();

        JournalService::create(
            [
                'date' => '2026-08-15', 'description' => 'Penjualan', 'source_type' => 'manual',
                'status' => JournalEntry::STATUS_POSTED, 'created_by' => $user->id,
            ],
            [
                ['account_id' => $cash->id, 'debit' => 75000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 75000],
            ]
        );

        $statements = app(FinancialStatementService::class);

        $tb = $statements->trialBalance('2026-08-01', '2026-08-31');
        $this->assertTrue($tb['is_balanced']);
        $this->assertEquals(75000, $tb['total_debit']);

        $is = $statements->incomeStatement('2026-08-01', '2026-08-31');
        $this->assertEquals(75000, $is['total_revenue']);
        $this->assertEquals(75000, $is['net_income']);

        $bs = $statements->balanceSheet('2026-08-31');
        $this->assertTrue($bs['is_balanced']);
        $this->assertEquals($bs['total_liability_equity'], $bs['total_assets']);

        $cf = $statements->cashFlow('2026-08-01', '2026-08-31');
        $this->assertEquals(75000, $cf['sections']['operating']['inflow']);
        $this->assertEquals(75000, $cf['net_change']);
    }
}
