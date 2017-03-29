<?php

namespace Miniflux\Model\Config;

use Miniflux\Helper;
use Miniflux\Model;
use DirectoryIterator;
use Miniflux\Session\SessionStorage;
use PicoDb\Database;

const TABLE = 'user_settings';

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

function get_timezones()
{
    $timezones = timezone_identifiers_list();
    return array_combine(array_values($timezones), $timezones);
}

function is_language_rtl()
{
    $languages = array(
        'ar_AR'
    );

    return in_array(Helper\config('language'), $languages);
}

function get_languages()
{
    return array(
        'ar_AR'       => 'عربي',
        'cs_CZ'       => 'Čeština',
        'de_DE'       => 'Deutsch',
        'en_US'       => 'English',
        'es_ES'       => 'Español',
        'fr_FR'       => 'Français',
        'hu_HU'       => 'Magyar',
        'it_IT'       => 'Italiano',
        'ja_JP'       => '日本語',
        'pt_BR'       => 'Português',
        'zh_CN'       => '简体中国',
        'sr_RS'       => 'српски',
        'sr_RS@latin' => 'srpski',
        'ru_RU'       => 'Русский',
        'tr_TR'       => 'Türkçe',
    );
}

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

function get_sorting_directions()
{
    return array(
        'asc'  => t('Older items first'),
        'desc' => t('Most recent first'),
    );
}

function get_display_mode()
{
    return array(
        'titles'    => t('Titles'),
        'summaries' => t('Summaries'),
        'full'      => t('Full contents'),
    );
}

function get_item_title_link()
{
    return array(
        'original' => t('Original'),
        'full'     => t('Full contents'),
    );
}

function get_autoflush_read_options()
{
    return array(
        '0'  => t('Never'),
        '-1' => t('Immediately'),
        '1'  => t('After %d day', 1),
        '5'  => t('After %d day', 5),
        '15' => t('After %d day', 15),
        '30' => t('After %d day', 30),
    );
}

function get_autoflush_unread_options()
{
    return array(
        '0'  => t('Never'),
        '15' => t('After %d day', 15),
        '30' => t('After %d day', 30),
        '45' => t('After %d day', 45),
        '60' => t('After %d day', 60),
    );
}

function get_paging_options()
{
    return array(
        10  => 10,
        20  => 20,
        30  => 30,
        50  => 50,
        100 => 100,
        150 => 150,
        200 => 200,
        250 => 250,
    );
}

function get_nothing_to_read_redirections()
{
    return array(
        'feeds'     => t('Subscriptions'),
        'history'   => t('History'),
        'bookmarks' => t('Bookmarks'),
        'nowhere'   => t('Do not redirect me'),
    );
}

function get_default_values()
{
    return array(
        'language'                      => 'en_US',
        'timezone'                      => 'UTC',
        'theme'                         => 'original',
        'autoflush'                     => READING_REMOVE_READ_ITEMS,
        'autoflush_unread'              => READING_REMOVE_UNREAD_ITEMS,
        'frontend_updatecheck_interval' => READING_FRONTEND_UPDATECHECK_INTERVAL,
        'favicons'                      => READING_FAVICONS,
        'nocontent'                     => READING_NOCONTENT,
        'image_proxy'                   => 0,
        'original_marks_read'           => READING_ORIGINAL_MARKS_READ,
        'instapaper_enabled'            => 0,
        'pinboard_enabled'              => 0,
        'pinboard_tags'                 => 'miniflux',
        'items_per_page'                => READING_ITEMS_PER_PAGE,
        'items_display_mode'            => READING_DISPLAY_MODE,
        'items_sorting_direction'       => READING_SORTING_DIRECTION,
        'redirect_nothing_to_read'      => READING_NOTHING_READ_REDIRECT,
        'item_title_link'               => READING_ITEM_TITLE_LINK,
    );
}

function get_all($user_id)
{
    $settings = Database::getInstance('db')
        ->hashtable(TABLE)
        ->eq('user_id', $user_id)
        ->getAll('key', 'value');

    if (empty($settings)) {
        save_defaults($user_id);
        $settings = Database::getInstance('db')
            ->hashtable(TABLE)
            ->eq('user_id', $user_id)
            ->getAll('key', 'value');
    }

    return $settings;
}

function save_defaults($user_id)
{
    return save($user_id, get_default_values());
}

function save($user_id, array $values)
{
    $db = Database::getInstance('db');
    $results = array();
    $db->startTransaction();

    if (isset($values['nocontent']) && (bool) $values['nocontent']) {
        $db
            ->table(Model\Item\TABLE)
            ->eq('user_id', $user_id)
            ->update(array('content' => ''));
    }

    foreach ($values as $key => $value) {
        if ($db->table(TABLE)->eq('user_id', $user_id)->eq('key', $key)->exists()) {
            $results[] = $db->table(TABLE)
                ->eq('user_id', $user_id)
                ->eq('key', $key)
                ->update(array('value' => $value));
        } else {
            $results[] = $db->table(TABLE)->insert(array(
                'key'     => $key,
                'value'   => $value,
                'user_id' => $user_id,
            ));
        }
    }

    if (in_array(false, $results, true)) {
        $db->cancelTransaction();
        return false;
    }

    $db->closeTransaction();
    SessionStorage::getInstance()->flushConfig();
    return true;
}
