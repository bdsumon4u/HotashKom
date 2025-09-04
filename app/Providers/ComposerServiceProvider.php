<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $parameters = optional(Route::current())
                ->parameters();
            foreach ($parameters ?: [] as $key => $value) {
                if (! $view->offsetExists($key)) {
                    $view->with($key, $value);
                }
            }
        });

        $menus = [
            'topbar-menu' => 'topbar',
            'header-menu' => 'header.menu.*',
            'quick-links' => 'footer',
        ];

        foreach ($menus as $slug => $view) {
            View::composer("partials.{$view}", function ($view) use ($slug): void {
                $view->withMenuItems(cache()->rememberForever('menus:'.$slug, fn () => optional(Menu::whereSlug($slug)->first())->menuItems ?: new Collection));
            });
        }

        View::composer(['layouts.yellow.master'], function ($view): void {
            $view->with('categories', Category::nested(10));
        });

        $settingsPages = [
            'partials.header.*',
            'partials.footer',
            'products.show',
            'admin.layouts.master',
            'admin.orders.show',
            'admin.orders.invoices',
            'admin.orders.stickers',
            'user.layouts.master',
            'layouts.light.master',
            'layouts.yellow.master',
            'layouts.reseller.master',
            'layouts.errors.master',
        ];
        View::composer($settingsPages, function ($view): void {
            $view->with(Setting::array());
        });
    }
}
