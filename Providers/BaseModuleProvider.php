<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class BaseModuleProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the defined middleware.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The commands to register.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Register repository bindings to the IoC
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMiddleware($this->app['router']);
        $this->registerModuleCommands();
        $this->registerModuleBindings();
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

    /**
     * Register the middleware.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        if (!count($this->middleware)) {
            return;
        }

        foreach ($this->middleware as $module => $middlewares) {
            if (!count($middlewares)) {
                continue;
            }
            foreach ($middlewares as $name => $middleware) {
                $class = sprintf('Cms\Modules\%s\Http\Middleware\%s', $module, $middleware);
                $router->middleware($name, $class);
            }
        }
    }

    /**
     * Register the commands.
     */
    private function registerModuleCommands()
    {
        if (!count($this->commands)) {
            return;
        }

        foreach ($this->commands as $module => $commands) {
            if (!count($commands)) {
                continue;
            }

            foreach ($commands as $command => $class) {
                $this->app[$command] = $this->app->share(function () use ($module, $class) {
                    $class = sprintf('Cms\Modules\%s\Console\%s', $module, $class);
                    return new $class($this->app);
                });
                $this->commands($command);
            }
        }
    }

    /**
     * Register the bindings for this module.
     *
     * @return void
     */
    public function registerModuleBindings()
    {
        if (!count($this->bindings)) {
            return;
        }

        foreach ($this->bindings as $namespace => $classes) {
            if (!count($classes)) {
                continue;
            }

            $this->app->bind(
                implode('\\', [$namespace, head($classes)]),
                implode('\\', [$namespace, last($classes)])
            );
        }
    }
}
