<?php namespace Cysha\Modules\Core;

use Illuminate\Foundation\AliasLoader;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerModuleCommands();
        $this->registerOtherPackages();
    }

    private function registerModuleCommands()
    {
        $commands = [
            'cms:install'     => __NAMESPACE__.'\Commands\CmsInstallCommand',
            'modules:install' => __NAMESPACE__.'\Commands\ModulesInstallCommand',
            'modules:test'    => __NAMESPACE__.'\Commands\ModulesTestCommand',
            'themes:gulp'     => __NAMESPACE__.'\Commands\ThemesGulpCommand',
        ];

        foreach ($commands as $command => $class) {
            $this->app[$command] = $this->app->share(function () use($class) {
                return new $class($this->app);
            });
            $this->commands($command);
        }
    }

    private function registerOtherPackages()
    {
        $serviceProviders = [
            'Stolz\Assets\ManagerServiceProvider',          # https://github.com/Stolz/Assets
            'Teepluss\Theme\ThemeServiceProvider',          # https://github.com/teepluss/laravel4-theme
            'Menu\MenuServiceProvider',                     # https://github.com/vespakoen/menu
            'Former\FormerServiceProvider',                 # https://github.com/Anahkiasen/former
            'Liebig\Cron\CronServiceProvider',
        ];

        foreach ($serviceProviders as $sp) {
            $this->app->register($sp);
        }

        $aliases = [
            'Menu'            => 'Menu\Menu',
            'Former'          => 'Former\Facades\Former',
            'Theme'           => 'Teepluss\Theme\Facades\Theme',
        ];

        foreach ($aliases as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

}
