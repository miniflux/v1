<?php

namespace Miniflux\Model\Favicon;

use Miniflux\Helper;
use Miniflux\Model;
use PicoDb\Database;

const TABLE      = 'favicons';
const JOIN_TABLE = 'favicons_feeds';

function create_feed_favicon($feed_id, $mime_type, $blob)
{
    $favicon_id = store_favicon($mime_type, $blob);
    if ($favicon_id === false) {
        return false;
    }

    return Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->insert(array(
            'feed_id'    => $feed_id,
            'favicon_id' => $favicon_id
        ));
}

function store_favicon($mime_type, $blob)
{
    if (empty($blob)) {
        return false;
    }

    $hash = sha1($blob);
    $favicon_id = Database::getInstance('db')
        ->table(TABLE)
        ->eq('hash', $hash)
        ->findOneColumn('id');

    if ($favicon_id) {
        return $favicon_id;
    }

    if (file_put_contents(get_favicon_filename($hash, $mime_type), $blob) === false) {
        return false;
    }

    return Database::getInstance('db')
        ->table(TABLE)
        ->persist(array(
            'hash' => $hash,
            'type' => $mime_type
        ));
}

function purge_favicons()
{
    $favicons = Database::getInstance('db')
        ->table(TABLE)
        ->join(JOIN_TABLE, 'favicon_id', 'id')
        ->isNull('feed_id')
        ->findAll();

    foreach ($favicons as $favicon) {
        $filename = get_favicon_filename($favicon['hash'], $favicon['type']);
        Database::getInstance('db')
            ->table(TABLE)
            ->eq('id', $favicon['id'])
            ->remove();

        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}

function has_favicon($feed_id)
{
    $favicon = Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->eq('feed_id', $feed_id)
        ->join(TABLE, 'id', 'favicon_id')
        ->findOne();

    $has_favicon = ! empty($favicon);

    if ($has_favicon && ! file_exists(get_favicon_filename($favicon['hash'], $favicon['type']))) {
        Database::getInstance('db')
            ->table(TABLE)
            ->eq('id', $favicon['id'])
            ->remove();

        return false;
    }

    return $has_favicon;
}

function get_favicons_by_feed_ids(array $feed_ids)
{
    $result = array();
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
        ->columns('feed_id', 'hash', 'type')
        ->join(JOIN_TABLE, 'favicon_id', 'id')
        ->join(Model\Feed\TABLE, 'id', 'feed_id', JOIN_TABLE)
        ->eq(Model\Feed\TABLE.'.user_id', $user_id)
        ->asc(TABLE.'.id')
        ->findAll();

    foreach ($favicons as &$favicon) {
        $favicon['data_url'] = get_favicon_data_url($favicon['hash'], $favicon['type']);
    }

    return $favicons;
}

function get_favicon_filename($hash, $mime_type)
{
    return FAVICON_DIRECTORY.DIRECTORY_SEPARATOR.$hash.Helper\favicon_extension($mime_type);
}

function get_favicon_data_url($hash, $mime_type)
{
    $blob = base64_encode(file_get_contents(get_favicon_filename($hash, $mime_type)));
    return sprintf('data:%s;base64,%s', $mime_type, $blob);
}
