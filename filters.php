<?php

// Route::when(Config::get('core::routes.paths.api').'/*', 'auth.basic');

/*
|--------------------------------------------------------------------------
| View Events
|--------------------------------------------------------------------------
*/
    View::composer('theme.*::layouts.*', function ($view) {
        $currentRoute = function () {
            if ( Route::currentRouteAction() === null ) {
                return '';
            }

            $route = explode('@', Route::currentRouteAction());
            if ( strstr($route[0], '\\') ) {
                $route[0] = explode('\\', $route[0]);
                $route[0] = end($route[0]);
            }

            return strtolower(($route[0] ?: '').' '.($route[1] ?: ''));
        };
        $view->with('currentRoute', $currentRoute());
    });
