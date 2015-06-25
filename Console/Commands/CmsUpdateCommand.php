<?php namespace Cms\Modules\Core\Console\Commands;

use Symfony\Component\Console\Input\InputOption;

class CmsUpdateCommand extends CmsInstallCommand
{
    protected $name = 'cms:update';
    protected $readableName = 'Phoenix CMS Updater';
    protected $description = 'Phoenix CMS Updater';

    public function fire()
    {
        $this->cmd = $this->option('verbose') ? 'call' : 'callSilent';

        $this->info('Updating...');

        $this->do_clearCompiled();

        $this->do_themePublish();

        $this->do_modulePublish();
        $this->do_modulePublishConfig();
        $this->do_modulePublishPermissions();
        $this->do_modulePublishTranslations();

        $this->do_modulePublishMigrations();
        $this->do_migrate();

        $this->do_cacheClear();
        $this->do_autoload();
        $this->do_optimize();

        $this->done();
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
