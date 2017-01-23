<?php

namespace Miniflux\Database;

use Miniflux\Schema;
use RuntimeException;
use PicoDb;

function get_connection()
{
    $db = new PicoDb\Database(get_connection_parameters());

    if (DEBUG_MODE) {
        $db->getStatementHandler()
            ->withLogging()
            ->withStopWatch();
    }

    if ($db->schema('\Miniflux\Schema')->check(Schema\VERSION)) {
        return $db;
    } else {
        $errors = $db->getLogMessages();
        $nb_errors = count($errors);
        $last_error = isset($errors[$nb_errors - 1]) ? $errors[$nb_errors - 1] : 'Enable debug mode to have more information';
        throw new RuntimeException('Unable to migrate the database schema: '.$last_error);
    }
}

function get_connection_parameters()
{
    if (DB_DRIVER === 'postgres') {
        require_once __DIR__.'/../schemas/postgres.php';
        $params = array(
            'driver'   => 'postgres',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'port'     => DB_PORT,
        );
    } elseif (DB_DRIVER === 'mysql') {
        require_once __DIR__.'/../schemas/mysql.php';
        $params = array(
            'driver'   => 'mysql',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'port'     => DB_PORT,
            'charset'  => 'utf8mb4',
        );
    } else {
        require_once __DIR__.'/../schemas/sqlite.php';
        $params = array(
            'driver'   => 'sqlite',
            'filename' => DB_FILENAME,
        );
    }

    return $params;
}
