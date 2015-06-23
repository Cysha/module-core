<?php

return [

    'backend_sidebar' => [
        '_root' => [
            'order' => 1,
            'children' => [
                [
                    'route' => 'pxcms.admin.index',
                    'text' => 'Dashboard',
                    'icon' => 'fa-dashboard',
                    'order' => 1,
                ],
            ],
        ],
        'User Management' => [
            'order' => 2,
            'children' => [],
        ],
        'System' => [
            'order' => 3,
            'children' => [],
        ],
    ],

];
