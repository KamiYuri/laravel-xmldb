<?php

return [
    'default' => 'local',

    'connections' => [
        'local' => [
            'driver' => 'xmldb',
            'path' => storage_path('xmldb'),
            'extension' => '.xml',
            'encoding' => 'UTF-8',
            'pretty_print' => true,
            'backup' => true,
            'backup_path' => storage_path('xmldb/backups'),
            'cache' => [
                'enabled' => true,
                'ttl' => 3600,
            ],
        ],
    ],

    'schema' => [
        'auto_create' => true,
        'validation' => true,
        'namespace' => 'http://laravel-xmldb.com/schema',
    ],
];
