<?php namespace Cms\Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DumpAutoloadCommand extends Command
{
    protected $name = 'dump-autoload';
    protected $readableName = 'Re Adds dump-autoload';
    protected $description = 'Re Adds dump-autoload';

    public function fire()
    {
        $command = 'composer dump-autoload';

        //echo ' $ '.$command . PHP_EOL;
        system($command);
    }
}
