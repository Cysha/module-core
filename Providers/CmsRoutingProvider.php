<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

abstract class CmsRoutingProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * @return string
     */
    abstract protected function getFrontendRoute();

    /**
     * @return string
     */
    abstract protected function getBackendRoute();

    /**
     * @return string
     */
    abstract protected function getApiRoute();

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->loadApiRoutes($router);
        $this->loadBackendRoutes($router);
        $this->loadFrontendRoutes($router);
    }

    /**
     * @param Router $router
     */
    private function loadFrontendRoutes(Router $router)
    {
        $routes = $this->getFrontendRoute();

        if ($routes && file_exists($routes)) {
            $router->group([
                'namespace'  => $this->namespace.'\Frontend',
                'prefix'     => config('cms.core.app.paths.frontend', '/'),
            ], function (Router $router) use ($routes) {
                require $routes;
            });
        }
    }

    /**
     * @param Router $router
     */
    private function loadBackendRoutes(Router $router)
    {
        $routes = $this->getBackendRoute();

        if ($routes && file_exists($routes)) {
            $router->group([
                'namespace'  => $this->namespace.'\Backend',
                'prefix'     => config('cms.core.app.paths.backend', 'admin/'),
                'middleware' => ['auth.admin']
            ], function (Router $router) use ($routes) {
                require $routes;
            });
        }
    }

    /**
     * @param Router $router
     */
    private function loadApiRoutes(Router $router)
    {
        $routes = $this->getApiRoute();

        if ($routes && file_exists($routes)) {
            $router->group([
                'namespace' => $this->namespace.'\Api',
                'prefix'    => config('cms.core.app.paths.api', 'api/'),
                'version'   => 'v1',
            ], function (Router $router) use ($routes) {
                require $routes;
            });
        }
    }
}
