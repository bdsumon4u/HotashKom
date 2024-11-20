<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('exp', function ($expression) {
            return "<?php $expression ?>";
        });

        foreach (['title', 'content'] as $layout) {
            Blade::directive($layout, function ($expression) use ($layout) {
                return "<?php \$__env->startSection('{$layout}', {$expression}); ?>";
            });
            Blade::directive("end{$layout}", function () {
                return '<?php $__env->stopSection(); ?>';
            });
        }

        foreach (['title', 'content'] as $layout) {
            Blade::directive($layout, function ($expression) use ($layout) {
                return "<?php \$__env->startSection('{$layout}', {$expression}); ?>";
            });
            Blade::directive("end{$layout}", function () {
                return '<?php $__env->stopSection(); ?>';
            });
        }

        Blade::directive('errors', function () {
            return '<?php if ($errors->any()): ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach($errors->all() as $error): ?>
                        <li>{{ $error }}</li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>';
        });
    }
}
