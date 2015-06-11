<?php namespace Cms\Modules\Core\Console\Commands;

use Illuminate\Filesystem\Filesystem;
use Pingpong\Modules\Process\Installer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

class CmsModuleMakeCommand extends BaseCommand
{
    protected $name = 'cms:module:make';
    protected $readableName = 'Phoenix CMS - Module Make';
    protected $description = 'Spawns a module with the details provided';

    protected $files;

    public function __construct()
    {
        parent::__construct();

        $this->files = app('Illuminate\Filesystem\Filesystem');
    }

    public function fire()
    {
        $this->cmd = $this->option('verbose') ? 'call' : 'callSilent';

        $this->info('Gathering Info...');

        $info = $this->gatherData();

        $this->spawnModule($info);

        $this->info('Generating Files...');
        if ($this->moveModule($info)) {
            $this->renamePlaceholderFiles($info);
            $this->replacePlaceholdersInFiles($info);

            $this->files->deleteDirectory(app_path('Modules/'.$info['%module_name'].'/.git'));
        }

        $this->info('Done!');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }

    protected function gatherData() {
        // set defaults
        $info = [
            '%author_name' => null,
            '%author_username' => null,
            '%author_website' => null,
            '%author_email' => null,
            '%module_name_lower' => null,
            '%module_name' => null,
            '%package_name' => null,
            '%module_description' => null,
        ];

        // actually gather the info
        $info['%author_name'] = $this->ask('What is your full name? (%author_name)');
        $info['%author_username'] = $this->ask('What is your github username? (%author_username)');
        $info['%author_website'] = $this->ask('What is your website address? (%author_website)', 'http://github.com/'.$info['%author_username']);
        $info['%author_email'] = $this->ask('What is your email address? (%author_email)');

        $module_name = $this->ask('What is your name? (%module_name)');

        $info['%module_name_lower'] = strtolower($module_name);
        $info['%module_name'] = ucwords($module_name);
        $info['%package_name'] = $this->ask('What is your module name? (%package_name)', sprintf('%s/pxcms-%s', $info['%author_username'], $info['%module_name_lower']));
        $info['%module_description'] = $this->ask('What is the purpose of your module? (%module_description)');

        return $info;
    }

    protected function spawnModule(array $info) {
        $this->info('Creating Module...');

        if ($this->files->isDirectory(app_path('Modules/PxcmsSkeleton'))) {
            $this->files->deleteDirectory(app_path('Modules/PxcmsSkeleton'));
        }

        ob_start();
        $installer = new Installer(
            'Cysha/pxcms-skeleton',
            null,
            'github',
            false
        );

        $installer->setRepository($this->laravel['modules']);

        $installer->setConsole($this);

        $installer->run();
        ob_end_clean();
    }

    protected function moveModule(array $info) {
        if ($this->files->isDirectory(app_path('Modules/'.$info['%module_name']))) {
            $this->error('[ERROR] The module you are trying to create already exists.');

            if ($this->confirm('Do you want to overwrite it?', false)) {
                $this->files->deleteDirectory(app_path('Modules/'.$info['%module_name']));
            } else {
                return false;
            }
        }

        if ($this->files->isDirectory(app_path('Modules/PxcmsSkeleton'))) {
            $this->files->move(
                app_path('Modules/PxcmsSkeleton'),
                app_path('Modules/'.$info['%module_name'])
            );

            return true;
        }

        return false;
    }

    protected function renamePlaceholderFiles(array $info) {
        $files = Finder::create()
            ->in(app_path('Modules/'.$info['%module_name']))
            ->name('*.php');

        foreach ($files as $file) {
            if (strpos($file->getRealPath(), '%') === false) {
                continue;
            }

            $this->files->move(
                $file->getRealPath(),
                $this->replacePlaceholders($file->getRealPath(), $info)
            );
        }
    }

    protected function replacePlaceholdersInFiles(array $info) {
        $files = Finder::create()
            ->in(app_path('Modules/'.$info['%module_name']))
            ->name('*.php')
            ->name('*.json')
            ->name('*.md');

        $infoKeys = array_keys($info);
        $infoValues = array_values($info);

        foreach ($files as $file) {
            $this->files->put(
                $file->getRealPath(),
                $this->replacePlaceholders($this->files->get($file->getRealPath()), $info)
            );
        }
    }

    protected function replacePlaceholders($string, array $info) {
        $infoKeys = array_keys($info);
        $infoValues = array_values($info);

        return str_replace($infoKeys, $infoValues, $string);
    }
}
