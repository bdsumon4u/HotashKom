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
        // Refactor the @exp directive
        Blade::extend(function ($view) {
            return preg_replace('/@exp\((.*?)\)/', '<?php $1 ?>', $view);
        });

        // Refactor the @title and @content directives
        foreach (['title', 'content'] as $layout) {
            Blade::extend(function ($view) use ($layout) {
                $startPattern = "/@$layout\((.*?)\)/";
                $startReplacement = "<?php \$__env->startSection('{$layout}', $1); ?>";

                $endPattern = "/@end{$layout}/";
                $endReplacement = '<?php $__env->stopSection(); ?>';

                $view = preg_replace($startPattern, $startReplacement, $view);
                return preg_replace($endPattern, $endReplacement, $view);
            });
        }

        // Refactor the @errors directive
        Blade::extend(function ($view) {
            $pattern = '/@errors/';
            $replacement = <<<'EOT'
<?php if ($errors->any()): ?>
    <div class="alert alert-danger" role="alert">
        <ul>
            <?php foreach($errors->all() as $error): ?>
            <li>{{ $error }}</li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>
EOT;

            return preg_replace($pattern, $replacement, $view);
        });
    }
}
