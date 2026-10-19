<?php

declare(strict_types=1);

namespace App\Tenancy\Bootstrappers;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class TenantConfigBootstrapper implements TenancyBootstrapper
{
    protected mixed $originalOninda = null;

    /**
     * Bootstrap tenant configuration.
     * For tenant websites, app.oninda is always converted to false.
     */
    public function bootstrap(Tenant $tenant): void
    {
        $this->originalOninda = config('app.oninda');
        config(['app.oninda' => false]);
    }

    /**
     * Revert configuration back to central platform settings.
     */
    public function revert(): void
    {
        config(['app.oninda' => $this->originalOninda]);
        $this->originalOninda = null;
    }
}
