<?php namespace Cysha\Modules\Core;

use Cysha\Modules\Core\Commands\InstallCommand;
use Illuminate\Foundation\AliasLoader;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerInstallCommand();
        $this->commands('cms:install');
        $this->registerOtherPackages();
    }

    private function registerInstallCommand()
    {
        $this->app['cms:install'] = $this->app->share(function () {
            return new InstallCommand;
        });
    }

    private function registerOtherPackages()
    {
        $serviceProviders = [
            'Barryvdh\Debugbar\ServiceProvider',            # https://github.com/barryvdh/laravel-debugbar

            'Stolz\Assets\ManagerServiceProvider',          # https://github.com/Stolz/Assets
            'Dingo\Api\ApiServiceProvider',
            'Teepluss\Theme\ThemeServiceProvider',          # https://github.com/teepluss/laravel4-theme
            'Former\FormerServiceProvider',                 # https://github.com/Anahkiasen/former
            'Toddish\Verify\VerifyServiceProvider',         # https://github.com/Toddish/Verify-L4

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
