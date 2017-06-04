<?php

namespace Miniflux\Helper;

use PicoDb\Database;
use PicoFeed\Logging\Logger;

function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

function generate_token()
{
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes(30));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(30));
    } elseif (ini_get('open_basedir') === '' && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        return hash('sha256', file_get_contents('/dev/urandom', false, null, 0, 30));
    }

    return hash('sha256', uniqid(mt_rand(), true));
}

function parse_app_version($refnames, $commithash)
{
    $version = 'master';

    if ($refnames !== '$Format:%d$') {
        $tag = preg_replace('/\s*\(.*tag:\sv([^,]+).*\)/i', '\1', $refnames);

        if ($tag !== null && $tag !== $refnames) {
            return $tag;
        }
    }

    if ($commithash !== '$Format:%H$') {
        $version .= '.'.$commithash;
    }

    return $version;
}

function get_current_base_url()
{
    if (BASE_URL) {
        $url = BASE_URL;
        return rtrim($url, '/') . '/';
    } else {
        $url = is_secure_connection() ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];

        if (strpos($_SERVER['HTTP_HOST'], ':') === false) {
            $url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':'.$_SERVER['SERVER_PORT'];
        }

        $url .= str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])) !== '/' ? str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])).'/' : '/';

        return $url;
    }
}

function is_secure_connection()
{
    return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
}

function write_debug_file() {
    if (DEBUG_MODE) {
        $feed_logs = Logger::getMessages();
        $db_logs = Database::getInstance('db')->getLogMessages();
        file_put_contents(DEBUG_FILENAME, implode(PHP_EOL, $feed_logs + $db_logs), FILE_APPEND|LOCK_EX);
    }
}
