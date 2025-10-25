<?php

namespace Hotash\LaravelMultiUi\Console;

use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

trait Helper
{
    protected $files;

    protected $model = 'User';

    protected $strtr = ["\r\n" => "\n"];

    /**
     * Parse model name
     * Get the model name in different cases.
     *
     * @return array
     */
    protected function parseName()
    {
        $name = $this->model;

        $this->strtr += [
            '{{UPPERCASE}}' => strtoupper($name),
            '{{PLURAL_SNAKE}}' => Str::plural(Str::snake($name)),
            '{{PLURAL_KEBAB}}' => Str::plural(Str::kebab($name)),
            '{{PLURAL_STUDLY}}' => Str::plural(Str::studly($name)),
            '{{SINGULAR_SNAKE}}' => Str::singular(Str::snake($name)),
            '{{SINGULAR_KEBAB}}' => Str::singular(Str::kebab($name)),
            '{{SINGULAR_STUDLY}}' => Str::singular(Str::studly($name)),
        ];

        return $this->strtr;
    }

    /**
     * Get File Content As String
     *
     * @param  string  $path
     * @return string
     */
    protected function strFile($path, $compile = false)
    {
        return $compile
            ? strtr(file_get_contents($path), $this->strtr)
            : str_replace("\r\n", "\n", file_get_contents($path));
    }

    /**
     * Generate Migration
     */
    protected function generateMigration($table = null)
    {
        if (is_null($table)) {
            $table = $this->strtr['{{PLURAL_SNAKE}}'];
        }

        $generate = true;
        $to = null;
        collect($this->files->allFiles(database_path('migrations')))
            ->each(function (SplFileInfo $file) use (&$generate, $table, &$to) {
                if (preg_match('/.+_create_'.$table.'_table.php$/', $file->getFilename(), $match)) {
                    $this->info($match[0].' migration already exists.');
                    if (! $this->confirm('Do you want to override it?')) {
                        $generate = false;
                        $this->info('Skipping..');
                    } else {
                        $generate = true;
                        $to = $match[0];
                    }

                    return true;
                }
            });

        // Migration Table Should Be Generated
        if ($generate) {
            $to = $to ?? date('Y_m_d_His').'_create_'.$table.'_table.php';
            $dst_path = database_path("/migrations/$to");

            $from = 'password_resets';
            $compile = false;
            if ($table != $from) {
                $from = 'model';
                $compile = true;
            }
            $stb_path = $this->getStubPath("migrations/$from.stub");

            file_put_contents($dst_path, $this->strFile($stb_path, $compile));
            $this->info('Migration Generated.');
        }

        return $this;
    }

    /**
     * Generate Migrations
     */
    protected function generateMigrations()
    {
        $this->generateMigration('password_resets')
            ->generateMigration();

        return $this;
    }

    /**
     * Generate Model
     */
    protected function generateModel()
    {
        $dst_path = app_path("{$this->strtr['{{SINGULAR_STUDLY}}']}.php");
        $compiled = $this->strFile($this->getStubPath('Model.stub'), true);

        if (file_exists($dst_path)) {
            $this->info("[{$this->strtr['{{SINGULAR_STUDLY}}']}] Model Is Already Exists.");
            if (! $this->confirm('Do You Want To Override It?')) {
                $this->info('Skipping..');

                return $this;
            }
        }
        file_put_contents($dst_path, $compiled);
        $this->info("[{$this->strtr['{{SINGULAR_STUDLY}}']}] Model Generated.");

        return $this;
    }

    /**
     * Generate Notifications
     */
    protected function generateNotifications()
    {
        if (! is_dir($dir = app_path('Notifications/'.$this->strtr['{{SINGULAR_STUDLY}}']))) {
            mkdir($dir, 0755, true);
        }

        $generated = false;
        collect($this->files->allFiles($this->getStubPath('Notifications')))
            ->each(function (SplFileInfo $file) use ($dir, &$generated) {
                $filename = Str::replaceLast('.stub', '.php', $file->getFilename());
                $dst_path = $dir.DIRECTORY_SEPARATOR.$filename;
                $compiled = $this->strFile($file, true);

                if (file_exists($dst_path)) {
                    if (! $this->confirm("[$filename] Notification Is Already Exists. Do You Want To Override It?")) {
                        goto done;
                    }
                }
                $generated = file_put_contents($dst_path, $compiled);

                return true;

                done:
                $this->info('Skipping..');
            });

        if ($generated) {
            $this->info('Notification(s) Generated.');
        }

        return $this;
    }

    /**
     * Generate Seeder
     */
    protected function generateSeeder()
    {
        $dst_path = database_path("seeds/{$this->strtr['{{SINGULAR_STUDLY}}']}Seeder.php");
        $stb_path = $this->getStubPath('Seeder.stub');
        $compiled = $this->strFile($stb_path, true);

        if (file_exists($dst_path)) {
            $this->info("[{$this->strtr['{{SINGULAR_STUDLY}}']}Seeder.php] Seeder Is Already Exists.");
            if (! $this->confirm('Do You Want To Override It?')) {
                $this->info('Skipping..');

                return $this;
            }
        }
        file_put_contents($dst_path, $compiled);
        $this->info('Seeder Generated.');

        return $this;
    }

    /**
     * Generate Factory
     */
    protected function generateFactory()
    {

        $dst_path = database_path("factories/{$this->strtr['{{SINGULAR_STUDLY}}']}Factory.php");
        $stb_path = $this->getStubPath('Factory.stub');
        $compiled = $this->strFile($stb_path, true);

        if (file_exists($dst_path)) {
            $this->info("[{$this->strtr['{{SINGULAR_STUDLY}}']}Factory.php] Factory Is Already Exists.");
            if (! $this->confirm('Do You Want To Override It?')) {
                $this->info('Skipping..');

                return $this;
            }
        }
        file_put_contents($dst_path, $compiled);
        $this->info('Factory Generated.');

        return $this;
    }

    /**
     * Generate Controllers
     */
    protected function generateControllers()
    {
        if (! is_dir($dir = app_path('Http/Controllers/'.$this->strtr['{{SINGULAR_STUDLY}}'].'/Auth'))) {
            mkdir($dir, 0755, true);
        }

        $generated = false;
        collect($this->files->allFiles($this->getStubPath('Http/Controllers/Auth')))
            ->each(function (SplFileInfo $file) use ($dir, &$generated) {
                $filename = Str::replaceLast('.stub', '.php', $file->getFilename());
                $dst_path = $dir.DIRECTORY_SEPARATOR.$filename;
                $compiled = $this->strFile($file, true);

                if (file_exists($dst_path)) {
                    $this->info("[{$filename}] Controller Already Exists.");
                    if (! $this->confirm('Do You Want To Override It?')) {
                        goto done;
                    }
                }
                $generated = file_put_contents($dst_path, $compiled);

                return true;

                done:
                $this->info('Skipping..');
            });

        if ($generated) {
            $this->info('Auth Controller(s) Generated.');
        }

        // Export Home Controller
        $dst_path = app_path('Http/Controllers/'.$this->strtr['{{SINGULAR_STUDLY}}'].'/HomeController.php');
        $stb_path = $this->getStubPath('Http/Controllers/HomeController.stub');
        $compiled = $this->strFile($stb_path, true);

        if (file_exists($dst_path)) {
            if (! $this->confirm('The [HomeController.php] Controller Already Exists. Do You Want To Override It?')) {
                $this->info('Skipping..');

                return $this;
            }
        }
        if (file_put_contents($dst_path, $compiled)) {
            $this->info('Home Controller Generated.');
        }

        return $this;
    }

    /**
     * Generate Routes
     */
    protected function generateRoutes()
    {
        if (! is_dir($dir = base_path('routes'))) {
            mkdir($dir, 0755, true);
        }

        $file = $this->strtr['{{SINGULAR_KEBAB}}'];
        if (file_exists("$dir/$file.php")) {
            $this->info("[$dir/$file.php] File Already Exists.");
            if (! $this->confirm('Do You Want To Override It?')) {
                goto register_map;
            }
        }
        file_put_contents("$dir/$file.php", $this->strFile($this->getStubPath('routes/file.stub'), true));
        $this->info('Route File Generated.');

        register_map:

        $provider_path = app_path('Providers/RouteServiceProvider.php');
        $provider = $this->strFile($provider_path);

        $registered = false;

        // Map
        $map = $this->strFile($this->getStubPath('routes/map.stub'), true);
        if (strpos($provider, $map) != false) {
            $this->info('Routes Are Already Registered.');
            $this->info('Skipping..');
        } else {
            $substr = substr($provider, 0, -3);
            $provider = str_replace($substr, $substr.$map, $provider);
            $registered = true;
        }

        // Call
        $call = $this->strFile($this->getStubPath('routes/call.stub'), true);
        if (strpos($provider, $call) != false) {
            $this->info('Routes Are Already Mapped.');
            $this->info('Skipping..');
        } else {
            $bait = '$this->mapWebRoutes();';
            $provider = str_replace($bait, $bait.$call, $provider);
            $registered = true;
        }

        if ($registered) {
            file_put_contents($provider_path, $provider);
            $this->info('Route File Registered.');
        }

        return $this;
    }

    /**
     * Generate Views
     */
    protected function generateViews()
    {
        $views = [
            'auth/login',
            'auth/passwords/confirm',
            'auth/passwords/email',
            'auth/passwords/reset',
            'auth/register',
            'auth/verify',
            'layouts/master',
            'home',
        ];

        if (! is_dir($dir = $this->getViewPath('layouts'))) {
            mkdir($dir, 0755, true);
        }

        if (! is_dir($dir = $this->getViewPath('auth/passwords'))) {
            mkdir($dir, 0755, true);
        }

        $generated = false;
        foreach ($views as $item) {
            if (file_exists($view = $this->getViewPath("$item.blade.php"))) {
                $this->info("[{$item}.blade.php] View Already Exists.");
                if (! $this->confirm('Do You Want To Override It?')) {
                    $this->info('Skipping..');

                    continue;
                }
            }

            $generated = file_put_contents(
                $view,
                $this->strFile(
                    $this->getStubPath("views/$item.stub"), true
                )
            );
        }

        if ($generated) {
            $this->info('View(s) Generated.');
        }

        return $this;
    }

    /**
     * Done
     */
    protected function done()
    {
        tap(new Composer($this->files), function ($composer) {
            $composer->dumpOptimized();
        });

        $this->info('Your Auth Module Is Ready For '.$this->strtr['{{SINGULAR_STUDLY}}'].' Model.');
    }

    /**
     * Get full stub path relative to the application's configured stub path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getStubPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            __DIR__.'/../../stubs', $path,
        ]);
    }

    /**
     * Get full view path relative to the application's configured view path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getViewPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'), $this->strtr['{{SINGULAR_KEBAB}}'], $path,
        ]);
    }
}
