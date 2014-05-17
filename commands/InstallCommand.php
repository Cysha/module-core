<?php namespace Cysha\Modules\Core\Commands;

use Schema;
use File;

class InstallCommand extends BaseCommand
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

            foreach (File::directories(app_path().'/modules/') as $module) {
                if (File::exists($module.'/commands/InstallCommand.php')) {
                    $module = explode('/', $module);
                    $module = end($module);

                    if ($module != 'core') {
                        $this->comment('Installing the '.$module.' module...');
                        $this->call('modules:install', ['module' => $module]);
                    }

                    $this->comment('Migrating module...');
                    $this->call('modules:migrate', ['module' => $module]);

                    if ($seed) {
                        $this->comment('Seeding module...');
                        $this->call('modules:seed', ['module' => $module]);
                    }
                }
            }

        }
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
