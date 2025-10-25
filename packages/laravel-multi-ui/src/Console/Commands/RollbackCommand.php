<?php

namespace Hotash\LaravelMultiUi\Console\Commands;

use Hotash\LaravelMultiUi\Console\Helper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Finder\SplFileInfo;

class RollbackCommand extends Command
{
    use Helper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multi-ui:rollback { model=User : Give your model name to rollback }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy everything releted to your authentication module you\'ve created';

    /**
     * Create a new controller creator command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->model = trim($this->argument('model'));
        $this->parseName();

        $this->destroyConfig()
            ->destroyModel()
            ->destroyNotifications()
            ->destroyMigration()
            ->destroySeeder()
            ->destroyFactory()
            ->destroyControllers()
            ->destroyRoutes()
            ->destroyViews()
            ->done();
    }

    /**
     * Destroy Config
     */
    protected function destroyConfig()
    {
        $auth = $this->strFile(config_path('auth.php'));
        $keys = [
            'guards' => $this->strtr['{{SINGULAR_KEBAB}}'],
            'providers' => $this->strtr['{{PLURAL_KEBAB}}'],
            'passwords' => $this->strtr['{{PLURAL_KEBAB}}'],
        ];

        foreach ($keys as $type => $key) {
            $last = array_key_first(config("auth.$type")) == $key ? "\n" : '';
            if (preg_match("/\s*'{$key}\'\s*=>\s*\[\n(\s*(\'|\w)+\s*=>\s*.*,?\s?){2,}\s*\]\,{$last}/", $auth, $match)) {
                $auth = str_replace($match[0], '', $auth);
            }
        }

        if (file_put_contents(config_path('auth.php'), $auth)) {
            $this->call('config:cache');
        }

        return $this;
    }

    /**
     * Destroy Model
     */
    protected function destroyModel()
    {
        if ($this->files->delete(app_path("{$this->strtr['{{SINGULAR_STUDLY}}']}.php"))) {
            $this->info('Model Destroyed.');
        } else {
            $this->error("Model Doesn't Found.");
        }

        return $this;
    }

    /**
     * Destroy Notifications
     */
    protected function destroyNotifications()
    {
        if ($this->files->deleteDirectory(app_path('Notifications/'.$this->strtr['{{SINGULAR_STUDLY}}']))) {
            $this->info('Notification(s) Destroyed.');
        } else {
            $this->error("Notifications Don't Found.");
        }

        return $this;
    }

    /**
     * Destroy Migration
     */
    protected function destroyMigration()
    {
        $destroyed = false;
        collect($this->files->allFiles(database_path('/migrations')))
            ->each(function (SplFileInfo $file) use (&$destroyed) {
                if (preg_match('/.*_create_'.$this->strtr['{{PLURAL_SNAKE}}'].'_table.php$/', $file->getFilename(), $match)) {
                    if ($this->files->delete(database_path("migrations/$match[0]"))) {
                        $this->info('Migration Destroyed.');
                    } else {
                        $this->error("Migration Doesn't Found.");
                    }

                    return true;
                }
            });

        return $this;
    }

    /**
     * Destroy Seeder
     */
    protected function destroySeeder()
    {
        if ($this->files->delete(database_path("seeds/{$this->strtr['{{SINGULAR_STUDLY}}']}Seeder.php"))) {
            $this->info('Seeder Destroyed.');
        } else {
            $this->error("Seeder Doesn't Found.");
        }

        return $this;
    }

    /**
     * Destroy Factory
     */
    protected function destroyFactory()
    {
        $destroyed = false;
        if ($this->files->delete(database_path("factories/{$this->strtr['{{SINGULAR_STUDLY}}']}Factory.php"))) {
            $this->info('Factory Destroyed.');
        } else {
            $this->error("Factory Doesn't Found.");
        }

        return $this;
    }

    /**
     * Destroy Controllers
     */
    protected function destroyControllers()
    {
        if ($this->files->deleteDirectory(app_path('Http/Controllers/'.$this->strtr['{{SINGULAR_STUDLY}}']))) {
            $this->info('Controller(s) Destroyed.');
        } else {
            $this->error("Controllers Don't Found.");
        }

        return $this;
    }

    /**
     * Destroy Routes
     */
    protected function destroyRoutes()
    {
        $destroyed = false;
        $provider_path = app_path('Providers/RouteServiceProvider.php');
        $provider = $this->strFile($provider_path);

        // Map
        $map = $this->strFile($this->getStubPath('routes/map.stub'), true);

        if (strpos($provider, $map) != false) {
            $provider = str_replace($map, '', $provider);
            $destroyed = true;
        }

        // Call
        $call = $this->strFile($this->getStubPath('routes/call.stub'), true);

        if (strpos($provider, $call) != false) {
            $provider = str_replace($call, '', $provider);
            $destroyed = true;
        }

        file_put_contents($provider_path, $provider);

        if (file_exists($file = base_path('routes/'.$this->strtr['{{SINGULAR_KEBAB}}'].'.php'))) {
            $destroyed = $this->files->delete($file);
        }

        if ($destroyed) {
            $this->info('Routes Destroyed.');
        } else {
            $this->error("Routes Don't Found.");
        }

        return $this;
    }

    /**
     * Destroy Views
     */
    protected function destroyViews()
    {
        if ($this->files->deleteDirectory($this->getViewPath(''))) {
            $this->info('View(s) Destroyed.');
        } else {
            $this->error("Views Don't Found.");
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

        $this->info('Your Auth Module Has Beed Deleted.');
    }
}
