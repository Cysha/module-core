<?php namespace Cysha\Modules\Core\Commands;

use Illuminate\Foundation\Application;
use Illuminate\Console\Command;

class BaseCommand extends Command
{

    /**
     * IoC
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * DI
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct();

        $this->app = $app;
    }

    public function info($message)
    {
        $this->comment('');
        $this->comment('-------------------------------------');
        parent::info(' '.$this->name.' - '.$message);
        $this->comment('');
    }

    public function header()
    {
        $this->comment('');
        $this->comment('=====================================');
        $this->comment('');
        parent::info(' '.$this->name.' ');
        $this->comment('');
        $this->comment('-------------------------------------');
        $this->comment('');

    }

}
