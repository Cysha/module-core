<?php

use Dingo\Api\Routing\Router as ApiRouter;

$router->version('v1', ['namespace' => 'v1'], function (ApiRouter $router) {
    $router->get('user', ['uses' => 'PagesController@getUser']);
});
