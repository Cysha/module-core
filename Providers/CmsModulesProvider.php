<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Cms\Modules\Core\Services\BladeExtender;

class CmsModulesProvider extends ServiceProvider
{
    public function register()
    {
        // register debug bar if we have it installed & debug is on
        if (config('app.debug') === true && class_exists('Barryvdh\Debugbar\ServiceProvider')) {
            $this->app->register('Barryvdh\Debugbar\ServiceProvider');
            AliasLoader::getInstance()->alias('Debugbar', 'Barryvdh\Debugbar\Facade');
        }

        $this->app->register('Pingpong\Modules\ModulesServiceProvider');

        BladeExtender::attach($this->app);
    }

}
