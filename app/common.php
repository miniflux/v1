<?php

require __DIR__.'/../vendor/autoload.php';

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
