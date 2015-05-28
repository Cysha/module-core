<?php namespace Cms\Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ModulePublishConfigCommand extends Command
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
            $options['--force'] = null;
        }

        $this->call('vendor:publish', $options);
    }


    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Do we want to force the publish?']
        ];
    }
}
