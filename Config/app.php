<?php

return [
    // base site
    'timezone' => 'UTC',

    // debug
    'debugfile' => storage_path('app/debugfile'),
    'debug' => 'false',

    // middleware
    'force-secure' => 'false',
    'minify-html' => 'false',

    'api' => [
        'admin-only-keys' => 'true',
        'cors' => 'false',
        'origin' => '*',
    ],

    // themes
    'themes' => [
        'frontend' => 'default',
        'backend' => 'default_admin',
    ],

    // site paths
    'paths' => [
        'api' => 'api/',
        'frontend' => '/',
        'backend' => 'admin/',
    ],

    // site middleware
    'middleware' => [
        'api' => ['api'],
        'frontend' => ['web'],
        'backend' => ['web', 'auth.admin'],
    ],

    'csrf-except' => [],

    // editors
    'default-editor' => 'core::editors.pagedown-bootstrap',
    'editors' => [
        'core::editors.pagedown-bootstrap',
    ],
];
