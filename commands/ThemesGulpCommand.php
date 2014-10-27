<?php namespace Cysha\Modules\Core\Commands;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Themes console commands
 */
class ThemesGulpCommand extends BaseCommand
{
    /**
     * Name of the command
     * @var string
     */
    protected $name = 'themes:gulp';

    /**
     * The Readable Command Name.
     *
     * @var string
     */
    protected $readableName = 'Theme gulp runner';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Run gulp from a Theme.';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        // grab some arguments
        $themeName = $this->argument('theme');

        // grab a list of themes in the system
        $themes = ['None, Exit'];
        foreach ($this->app['files']->directories('public/themes/') as $dir) {
            $themes[] = str_replace(['/', '\\', 'publicthemes'], '', $dir);
        }

        // if we didnt specify any themes, ask the user which one we want
        if ($themeName === '0') {
            $themeName = $this->choice('Please select a theme:', $themes, '0');
        }
        if ($themeName === '0') {
            $this->error('No theme selected, exiting...');
            return;
        }

        // make sure the test dir & file actually exists
        if (!$this->app['files']->exists('public/themes/'.$themeName.'/gulpfile.js')) {
            $this->error('Theme <info>\'' . $themeName . '\'</info> has no gulpfile to run.');
            return;
        }

        // Run baby, run
        $command = sprintf('cd public/themes/%1$s/ && gulp theme', $themeName);
        echo ' $ '.$command . PHP_EOL;
        system($command);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('theme', InputArgument::OPTIONAL, 'The name of theme.', '0'),
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
