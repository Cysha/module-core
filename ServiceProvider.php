<?php namespace Cysha\Modules\Core;

use Cysha\Modules\BaseServiceProvider;
use Cysha\Modules\Core\Commands\InstallCommand;

class ServiceProvider extends BaseServiceProvider {

    public function register() {
        $this->registerInstallCommand();
        $this->commands('cms:install');
    }

    private function registerInstallCommand(){
        $this->app['cms:install'] = $this->app->share(function($app){
            return new InstallCommand;
        });
    }

}