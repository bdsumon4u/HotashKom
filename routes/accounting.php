<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AccountingTransactionController;
use App\Http\Controllers\Admin\LedgerReportController;
use App\Http\Controllers\Admin\TransactionCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accounting Module Routes
|--------------------------------------------------------------------------
|
| These routes handle ledger reports, chart of accounts, categories,
| and double-entry general ledger transactions.
|
*/

Route::get('ledger/monthly', [LedgerReportController::class, 'monthly'])->name('ledger.monthly');
Route::post('ledger/courier-payout', [LedgerReportController::class, 'recordCourierPayout'])->name('ledger.courier-payout');
Route::resource('accounts', AccountController::class);
Route::resource('categories', TransactionCategoryController::class);
Route::resource('transactions', AccountingTransactionController::class)->except(['show']);
