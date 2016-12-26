<?php

require __DIR__.'/../vendor/autoload.php';

if (file_exists(__DIR__.'/../config.php')) {
    require __DIR__.'/../config.php';
}

require_once __DIR__.'/constants.php';
require_once __DIR__.'/check_setup.php';
require_once __DIR__.'/functions.php';

PicoDb\Database::setInstance('db', function() {
    $db = new PicoDb\Database(array(
        'driver' => 'sqlite',
        'filename' => DB_FILENAME,
    ));

    $db->getStatementHandler()->withLogging();

    if ($db->schema('\Miniflux\Schema')->check(Miniflux\Schema\VERSION)) {
        return $db;
    } else {
        $errors = $db->getLogMessages();
        $nb_errors = count($errors);

        $html = 'Unable to migrate the database schema, <strong>please copy and paste this message and create a bug report:</strong><hr/>';
        $html .= '<pre><code>';
        $html .= (isset($errors[$nb_errors - 1]) ? $errors[$nb_errors - 1] : 'Unknown SQL error').PHP_EOL.PHP_EOL;
        $html .= '- PHP version: '.phpversion().PHP_EOL;
        $html .= '- SAPI: '.php_sapi_name().PHP_EOL;
        $html .= '- PDO Sqlite version: '.phpversion('pdo_sqlite').PHP_EOL;
        $html .= '- Sqlite version: '.$db->getDriver()->getDatabaseVersion().PHP_EOL;
        $html .= '- OS: '.php_uname();
        $html .= '</code></pre>';

        die($html);
    }
});
