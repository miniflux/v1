<?php

namespace Miniflux\Model\Bookmark;

use Miniflux\Helper;
use Miniflux\Model;
use PicoDb\Database;

function count_bookmarked_items($user_id, array $feed_ids = array())
{
    return Database::getInstance('db')
        ->table(Model\Item\TABLE)
        ->eq('bookmark', 1)
        ->eq('user_id', $user_id)
        ->in('feed_id', $feed_ids)
        ->in('status', array(Model\Item\STATUS_READ, Model\Item\STATUS_UNREAD))
        ->count();
}

function get_bookmarked_items($user_id, $offset = null, $limit = null, array $feed_ids = array())
{
    return Database::getInstance('db')
        ->table(Model\Item\TABLE)
        ->columns(
            'items.id',
            'items.checksum',
            'items.title',
            'items.updated',
            'items.url',
            'items.enclosure_url',
            'items.enclosure_type',
            'items.bookmark',
            'items.status',
            'items.content',
            'items.feed_id',
            'items.language',
            'items.rtl',
            'items.author',
            'feeds.site_url',
            'feeds.title AS feed_title'
        )
        ->join(Model\Feed\TABLE, 'id', 'feed_id')
        ->eq('items.user_id', $user_id)
        ->in('items.feed_id', $feed_ids)
        ->neq('items.status', Model\Item\STATUS_REMOVED)
        ->eq('items.bookmark', 1)
        ->orderBy('items.updated', Helper\config('items_sorting_direction'))
        ->offset($offset)
        ->limit($limit)
        ->findAll();
}

function get_bookmarked_item_ids($user_id)
{
    return Database::getInstance('db')
        ->table(Model\Item\TABLE)
        ->eq('user_id', $user_id)
        ->eq('bookmark', 1)
        ->asc('id')
        ->findAllByColumn('id');
}

function set_flag($user_id, $item_id, $value)
{
    return Database::getInstance('db')
        ->table(Model\Item\TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $item_id)
        ->update(array('bookmark' => (int) $value));
}
