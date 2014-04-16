<?php

// if the request matches the admin route group, or we are in console(easier to debug), add the routes
if (Request::is(\Config::get('core::routes.paths.admin').'/*') || App::runningInConsole()) {

}
