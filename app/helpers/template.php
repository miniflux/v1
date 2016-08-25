<?php

namespace Miniflux\Helper;

use Miniflux\Model\Config;

function flash($type, $html)
{
    $data = '';

    if (isset($_SESSION[$type])) {
        $data = sprintf($html, escape($_SESSION[$type]));
        unset($_SESSION[$type]);
    }

    return $data;
}

function is_rtl(array $item)
{
    return ! empty($item['rtl']) || \PicoFeed\Parser\Parser::isLanguageRTL($item['language']);
}

function css()
{
    $theme = Config\get('theme');

    if ($theme !== 'original') {
        $css_file = THEME_DIRECTORY.'/'.$theme.'/css/app.css';

        if (file_exists($css_file)) {
            return $css_file.'?version='.filemtime($css_file);
        }
    }

    return 'assets/css/app.css?version='.filemtime('assets/css/app.css');
}

function format_bytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'k', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
}

function get_host_from_url($url)
{
    return escape(parse_url($url, PHP_URL_HOST)) ?: $url;
}

function summary($value, $min_length = 5, $max_length = 120, $end = '[...]')
{
    $length = strlen($value);

    if ($length > $max_length) {
        $max = strpos($value, ' ', $max_length);
        if ($max === false) {
            $max = $max_length;
        }
        return substr($value, 0, $max).' '.$end;
    } elseif ($length < $min_length) {
        return '';
    }

    return $value;
}

function relative_time($timestamp, $fallback_date_format = '%e %B %Y %k:%M')
{
    $diff = time() - $timestamp;

    if ($diff < 0) {
        return \dt($fallback_date_format, $timestamp);
    }

    if ($diff < 60) {
        return \t('%d second ago', $diff);
    }

    $diff = floor($diff / 60);
    if ($diff < 60) {
        return \t('%d minute ago', $diff);
    }

    $diff = floor($diff / 60);
    if ($diff < 24) {
        return \t('%d hour ago', $diff);
    }

    $diff = floor($diff / 24);
    if ($diff < 7) {
        return \t('%d day ago', $diff);
    }

    $diff = floor($diff / 7);
    if ($diff < 4) {
        return \t('%d week ago', $diff);
    }

    $diff = floor($diff / 4);
    if ($diff < 12) {
        return \t('%d month ago', $diff);
    }

    return \dt($fallback_date_format, $timestamp);
}
