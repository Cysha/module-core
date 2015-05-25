<?php

$router->get('test', 'PagesController@test');
$router->get('/', ['as' => 'pxcms.pages.home', 'uses' => config('cms.core.app.pxcms-index', 'PagesController@getHomepage')]);
