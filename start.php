<?php

$module = last(explode(DIRECTORY_SEPARATOR, __DIR__));
$namespace = sprintf('\Cms\Modules\%s\Http\Controllers', $module);

require __DIR__ . '/Http/routes-frontend.php';
require __DIR__ . '/Http/routes-backend.php';
require __DIR__ . '/Http/routes-api.php';
// require __DIR__ . '/composers.php';
