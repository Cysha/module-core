<?php namespace Cysha\Modules\Core\Commands;

use Schema;
use File;

class CmsInstallCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cms:install';

    /**
     * The Readable Module Name.
     *
     * @var string
     */
    protected $readableName = 'Core Install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the CMS';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->header();
        if ($this->confirm(' This command will rebuild your database! Continue? [yes|no]', true)) {

            $this->info('Clearing out the System Cache...');
            $this->call('cache:clear');

            $this->info('Generating Secure Key...');
            $this->call('key:generate');

            $path = app_path().'/storage/migrations/';
            if (File::exists($path)) {
                $this->info('Clearing out the Module Migrations Folder...');
                File::cleanDirectory($path);
            } else {
                $this->info('Creating the Module Migrations Folder...');
                File::makeDirectory($path);
            }

            if (Schema::hasTable('migrations')) {
                $this->info('Clearing out the database...');
                $this->call('migrate:reset');
            } else {
                $this->info('Setting up the database...');
                $this->call('migrate:install');
            }

            $seed = false;
            if ($this->confirm(' Do you want to install test data into the database? [yes|no]', true)) {
                $seed = true;
            }

            $modules = app('modules')->modules();
            if (count($modules)) {
                foreach ($modules as $module) {
                    if (!$module->enabled()) {
                        continue;
                    }
                    $moduleName = $module->name();

                    $this->info('Setting up the '.$moduleName.' module...');
                    $this->comment($module->path().'/commands/InstallCommand.php');
                    if (File::exists($module->path().'/commands/InstallCommand.php')) {
                        $this->comment('Running the dependency installer for the '.$moduleName.' module...');
                        $this->call('modules:install', ['module' => $moduleName]);
                    }

                    $this->comment('Migrating module... '.$moduleName);
                    $this->call('modules:migrate', ['module' => $moduleName]);

                    if ($seed) {
                        $this->comment('Seeding module... '.$moduleName);
                        $this->call('modules:seed', ['module' => $moduleName]);
                    }
                }
            }

        }
        $this->call('dump-autoload');
        $this->info('Done');
        $this->comment('=====================================');
        $this->comment('');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
        );
    }

}
