<?php

namespace Tests\Feature;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\GoodsReceipt;
use App\Models\Inventory;
use App\Models\JournalEntry;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SalesReturn;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WholesaleOrder;
use Database\Seeders\ChartOfAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountingSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->seed(ChartOfAccountSeeder::class);
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_entire_flow_connected(): void
    {
        AccountingPeriod::create([
            'name' => 'Periode Aktif',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        // 1. Halaman dashboard akuntansi
        $this->actingAs($this->owner)->get(route('accounting.index'))->assertOk();

        // 2. COA index + edit + update
        $account = ChartOfAccount::where('is_posting', true)->first();
        $this->actingAs($this->owner)->get(route('accounting.coa.index'))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.coa.edit', $account))->assertOk();
        $this->actingAs($this->owner)->put(route('accounting.coa.update', $account), [
            'name' => $account->name,
            'is_active' => 1,
            'is_posting' => 1,
        ])->assertRedirect();

        // 3. Jurnal manual: create → index → show → post → reverse
        $expenseAccount = ChartOfAccount::where('code', '5-113')->firstOrFail();
        $cashAccount = ChartOfAccount::where('code', '1-111')->firstOrFail();

        $this->actingAs($this->owner)->get(route('accounting.journal.create'))->assertOk();
        $storeResp = $this->actingAs($this->owner)->post(route('accounting.journal.store'), [
            'date' => now()->toDateString(),
            'description' => 'Smoke test manual journal',
            'entries' => [
                ['account_id' => $expenseAccount->id, 'debit' => 50000, 'credit' => null, 'memo' => 'x'],
                ['account_id' => $cashAccount->id, 'debit' => null, 'credit' => 50000, 'memo' => 'x'],
            ],
        ]);
        $storeResp->assertRedirect();

        $journal = JournalEntry::latest('id')->firstOrFail();
        $this->actingAs($this->owner)->get(route('accounting.journal.index'))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.journal.show', $journal))->assertOk();
        $this->actingAs($this->owner)->post(route('accounting.journal.post', $journal))->assertRedirect();
        $journal->refresh();
        $this->assertSame('posted', $journal->status);
        $this->actingAs($this->owner)->post(route('accounting.journal.reverse', $journal))->assertRedirect();
        $journal->refresh();
        $this->assertSame('reversed', $journal->status);

        // 4. Auto-posting dari transaksi operasional (penjualan retail)
        $product = Product::create([
            'name' => 'Parfum Smoke Test',
            'product_category_id' => ProductCategory::create(['name' => 'Smoke'])->id,
            'purchase_price' => 20000,
            'selling_price' => 50000,
            'is_active' => true,
            'track_inventory' => true,
        ]);
        $inventory = Inventory::create([
            'product_id' => $product->id,
            'current_stock' => 100,
        ]);
        $customer = Customer::create([
            'name' => 'Pelanggan Smoke',
            'phone' => '081299998888',
            'type' => 'retail',
        ]);

        $txResp = $this->actingAs($this->owner)->post(route('transactions.store'), [
            'customer_id' => $customer->id,
            'customer_type' => 'retail',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 50000,
                    'inventory_id' => $inventory->id,
                ],
            ],
            'payment_method' => 'cash',
            'paid_amount' => 100000,
            'cash_received' => 100000,
        ]);
        $txResp->assertOk();
        $this->assertNotNull($txResp->json('transaction_id'));

        $sale = Transaction::latest('id')->firstOrFail();
        $saleJournal = JournalEntry::where('transaction_type', 'sale')
            ->where('transaction_id', $sale->id)
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->first();
        $this->assertNotNull($saleJournal, 'Auto-posting penjualan harus membuat jurnal');
        $this->assertSame('posted', $saleJournal->status);

        // 5. Auto-posting expense
        $category = ExpenseCategory::create(['name' => 'listrik']);
        $expense = Expense::create([
            'category_id' => $category->id,
            'amount' => 150000,
            'date' => now()->toDateString(),
            'description' => 'Token listrik',
        ]);
        $this->actingAs($this->owner)->get(route('expenses.index'))->assertOk();

        // 6. Laporan keuangan — semua halaman render tanpa error
        $from = now()->startOfMonth()->toDateString();
        $to = now()->toDateString();
        $this->actingAs($this->owner)->get(route('accounting.trial-balance.index', compact('from', 'to')))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.income-statement.index', compact('from', 'to')))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.balance-sheet.index', ['as_of' => $to]))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.cash-flow.index', compact('from', 'to')))->assertOk();

        // 7. Ledger + periods + export PDF
        $this->actingAs($this->owner)->get(route('accounting.ledger.index', [
            'account_id' => $cashAccount->id, 'from' => $from, 'to' => $to,
        ]))->assertOk();

        $this->actingAs($this->owner)->post(route('accounting.periods.store'), [
            'name' => 'Periode '.now()->format('F Y'),
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ])->assertRedirect();
        $this->actingAs($this->owner)->get(route('accounting.periods.index'))->assertOk();

        foreach (['trial-balance', 'income-statement', 'balance-sheet', 'cash-flow'] as $report) {
            $this->actingAs($this->owner)->get(route("accounting.$report.index", [
                'from' => $from, 'to' => $to, 'export' => 'pdf',
            ]))->assertOk();
        }

        $this->actingAs($this->owner)->get(route('accounting.ledger.index', [
            'account_id' => $cashAccount->id, 'from' => $from, 'to' => $to, 'export' => 'pdf',
        ]))->assertOk();

        // 8. Auto-posting grosir: order → confirm → jurnal wholesale
        $wsProduct = Product::create([
            'name' => 'Parfum Grosir Test',
            'product_category_id' => $product->product_category_id,
            'purchase_price' => 30000,
            'selling_price' => 60000,
            'is_active' => true,
            'track_inventory' => true,
        ]);
        Inventory::create([
            'product_id' => $wsProduct->id,
            'current_stock' => 1000,
        ]);
        $wsCustomer = Customer::create([
            'name' => 'Toko Grosir Smoke',
            'phone' => '081288887777',
            'type' => 'wholesale',
        ]);

        $wsResp = $this->actingAs($this->owner)->post(route('wholesale.store'), [
            'customer_id' => $wsCustomer->id,
            'recipient_name' => 'Toko Grosir Smoke',
            'recipient_phone' => '081288887777',
            'shipping_address' => 'Jl. Raya Bekasi No. 88',
            'shipping_cost' => 15000,
            'items' => [
                [
                    'product_id' => $wsProduct->id,
                    'product_name' => 'Parfum Grosir Test',
                    'quantity' => 10,
                    'price' => 50000,
                    'volume_ml' => 100,
                ],
            ],
        ]);
        $wsResp->assertRedirect();
        $this->assertSame([], session('errors') ? session('errors')->all() : [], 'Wholesale store harus tanpa error validasi: '.json_encode(session('errors') ? session('errors')->all() : []));
        $this->assertNull(session('error'), 'Wholesale store flash error: '.session('error'));

        $wsOrder = WholesaleOrder::latest('id')->firstOrFail();
        $this->assertSame('pending', $wsOrder->status);

        $this->actingAs($this->owner)->post(route('wholesale.confirm', $wsOrder))->assertRedirect();
        $wsOrder->refresh();
        $this->assertSame('reviewed', $wsOrder->status);

        $wsJournal = JournalEntry::where('transaction_type', 'wholesale')
            ->where('transaction_id', $wsOrder->id)
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->first();
        $this->assertNotNull($wsJournal, 'Auto-posting grosir harus membuat jurnal');
        $this->assertSame('posted', $wsJournal->status);

        // 9. Auto-posting retur penjualan: store → approve → jurnal sales_return
        $saleDetail = $sale->details()->firstOrFail();
        $this->actingAs($this->owner)->post(route('returns.store'), [
            'transaction_id' => $sale->id,
            'reason' => 'Barang rusak saat pengiriman',
            'items' => [
                ['detail_id' => $saleDetail->id, 'quantity' => 1],
            ],
        ])->assertRedirect();

        $salesReturn = SalesReturn::latest('id')->firstOrFail();
        $this->assertSame('pending', $salesReturn->status);

        $this->actingAs($this->owner)->post(route('returns.approve', $salesReturn))->assertRedirect();
        $salesReturn->refresh();
        $this->assertSame('approved', $salesReturn->status);

        $returnJournal = JournalEntry::where('transaction_type', 'sales_return')
            ->where('transaction_id', $salesReturn->id)
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->first();
        $this->assertNotNull($returnJournal, 'Auto-posting retur harus membuat jurnal');
        $this->assertSame('posted', $returnJournal->status);

        // 10. Auto-posting penerimaan barang: goods receipt → jurnal goods_receipt
        $grResp = $this->actingAs($this->owner)->post(route('goods-receipts.store'), [
            'product_id' => $wsProduct->id,
            'quantity' => 50,
            'received_date' => now()->toDateString(),
            'unit_cost' => 30000,
        ]);
        $grResp->assertRedirect();
        $this->assertSame(302, $grResp->getStatusCode(), 'Goods receipt status harus redirect, dapat: '.$grResp->getStatusCode().' body: '.substr($grResp->getContent(), 0, 500));
        $this->assertNull(session('error'), 'Goods receipt flash error: '.session('error'));

        $receipt = GoodsReceipt::latest('id')->firstOrFail();
        $grJournal = JournalEntry::where('transaction_type', 'goods_receipt')
            ->where('transaction_id', $receipt->id)
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->first();
        $this->assertNotNull($grJournal, 'Auto-posting penerimaan barang harus membuat jurnal');
        $this->assertSame('posted', $grJournal->status);

        // 11. Auto-posting pembayaran hutang/piutang: debt payment → jurnal debt_payment
        $debtTx = Transaction::create([
            'invoice_number' => 'DEBT-'.Str::upper(Str::random(6)),
            'customer_id' => $customer->id,
            'customer_type' => 'retail',
            'user_id' => $this->owner->id,
            'total_amount' => 75000,
            'final_amount' => 75000,
            'paid_amount' => 0,
            'debt_amount' => 75000,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ]);

        $dpResp = $this->actingAs($this->owner)->post(route('debts.payment', $debtTx), [
            'amount' => 75000,
            'payment_method' => 'cash',
        ]);
        $dpResp->assertRedirect();
        $this->assertNull(session('error'), 'Debt payment flash error: '.session('error'));

        $payment = DebtPayment::latest('id')->firstOrFail();
        $dpJournal = JournalEntry::where('transaction_type', 'debt_payment')
            ->where('transaction_id', $payment->id)
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->first();
        $this->assertNotNull($dpJournal, 'Auto-posting pembayaran piutang harus membuat jurnal');
        $this->assertSame('posted', $dpJournal->status);

        // 12. Auto-posting payroll: generate → jurnal payroll
        $branch = Branch::create([
            'name' => 'Cabang Smoke',
            'code' => 'SMO',
            'is_active' => true,
        ]);
        $employee = User::factory()->create([
            'role' => 'cashier',
            'branch_id' => $branch->id,
            'basic_salary' => 500000,
        ]);
        $employee->payrollSettings()->create([
            'allowance' => 100000,
            'deduction' => 0,
        ]);

        $this->actingAs($this->owner)->post(route('payroll.generate'), [
            'month' => now()->format('Y-m'),
        ])->assertRedirect();

        $payroll = Payroll::latest('id')->firstOrFail();
        $this->assertSame('pending', $payroll->status);

        $prJournal = JournalEntry::where('transaction_type', 'payroll')
            ->where('status', '!=', JournalEntry::STATUS_REVERSED)
            ->first();
        $this->assertNotNull($prJournal, 'Auto-posting payroll harus membuat jurnal');
        $this->assertSame('posted', $prJournal->status);

        // 13. Semua laporan tetap render setelah seluruh alur operasional
        $this->actingAs($this->owner)->get(route('accounting.trial-balance.index', compact('from', 'to')))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.income-statement.index', compact('from', 'to')))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.balance-sheet.index', ['as_of' => $to]))->assertOk();
        $this->actingAs($this->owner)->get(route('accounting.cash-flow.index', compact('from', 'to')))->assertOk();
    }
}
