<?php namespace Cms\Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;

class DumpAutoloadCommand extends Command
{
    protected $name = 'dump-autoload';
    protected $readableName = 'Re Adds dump-autoload';
    protected $description = 'Re Adds dump-autoload';

    /**
     * @var \Illuminate\Foundation\Composer
     */
    protected $composer;

    /**
     * Create a new queue job table command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->boot();
    }

    public function boot()
    {
        $this->composer = app('Illuminate\Foundation\Composer');
    }

    public function fire()
    {
        $this->composer->dumpAutoloads();
    }
}
