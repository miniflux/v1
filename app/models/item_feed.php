<?php

namespace Miniflux\Model\ItemFeed;

use Miniflux\Model\Feed;
use Miniflux\Model\Item;
use PicoDb\Database;

function count_items_by_status($user_id, $feed_id)
{
    $counts = Database::getInstance('db')
        ->table(Item\TABLE)
        ->columns('status', 'count(*) as item_count')
        ->in('status', array(Item\STATUS_READ, Item\STATUS_UNREAD))
        ->eq('user_id', $user_id)
        ->eq('feed_id', $feed_id)
        ->groupBy('status')
        ->findAll();

    $result = array(
        'items_unread' => 0,
        'items_total' => 0,
    );

    foreach ($counts as &$count) {
        if ($count['status'] === Item\STATUS_UNREAD) {
            $result['items_unread'] = (int) $count['item_count'];
        }

        $result['items_total'] += $count['item_count'];
    }

    return $result;
}

function count_items($user_id, $feed_id)
{
    return Database::getInstance('db')
        ->table(Item\TABLE)
        ->eq('feed_id', $feed_id)
        ->eq('user_id', $user_id)
        ->in('status', array(Item\STATUS_READ, Item\STATUS_UNREAD))
        ->count();
}

function get_all_items($user_id, $feed_id, $offset = null, $limit = null, $order_column = 'updated', $order_direction = 'desc')
{
    return Database::getInstance('db')
        ->table('items')
        ->columns(
            'items.id',
            'items.title',
            'items.updated',
            'items.url',
            'items.enclosure_url',
            'items.enclosure_type',
            'items.feed_id',
            'items.status',
            'items.content',
            'items.bookmark',
            'items.language',
            'items.rtl',
            'items.author',
            'feeds.site_url',
            'feeds.title AS feed_title'
        )
        ->join(Feed\TABLE, 'id', 'feed_id')
        ->in('status', array(Item\STATUS_UNREAD, Item\STATUS_READ))
        ->eq('items.feed_id', $feed_id)
        ->eq('items.user_id', $user_id)
        ->orderBy($order_column, $order_direction)
        ->offset($offset)
        ->limit($limit)
        ->findAll();
}

function change_items_status($user_id, $feed_id, $current_status, $new_status, $before = null)
{
    $query = Database::getInstance('db')
        ->table(Item\TABLE)
        ->eq('status', $current_status)
        ->eq('feed_id', $feed_id)
        ->eq('user_id', $user_id);

    if ($before !== null) {
        $query->lte('updated', $before);
    }

    return $query->update(array('status' => $new_status));
}
