<?php

// HTTP_TIMEOUT => default value is 20 seconds (Maximum time to fetch a feed)
define('HTTP_TIMEOUT', '20');

// HTTP_MAX_RESPONSE_SIZE => Maximum accepted size of the response body in MB (default 10MB)
define('HTTP_MAX_RESPONSE_SIZE', 10485760);

// BASE_URL => URL that clients should access this instance of miniflux from
define('BASE_URL', '');

// DATA_DIRECTORY => default is data (writable directory)
define('DATA_DIRECTORY', __DIR__.'/data');

// FAVICON_DIRECTORY => default is favicons (writable directory)
define('FAVICON_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'favicons');

// FAVICON_URL_PATH => default is data/favicons/
define('FAVICON_URL_PATH', 'data/favicons');

// Database driver: "sqlite", "postgres", or "mysql" default is sqlite
define('DB_DRIVER', 'sqlite');

// Database connection parameters when Postgres or MySQL is used
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'miniflux');
define('DB_USERNAME', 'postgres');
define('DB_PASSWORD', '');

// DB_FILENAME => database file when Sqlite is used
define('DB_FILENAME', DATA_DIRECTORY.'/db.sqlite');

// Enable/disable debug mode
define('DEBUG_MODE', false);

// DEBUG_FILENAME => default is data/debug.log
define('DEBUG_FILENAME', DATA_DIRECTORY.'/debug.log');

// Theme folder on the filesystem => default is themes
define('THEME_DIRECTORY', 'themes');

// Theme URL path => default is themes
define('THEME_URL_PATH', 'themes');

// SESSION_SAVE_PATH => default is empty (used to store session files in a custom directory)
define('SESSION_SAVE_PATH', '');

// PROXY_HOSTNAME => default is empty (make HTTP requests through a HTTP proxy if set)
define('PROXY_HOSTNAME', '');

// PROXY_PORT => default is 3128 (default port of Squid)
define('PROXY_PORT', 3128);

// PROXY_USERNAME => default is empty (set the proxy username is needed)
define('PROXY_USERNAME', '');

// PROXY_PASSWORD => default is empty
define('PROXY_PASSWORD', '');

// SUBSCRIPTION_CONCURRENT_REQUESTS => number of concurrent feeds to refresh at once
// Reduce this number on systems with limited processing power
define('SUBSCRIPTION_CONCURRENT_REQUESTS', 5);

// Disable automatically a feed after X parsing failure
define('SUBSCRIPTION_DISABLE_THRESHOLD_ERROR', 10);

// Allow the cronjob to be accessible from the browser
define('ENABLE_CRONJOB_HTTP_ACCESS', true);

// Enable/disable HTTP header X-Frame-Options
define('ENABLE_XFRAME', true);

// Enable/disable HSTS HTTP header
define('ENABLE_HSTS', true);

// Reading preferences

// Remove automatically read items. Values:
// 0 - Never
// -1 - Immediately
// 1 - After 1 day
// 5 - After 5 days
// 15 - After 15 days
// 30 - After 30 days
define('READING_REMOVE_READ_ITEMS', 15);

// Remove automatically unread items. Values:
// 0 - Never
// 15 - After 15 days
// 45 - After 45 days
// 60 - After 60 days
define('READING_REMOVE_UNREAD_ITEMS', 45);

// Items per page. Values: 10, 20, 30, 50, 100, 150, 200, 250
define('READING_ITEMS_PER_PAGE', 100);

// Default sorting order for items. Values:
// 'asc' - Older items first
// 'desc' - Most recent first
define('READING_SORTING_DIRECTION', 'desc');

// Display items on lists. Values:
// 'titles' - Titles
// 'summaries' - Summaries
// 'full' - Full contents
define('READING_DISPLAY_MODE', 'summaries');

// Item title link. Values:
// 'original' - Original
// 'full' - Full contents
define('READING_ITEM_TITLE_LINK', 'full');

// When there is nothing to read, redirect me to this page. Values:
// 'feeds' - Subscriptions
// 'history' - History
// 'bookmarks' - Bookmarks
// 'nowhere' - Do not redirect me
define('READING_NOTHING_READ_REDIRECT', 'feeds');

// Refresh interval in minutes for unread counter.
define('READING_FRONTEND_UPDATECHECK_INTERVAL', 10);

// Original link marks article as read. Values: 0, 1
define('READING_ORIGINAL_MARKS_READ', 1);

// Do not fetch the content of articles. Values: 0, 1
define('READING_NOCONTENT', 0);

// Download favicons.Values: 0, 1
define('READING_FAVICONS', 1);
