<?php

return [
    'site-name'    => 'PhoenixCMS',
    'debugfile'    => storage_path('app/debugfile'),
    'debug'        => 'false',
    'force-secure' => 'false',
    'minify-html'  => 'false',

    'themes'    => [
        'frontend' => 'default',
        'backend'  => 'default_admin',
    ],

    'paths' => [
        'api'      => 'api/',
        'frontend' => '/',
        'backend'  => 'admin/',
    ],

    'default-editor' => 'core::editors.pagedown-bootstrap',
    'editors' => [
        'core::editors.pagedown-bootstrap',
    ],
];
