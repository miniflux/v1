<?php

namespace Miniflux\Model\Favicon;

use Miniflux\Helper;
use Miniflux\Model;
use PicoDb\Database;
use PicoFeed\Reader\Favicon;

const TABLE      = 'favicons';
const JOIN_TABLE = 'favicons_feeds';

function create_feed_favicon($feed_id, $site_url, $icon_link)
{
    $favicon = fetch_favicon($feed_id, $site_url, $icon_link);
    if ($favicon === false) {
        return false;
    }

    $favicon_id = store_favicon($favicon->getType(), $favicon->getContent());
    if ($favicon_id === false) {
        return false;
    }

    return Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->save(array(
            'feed_id'    => $feed_id,
            'favicon_id' => $favicon_id
        ));
}

function fetch_favicon($feed_id, $site_url, $icon_link)
{
    if (Helper\bool_config('favicons') && ! has_favicon($feed_id)) {
        $favicon = new Favicon();
        $favicon->find($site_url, $icon_link);
        return $favicon;
    }

    return false;
}

function store_favicon($mime_type, $blob)
{
    if (empty($blob)) {
        return false;
    }

    $hash = sha1($blob);
    $favicon_id = get_favicon_id($hash);

    if ($favicon_id) {
        return $favicon_id;
    }

    $file = $hash.Helper\favicon_extension($mime_type);
    if (file_put_contents(FAVICON_DIRECTORY.DIRECTORY_SEPARATOR.$file, $blob) === false) {
        return false;
    }

    return Database::getInstance('db')
        ->table(TABLE)
        ->persist(array(
            'hash' => $hash,
            'type' => $mime_type
        ));
}

function get_favicon_data_url($filename, $mime_type)
{
    $blob = base64_encode(file_get_contents(FAVICON_DIRECTORY.DIRECTORY_SEPARATOR.$filename));
    return sprintf('data:%s;base64,%s', $mime_type, $blob);
}

function get_favicon_id($hash)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('hash', $hash)
        ->findOneColumn('id');
}

function delete_favicon(array $favicon)
{
    unlink(FAVICON_DIRECTORY.DIRECTORY_SEPARATOR.$favicon['hash'].Helper\favicon_extension($favicon['type']));

    Database::getInstance('db')
        ->table(TABLE)
        ->eq('hash', $favicon['hash'])
        ->remove();
}

function has_favicon($feed_id)
{
    return Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->eq('feed_id', $feed_id)
        ->exists();
}

function get_favicons_by_feed_ids(array $feed_ids)
{
    $result = array();

    if (! Helper\bool_config('favicons')) {
        return $result;
    }

    $favicons = Database::getInstance('db')
        ->table(TABLE)
        ->columns(
            'favicons.type',
            'favicons.hash',
            'favicons_feeds.feed_id'
        )
        ->join('favicons_feeds', 'favicon_id', 'id')
        ->in('favicons_feeds.feed_id', $feed_ids)
        ->findAll();

    foreach ($favicons as $favicon) {
        $result[$favicon['feed_id']] = $favicon;
    }

    return $result;
}

function get_items_favicons(array $items)
{
    $feed_ids = array();

    foreach ($items as $item) {
        $feed_ids[] = $item['feed_id'];
    }

    return get_favicons_by_feed_ids(array_unique($feed_ids));
}

function get_feeds_favicons(array $feeds)
{
    $feed_ids = array();

    foreach ($feeds as $feed) {
        $feed_ids[] = $feed['id'];
    }

    return get_favicons_by_feed_ids($feed_ids);
}

function get_favicons_with_data_url($user_id)
{
    $favicons = Database::getInstance('db')
        ->table(TABLE)
        ->columns(JOIN_TABLE.'.feed_id', TABLE.'.file', TABLE.'.type')
        ->join(JOIN_TABLE, 'favicon_id', 'id', TABLE)
        ->join(Model\Feed\TABLE, 'id', 'feed_id')
        ->eq(Model\Feed\TABLE.'.user_id', $user_id)
        ->findAll();

    foreach ($favicons as &$favicon) {
        $favicon['url'] = get_favicon_data_url($favicon['file'], $favicon['mime_type']);
    }

    return $favicons;
}
