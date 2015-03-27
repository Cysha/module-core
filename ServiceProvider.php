<?php namespace Cysha\Modules\Core;

use Illuminate\Foundation\AliasLoader;

use Schema;
use Cache;
use App;
use Config;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerViewComposers();
        $this->registerModuleCommands();
        $this->registerOtherPackages();
        $this->registerConfig();
    }

    public function registerViewComposers()
    {
        $this->app->make('view')->composer('theme.*::layouts.*', '\Cysha\Modules\Core\Composers\CurrentRoute');
        $this->app->make('view')->composer('core::editors.pagedown-bootstrap', '\Cysha\Modules\Core\Composers\Editors_Pagedown');
    }

    private function registerModuleCommands()
    {
        $commands = [
            'cms.modules.core:install' => __NAMESPACE__.'\Commands\InstallCommand',
            'cms:install'              => __NAMESPACE__.'\Commands\CmsInstallCommand',
            'modules:install'          => __NAMESPACE__.'\Commands\ModulesInstallCommand',
            'modules:codecept'         => __NAMESPACE__.'\Commands\ModulesCodeceptCommand',
            'themes:gulp'              => __NAMESPACE__.'\Commands\ThemesGulpCommand',
        ];

        foreach ($commands as $command => $class) {
            $this->app[$command] = $this->app->share(function () use ($class) {
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
            'Mews\Purifier\PurifierServiceProvider',        # https://github.com/mewebstudio/Purifier
        ];

        foreach ($serviceProviders as $sp) {
            $this->app->register($sp);
        }

        $aliases = [
            'Menu'            => 'Menu\Menu',
            'Former'          => 'Former\Facades\Former',
            'Theme'           => 'Teepluss\Theme\Facades\Theme',
            'Purifier'        => 'Mews\Purifier\Facades\Purifier',
        ];

        foreach ($aliases as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

    public function registerConfig()
    {
        if (!Schema::hasTable('config')) {
            return;
        }

        $table = Cache::rememberForever('core.config_table', function () {
            return \Cysha\Modules\Core\Models\DBConfig::orderBy('environment', 'asc')->get();
        });

        if ($table->count() == 0) {
            return;
        }

        foreach (['*', App::Environment()] as $env) {
            foreach ($table as $item) {
                // check if we have the right environment
                if ($item->environment != $env) {
                    continue;
                }

                // and then override it
                Config::set($item->key, $item->value);
            }
        }
    }

}
