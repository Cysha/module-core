<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Cms\Modules\Core\Services\BladeExtender;

class CmsModulesProvider extends ServiceProvider
{
    public function register()
    {
        if (app()->environment() !== 'production' && class_exists('Barryvdh\Debugbar\ServiceProvider')) {
            $this->app->register('Barryvdh\Debugbar\ServiceProvider');

            if (config('app.debug') === true) {
                AliasLoader::getInstance()->alias('Debugbar', 'Barryvdh\Debugbar\Facade');
            }
        }

        $this->app->register('Pingpong\Modules\ModulesServiceProvider');

        BladeExtender::attach($this->app);
    }

}
