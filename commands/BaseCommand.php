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

    public function getModuleName()
    {
        return $this->readableName;
    }

    public function info($message)
    {
        parent::comment('');
        parent::comment('-------------------------------------');
        parent::info(' '.$this->getModuleName().' - '.$message);
        parent::comment('');
    }

    public function comment($message)
    {
        parent::comment("\n".$message);
    }

    public function header()
    {
        parent::comment('');
        parent::comment('=====================================');
        parent::comment('');
        parent::info(' '.$this->getModuleName().' ');
        parent::comment('');
        parent::comment('-------------------------------------');
        parent::comment('');

    }

}
