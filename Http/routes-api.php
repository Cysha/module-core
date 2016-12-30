<?php

use Dingo\Api\Routing\Router as ApiRouter;

$router->version('v1', ['middleware' => ['api.auth'], 'namespace' => 'V1'], function (ApiRouter $router) {

});
