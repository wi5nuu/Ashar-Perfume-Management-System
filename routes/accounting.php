<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingController;

// Cashier role intentionally excluded — accounting data is owner/admin/manager only.
// Wajib dibungkus grup 'web' (seperti routes/auth.php) karena file ini dimuat via
// callback then() yang tidak mendapat default middleware group — tanpa ini session,
// CSRF, ShareErrorsFromSession, dan middleware keamanan kustom tidak berlaku.
Route::middleware('web')->group(function () {
    Route::middleware(['auth', 'role:owner,admin,manager'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/', [AccountingController::class, 'index'])->name('index');

    // Chart of Accounts
    Route::get('/coa', [AccountingController::class, 'coaIndex'])->name('coa.index');
    Route::get('/coa/{account}/edit', [AccountingController::class, 'coaEdit'])->name('coa.edit');
    Route::post('/coa', [AccountingController::class, 'coaStore'])->name('coa.store');
    Route::put('/coa/{account}', [AccountingController::class, 'coaUpdate'])->name('coa.update');
    Route::delete('/coa/{account}', [AccountingController::class, 'coaDestroy'])->name('coa.destroy');

    // Accounting Periods
    Route::get('/periods', [AccountingController::class, 'periodIndex'])->name('periods.index');
    Route::post('/periods', [AccountingController::class, 'periodStore'])->name('periods.store');
    Route::post('/periods/{period}/close', [AccountingController::class, 'periodClose'])->name('periods.close');

    // Journals
    Route::get('/journal', [AccountingController::class, 'journalIndex'])->name('journal.index');
    Route::get('/journal/create', [AccountingController::class, 'journalCreate'])->name('journal.create');
    Route::post('/journal', [AccountingController::class, 'journalStore'])->name('journal.store');
    Route::get('/journal/{journal}', [AccountingController::class, 'journalShow'])->name('journal.show');
    Route::post('/journal/{journal}/post', [AccountingController::class, 'journalPost'])->name('journal.post');
    Route::post('/journal/{journal}/reverse', [AccountingController::class, 'journalReverse'])->name('journal.reverse');
    Route::delete('/journal/{journal}', [AccountingController::class, 'journalDestroy'])->name('journal.destroy');

    // Financial Statements
    Route::get('/ledger', [AccountingController::class, 'ledger'])->name('ledger.index');
    Route::get('/trial-balance', [AccountingController::class, 'trialBalance'])->name('trial-balance.index');
    Route::get('/income-statement', [AccountingController::class, 'incomeStatement'])->name('income-statement.index');
    Route::get('/balance-sheet', [AccountingController::class, 'balanceSheet'])->name('balance-sheet.index');
    Route::get('/cash-flow', [AccountingController::class, 'cashFlow'])->name('cash-flow.index');
    });
});
