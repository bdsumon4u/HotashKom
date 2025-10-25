<?php

namespace Hotash\LaravelMultiUi\Console\Commands;

use Hotash\LaravelMultiUi\Console\Helper;
use Hotash\LaravelMultiUi\Presets\Bootstrap;
use Hotash\LaravelMultiUi\Presets\React;
use Hotash\LaravelMultiUi\Presets\Vue;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class InitialCommand extends Command
{
    use Helper;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'multi-ui:init { type : The preset type (bootstrap, vue, react) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swap the front-end scaffolding for the application';

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
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $this->parseName();
        $this->fixWelcome()
            ->setupMiddleware()
            ->setupKernel()
            ->setupConfig()
            ->generateMigrations()
            ->generateModel()
            ->generateNotifications()
            ->generateSeeder()
            ->generateFactory()
            ->generateControllers()
            ->generateRoutes()
            ->generateViews()
            ->done();

        if (static::hasMacro($this->argument('type'))) {
            return call_user_func(static::$macros[$this->argument('type')], $this);
        }

        if (! in_array($this->argument('type'), ['bootstrap', 'vue', 'react'])) {
            throw new InvalidArgumentException('Invalid preset.');
        }

        $this->{$this->argument('type')}();
    }

    /**
     * Install the "bootstrap" preset.
     *
     * @return void
     */
    protected function bootstrap()
    {
        Bootstrap::install();

        $this->info('Bootstrap scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Install the "vue" preset.
     *
     * @return void
     */
    protected function vue()
    {
        Bootstrap::install();
        Vue::install();

        $this->info('Vue scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Install the "react" preset.
     *
     * @return void
     */
    protected function react()
    {
        Bootstrap::install();
        React::install();

        $this->info('React scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Fix Welcome
     *
     * @return $this
     */
    protected function fixWelcome()
    {
        $dst_path = resource_path('views/welcome.blade.php');

        if (file_exists($dst_path)) {
            $this->info('[welcome.blade.php] File Is Already Exists.');
            if (! $this->confirm('Do You Want To Override It?')) {
                $this->info('Skipping..');

                return $this;
            }
        }

        if (
            file_put_contents(
                $dst_path,
                $this->strFile(
                    $this->getStubPath('views/welcome.stub')
                )
            )
        ) {
            $this->info('[welcome.blade.php] File Is Fixed.');
        }

        return $this;
    }

    /**
     * Setup Middleware
     *
     * @return $this
     */
    protected function setupMiddleware()
    {
        $middlewares = [
            'Authenticate',
            'EnsureEmailIsVerified',
            'RedirectIfAuthenticated',
        ];

        foreach ($middlewares as $item) {
            $dst_path = app_path('Http/Middleware/'.$item.'.php');
            $stb_path = $this->getStubPath('Http/Middleware/'.$item.'.stub');
            $compiled = $this->strFile($stb_path);

            // Middleware Doesn't Exists.
            if (file_exists($dst_path)) {
                $dst_file = $this->strFile($dst_path);
                // Middleware Is Already Fixed.
                if (! strcmp($dst_file, $compiled)) {
                    $this->info("[$item] Middleware Is Already Fixed..");

                    continue;
                }

                // Middleware Is Not Fixed.
                $this->info("[$item] Middleware Is Not Fixed.");
                if (! $this->confirm('Do You Want To Override It?')) {
                    $this->warn('Fix That Manually..');

                    continue;
                }
            }

            if (file_put_contents($dst_path, $compiled)) {
                $this->info("[$item] Middleware Is Fixed.");
            }
        }

        return $this;
    }

    /**
     * Setup Kernel
     *
     * @return $this
     */
    protected function setupKernel()
    {
        $dst_path = app_path('Http/Kernel.php');
        $dst_file = $this->strFile($dst_path);
        $stb_path = $this->getStubPath('Http/Kernel.stub');
        $compiled = $this->strFile($stb_path);

        // Kernel Is Already Fixed.
        if (! strcmp($dst_file, $compiled)) {
            $this->info("[Http\Kernel.php] File Is Already Fixed..");

            return $this;
        }

        // Kernel Is Not Fixed
        $this->info("[Http\Kernel.php] File Is Not Fixed.");
        if (! $this->confirm('Do You Want To Override It?')) {
            $this->warn('Fix That Manually.');

            return $this;
        }

        if (file_put_contents($dst_path, $compiled)) {
            $this->info("[Http\Kernel.php] File Is Fixed.");
        }

        return $this;
    }

    /**
     * Setup Config
     */
    protected function setupConfig()
    {
        $dst_path = config_path('auth.php');
        $dst_file = $this->strFile($dst_path);
        $treated = str_replace("'web'", "'user'", $dst_file);

        if (file_put_contents($dst_path, $treated)) {
            $this->info('Config Is Fixed.');
        }

        return $this;
    }
}
