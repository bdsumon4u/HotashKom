<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('exp', fn ($expression): string => "<?php $expression ?>");

        foreach (['title', 'content'] as $layout) {
            Blade::directive($layout, fn ($expression): string => "<?php \$__env->startSection('{$layout}', {$expression}); ?>");
            Blade::directive("end{$layout}", fn (): string => '<?php $__env->stopSection(); ?>');
        }

        foreach (['title', 'content'] as $layout) {
            Blade::directive($layout, fn ($expression): string => "<?php \$__env->startSection('{$layout}', {$expression}); ?>");
            Blade::directive("end{$layout}", fn (): string => '<?php $__env->stopSection(); ?>');
        }

        Blade::directive('errors', fn (): string => '<?php if ($errors->any()): ?>
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
