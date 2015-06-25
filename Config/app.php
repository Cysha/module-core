<?php

return [
    // base site
    'site-name' => 'PhoenixCMS',
    'timezone' => 'UTC',

    // debug
    'debugfile' => storage_path('app/debugfile'),
    'debug' => 'false',

    // middleware
    'force-secure' => 'false',
    'minify-html' => 'false',
    'cors' => 'false',

    // themes
    'themes' => [
        'frontend' => 'default',
        'backend' => 'default_admin',
    ],

    // site paths
    'paths' => [
        'api' => 'api/',
        'frontend' => '/',
        'backend'  => 'admin/',
    ],

    // editors
    'default-editor' => 'core::editors.pagedown-bootstrap',
    'editors' => [
        'core::editors.pagedown-bootstrap',
    ],
];
