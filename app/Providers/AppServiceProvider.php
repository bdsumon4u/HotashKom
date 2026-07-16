<?php

namespace App\Providers;

use App\Extensions\DatabaseSessionHandler;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Page;
use App\Models\Product;
use App\Models\Slide;
use App\Observers\ResponseCacheObserver;
use App\Pathao\Apis\AreaApi;
use App\Pathao\Apis\OrderApi;
use App\Pathao\Apis\StoreApi;
use App\Pathao\Manage\Manage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
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
            fn ($relation, $constraint) => $this
                ->whereHas($relation, $constraint)
                ->with([$relation => $constraint])
        );

        $this->app->bind('pathao', fn (): Manage => new Manage(
            new AreaApi,
            new StoreApi,
            new OrderApi
        ));

        $this->app->bind('redx', fn (): \App\Redx\Manage\Manage => new \App\Redx\Manage\Manage(
            new \App\Redx\Apis\AreaApi,
            new \App\Redx\Apis\StoreApi,
            new \App\Redx\Apis\OrderApi
        ));

        collect([
            Brand::class,
            Category::class,
            HomeSection::class,
            Page::class,
            Product::class,
            Slide::class,
        ])->each(static function (string $model): void {
            $model::observe(ResponseCacheObserver::class);
        });

        // Release the MySQL connection between queue jobs so workers do not
        // hold idle connections open while waiting for the next job.
        Event::listen(JobProcessed::class, static function (): void {
            DB::disconnect();
        });

        // Reconnect cleanly before the next job starts in case the connection
        // was dropped by MySQL's wait_timeout during the idle period.
        Event::listen(JobProcessing::class, static function (): void {
            DB::reconnect();
        });
    }
}
