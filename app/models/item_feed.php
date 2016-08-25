<?php

namespace Miniflux\Model\ItemFeed;

use PicoDb\Database;

function count_items($feed_id)
{
    return Database::getInstance('db')
        ->table('items')
        ->eq('feed_id', $feed_id)
        ->in('status', array('unread', 'read'))
        ->count();
}

function get_all_items($feed_id, $offset = null, $limit = null, $order_column = 'updated', $order_direction = 'desc')
{
    return Database::getInstance('db')
        ->table('items')
        ->columns(
            'items.id',
            'items.title',
            'items.updated',
            'items.url',
            'items.enclosure',
            'items.enclosure_type',
            'items.feed_id',
            'items.status',
            'items.content',
            'items.bookmark',
            'items.language',
            'items.author',
            'feeds.site_url',
            'feeds.rtl'
        )
        ->join('feeds', 'id', 'feed_id')
        ->in('status', array('unread', 'read'))
        ->eq('feed_id', $feed_id)
        ->orderBy($order_column, $order_direction)
        ->offset($offset)
        ->limit($limit)
        ->findAll();
}

function mark_all_as_read($feed_id)
{
    return Database::getInstance('db')
        ->table('items')
        ->eq('status', 'unread')
        ->eq('feed_id', $feed_id)
        ->update(array('status' => 'read'));
}
