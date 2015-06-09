<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Cms\Modules\Core\Services\BladeExtender;

class CmsModulesProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register('Pingpong\Modules\ModulesServiceProvider');

        if (config('app.debug') === true) {
            AliasLoader::getInstance()->alias('Debugbar', 'Barryvdh\Debugbar\Facade');
        }

        BladeExtender::attach($this->app);
    }

}
