<?php

return array(
    'site-name' => 'PhoenixCMS',
    'debugfile' => storage_path().'/meta/debugfile',

    'themes'    => array(
        'frontend' => 'default',
        'backend'  => 'default_admin',
    ),

    'pxcms-index' => 'Cysha\Modules\Core\Controllers\Module\ExtrasController@getHomepage',
    'default-editor' => 'core::editors.pagedown-bootstrap',
    'editors' => [
        'core::editors.pagedown-bootstrap',
    ],
);
