<?php

namespace Cms\Modules\Core\Composers;

use Route;

class CurrentRoute
{
    /**
     * Adds $currentRoute var to the views, this describes the current controller/method being used.
     */
    public function compose($view)
    {
        $currentRoute = function () {
            if (Route::currentRouteAction() === null) {
                return;
            }

            $route = explode('@', Route::currentRouteAction());
            if (strstr($route[0], '\\')) {
                $route[0] = explode('\\', $route[0]);
                $route[0] = end($route[0]);
            }

            $return = [($route[0] ?: ''), ($route[1] ?: '')];

            return strtolower(implode(' ', $return));
        };
        $view->with('currentRoute', $currentRoute());
    }
}
