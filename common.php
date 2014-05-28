<?php

require __DIR__.'/lib/Translator.php';
require __DIR__.'/vendor/PicoDb/Database.php';
require __DIR__.'/vendor/PicoFeed/PicoFeed.php';
require __DIR__.'/vendor/Readability/Readability.php';

require __DIR__.'/vendor/SimpleValidator/Validator.php';
require __DIR__.'/vendor/SimpleValidator/Base.php';
require __DIR__.'/vendor/SimpleValidator/Validators/Required.php';
require __DIR__.'/vendor/SimpleValidator/Validators/Unique.php';
require __DIR__.'/vendor/SimpleValidator/Validators/MaxLength.php';
require __DIR__.'/vendor/SimpleValidator/Validators/MinLength.php';
require __DIR__.'/vendor/SimpleValidator/Validators/Integer.php';
require __DIR__.'/vendor/SimpleValidator/Validators/Equals.php';
require __DIR__.'/vendor/SimpleValidator/Validators/AlphaNumeric.php';

require __DIR__.'/models/config.php';
require __DIR__.'/models/user.php';
require __DIR__.'/models/feed.php';
require __DIR__.'/models/item.php';
require __DIR__.'/models/schema.php';
require __DIR__.'/models/auto_update.php';
require __DIR__.'/models/database.php';
require __DIR__.'/models/remember_me.php';

if (file_exists('config.php')) {
    require 'config.php';
}

defined('APP_VERSION') or define('APP_VERSION', 'master');
defined('HTTP_TIMEOUT') or define('HTTP_TIMEOUT', 20);

defined('BASE_URL_DIRECTORY') or define('BASE_URL_DIRECTORY', dirname($_SERVER['PHP_SELF']));
defined('ROOT_DIRECTORY') or define('ROOT_DIRECTORY', __DIR__);
defined('DATA_DIRECTORY') or define('DATA_DIRECTORY', 'data');

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
        'filename' => \Model\Database\get_path(),
    ));

    if ($db->schema()->check(Model\Config\DB_VERSION)) {
        return $db;
    }
    else {
        die('Unable to migrate database schema.');
    }
});
