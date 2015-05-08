<?php namespace Cms\Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ThemePublishCommand extends Command
{
    protected $name = 'theme:publish';
    protected $readableName = 'Publish Theme Assets';
    protected $description = 'Publish theme assets';

    protected $file;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->registerFile();
    }

    public function registerFile()
    {
        $this->file = app('Illuminate\Filesystem\Filesystem');
    }

    public function fire()
    {
        if ($name = $this->argument('theme')) {
            return $this->publishTheme($name);
        }

        $this->publishAll();
    }

    protected function publishAll()
    {
        $themeDir = public_path(config('theme.themeDir'));
        $dirs = $this->file->directories($themeDir);
        if (!count($dirs)) {
            return;
        }

        foreach ($dirs as $dir) {
            $this->publishTheme(class_basename($dir));
        }
    }

    protected function publishTheme($name)
    {
        $themeDir = public_path(config('theme.themeDir'));
        $assetDir = config('theme.containerDir.asset');
        $themeAssetDir = [$themeDir, $name, $assetDir];
        $publicAssetDir = [public_path('themes'), $name];

        $this->line("<info>Published</info>: {$name}");

        $this->file->copyDirectory(implode($themeAssetDir, DIRECTORY_SEPARATOR), implode($publicAssetDir, DIRECTORY_SEPARATOR));

    }

    protected function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'Name of the theme you wish to publish']
        ];
    }
}
