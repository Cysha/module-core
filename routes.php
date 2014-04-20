<?php

$namespace = 'Cysha\Modules\Core\Controllers';

require_once 'routes-admin.php';
require_once 'routes-api.php';
require_once 'routes-module.php';

Route::get('test', function () {
    $test = [];
    $test[] = Route::getRoutes()->hasNamedRoute('pxcms.forum.category.view');
    $test[] = URL::Route('pxcms.forum.category.view', 'news');

    dd($test);
});
