<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * The default settings point to Vagrant machine.
     */
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
];
