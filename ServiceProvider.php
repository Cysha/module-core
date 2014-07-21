<?php namespace Cysha\Modules\Core;

use Cysha\Modules\Core\Commands\CmsInstallCommand;
use Cysha\Modules\Core\Commands\ModulesInstallCommand;
use Illuminate\Foundation\AliasLoader;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerInstallCommand();
        $this->registerModuleInstallCommand();

        $this->registerOtherPackages();
    }

    private function registerInstallCommand()
    {
        $this->app['cms:install'] = $this->app->share(function () {
            return new CmsInstallCommand($this->app);
        });
        $this->commands('cms:install');
    }

    private function registerModuleInstallCommand()
    {
        $this->app['modules:install'] = $this->app->share(function () {
            return new ModulesInstallCommand($this->app);
        });
        $this->commands('modules:install');
    }

    private function registerOtherPackages()
    {
        $serviceProviders = [
            'Stolz\Assets\ManagerServiceProvider',          # https://github.com/Stolz/Assets
            'Dingo\Api\ApiServiceProvider',
            'Teepluss\Theme\ThemeServiceProvider',          # https://github.com/teepluss/laravel4-theme
            'Menu\MenuServiceProvider',                     # https://github.com/vespakoen/menu
            'Former\FormerServiceProvider',                 # https://github.com/Anahkiasen/former
        ];

        foreach ($serviceProviders as $sp) {
            $this->app->register($sp);
        }

        $aliases = [
            'Controller'      => 'Dingo\Api\Routing\Controller',
            'API'             => 'Dingo\Api\Facades\API',

            'Menu'            => 'Menu\Menu',
            'Former'          => 'Former\Facades\Former',
            'Theme'           => 'Teepluss\Theme\Facades\Theme',
        ];

        foreach ($aliases as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

}
