<?php

// if the request matches the route group, or we are in console(easier to debug), add the routes
if (Request::is(\Config::get('core::routes.paths.api').'/*') || App::runningInConsole()) {

    Route::group(array('prefix' => \Config::get('core::routes.paths.api').'/v1'), function () {

    });

}
