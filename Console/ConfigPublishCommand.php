<?php namespace Cms\Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ConfigPublishCommand extends Command
{
    protected $name = 'module:publish-config';
    protected $readableName = 'Publish a modules configuration';
    protected $description = 'Publish a modules configuration';

    public function fire()
    {

        $options = [
            '--provider' => 'Cms\Modules\Core\Providers\CoreModuleServiceProvider'
        ];

        if ($this->argument('force')) {
            $options[] = '--force';
        }

        $this->call('vendor:publish', $options);
    }


    protected function getArgument()
    {
        return [
            ['force', InputArgument::OPTIONAL, 'Name of the theme you wish to publish']
        ];
    }
}
