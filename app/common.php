<?php

require __DIR__.'/../vendor/autoload.php';

$dbUrlParser = new PicoDb\UrlParser();

if ($dbUrlParser->isEnvironmentVariableDefined()) {
    $dbSettings = $dbUrlParser->getSettings();

    define('DB_DRIVER', $dbSettings['driver']);
    define('DB_USERNAME', $dbSettings['username']);
    define('DB_PASSWORD', $dbSettings['password']);
    define('DB_HOSTNAME', $dbSettings['hostname']);
    define('DB_PORT', $dbSettings['port']);
    define('DB_NAME', $dbSettings['database']);
}

if (file_exists(__DIR__.'/../config.php')) {
    require __DIR__.'/../config.php';
}

require_once __DIR__.'/constants.php';
require_once __DIR__.'/check_setup.php';
require_once __DIR__.'/functions.php';

PicoDb\Database::setInstance('db', function () {
    try {
        return Miniflux\Database\get_connection();
    } catch (Exception $e) {
        die($e->getMessage());
    }
});
