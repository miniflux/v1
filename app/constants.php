<?php

defined('APP_VERSION') or define('APP_VERSION', Miniflux\Helper\parse_app_version('$Format:%d$','$Format:%H$'));

define('HTTP_USER_AGENT', 'Miniflux (https://miniflux.net)');
defined('HTTP_TIMEOUT') or define('HTTP_TIMEOUT', 20);
defined('HTTP_MAX_RESPONSE_SIZE') or define('HTTP_MAX_RESPONSE_SIZE', 10485760);

defined('BASE_URL_DIRECTORY') or define('BASE_URL_DIRECTORY', dirname($_SERVER['PHP_SELF']));
defined('ROOT_DIRECTORY') or define('ROOT_DIRECTORY', implode(DIRECTORY_SEPARATOR, array(__DIR__, '..')));
defined('DATA_DIRECTORY') or define('DATA_DIRECTORY', ROOT_DIRECTORY.DIRECTORY_SEPARATOR.'data');

defined('FAVICON_DIRECTORY') or define('FAVICON_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'favicons');
defined('FAVICON_URL_PATH') or define('FAVICON_URL_PATH', 'data/favicons');

defined('DB_DRIVER') or define('DB_DRIVER', 'sqlite');
defined('DB_FILENAME') or define('DB_FILENAME', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'db.sqlite');
defined('DB_HOSTNAME') or define('DB_HOSTNAME', 'localhost');
defined('DB_PORT') or define('DB_PORT', null);
defined('DB_NAME') or define('DB_NAME', 'miniflux');
defined('DB_USERNAME') or define('DB_USERNAME', '');
defined('DB_PASSWORD') or define('DB_PASSWORD', '');

defined('DEBUG_MODE') or define('DEBUG_MODE', false);
defined('DEBUG_FILENAME') or define('DEBUG_FILENAME', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'debug.log');

defined('BASE_URL') or define('BASE_URL', '');
defined('THEME_DIRECTORY') or define('THEME_DIRECTORY', 'themes');
defined('THEME_URL_PATH') or define('THEME_URL_PATH', 'themes');
defined('SESSION_SAVE_PATH') or define('SESSION_SAVE_PATH', '');

defined('PROXY_HOSTNAME') or define('PROXY_HOSTNAME', '');
defined('PROXY_PORT') or define('PROXY_PORT', 3128);
defined('PROXY_USERNAME') or define('PROXY_USERNAME', '');
defined('PROXY_PASSWORD') or define('PROXY_PASSWORD', '');

defined('SUBSCRIPTION_CONCURRENT_REQUESTS') or define('SUBSCRIPTION_CONCURRENT_REQUESTS', 5);
defined('SUBSCRIPTION_DISABLE_THRESHOLD_ERROR') or define('SUBSCRIPTION_DISABLE_THRESHOLD_ERROR', 10);

defined('RULES_DIRECTORY') or define('RULES_DIRECTORY', ROOT_DIRECTORY.DIRECTORY_SEPARATOR.'rules');

defined('ENABLE_XFRAME') or define('ENABLE_XFRAME', true);
defined('ENABLE_HSTS') or define('ENABLE_HSTS', true);
defined('ENABLE_CRONJOB_HTTP_ACCESS') or define('ENABLE_CRONJOB_HTTP_ACCESS', true);

defined('BEANSTALKD_HOST') or define('BEANSTALKD_HOST', '127.0.0.1');
defined('BEANSTALKD_QUEUE') or define('BEANSTALKD_QUEUE', 'feeds');
defined('BEANSTALKD_TTL') or define('BEANSTALKD_TTL', 120);

defined('READING_REMOVE_READ_ITEMS') or define('READING_REMOVE_READ_ITEMS', 15);
defined('READING_REMOVE_UNREAD_ITEMS') or define('READING_REMOVE_UNREAD_ITEMS', 45);
defined('READING_ITEMS_PER_PAGE') or define('READING_ITEMS_PER_PAGE', 100);
defined('READING_SORTING_DIRECTION') or define('READING_SORTING_DIRECTION', 'desc');
defined('READING_DISPLAY_MODE') or define('READING_DISPLAY_MODE', 'summaries');
defined('READING_ITEM_TITLE_LINK') or define('READING_ITEM_TITLE_LINK', 'full');
defined('READING_NOTHING_READ_REDIRECT') or define('READING_NOTHING_READ_REDIRECT', 'feeds');
defined('READING_FRONTEND_UPDATECHECK_INTERVAL') or define('READING_FRONTEND_UPDATECHECK_INTERVAL', 10);
defined('READING_ORIGINAL_MARKS_READ') or define('READING_ORIGINAL_MARKS_READ', 1);
defined('READING_NOCONTENT') or define('READING_NOCONTENT', 0);
defined('READING_FAVICONS') or define('READING_FAVICONS', 1);
