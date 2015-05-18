<?php

$router->get('test', 'PagesController@test');
$router->get('/', ['as' => 'pxcms.pages.home', 'uses' => 'PagesController@getHomepage']);
