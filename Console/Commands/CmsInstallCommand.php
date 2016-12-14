<?php

namespace Cms\Modules\Core\Console\Commands;

use Carbon\Carbon;
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

        $this->do_clearCompiled();
        $this->do_cacheClear();
        $this->do_keyGenerate();
        $this->do_migrateCheck();
        $this->do_clearMigrationPath();

        $this->do_modulePublish();
        $this->do_modulePublishTranslations();
        $this->do_modulePublishConfig();

        $this->do_modulePublishMigrations();
        $this->do_migrate();

        $this->do_moduleProcessing();
        $this->do_installAdmin();
        $this->do_cacheClear();
        $this->do_autoload();
        $this->do_optimize();

        $this->done();
    }

    protected function getOptions()
    {
        return [];
    }

    /**
     * INSTALLER METHODS.
     */
    protected function do_dbCheck()
    {
        try {
            \DB::connection()->getDatabaseName();
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }

    protected function do_cacheClear()
    {
        $this->comment('Clearing out the System Cache...');
        $this->{$this->cmd}('cache:clear');
    }

    protected function do_keyGenerate()
    {
        $this->comment('Generating Secure Key...');
        $this->{$this->cmd}('key:generate');
    }

    protected function do_migrate()
    {
        $this->comment('Setting up the database...');
        try {
            \DB::connection()->getDatabaseName();
        } catch (\PDOException $e) {
            $this->error('Database Details seem to be invalid, cannot run migrations...');

            return false;
        }

        try {
            $this->{$this->cmd}('migrate', ['--force' => null]);
        } catch (Exception $e) {
            $this->error('There appears to be an invalid Migration, cannot run migrations...');
        }
    }

    protected function do_migrateCheck()
    {
        // if we have migrations in the db already
        if (!Schema::hasTable('migrations')) {
            return false;
        }

        // just run a reset
        $this->comment('Migrating all the new things!...');
        $this->{$this->cmd}('migrate:reset');
    }

    protected function do_themePublish()
    {
        $this->comment('Publishing Theme Assets...');
        $this->{$this->cmd}('theme:publish', ['--force' => null]);
    }

    protected function do_modulePublish()
    {
        $this->comment('Publishing Module Assets...');
        $this->{$this->cmd}('module:publish');
    }

    protected function do_modulePublishTranslations()
    {
        $this->comment('Publishing Module Translations...');
        $this->{$this->cmd}('module:publish-translation');
    }

    protected function do_modulePublishPermissions()
    {
        $this->comment('Publishing Module Permissions...');
        try {
            \DB::connection()->getDatabaseName();
        } catch (\PDOException $e) {
            $this->error('Database Details seem to be invalid, cannot continue...');

            return false;
        }

        try {
            $this->{$this->cmd}('module:publish-permissions');
        } catch (\PDOException $e) {
            $this->error('Module Permissions seem to be invalid, cannot continue...');
        }
    }

    protected function do_modulePublishConfig()
    {
        $this->comment('Publishing Module Configs...');
        $this->{$this->cmd}('module:publish-config', ['--force' => null]);
    }

    protected function do_clearMigrationPath()
    {
        $this->comment('Clearing out migration directory...');
        File::cleanDirectory(config('modules.paths.migration'));
    }

    protected function do_modulePublishMigrations()
    {
        $this->comment('Publishing Module Migrations...');
        $this->{$this->cmd}('module:publish-migration');
    }

    protected function do_moduleProcessing()
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

    protected function do_modulesSeeding($module)
    {
        $this->comment('Seeding Module...');
        $this->{$this->cmd}('module:seed', ['module' => ucwords($module->getName())]);
    }

    protected function do_clearCompiled()
    {
        $this->comment('Clearing compiled classes...');
        $this->{$this->cmd}('clear-compiled');
    }

    protected function do_optimize()
    {
        $this->comment('Generating optimized class loader...');
        $this->{$this->cmd}('optimize');
    }

    protected function do_autoload()
    {
        $this->comment('Generating autoload files...');
        $this->{$this->cmd}('dump-autoload');
    }

    protected function do_modulesDependencyInstallers($module)
    {
        // $this->comment($module->getPath().'/Console/InstallCommand.php');
        // if (File::exists($module->getPath().'/Console/InstallCommand.php')) {
        //     $this->comment('Running the dependency installer for the '.$module->getName().' module...');
        //     $this->{$this->cmd}('module:install', ['module' => $module->getName()]);
        // }
    }

    protected function do_installAdmin()
    {
        $this->info('Building Admin User...');

        $data = [
            'name' => null,
            'username' => null,
            'email' => null,
            'password' => null,
        ];

        $data['name'] = $this->ask('What is your full name?');
        $data['username'] = $this->ask('What is your user name?');
        $data['email'] = $this->ask('What is your email?');
        $data['password'] = $this->secret('Please enter your password');

        if ($this->secret('Please enter your password for confirmation') !== $data['password']) {
            $this->error('Could not verify password, exiting here');

            return;
        }

        // add some data tot the array
        $data['verified_at'] = Carbon::now();
        $data['role'] = 1;

        // grab the auth model
        $userModel = config('cms.auth.config.user_model');

        // spawn a new copy and fill with the data details
        $user = with(new $userModel());
        $user->fill(array_except($data, 'role'));
        $save = $user->save();

        // if we cant save throw out the errors
        if ($save === false) {
            print_r($user->getErrors());
            die();
        }

        // attach the admin role to this user
        $user->roles()->attach(
            array_get($data, 'role'),
            ['caller_type' => $user->getCallerType()]
        );
    }

    protected function done()
    {
        $this->info('Done!');
    }
}
