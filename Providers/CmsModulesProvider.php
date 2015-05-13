<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CmsModulesProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register('Pingpong\Modules\ModulesServiceProvider');
        $this->app->register('Pingpong\Modules\Providers\BootstrapServiceProvider');

        if (config('app.debug') === true) {
            AliasLoader::getInstance()->alias('Debugbar', 'Barryvdh\Debugbar\Facade');
        }
    }

}
