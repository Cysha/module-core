<?php

return [
    'site-name' => 'PhoenixCMS',
    'debugfile' => storage_path().'/app/debugfile',

    'themes'    => [
        'frontend' => 'default',
        'backend'  => 'default_admin',
    ],

    'default-editor' => 'core::editors.pagedown-bootstrap',
    'editors' => [
        'core::editors.pagedown-bootstrap',
    ],
];
