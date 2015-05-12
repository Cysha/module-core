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

        if ($this->option('force', false)) {
            $options[] = '--force';
        }

        $this->call('vendor:publish', $options);
    }


    protected function getOptions()
    {
        return [
            ['force', InputArgument::OPTIONAL, 'Force the override the config ']
        ];
    }
}
