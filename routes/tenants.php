<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin SaaS Tenant Management Routes
|--------------------------------------------------------------------------
|
| These routes handle managing multi-tenant websites, custom domains,
| and impersonation for SaaS platform admins.
|
*/

Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
Route::get('tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])->name('tenants.impersonate');
Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
Route::put('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
Route::post('tenants/{tenant}/domains', [TenantController::class, 'addDomain'])->name('tenants.domains.add');
Route::delete('tenants/domains/{domain}', [TenantController::class, 'deleteDomain'])->name('tenants.domains.delete');
