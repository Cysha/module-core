<?php

namespace Cms\Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class DumpAutoloadCommand extends Command
{
    protected $name = 'dump-autoload';
    protected $readableName = 'Re Adds dump-autoload';
    protected $description = 'Re Adds dump-autoload';

    /**
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new queue job table command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->boot();
    }

    public function boot()
    {
        $this->composer = app('Illuminate\Support\Composer');
    }

    public function fire()
    {
        $this->composer->dumpAutoloads();
    }
}
