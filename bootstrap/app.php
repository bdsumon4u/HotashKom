<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CaptureUtmTracking;
use App\Http\Middleware\EnsureAccountingEnabled;
use App\Http\Middleware\EnsureSpaResponse;
use App\Http\Middleware\InitializeTenancyIfTenantDomain;
use App\Http\Middleware\LogDatabaseUsage;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use Spatie\ResponseCache\Middlewares\DoNotCacheResponse;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByDomainException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (file_exists($path = __DIR__.'/../vendor/lib.php')) {
    require $path;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(prepend: [
            InitializeTenancyIfTenantDomain::class,
        ], append: [
            EnsureSpaResponse::class,
            CaptureUtmTracking::class,
        ]);

        $middleware->api(prepend: [
            InitializeTenancyIfTenantDomain::class,
        ]);

        $middleware->append(LogDatabaseUsage::class);

        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'response.cache' => CacheResponse::class,
            'doNotCacheResponse' => DoNotCacheResponse::class,
            'tenancy.domain' => InitializeTenancyByDomain::class,
            'tenancy.prevent_central' => PreventAccessFromCentralDomains::class,
            'accounting.enabled' => EnsureAccountingEnabled::class,
        ])
            ->validateCsrfTokens(except: ['*']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TenantCouldNotBeIdentifiedByDomainException $e, Request $request) {
            abort(404, 'Store or tenant website not found.');
        });

        $exceptions->render(function (TenantCouldNotBeIdentifiedOnDomainException $e, Request $request) {
            abort(404, 'Store or tenant website not found.');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.',
                ], 404);
            }
        });
    })
    ->create();
