<?php namespace Cysha\Modules\Core\Composers;

use Route;
use Str;

class CurrentRoute
{
    /**
     * Adds $currentRoute var to the views, this describes the current controller/method being used
     */
    public function compose($view)
    {
        $currentRoute = function () {
            if (Route::currentRouteAction() === null) {
                return null;
            }

            $route = explode('@', Route::currentRouteAction());
            if (strstr($route[0], '\\')) {
                $route[0] = explode('\\', $route[0]);
                $route[0] = end($route[0]);
            }

            $return = [($route[0] ?: ''), ($route[1] ?: '')];
            return Str::lower(implode(' ', $return));
        };
        $view->with('currentRoute', $currentRoute());
    }
}
