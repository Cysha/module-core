<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CmsModulesProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register('Pingpong\Modules\ModulesServiceProvider');
        $this->app->register('Pingpong\Modules\Providers\BootstrapServiceProvider');
    }

}
