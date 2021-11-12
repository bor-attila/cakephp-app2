<?php

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;

$cached_config = null;
$cached_config_key = sprintf('__%s_configuration__', env('APP_NAME', 'myapp'));
if (filter_var(env('APP_CACHE_CONFIG', false), FILTER_VALIDATE_BOOLEAN)) {
    $cached_config = apc_fetch($cached_config_key);
}

if (!$cached_config) {
    /*
     * See https://github.com/josegonzalez/php-dotenv for API details.
     *
     * Uncomment block of code below if you want to use `.env` file during development.
     * You should copy `config/.env.example` to `config/.env` and set/modify the
     * variables as required.
     *
     * The purpose of the .env file is to emulate the presence of the environment
     * variables like they would be present in production.
     *
     * If you use .env files, be careful to not commit them to source control to avoid
     * security risks. See https://github.com/josegonzalez/php-dotenv#general-security-information
     * for more information for recommended practices.
    */
    if (file_exists(CONFIG . '.env')) {
        $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
        $dotenv->parse()
            ->putenv(true)
            ->toEnv()
            ->toServer();
    }

    /*
     * Read configuration file and inject configuration into various
     * CakePHP classes.
     *
     * By default there is only one configuration file. It is often a good
     * idea to create multiple configuration files, and separate the configuration
     * that changes from configuration that does not. This makes deployment simpler.
     */
    try {
        Configure::config('default', new PhpConfig());
        Configure::load('app', 'default', false);
    } catch (\Exception $e) {
        exit($e->getMessage() . "\n");
    }

    /*
     * Load an environment local configuration file to provide overrides to your configuration.
     * Notice: For security reasons app_local.php **should not** be included in your git repo.
     */
    if (file_exists(CONFIG . 'app_local.php')) {
        Configure::load('app_local', 'default');
    }

    if (file_exists(CONFIG . 'app_secret.php')) {
        Configure::load('app_secret', 'default');
    }

    $additional_config_file = env('APP_ADDITIONAL_CONFIG', false);
    if ($additional_config_file && file_exists($additional_config_file)) {
        $filename = time() . 'additional_config';
        copy($additional_config_file, CONFIG . $filename . '.php');
        Configure::load($filename, 'default');
        unlink(CONFIG . $filename . '.php');
    }

    $cached_config = Configure::read();
}

if (filter_var(env('APP_CACHE_CONFIG', false), FILTER_VALIDATE_BOOLEAN)) {
    apc_store($cached_config_key, $cached_config, (int)env('APP_CACHE_TIME', 600));
}
