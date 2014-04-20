<?php

// if the request matches the route group, or we are in console(easier to debug), add the routes
// if (Request::is(\Config::get('core::routes.paths.api', 'api').'/*') || App::runningInConsole()) {

//     Route::api(['version' => 'v1', 'prefix' => \Config::get('core::routes.paths.api', 'api')], function () use ($namespace) {
//         $namespace .= '\Api\V1';

//     });

// }
