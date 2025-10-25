<?php

namespace Hotash\LaravelMultiUi\Console\Commands;

use Hotash\LaravelMultiUi\Console\Helper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateCommand extends Command
{
    use Helper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multi-ui:generate { model=User : Give a name for authenticable model }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate multiple authentication module';

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

        $this->generateConfig()
            ->generateModel()
            ->generateNotifications()
            ->generateMigration()
            ->generateSeeder()
            ->generateFactory()
            ->generateControllers()
            ->generateRoutes()
            ->generateViews()
            ->done();
    }

    /**
     * Generate Config
     */
    protected function generateConfig()
    {
        $auth = file_get_contents(config_path('auth.php'));
        $keys = [
            'guards' => $this->strtr['{{SINGULAR_KEBAB}}'],
            'providers' => $this->strtr['{{PLURAL_KEBAB}}'],
            'passwords' => $this->strtr['{{PLURAL_KEBAB}}'],
        ];

        foreach ($keys as $type => $key) {
            $items = array_keys(config("auth.$type"));
            if (! in_array($key, $items)) {
                $auth = str_replace("'".$type."' => [", "'".$type."' => [".$this->strFile($this->getStubPath("config/$type.stub"), true), $auth);
            }
        }

        if (file_put_contents(config_path('auth.php'), $auth)) {
            $this->call('config:cache');
        }

        return $this;
    }
}
