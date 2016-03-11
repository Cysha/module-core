<?php

namespace Cms\Modules\Core\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;

class ThemeGulpCommand extends BaseCommand
{
    protected $name = 'theme:gulp';
    protected $readableName = 'Theme gulp runner';
    protected $description = 'Run gulp from a Theme.';

    /**
     * Execute the console command.
     */
    public function fire()
    {
        // grab some arguments
        $themeName = $this->argument('theme');
        $task = $this->argument('task');

        // grab a list of themes in the system
        $themes = ['None, Exit'];
        $themeDir = public_path(config('theme.themeDir'));
        foreach ($this->app['files']->directories($themeDir) as $dir) {
            $themes[] = class_basename($dir);
        }

        // if we didnt specify any themes, ask the user which one we want
        if ($themeName === '0') {
            $themeName = $this->choice('Please select a theme:', $themes, '0');
        }
        if (!in_array($themeName, $themes)) {
            $this->error('No theme selected, exiting...');

            return;
        }

        // make sure the test dir & file actually exists
        $gulpFile = $themeDir.'/'.$themeName.'/gulpfile.js';
        if (!$this->app['files']->exists($gulpFile)) {
            $this->error('Theme <info>\''.$themeName.'\'</info> has no gulpfile to run.');

            return;
        }

        if (empty($task)) {
            $task = 'theme';
        }

        // Run baby, run
        $command = sprintf('cd %3$s/%1$s/ && gulp %2$s', $themeName, $task, $themeDir);
        echo ' $ '.$command.PHP_EOL;
        system($command);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('theme', InputArgument::OPTIONAL, 'The name of theme.', '0'),
            array('task', InputArgument::OPTIONAL, 'The task to run from the gulp file.', 'watch'),
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
