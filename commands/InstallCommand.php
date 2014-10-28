<?php namespace Cysha\Modules\Core\Commands;

class InstallCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cms.modules.core:install';

    /**
     * The Readable Module Name.
     *
     * @var string
     */
    protected $readableName = 'Core Module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the Core Module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $packages = array(
            'liebig/cron' => array(
                'name'      => 'Cron',
                'migrate'   => true,
                'seed'      => false,
                'config'    => true,
            ),
        );

        $this->install($packages);
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
