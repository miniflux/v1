<?php

namespace Miniflux\Model\Bookmark;

use PicoDb\Database;
use Miniflux\Handler\Service;
use Miniflux\Model\Config;

function count_items($feed_ids = array())
{
    return Database::getInstance('db')
        ->table('items')
        ->eq('bookmark', 1)
        ->in('feed_id', $feed_ids)
        ->in('status', array('read', 'unread'))
        ->count();
}

function get_all_items($offset = null, $limit = null, $feed_ids = array())
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
            'items.bookmark',
            'items.status',
            'items.content',
            'items.feed_id',
            'items.language',
            'items.author',
            'feeds.site_url',
            'feeds.title AS feed_title',
            'feeds.rtl'
        )
        ->join('feeds', 'id', 'feed_id')
        ->in('feed_id', $feed_ids)
        ->in('status', array('read', 'unread'))
        ->eq('bookmark', 1)
        ->orderBy('updated', Config\get('items_sorting_direction'))
        ->offset($offset)
        ->limit($limit)
        ->findAll();
}

function set_flag($id, $value)
{
    if ($value == 1) {
        Service\sync($id);
    }

    return Database::getInstance('db')
        ->table('items')
        ->eq('id', $id)
        ->in('status', array('read', 'unread'))
        ->save(array('bookmark' => $value));
}
