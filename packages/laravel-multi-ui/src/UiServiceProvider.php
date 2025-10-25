<?php

namespace Hotash\LaravelMultiUi;

use Hotash\LaravelMultiUi\Console\Commands\GenerateCommand;
use Hotash\LaravelMultiUi\Console\Commands\InitialCommand;
use Hotash\LaravelMultiUi\Console\Commands\RollbackCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UiServiceProvider extends ServiceProvider
{
    /**
     * Register the package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->registerBladeDirectives();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::mixin(new AuthRouteMethods);
    }

    /**
     * Register Commands
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitialCommand::class,
                GenerateCommand::class,
                RollbackCommand::class,
            ]);
        }
    }

    /**
     * Register Blade Directives
     */
    protected function registerBladeDirectives()
    {
        $this->app->afterResolving('blade.compiler', function ($blade) {
            $blade->directive('everified', function ($guard) {
                $guard = str_replace(['"', "'"], '', $guard);
                if ($guard == '*') {
                    foreach (array_keys(config('auth.guards')) as $guard) {
                        if (auth("$guard")->check()) {
                            break;
                        }
                    }
                }

                return "<?php if( optional(request()->user('{$guard}' == '*' ? null : '{$guard}'))->hasVerifiedEmail() ): ?>";
            });

            $blade->directive('endeverified', function () {
                return '<?php endif; ?>';
            });
        });
    }
}
