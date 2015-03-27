<?php namespace Cysha\Modules\Core\Commands;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Modules console commands
 */
class ModulesCodeceptCommand extends BaseCommand
{
    /**
     * Name of the command
     * @var string
     */
    protected $name = 'modules:codecept';

    /**
     * The Readable Module Name.
     *
     * @var string
     */
    protected $readableName = 'Module Tester CMD for Codeception';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Run codeception tests for a module.';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        // grab some arguments
        $moduleName = $this->argument('module');
        $suite = $this->argument('suite');
        $file = $this->argument('file');
        $test = $this->argument('test');

        // grab a list of modules in the system
        $modules = ['None, Exit'];
        foreach ($this->app['files']->directories('app/modules/') as $dir) {
            $modules[] = str_replace(['/', '\\', 'appmodules'], '', $dir);
        }

        // if we didnt specify any modules, ask the user which one we want
        if ($moduleName === '0') {
            $moduleName = $this->choice('Please select a Module:', $modules, '0');
        }
        if ($moduleName === '0') {
            $this->error('No module selected, exiting...');
            return;
        }
        $module = app('modules')->module($moduleName);

        // run a extra check make sure we have the modules
        if (!$module) {
            $this->error('Module \'' . $moduleName . '\' does not exist.');
            return;
        }

        // run a check on the suite
        if ($suite === '0') {
            $suite = $this->choice('Please select a test suite:', ['None, Exit', 'functional', 'acceptance', 'unit'], '0');
        }
        if ($suite === '0') {
            $this->error('No test suite selected, exiting...');
            return;
        }

        // check if we have specified a file to test from
        if ($file === '0') {
            $file = null;
        } else {
            if ($test === '0') {
                $test = null;
            }
        }

        // make sure the test dir & file actually exists
        if (!$this->app['files']->exists('app/modules/'.$module->name().'/tests/'.$suite.'/'.$file)) {
            $this->error('Module <info>\'' . $module->name() . '\'</info> has no '.$suite.' tests to run.');
            return;
        }

        // append the test function if needed
        if ($test != '0') {
            $file .= ':'.$test;
        }

        // Run baby, run
        $command = sprintf('vendor/bin/codecept run %1$s ../../app/modules/%2$s/tests/%1$s/%3$s', $suite, $module->name(), $file);
        $this->line(' $ '.$command);

        // try the cmd the first time
        $output = null;
        passthru($command, $output);
var_dump($output);
        // if it fails, try and install codeception & try it again
        // if ($output == 1) {
        //     $this->comment('Codeception not installed, installing...');
        //     passthru('vendor/bin/codecept build');
        //     $this->comment('continuing.. ');
        //     $this->line('$ '.$command);
        //     passthru($command);
        // }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('module', InputArgument::OPTIONAL, 'The name of module.', '0'),
            array('suite', InputArgument::OPTIONAL, 'The suite of tests being ran.', '0'),
            array('file', InputArgument::OPTIONAL, 'Specific file to run tests from.', '0'),
            array('test', InputArgument::OPTIONAL, 'Specific test to run.', '0'),
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
