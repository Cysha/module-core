<?php namespace Cysha\Modules\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 */
class ModulesInstallCommand extends BaseCommand
{
    /**
     * Name of the command
     * @var string
     */
    protected $name = 'modules:install';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Run Install for modules.';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        // Get all modules or 1 specific
        if ($moduleName = $this->input->getArgument('module')) {
            $module = app('modules')->module($moduleName);
        } else {
            $this->error('You need to specify a module to install');
        }

        if ($module) {
            if ($this->app['files']->exists($module->path('commands'))) {
                // Run command
                $this->call('cms.modules.'.$module->name().':install');

            } else {
                $this->line('Module <info>\'' . $module->name() . '\'</info> has no install command.');
            }
        } else {
            $this->error('Module \'' . $module->name() . '\' does not exist.');
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('module', InputArgument::REQUIRED, 'The name of module being installed.'),
        );
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return array(
        );
    }

}
