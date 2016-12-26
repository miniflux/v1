<?php

namespace Miniflux\Model\Feed;

use Miniflux\Model\Item;
use Miniflux\Model\Group;
use PicoDb\Database;
use PicoFeed\Parser\Feed;

const STATUS_ACTIVE   = 1;
const STATUS_INACTIVE = 0;
const TABLE           = 'feeds';

function create($user_id, Feed $feed, $etag, $last_modified, $rtl = false, $scraper = false, $cloak_referrer = false)
{
    $db = Database::getInstance('db');

    if ($db->table('feeds')->eq('user_id', $user_id)->eq('feed_url', $feed->getFeedUrl())->exists()) {
        return -1;
    }

    $feed_id = $db
        ->table(TABLE)
        ->persist(array(
            'user_id'          => $user_id,
            'title'            => $feed->getTitle(),
            'site_url'         => $feed->getSiteUrl(),
            'feed_url'         => $feed->getFeedUrl(),
            'download_content' => $scraper ? 1 : 0,
            'rtl'              => $rtl ? 1 : 0,
            'etag'             => $etag,
            'last_modified'    => $last_modified,
            'last_checked'     => time(),
            'cloak_referrer'   => $cloak_referrer ? 1 : 0,
        ));

    if ($feed_id !== false) {
        Item\update_feed_items($user_id, $feed_id, $feed->getItems(), $rtl);
    }

    return $feed_id;
}

function get_feeds($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->desc('parsing_error')
        ->desc('enabled')
        ->asc('title')
        ->findAll();
}

function get_feeds_with_items_count($user_id)
{
    $feeds_count = array();
    $feeds = get_feeds($user_id);
    $feed_items = Database::getInstance('db')
        ->table(Item\TABLE)
        ->columns('feed_id', 'status', 'count(*) as nb')
        ->eq('user_id', $user_id)
        ->neq('status', Item\STATUS_REMOVED)
        ->groupBy('feed_id', 'status')
        ->findAll();

    foreach ($feed_items as $row) {
        if (! isset($feeds_count[$row['feed_id']])) {
            $feeds_count[$row['feed_id']] = array('unread' => 0, 'total' => 0);
        }

        if ($row['status'] === 'unread') {
            $feeds_count[$row['feed_id']]['unread'] = $row['nb'];
        }

        $feeds_count[$row['feed_id']]['total'] += $row['nb'];
    }

    foreach ($feeds as &$feed) {
        $feed['items_unread'] = isset($feeds_count[$feed['id']]) ? $feeds_count[$feed['id']]['unread'] : 0;
        $feed['items_total'] = isset($feeds_count[$feed['id']]) ? $feeds_count[$feed['id']]['total'] : 0;
    }

    return $feeds;
}

function get_feed_ids($user_id, $limit = null)
{
    $query = Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('enabled', STATUS_ACTIVE)
        ->asc('last_checked')
        ->asc('id');

    if ($limit !== null) {
        $query->limit($limit);
    }

    return $query->findAllByColumn('id');
}

function get_feed($user_id, $feed_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $feed_id)
        ->findOne();
}

function update_feed($user_id, $feed_id, array $values)
{
    $db = Database::getInstance('db');
    $db->startTransaction();

    $feed = $values;
    unset($feed['id']);
    unset($feed['group_name']);
    unset($feed['feed_group_ids']);

    $result = Database::getInstance('db')
            ->table('feeds')
            ->eq('user_id', $user_id)
            ->eq('id', $feed_id)
            ->save($feed);

    if ($result) {
        if (isset($values['feed_group_ids']) && isset($values['group_name']) &&
            ! Group\update_feed_groups($user_id, $values['id'], $values['feed_group_ids'], $values['group_name'])) {
            $db->cancelTransaction();
            return false;
        }

        $db->closeTransaction();
        return true;
    }

    $db->cancelTransaction();
    return false;
}

function change_feed_status($user_id, $feed_id, $status = STATUS_ACTIVE)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $feed_id)
        ->save((array('enabled' => $status)));
}

function remove_feed($user_id, $feed_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $feed_id)
        ->remove();
}

function count_failed_feeds($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('parsing_error', 1)
        ->count();
}

function count_feeds($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->count();
}

