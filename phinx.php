<?php

return [
    'paths' => [
        'migrations' => __DIR__ . '/src/Migration',
        'seeds'      => __DIR__ . '/src/Test/Seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'production',
        'production' => [
            'adapter' => 'sqlite',
            'name'    => str_replace('.sqlite3', '', $_ENV['UAB_DB_PATH'] ?? __DIR__ . '/var/db/database'),
            'charset' => 'utf-8'
        ]
    ],
    'version_order' => 'creation'
];
