<?php

use Illuminate\Routing\Router;

$router->group(['version' => 'v1', 'namespace' => 'V1'], function (Router $router) {
    $router->get('user', ['uses' => 'PagesController@getUser']);
});
