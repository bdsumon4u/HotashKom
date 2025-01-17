<?php

namespace App\Providers;

use App\Extensions\DatabaseSessionHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Paginator::useBootstrap();

        Session::extend('custom', function ($app) {
            $table = $app['config']['session.table'];
            $lifetime = $app['config']['session.lifetime'];
            $connection = $app['db']->connection($app['config']['session.connection']);

            return new DatabaseSessionHandler($connection, $table, $lifetime, $app);
        });

        Builder::macro(
            'withWhereHas',
            fn($relation, $constraint) => $this
                ->whereHas($relation, $constraint)
                ->with([$relation => $constraint])
        );

        $this->app->bind('pathao', fn() => new \App\Pathao\Manage\Manage(
            new \App\Pathao\Apis\AreaApi,
            new \App\Pathao\Apis\StoreApi,
            new \App\Pathao\Apis\OrderApi
        ));
    }
}
