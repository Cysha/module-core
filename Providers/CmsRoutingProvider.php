<?php

namespace Cms\Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Dingo\Api\Routing\Router as ApiRouter;
use Illuminate\Routing\Router;

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
     * @param \Illuminate\Routing\Router $router
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
     * @param \Illuminate\Routing\Router $router
     */
    public function map(Router $router)
    {
        $this->loadFrontendRoutes($router);
        $this->loadBackendRoutes($router);
        $this->loadApiRoutes($this->app['api.router']);
    }

    /**
     * @param Router $router
     */
    private function loadFrontendRoutes(Router $router)
    {
        $routes = $this->getFrontendRoute();

        if ($routes && file_exists($routes)) {
            $router->group([
                'namespace' => $this->namespace.'\Frontend',
                'prefix' => config('cms.core.app.paths.frontend', '/'),
                'middleware' => config('cms.core.app.middleware.frontend', []),
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
                'namespace' => $this->namespace.'\Backend',
                'prefix' => config('cms.core.app.paths.backend', 'admin/'),
                'middleware' => config('cms.core.app.middleware.backend', []),
            ], function (Router $router) use ($routes) {
                require $routes;
            });
        }
    }

    /**
     * @param Router $router
     */
    private function loadApiRoutes(ApiRouter $router)
    {
        $routes = $this->getApiRoute();

        if ($routes && file_exists($routes)) {
            $router->group([
                'version' => 'v1',
                'namespace' => $this->namespace.'\Api',
                'prefix' => config('cms.core.app.paths.api', 'api/'),
                'middleware' => config('cms.core.app.middleware.api', []),
            ], function (ApiRouter $router) use ($routes) {
                require $routes;
            });
        }
    }
}
