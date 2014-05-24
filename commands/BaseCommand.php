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

    public function install(Array $packages)
   {
        foreach ($packages as $pkg => $settings) {
            if (array_get($settings, 'migrate', false) == true && !empty($pkg)) {
                $this->comment(sprintf('Migrating %s Package...', array_get($settings, 'name', '')));
                $this->call('migrate', array('--package' => $pkg));
            }

            if (array_get($settings, 'seed', false) == true && array_get($settings, 'seedclass', false) !== false) {
                $this->comment(sprintf('Seeding %s Package...', array_get($settings, 'name', '')));
                $this->call('db:seed', array('--class' => array_get($settings, 'seedclass')));
            }

            if (array_get($settings, 'config', false) == true && !empty($pkg)) {
                $this->comment(sprintf('Publishing %s Config...', array_get($settings, 'name', '')));
                $this->call('config:publish', array('package' => $pkg));
            }
        }
    }
}
