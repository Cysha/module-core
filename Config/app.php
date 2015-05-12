<?php

return [
    'site-name' => 'PhoenixCMS',

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
