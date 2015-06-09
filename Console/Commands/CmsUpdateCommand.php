<?php namespace Cms\Modules\Core\Console\Commands;

use Symfony\Component\Console\Input\InputOption;

class CmsUpdateCommand extends BaseCommand
{
    protected $name = 'cms:update';
    protected $readableName = 'Phoenix CMS Updater';
    protected $description = 'Phoenix CMS Updater';

    public function fire()
    {
        $cmd = $this->option('verbose') ? 'call' : 'callSilent';

        $this->comment('Publishing Theme Assets...');
        $this->$cmd('theme:publish', ['--force' => null]);

        $this->comment('Publishing Module Assets...');
        $this->$cmd('module:publish');

        $this->comment('Publishing Module Configs...');
        $this->$cmd('module:publish-config', ['--force' => null]);

        $this->comment('Publishing New Module Permissions...');
        $this->$cmd('module:publish-permissions');

        $this->comment('Publishing New Module Translations...');
        $this->$cmd('module:publish-translation');

        $this->comment('Publishing Module Migrations...');
        $this->$cmd('module:publish-migration');

        $this->comment('Migrating New Module Migrations...');
        $this->$cmd('module:migrate');

        $this->comment('Generating autoload files...');
        $this->$cmd('dump-autoload');

        $this->info('Done');
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
}