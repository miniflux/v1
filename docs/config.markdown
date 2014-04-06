Configuration parameters
========================

How do I override application variables?
----------------------------------------

There are few settings that can't be changed by the user interface.
These parameters are defined with PHP constants.

To override them, rename the file `config.default.php` to `config.php`.

Actually, the following constants can be overrided:

    <?php

    // HTTP_TIMEOUT => default value is 20 seconds (Maximum time to fetch a feed)
    define('HTTP_TIMEOUT', '20');

    // DATA_DIRECTORY => default is data (writable directory)
    define('DATA_DIRECTORY', 'data');

    // DB_FILENAME => default value is db.sqlite (default database filename)
    define('DB_FILENAME', 'db.sqlite');

    // ENABLE_MULTIPLE_DB => default value is true (multiple users support)
    define('ENABLE_MULTIPLE_DB', true);

    // DEBUG => default is true (enable logging of PicoFeed)
    define('DEBUG', true);

    // DEBUG_FILENAME => default is data/debug.log
    define('DEBUG_FILENAME', DATA_DIRECTORY.DIRECTORY_SEPARATOR.'debug.log');

    // THEME_DIRECTORY => default is themes
    define('THEME_DIRECTORY', 'themes');

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

    // ENABLE_AUTO_UPDATE => default is true (enable Miniflux update from the user interface)
    define('ENABLE_AUTO_UPDATE', true);


How to override/extends the content filtering blacklist/whitelist?
------------------------------------------------------------------

Miniflux use [PicoFeed](https://github.com/fguillot/picoFeed) to parse the content of each item.
These variables are public static arrays, extends the actual array or replace it.

**Be careful, you can break everything by doing that!!!**

Put your modifications in your custom `config.php` like described above.

By example to add a new iframe whitelist:

    \PicoFeed\Filter::$iframe_whitelist[] = 'http://www.kickstarter.com';

Or to replace the entire whitelist:

    \PicoFeed\Filter::$iframe_whitelist = array('http://www.kickstarter.com');

Available variables:

    // Allow only specified tags and attributes
    \PicoFeed\Filter::$whitelist_tags

    // Strip content of these tags
    \PicoFeed\Filter::$blacklist_tags

    // Allow only specified URI scheme
    \PicoFeed\Filter::$whitelist_scheme

    // List of attributes used for external resources: src and href
    \PicoFeed\Filter::$media_attributes

    // Blacklist of external resources
    \PicoFeed\Filter::$media_blacklist

    // Required attributes for tags, if the attribute is missing the tag is dropped
    \PicoFeed\Filter::$required_attributes

    // Add attribute to specified tags
    \PicoFeed\Filter::$add_attributes

    // Attributes that must be integer
    \PicoFeed\Filter::$integer_attributes

    // Iframe allowed source
    \PicoFeed\Filter::$iframe_whitelist

For more details, have a look to the file `vendor/PicoFeed/Filter.php`.
