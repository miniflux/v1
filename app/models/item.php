<?php

namespace Miniflux\Model\Item;

use PicoDb\Database;
use Miniflux\Model\Group;
use Miniflux\Handler;
use Miniflux\Helper;
use PicoFeed\Parser\Parser;

const TABLE          = 'items';
const STATUS_UNREAD  = 'unread';
const STATUS_READ    = 'read';
const STATUS_REMOVED = 'removed';

function change_item_status($user_id, $item_id, $status)
{
    if (! in_array($status, array(STATUS_READ, STATUS_UNREAD, STATUS_REMOVED))) {
        return false;
    }

    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $item_id)
        ->update(array('status' => $status));
}

function change_items_status($user_id, $current_status, $new_status, $before = null)
{
    if (! in_array($new_status, array(STATUS_READ, STATUS_UNREAD, STATUS_REMOVED))) {
        return false;
    }

    $query = Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('status', $current_status);

    if ($before !== null) {
        $query->lte('updated', $before);
    }

    return $query->update(array('status' => $new_status));
}

function change_item_ids_status($user_id, array $item_ids, $status)
{
    if (! in_array($status, array(STATUS_READ, STATUS_UNREAD, STATUS_REMOVED))) {
        return false;
    }

    if (empty($item_ids)) {
        return false;
    }

    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->in('id', $item_ids)
        ->update(array('status' => $status));
}

function update_feed_items($user_id, $feed_id, array $items, $rtl = false, array $ignore_urls = array())
{
    $items_in_feed = array();
    $db = Database::getInstance('db');
    $db->startTransaction();

    foreach ($items as $item) {
        if ($item->getId() && $item->getUrl()) {
            $item_id = get_item_id_from_checksum($feed_id, $item->getId());

            $values = array(
                'title'          => $item->getTitle(),
                'url'            => $item->getUrl(),
                'updated'        => $item->getDate()->getTimestamp(),
                'author'         => $item->getAuthor(),
                'content'        => Helper\bool_config('nocontent') ? '' : $item->getContent(),
                'enclosure_url'  => $item->getEnclosureUrl(),
                'enclosure_type' => $item->getEnclosureType(),
                'language'       => $item->getLanguage(),
                'rtl'            => $rtl || Parser::isLanguageRTL($item->getLanguage()) ? 1 : 0,
            );

            if ($item_id > 0) {
                if (in_array($item->getUrl(), $ignore_urls)) {
                    unset($values['content']);
                }

                $db
                    ->table(TABLE)
                    ->eq('user_id', $user_id)
                    ->eq('feed_id', $feed_id)
                    ->eq('id', $item_id)
                    ->update($values);
            } else {
                $values['checksum'] = $item->getId();
                $values['user_id'] = $user_id;
                $values['feed_id'] = $feed_id;
                $values['status'] = STATUS_UNREAD;
                $item_id = $db->table(TABLE)->persist($values);
            }

            $items_in_feed[] = $item_id;
        }
    }

    cleanup_feed_items($feed_id, $items_in_feed);
    $db->closeTransaction();
}

function cleanup_feed_items($feed_id, array $items_in_feed)
{
    if (! empty($items_in_feed)) {
        $db = Database::getInstance('db');

        $removed_items = $db
            ->table(TABLE)
            ->columns('id')
            ->notIn('id', $items_in_feed)
            ->eq('status', STATUS_REMOVED)
            ->eq('feed_id', $feed_id)
            ->desc('updated')
            ->findAllByColumn('id');

        // Keep a buffer of 2 items
        // It's workaround for buggy feeds (cache issue with some Wordpress plugins)
        if (is_array($removed_items)) {
            $items_to_remove = array_slice($removed_items, 2);

            if (! empty($items_to_remove)) {
                // Handle the case when there is a huge number of items to remove
                // Sqlite have a limit of 1000 sql variables by default
                // Avoid the error message "too many SQL variables"
                // We remove old items by batch of 500 items
                $chunks = array_chunk($items_to_remove, 500);

                foreach ($chunks as $chunk) {
                    $db->table(TABLE)
                        ->in('id', $chunk)
                        ->eq('status', STATUS_REMOVED)
                        ->eq('feed_id', $feed_id)
                        ->remove();
                }
            }
        }
    }
}

function get_item_id_from_checksum($feed_id, $checksum)
{
    return (int) Database::getInstance('db')
        ->table(TABLE)
        ->eq('feed_id', $feed_id)
        ->eq('checksum', $checksum)
        ->findOneColumn('id');
}

function get_item($user_id, $item_id)
{
    return Database::getInstance('db')
        ->table('items')
        ->eq('user_id', $user_id)
        ->eq('id', $item_id)
        ->findOne();
}

function get_item_nav($user_id, array $item, $status = array(STATUS_UNREAD), $bookmark = array(1, 0), $feed_id = null, $group_id = null)
{
    $query = Database::getInstance('db')
        ->table(TABLE)
        ->columns('id', 'status', 'title', 'bookmark')
        ->neq('status', STATUS_REMOVED)
        ->eq('user_id', $user_id)
        ->orderBy('updated', Helper\config('items_sorting_direction'))
        ->desc('id')
    ;

    if ($feed_id) {
        $query->eq('feed_id', $feed_id);
    }

    if ($group_id) {
        $query->in('feed_id', Group\get_feed_ids_by_group($group_id));
    }

    $items = $query->findAll();

    $next_item = null;
    $previous_item = null;

    for ($i = 0, $ilen = count($items); $i < $ilen; ++$i) {
        if ($items[$i]['id'] == $item['id']) {
            if ($i > 0) {
                $j = $i - 1;

                while ($j >= 0) {
                    if (in_array($items[$j]['status'], $status) && in_array($items[$j]['bookmark'], $bookmark)) {
                        $previous_item = $items[$j];
                        break;
                    }

                    --$j;
                }
            }

            if ($i < ($ilen - 1)) {
                $j = $i + 1;

                while ($j < $ilen) {
                    if (in_array($items[$j]['status'], $status) && in_array($items[$j]['bookmark'], $bookmark)) {
                        $next_item = $items[$j];
                        break;
                    }

                    ++$j;
                }
            }

            break;
        }
    }

    return array(
        'next' => $next_item,
        'previous' => $previous_item
    );
}

function get_items_by_status($user_id, $status, $feed_ids = array(), $offset = null, $limit = null, $order_column = 'updated', $order_direction = 'desc')
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->columns(
            'items.id',
            'items.checksum',
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
        ->join('feeds', 'id', 'feed_id')
        ->eq('items.user_id', $user_id)
        ->eq('items.status', $status)
        ->in('items.feed_id', $feed_ids)
        ->orderBy($order_column, $order_direction)
        ->offset($offset)
        ->limit($limit)
        ->findAll();
}

function get_items($user_id, $since_id = null, array $item_ids = array(), $limit = 50)
{
    $query = Database::getInstance('db')
        ->table(TABLE)
        ->columns(
            'items.id',
            'items.checksum',
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
        ->join('feeds', 'id', 'feed_id')
        ->eq('items.user_id', $user_id)
        ->neq('items.status', STATUS_REMOVED)
        ->limit($limit)
        ->asc('items.id');

    if ($since_id !== null) {
        $query->gt('items.id', $since_id);
    } elseif (! empty($item_ids)) {
        $query->in('items.id', $item_ids);
    }

    return $query->findAll();
}

function get_item_ids_by_status($user_id, $status)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('status', $status)
        ->asc('id')
        ->findAllByColumn('id');
}

function get_item_urls($user_id, $feed_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('feed_id', $feed_id)
        ->findAllByColumn('url');
}

function get_latest_unread_items_timestamps($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->columns(
            'feed_id',
            'MAX(updated) as updated'
        )
        ->eq('user_id', $user_id)
        ->eq('status', STATUS_UNREAD)
        ->groupBy('feed_id')
        ->desc('updated')
        ->findAll();
}

function count_by_status($user_id, $status, $feed_ids = array())
{
    $query = Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->in('feed_id', $feed_ids);

    if (is_array($status)) {
        $query->in('status', $status);
    } else {
        $query->eq('status', $status);
    }

    return $query->count();
}

function autoflush_read($user_id)
{
    $autoflush = Helper\int_config('autoflush');

    if ($autoflush > 0) {
        Database::getInstance('db')
            ->table(TABLE)
            ->eq('user_id', $user_id)
            ->eq('bookmark', 0)
            ->eq('status', STATUS_READ)
            ->lt('updated', strtotime('-'.$autoflush.'day'))
            ->save(array('status' => STATUS_REMOVED, 'content' => ''));
    } elseif ($autoflush === -1) {
        Database::getInstance('db')
            ->table(TABLE)
            ->eq('user_id', $user_id)
            ->eq('bookmark', 0)
            ->eq('status', STATUS_READ)
            ->save(array('status' => STATUS_REMOVED, 'content' => ''));
    }
}

function autoflush_unread($user_id)
{
    $autoflush = Helper\int_config('autoflush_unread');

    if ($autoflush > 0) {
        Database::getInstance('db')
            ->table(TABLE)
            ->eq('user_id', $user_id)
            ->eq('bookmark', 0)
            ->eq('status', STATUS_UNREAD)
            ->lt('updated', strtotime('-'.$autoflush.'day'))
            ->save(array('status' => STATUS_REMOVED, 'content' => ''));
    }
}
