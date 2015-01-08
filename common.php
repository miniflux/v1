<?php

require __DIR__.'/vendor/autoload.php';

if (file_exists(__DIR__.'/config.php')) {
    require __DIR__.'/config.php';
}

defined('APP_VERSION') or define('APP_VERSION', Helper\parseAppVersion('$Format:%d$','$Format:%H$'));
defined('HTTP_TIMEOUT') or define('HTTP_TIMEOUT', 20);

defined('BASE_URL_DIRECTORY') or define('BASE_URL_DIRECTORY', dirname($_SERVER['PHP_SELF']));
defined('ROOT_DIRECTORY') or define('ROOT_DIRECTORY', __DIR__);
defined('DATA_DIRECTORY') or define('DATA_DIRECTORY', ROOT_DIRECTORY.DIRECTORY_SEPARATOR.'data');

defined('ENABLE_MULTIPLE_DB') or define('ENABLE_MULTIPLE_DB', true);
defined('DB_FILENAME') or define('DB_FILENAME', 'db.sqlite');

defined('DEBUG') or define('DEBUG', true);
defined('DEBUG_FILENAME') or define('DEBUG_FILENAME', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'debug.log');

defined('THEME_DIRECTORY') or define('THEME_DIRECTORY', 'themes');
defined('SESSION_SAVE_PATH') or define('SESSION_SAVE_PATH', '');

defined('PROXY_HOSTNAME') or define('PROXY_HOSTNAME', '');
defined('PROXY_PORT') or define('PROXY_PORT', 3128);
defined('PROXY_USERNAME') or define('PROXY_USERNAME', '');
defined('PROXY_PASSWORD') or define('PROXY_PASSWORD', '');

defined('ENABLE_AUTO_UPDATE') or define('ENABLE_AUTO_UPDATE', true);
defined('AUTO_UPDATE_DOWNLOAD_DIRECTORY') or define('AUTO_UPDATE_DOWNLOAD_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'download');
defined('AUTO_UPDATE_ARCHIVE_DIRECTORY') or define('AUTO_UPDATE_ARCHIVE_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'archive');
defined('AUTO_UPDATE_BACKUP_DIRECTORY') or define('AUTO_UPDATE_BACKUP_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'backup');

require __DIR__.'/check_setup.php';

PicoDb\Database::bootstrap('db', function() {

    $db = new PicoDb\Database(array(
        'driver' => 'sqlite',
        'filename' => Model\Database\get_path(),
    ));

    if ($db->schema()->check(Schema\VERSION)) {
        return $db;
    }
    else {
        $errors = $db->getLogMessages();

        $pdo = new \PDO('sqlite::memory:');
        $result = $pdo->query('select sqlite_version()', PDO::FETCH_COLUMN, 0);
        $sqlite_version = $result ? $result->fetch() : '?';

        $html = 'Unable to migrate the database schema, <strong>please copy and paste this message and create a bug report:</strong><hr/>';
        $html .= '<pre><code>';
        $html .= (isset($errors[0]) ? $errors[0] : 'Unknown SQL error').PHP_EOL.PHP_EOL;
        $html .= '- PHP version: '.phpversion().PHP_EOL;
        $html .= '- SAPI: '.php_sapi_name().PHP_EOL;
        $html .= '- PDO Sqlite version: '.phpversion('pdo_sqlite').PHP_EOL;
        $html .= '- Sqlite version: '.$sqlite_version.PHP_EOL;
        $html .= '- OS: '.php_uname();
        $html .= '</code></pre>';

        die($html);
    }
});