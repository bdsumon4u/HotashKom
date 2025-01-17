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
        Blade::directive('exp', fn($expression) => "<?php $expression ?>");

        foreach (['title', 'content'] as $layout) {
            Blade::directive($layout, fn($expression) => "<?php \$__env->startSection('{$layout}', {$expression}); ?>");
            Blade::directive("end{$layout}", fn() => '<?php $__env->stopSection(); ?>');
        }

        foreach (['title', 'content'] as $layout) {
            Blade::directive($layout, fn($expression) => "<?php \$__env->startSection('{$layout}', {$expression}); ?>");
            Blade::directive("end{$layout}", fn() => '<?php $__env->stopSection(); ?>');
        }

        Blade::directive('errors', fn() => '<?php if ($errors->any()): ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach($errors->all() as $error): ?>
                        <li>{{ $error }}</li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>');
    }
}
