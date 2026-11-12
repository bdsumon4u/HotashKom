<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AccountingMovementController;
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
Route::get('shipments', [AccountingMovementController::class, 'shipments'])->name('shipments.index');
Route::get('returns', [AccountingMovementController::class, 'returns'])->name('returns.index');
Route::get('movements/{journalEntry}', [AccountingMovementController::class, 'show'])->name('movements.show');
Route::post('ledger/courier-payout', [LedgerReportController::class, 'recordCourierPayout'])->name('ledger.courier-payout');
Route::get('shipments/create', function () {
    abort_unless(request()->user()->is('admin'), 403);

    return view('admin.accounting.shipments.create');
})->name('shipments.create');
Route::get('settlements/create', function () {
    abort_unless(request()->user()->is('admin'), 403);

    return view('admin.accounting.settlements.create');
})->name('settlements.create');
Route::get('returns/create', function () {
    abort_unless(request()->user()->is('admin'), 403);

    return view('admin.accounting.returns.create');
})->name('returns.create');
Route::get('adjustments', [AccountingMovementController::class, 'adjustments'])->name('adjustments.index');
Route::get('adjustments/create', function () {
    abort_unless(request()->user()->is('admin'), 403);

    return view('admin.accounting.adjustments.create');
})->name('adjustments.create');
Route::resource('accounts', AccountController::class);
Route::resource('categories', TransactionCategoryController::class);
Route::resource('transactions', AccountingTransactionController::class)->except(['show']);
