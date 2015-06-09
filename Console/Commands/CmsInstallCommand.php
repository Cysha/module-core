<?php namespace Cms\Modules\Core\Console\Commands;

use Schema;
use File;

class CmsInstallCommand extends BaseCommand
{
    protected $name = 'cms:install';
    protected $readableName = 'Phoenix CMS Installer';
    protected $description = 'Installs the CMS';

    public function fire()
    {
        $this->header();

        $this->cmd = $this->option('verbose') ? 'call' : 'callSilent';

        // test for db connectivity
        if ($this->do_dbCheck() === false) {
            $this->error('Database Details seem to be invalid, you need to fix this before we can continue...');
            return;
        }

        if ($this->confirm(' This command will (re)build your database! Continue? ', true) === false) {
            $this->done();
            return;
        }

        $this->do_cacheClear();
        $this->do_keyGenerate();
        $this->do_migrateCheck();
        $this->do_modulePublish();
        $this->do_modulePublishTranslation();
        $this->do_modulePublishConfig();
        $this->do_clearMigrationPath();
        $this->do_modulePublishMigration();
        $this->do_migrate();

        $this->do_moduleProcessing();
        $this->do_cacheClear();
        $this->done();
    }

    protected function getOptions()
    {
        return [];
    }

/**
 * INSTALLER METHODS
 */
    private function do_dbCheck()
    {
        try {
            \DB::connection()->getDatabaseName();
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }

    private function do_cacheClear()
    {
        $this->info('Clearing out the System Cache...');
        $this->{$this->cmd}('cache:clear');
    }

    private function do_keyGenerate()
    {
        $this->info('Generating Secure Key...');
        $this->{$this->cmd}('key:generate');
    }

    private function do_migrate()
    {
        $this->info('Setting up the database...');
        $this->{$this->cmd}('migrate');
    }

    private function do_migrateCheck()
    {
        // if we have migrations in the db already
        if (!Schema::hasTable('migrations')) {
            return false;
        }

        // just run a reset
        $this->info('Clearing out the database...');
        $this->{$this->cmd}('migrate:reset');
    }

    private function do_modulePublish()
    {
        $this->comment('Publishing Module Assets...');
        $this->{$this->cmd}('module:publish');
    }

    private function do_modulePublishTranslation()
    {
        $this->comment('Publishing Module Translations...');
        $this->{$this->cmd}('module:publish-translation');
    }

    private function do_modulePublishConfig()
    {
        $this->comment('Publishing Module Configs...');
        $this->{$this->cmd}('module:publish-config', ['--force' => null]);
    }

    private function do_clearMigrationPath()
    {
        $this->comment('Clearing out migration directory...');
        File::cleanDirectory(config('modules.paths.migration'));
    }

    private function do_modulePublishMigration()
    {
        $this->comment('Publishing Module Migrations...');
        $this->{$this->cmd}('module:publish-migration');
    }

    private function do_moduleProcessing()
    {
        $objModules = app('modules');
        if (!count($objModules->enabled())) {
            return;
        }

        foreach ($objModules->getOrdered() as $module) {
            if (!$module->enabled()) {
                continue;
            }
            $this->info($module->getName().' module...');

            $this->do_modulesDependencyInstallers($module);
            $this->do_modulesSeeding($module);
        }
    }

    private function do_modulesSeeding($module)
    {
        $this->comment('Seeding Module...');
        $this->{$this->cmd}('module:seed', ['module' => $module->getName()]);
    }

    private function do_modulesDependencyInstallers($module)
    {
        // $this->comment($module->getPath().'/Console/InstallCommand.php');
        // if (File::exists($module->getPath().'/Console/InstallCommand.php')) {
        //     $this->comment('Running the dependency installer for the '.$module->getName().' module...');
        //     $this->{$this->cmd}('module:install', ['module' => $module->getName()]);
        // }
    }

    private function done()
    {
        $this->call('dump-autoload');
        $this->info('Done');
        $this->comment('=====================================');
        $this->comment('');
    }
}
