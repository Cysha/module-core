<?php

$router->get('test', 'PagesController@test');

/*
 * This setting can be controlled from the admin panel.
 * In the interest of keeping this dynamic, don't directly overload it,
 * add a setting to your modules config file.
 * cms.MODULE.config.pxcms-index
 */
$router->get('/', [
    'as' => 'pxcms.pages.home',
    'uses' => config('cms.core.app.pxcms-index', 'PagesController@getHomepage'),
]);
