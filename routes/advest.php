<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\InvestmentEarningController;
use App\Http\Controllers\Admin\InvestorController;
use App\Http\Controllers\Admin\InvestorWithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Investment Module Routes
|--------------------------------------------------------------------------
|
| Routes for managing investors, creating investments, viewing installment
| schedules, approving withdrawals, and tracking company earnings.
|
*/

// Investors CRUD & investment creation
Route::resource('investors', InvestorController::class);
Route::post('investors/{investor}/investments', [InvestorController::class, 'storeInvestment'])->name('investors.investments.store');

// Investments & Installments
Route::get('investments', [InvestmentController::class, 'index'])->name('investments.index');
Route::get('investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
Route::post('investments/process-due', [InvestmentController::class, 'processDueInstallments'])->name('investments.process-due');

// Investor Withdrawals (Money Requests)
Route::get('withdrawals', [InvestorWithdrawalController::class, 'index'])->name('withdrawals.index');
Route::get('withdrawals/data', [InvestorWithdrawalController::class, 'data'])->name('withdrawals.data');
Route::post('withdrawals/confirm', [InvestorWithdrawalController::class, 'confirm'])->name('withdrawals.confirm');
Route::post('withdrawals/delete', [InvestorWithdrawalController::class, 'deleteRequest'])->name('withdrawals.delete');

// Company Earnings from Deductions
Route::get('earnings', [InvestmentEarningController::class, 'index'])->name('earnings.index');
