<?php

return [
    'default' => env('XMLDB_DEFAULT', 'default'),

    'connections' => [
        'default' => [
            'driver' => 'xml',
            'path' => storage_path('xmldb/default'),
            'backup_path' => storage_path('xmldb/backup'),
            'cache_path' => storage_path('xmldb/cache'),
            'hierarchy_path' => storage_path('xmldb/hierarchy'),
        ],
    ],

    'cache' => [
        'enabled' => true,
        'ttl' => 60, // Cache time-to-live in minutes
    ],

    'backup' => [
        'enabled' => true,
        'frequency' => 'daily', // Options: daily, weekly, monthly
    ],
];
