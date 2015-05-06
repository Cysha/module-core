<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Pingpong\Modules\Module;

class CoreModuleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middleware = [
        'Core' => [
            'isInstalled'             => 'IsInstalledMiddleware',
        ],
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMiddleware($this->app['router']);
        $this->registerModuleCommands();
        $this->registerModuleResourceNamespaces();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }


    private function registerModuleCommands()
    {
        $namespace = 'Cms\Modules\Core\Console';
        $commands = [
            //'cms.modules.core:install' => $namespace.'\InstallCommand',
            //'cms:install'              => $namespace.'\CmsInstallCommand',
            //'modules:install'          => $namespace.'\ModulesInstallCommand',
            //'modules:codecept'         => $namespace.'\ModulesCodeceptCommand',
            // 'themes:publish'              => $namespace.'\ThemePublishCommand',
            'themes:gulp'              => $namespace.'\ThemeGulpCommand',
        ];

        foreach ($commands as $command => $class) {
            $this->app[$command] = $this->app->share(function () use ($class) {
                return new $class($this->app);
            });
            $this->commands($command);
        }
    }

    /**
     * Register the middleware.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {

        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = sprintf('Cms\Modules\%s\Http\Middleware\%s', $module, $middleware);
                $router->middleware($name, $class);
            }
        }
    }

    /**
     * Register the modules aliases
     */
    private function registerModuleResourceNamespaces()
    {
        foreach ($this->app['modules']->enabled() as $module) {
            $this->registerViewNamespace($module);
            $this->registerLanguageNamespace($module);
            $this->registerConfigNamespace($module);
        }
    }

    /**
     * Register the view namespaces for the modules
     * @param Module $module
     */
    protected function registerViewNamespace(Module $module)
    {
        $this->app['view']->addNamespace(
            $module->getName(),
            $module->getPath() . '/Resources/views'
        );
    }

    /**
     * Register the language namespaces for the modules
     * @param Module $module
     */
    protected function registerLanguageNamespace(Module $module)
    {
        $this->app['translator']->addNamespace(
            $module->getName(),
            $module->getPath() . '/Resources/lang'
        );
    }

    /**
     * Register the config namespace
     * @param Module $module
     */
    private function registerConfigNamespace(Module $module)
    {
        $files = $this->app['files']->files($module->getPath() . '/Config');

        $package = $module->getName();

        foreach ($files as $file) {
            $filename = $this->getConfigFilename($file, $package);

            $this->mergeConfigFrom(
                $file,
                $filename
            );

            $this->publishes([
                $file => config_path($filename . '.php'),
            ], 'config');
        }
    }

    /**
     * @param $file
     * @param $package
     * @return string
     */
    private function getConfigFilename($file, $package)
    {
        $name = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($file));

        return sprintf('modules.%s.%s', $package, $name);
    }

}
