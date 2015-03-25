<?php

return array(
    'site-name' => 'PhoenixCMS',
    'debugfile' => storage_path().'/meta/debugfile',

    'themes'    => array(
        'frontend' => 'default',
        'backend'  => 'default_admin',
    ),

    'default-editor' => 'core::editors.pagedown-bootstrap',
    'editors' => [
        'core::editors.pagedown-bootstrap',
    ],
);
