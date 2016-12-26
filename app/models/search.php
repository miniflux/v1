<?php

namespace Miniflux\Model\ItemSearch;

use PicoDb\Database;
use Miniflux\Model\Feed;
use Miniflux\Model\Item;

function count_items($user_id, $text)
{
    return Database::getInstance('db')
        ->table(Item\TABLE)
        ->eq('user_id', $user_id)
        ->neq('status', Item\STATUS_REMOVED)
        ->ilike('title', '%' . $text . '%')
        ->count();
}

function get_all_items($user_id, $text, $offset = null, $limit = null)
{
    return Database::getInstance('db')
        ->table(Item\TABLE)
        ->columns(
            'items.id',
            'items.title',
            'items.updated',
            'items.url',
            'items.enclosure_url',
            'items.enclosure_type',
            'items.bookmark',
            'items.feed_id',
            'items.status',
            'items.content',
            'items.language',
            'items.rtl',
            'items.author',
            'feeds.site_url',
            'feeds.title AS feed_title'
        )
        ->join(Feed\TABLE, 'id', 'feed_id')
        ->eq('items.user_id', $user_id)
        ->neq('items.status', Item\STATUS_REMOVED)
        ->ilike('items.title', '%' . $text . '%')
        ->orderBy('items.updated', 'desc')
        ->offset($offset)
        ->limit($limit)
        ->findAll();
}
