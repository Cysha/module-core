<?php

return [

    'backend_sidebar' => [
        '_root' => [
            'order' => 1,
            'children' => [
                [
                    'text' => 'System',
                    'type' => 'header',
                    'order' => 0,
                ],
                [
                    'route' => 'pxcms.admin.index',
                    'text' => 'Dashboard',
                    'icon' => 'fa-dashboard',
                    'order' => 1,
                ],
            ],
        ],
        'Site Management' => [
            'order' => 2,
            'children' => [],
        ],
        'User Management' => [
            'order' => 3,
            'children' => [],
        ],
        [
            'text' => 'Modules',
            'type' => 'header',
            'order' => 100,
        ],
    ],

];
