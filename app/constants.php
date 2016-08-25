<?php

defined('APP_VERSION') or define('APP_VERSION', Miniflux\Helper\parse_app_version('$Format:%d$','$Format:%H$'));

defined('HTTP_TIMEOUT') or define('HTTP_TIMEOUT', 20);
defined('HTTP_MAX_RESPONSE_SIZE') or define('HTTP_MAX_RESPONSE_SIZE', 2097152);

defined('BASE_URL_DIRECTORY') or define('BASE_URL_DIRECTORY', dirname($_SERVER['PHP_SELF']));
defined('ROOT_DIRECTORY') or define('ROOT_DIRECTORY', implode(DIRECTORY_SEPARATOR, array(__DIR__, '..')));
defined('DATA_DIRECTORY') or define('DATA_DIRECTORY', ROOT_DIRECTORY.DIRECTORY_SEPARATOR.'data');

defined('FAVICON_DIRECTORY') or define('FAVICON_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'favicons');
defined('FAVICON_URL_PATH') or define('FAVICON_URL_PATH', 'data/favicons');

defined('ENABLE_MULTIPLE_DB') or define('ENABLE_MULTIPLE_DB', true);
defined('DB_FILENAME') or define('DB_FILENAME', 'db.sqlite');

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

defined('SUBSCRIPTION_CONCURRENT_REQUESTS') or define('SUBSCRIPTION_CONCURRENT_REQUESTS', 5);

defined('RULES_DIRECTORY') or define('RULES_DIRECTORY', ROOT_DIRECTORY.DIRECTORY_SEPARATOR.'rules');

defined('ENABLE_HSTS') or define('ENABLE_HSTS', true);

defined('BEANSTALKD_HOST') or define('BEANSTALKD_HOST', '127.0.0.1');
defined('BEANSTALKD_QUEUE') or define('BEANSTALKD_QUEUE', 'feeds');
defined('BEANSTALKD_TTL') or define('BEANSTALKD_TTL', 120);
