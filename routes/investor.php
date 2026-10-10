<?php

declare(strict_types=1);

use App\Http\Controllers\Investor\Auth\LoginController;
use App\Http\Controllers\Investor\DashboardController;
use App\Http\Controllers\Investor\InvestmentController;
use App\Http\Controllers\Investor\ProfileController;
use App\Http\Controllers\Investor\ReferralController;
use App\Http\Controllers\Investor\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Investor Portal Routes
|--------------------------------------------------------------------------
|
| These routes handle investor authentication, dashboard, investments,
| referral program tracking, wallet transactions, and withdrawal requests.
|
*/

Route::middleware('guest:investor')->group(function (): void {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
});

Route::middleware('auth:investor')->group(function (): void {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::get('investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('withdraw-request', [TransactionController::class, 'withdrawRequest'])->name('withdraw.request');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
