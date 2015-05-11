<?php namespace Cms\Modules\Core\Console;

use Schema;
use File;

class CmsInstallCommand extends BaseCommand
{
    protected $name = 'cms:install';
    protected $readableName = 'Core Install';
    protected $description = 'Installs the CMS';

    public function fire()
    {
        $this->header();

        // test for db connectivity
        try {
            \DB::connection()->getDatabaseName();
        } catch (\PDOException $e) {
            $this->error('Database Details seem to be invalid, you need to fix this before we can continue...');
            exit;
        }

        if ($this->confirm(' This command will (re)build your database! Continue? [yes|no]', true)) {

            $this->info('Clearing out the System Cache...');
            $this->call('cache:clear');

            $this->info('Generating Secure Key...');
            $this->call('key:generate');

            // if we have migrations in the db already
            if (Schema::hasTable('migrations')) {
                // just run a reset
                $this->info('Clearing out the database...');
                $this->call('migrate:reset');
            }

            // wipe out the migrations in there
            $this->info('Clearing out migration directory...');
            File::cleanDirectory(config('modules.paths.migration'));

            // publish the module migrations again
            $this->info('Publishing Module Migrations...');
            $this->call('module:publish-migration');
            $this->call('dump-autoload');

            // then actually migrate!
            $this->info('Setting up the database...');
            $this->call('migrate');

            $seed = false;
            if ($this->confirm(' Do you want to install test data into the database? [yes|no]', true)) {
                $seed = true;
            }

            $objModules = app('modules');
            if (count($objModules->enabled())) {
                foreach ($objModules->getOrdered() as $module) {
                    if (!$module->enabled()) {
                        continue;
                    }
                    $moduleName = $module->getName();

                    $this->info('Setting up the '.$moduleName.' module...');
                    $this->comment($module->getPath().'/Console/InstallCommand.php');
                    if (File::exists($module->getPath().'/Console/InstallCommand.php')) {
                        $this->comment('Running the dependency installer for the '.$moduleName.' module...');
                        $this->call('module:install', ['module' => $moduleName]);
                    }

                    //$this->comment('Migrating module... '.$moduleName);
                    $this->call('module:migrate', ['module' => $moduleName]);

                    if ($seed) {
                        //$this->comment('Seeding module... '.$moduleName);
                        $this->call('module:seed', ['module' => $moduleName]);
                    }
                }

                $this->comment('Publishing Module Assets...'.$moduleName);
                $this->call('module:publish');
            }

        }
        $this->call('dump-autoload');
        $this->info('Done');
        $this->comment('=====================================');
        $this->comment('');
    }

    protected function getOptions()
    {
        return array(
        );
    }
}
