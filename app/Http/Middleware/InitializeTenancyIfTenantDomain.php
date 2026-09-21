<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyIfTenantDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('tenancy.enabled', false)) {
            return $next($request);
        }

        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);

        // If host is in central domains, do not initialize tenancy (Central platform mode)
        if (in_array($host, $centralDomains, true)) {
            return $next($request);
        }

        // Otherwise, resolve tenant by domain/subdomain
        try {
            $resolver = app(DomainTenantResolver::class);
            $tenancy = app(Tenancy::class);
            $tenant = $resolver->resolve($host);
            $tenancy->initialize($tenant);
        } catch (TenantCouldNotBeIdentifiedOnDomainException $e) {
            abort(404, 'Store or tenant website not found.');
        }

        return $next($request);
    }
}
