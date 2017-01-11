Configuration Parameters
========================

How do I override application variables?
----------------------------------------

There are few settings that can't be changed by the user interface.
These parameters are defined with PHP constants.

To override them, rename the file `config.default.php` to `config.php`.

Database configuration
----------------------

By default, Miniflux uses Sqlite but Postgres and MySQL are also supported.

### Sqlite configuration

You could change the default location of the Sqlite file by changing the default values:

```php
define('DB_DRIVER', 'sqlite');
define('DB_FILENAME', DATA_DIRECTORY.'/db.sqlite');
```

### Postgres configuration

Miniflux will creates the schema automatically but not the database itself:

```sql
CREATE DATABASE miniflux;
```

The `config.php` have to be modified as well:

```php
define('DB_DRIVER', 'postgres');

// Replace these values:
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'miniflux');
define('DB_USERNAME', 'my postgres user');
define('DB_PASSWORD', 'my secret password');
```

### MySQL configuration

Miniflux will creates the schema automatically but not the database itself:

```sql
CREATE DATABASE miniflux;
```

The `config.php` have to be modified as well:

```php
define('DB_DRIVER', 'mysql');

// Replace these values:
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'miniflux');
define('DB_USERNAME', 'my mysql user');
define('DB_PASSWORD', 'my secret password');
```

List of parameters
------------------

Actually, the following constants can be overridden:

```php
// HTTP_TIMEOUT => default value is 20 seconds (Maximum time to fetch a feed)
define('HTTP_TIMEOUT', '20');

// HTTP_MAX_RESPONSE_SIZE => Maximum accepted size of the response body in MB (default 2MB)
define('HTTP_MAX_RESPONSE_SIZE', 2097152);

// DATA_DIRECTORY => default is data (writable directory)
define('DATA_DIRECTORY', 'data');

// FAVICON_DIRECTORY => default is favicons (writable directory)
define('FAVICON_DIRECTORY', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'favicons');

// FAVICON_URL_PATH => default is data/favicons/
define('FAVICON_URL_PATH', 'data/favicons');

// Database driver: "sqlite", "postgres", or "mysql" default is sqlite
define('DB_DRIVER', 'sqlite');

// Database connection parameters when Postgres or MySQL are used
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'miniflux');
define('DB_USERNAME', 'my db user');
define('DB_PASSWORD', 'my secret password');

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
```