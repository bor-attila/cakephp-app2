<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    'Datasources' => [
        'default' => [
            'host' => 'localhost',
            'port' => 3306,
            'username' => 'vagrant',
            'password' => 'vagrant',
            'database' => 'vagrant',
            'url' => env('DATABASE_URL', null),
        ],
        'test' => [
            'host' => 'localhost',
            'port' => 3306,
            'username' => 'vagrant',
            'password' => 'vagrant',
            'database' => 'test_vagrant',
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],

    'Session' => [
        'defaults' => 'cake',
        'cookie' => 's',
    ],
];
