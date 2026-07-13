<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingController;

Route::middleware(['auth'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/', [AccountingController::class, 'index'])->name('index');
    Route::get('/coa', [AccountingController::class, 'coaIndex'])->name('coa.index');
    Route::post('/coa', [AccountingController::class, 'coaStore'])->name('coa.store');
    Route::get('/journal', [AccountingController::class, 'journalIndex'])->name('journal.index');
    Route::get('/journal/create', [AccountingController::class, 'journalCreate'])->name('journal.create');
    Route::post('/journal', [AccountingController::class, 'journalStore'])->name('journal.store');
    Route::get('/journal/{journal}', [AccountingController::class, 'journalShow'])->name('journal.show');
    Route::post('/journal/{journal}/post', [AccountingController::class, 'journalPost'])->name('journal.post');
    Route::get('/ledger', [AccountingController::class, 'ledger'])->name('ledger.index');
    Route::get('/trial-balance', [AccountingController::class, 'trialBalance'])->name('trial-balance.index');
    Route::get('/income-statement', [AccountingController::class, 'incomeStatement'])->name('income-statement.index');
    Route::get('/balance-sheet', [AccountingController::class, 'balanceSheet'])->name('balance-sheet.index');
    Route::get('/cash-flow', [AccountingController::class, 'cashFlow'])->name('cash-flow.index');
});
