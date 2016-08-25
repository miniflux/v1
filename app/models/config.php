<?php

namespace Miniflux\Model\Config;

use Miniflux\Helper;
use Miniflux\Translator;
use DirectoryIterator;
use PicoDb\Database;
use PicoFeed\Config\Config as ReaderConfig;
use PicoFeed\Logging\Logger;

const HTTP_USER_AGENT = 'Miniflux (https://miniflux.net)';

// Get PicoFeed config
function get_reader_config()
{
    $config = new ReaderConfig;
    $config->setTimezone(get('timezone'));

    // Client
    $config->setClientTimeout(HTTP_TIMEOUT);
    $config->setClientUserAgent(HTTP_USER_AGENT);
    $config->setMaxBodySize(HTTP_MAX_RESPONSE_SIZE);

    // Grabber
    $config->setGrabberRulesFolder(RULES_DIRECTORY);

    // Proxy
    $config->setProxyHostname(PROXY_HOSTNAME);
    $config->setProxyPort(PROXY_PORT);
    $config->setProxyUsername(PROXY_USERNAME);
    $config->setProxyPassword(PROXY_PASSWORD);

    // Filter
    $config->setFilterIframeWhitelist(get_iframe_whitelist());

    if ((bool) get('debug_mode')) {
        Logger::enable();
    }

    // Parser
    $config->setParserHashAlgo('crc32b');

    return $config;
}

function get_iframe_whitelist()
{
    return array(
        'http://www.youtube.com',
        'https://www.youtube.com',
        'http://player.vimeo.com',
        'https://player.vimeo.com',
        'http://www.dailymotion.com',
        'https://www.dailymotion.com',
    );
}

// Send a debug message to the console
function debug($line)
{
    Logger::setMessage($line);
    write_debug();
}

// Write PicoFeed debug output to a file
function write_debug()
{
    if ((bool) get('debug_mode')) {
        file_put_contents(DEBUG_FILENAME, implode(PHP_EOL, Logger::getMessages()));
    }
}

// Get available timezone
function get_timezones()
{
    $timezones = timezone_identifiers_list();
    return array_combine(array_values($timezones), $timezones);
}

// Returns true if the language is RTL
function is_language_rtl()
{
    $languages = array(
        'ar_AR'
    );

    return in_array(get('language'), $languages);
}

// Get all supported languages
function get_languages()
{
    return array(
        'ar_AR' => 'عربي',
        'cs_CZ' => 'Čeština',
        'de_DE' => 'Deutsch',
        'en_US' => 'English',
        'es_ES' => 'Español',
        'fr_FR' => 'Français',
        'it_IT' => 'Italiano',
        'ja_JP' => '日本語',
        'pt_BR' => 'Português',
        'zh_CN' => '简体中国',
        'sr_RS' => 'српски',
        'sr_RS@latin' => 'srpski',
        'ru_RU' => 'Русский',
        'tr_TR' => 'Türkçe',
    );
}

// Get all skins
function get_themes()
{
    $themes = array(
        'original' => t('Default')
    );

    if (file_exists(THEME_DIRECTORY)) {
        $dir = new DirectoryIterator(THEME_DIRECTORY);

        foreach ($dir as $fileinfo) {
            if (! $fileinfo->isDot() && $fileinfo->isDir()) {
                $themes[$dir->getFilename()] = ucfirst($dir->getFilename());
            }
        }
    }

    return $themes;
}

// Sorting direction choices for items
function get_sorting_directions()
{
    return array(
        'asc' => t('Older items first'),
        'desc' => t('Most recent first'),
    );
}

// Display summaries or full contents on lists
function get_display_mode()
{
    return array(
        'summaries' => t('Summaries'),
        'full' => t('Full contents')
    );
}

// Item title links to original or full contents
function get_item_title_link()
{
    return array(
        'original' => t('Original'),
        'full' => t('Full contents')
    );
}

// Autoflush choices for read items
function get_autoflush_read_options()
{
    return array(
        '0' => t('Never'),
        '-1' => t('Immediately'),
        '1' => t('After %d day', 1),
        '5' => t('After %d day', 5),
        '15' => t('After %d day', 15),
        '30' => t('After %d day', 30)
    );
}

// Autoflush choices for unread items
function get_autoflush_unread_options()
{
    return array(
        '0' => t('Never'),
        '15' => t('After %d day', 15),
        '30' => t('After %d day', 30),
        '45' => t('After %d day', 45),
        '60' => t('After %d day', 60),
    );
}

// Number of items per pages
function get_paging_options()
{
    return array(
        10 => 10,
        20 => 20,
        30 => 30,
        50 => 50,
        100 => 100,
        150 => 150,
        200 => 200,
        250 => 250,
    );
}

// Get redirect options when there is nothing to read
function get_nothing_to_read_redirections()
{
    return array(
        'feeds' => t('Subscriptions'),
        'history' => t('History'),
        'bookmarks' => t('Bookmarks'),
    );
}


// Regenerate tokens for the API and bookmark feed
function new_tokens()
{
    $values = array(
        'api_token' => Helper\generate_token(),
        'feed_token' => Helper\generate_token(),
        'bookmarklet_token' => Helper\generate_token(),
        'fever_token' => substr(Helper\generate_token(), 0, 8),
    );

    return Database::getInstance('db')->hashtable('settings')->put($values);
}

// Get a config value from the DB or from the session
function get($name)
{
    if (! isset($_SESSION)) {
        return current(Database::getInstance('db')->hashtable('settings')->get($name));
    } else {
        if (! isset($_SESSION['config'][$name])) {
            $_SESSION['config'] = get_all();
        }

        if (isset($_SESSION['config'][$name])) {
            return $_SESSION['config'][$name];
        }
    }

    return null;
}

// Get all config parameters
function get_all()
{
    $config = Database::getInstance('db')->hashtable('settings')->get();
    unset($config['password']);
    return $config;
}

// Save config into the database and update the session
function save(array $values)
{
    // Update the password if needed
    if (! empty($values['password'])) {
        $values['password'] = password_hash($values['password'], PASSWORD_BCRYPT);
    } else {
        unset($values['password']);
    }

    unset($values['confirmation']);

    // If the user does not want content of feeds, remove it in previous ones
    if (isset($values['nocontent']) && (bool) $values['nocontent']) {
        Database::getInstance('db')->table('items')->update(array('content' => ''));
    }

    if (Database::getInstance('db')->hashtable('settings')->put($values)) {
        reload();
        return true;
    }

    return false;
}

// Reload the cache in session
function reload()
{
    $_SESSION['config'] = get_all();
    Translator\load(get('language'));
}
