<?php

require __DIR__.'/app/common.php';

use JsonRPC\Server;
use Miniflux\Model;

$server = new Server();

$server->authentication(array(
    Model\Config\get('username') => Model\Config\get('api_token')
));

$procedureHandler = $server->getProcedureHandler();

// Get version
$procedureHandler->withCallback('app.version', function () {
    return array('version' => APP_VERSION);
});

// Get all feeds
$procedureHandler->withCallback('feed.list', function () {
    $feeds = Model\Feed\get_all();
    if (empty($feeds)) {
        return array();
    }

    $groups = Model\Group\get_feeds_map();
    foreach ($feeds as &$feed) {
        $feed_id = $feed['id'];
        $feed['feed_group_ids'] = array();
        if (isset($groups[$feed_id])) {
            $feed['feed_group_ids'] = $groups[$feed_id];
        }
    }

    return $feeds;
});

// Get one feed
$procedureHandler->withCallback('feed.info', function ($feed_id) {
    $result = Model\Feed\get($feed_id);
    $result['feed_group_ids'] = Model\Group\get_feed_group_ids($feed_id);
    return $result;
});

// Add a new feed
$procedureHandler->withCallback('feed.create', function($url) {
    try {
        $result = Model\Feed\create($url);
    } catch (Exception $e) {
        $result = false;
    }

    Model\Config\write_debug();

    return $result;
});

// Delete a feed
$procedureHandler->withCallback('feed.delete', function($feed_id) {
    return Model\Feed\remove($feed_id);
});

// Delete all feeds
$procedureHandler->withCallback('feed.delete_all', function() {
    return Model\Feed\remove_all();
});

// Enable a feed
$procedureHandler->withCallback('feed.enable', function($feed_id) {
    return Model\Feed\enable($feed_id);
});

// Disable a feed
$procedureHandler->withCallback('feed.disable', function($feed_id) {
    return Model\Feed\disable($feed_id);
});

// Update a feed
$procedureHandler->withCallback('feed.update', function($feed_id) {
    return Model\Feed\refresh($feed_id);
});

// Get all groups
$procedureHandler->withCallback('group.list', function () {
    return Model\Group\get_all();
});

// Add a new group
$procedureHandler->withCallback('group.create', function($title) {
    return Model\Group\create($title);
});

// Get assoc array of group ids with assigned feeds ids
$procedureHandler->withCallback('group.map', function() {
    return Model\Group\get_map();
});

// Get the id of a group
$procedureHandler->withCallback('group.id', function($title) {
    return Model\Group\get_group_id($title);
});

// Get all feed ids assigned to a group
$procedureHandler->withCallback('group.feeds', function($group_id) {
    return Model\Group\get_feeds_by_group($group_id);
});

// Add groups to feed
$procedureHandler->withCallback('group.add', function($feed_id, $group_ids) {
    return Model\Group\add($feed_id, $group_ids);
});

// Remove groups from feed
$procedureHandler->withCallback('group.remove', function($feed_id, $group_ids) {
    return Model\Group\remove($feed_id, $group_ids);
});

// Remove all groups from feed
$procedureHandler->withCallback('group.remove_all', function($feed_id) {
    return Model\Group\remove_all($feed_id);
});

// Update feed group associations
$procedureHandler->withCallback('group.update_feed_groups', function($feed_id, $group_ids, $create_group = '') {
    return Model\Group\update_feed_groups($feed_id, $group_ids, $create_group);
});

// Get all items for a specific feed
$procedureHandler->withCallback('item.feed.list', function ($feed_id, $offset = null, $limit = null) {
    return Model\ItemFeed\get_all_items($feed_id, $offset, $limit);
});

// Count all feed items
$procedureHandler->withCallback('item.feed.count', function ($feed_id) {
    return Model\ItemFeed\count_items($feed_id);
});

// Get all bookmark items
$procedureHandler->withCallback('item.bookmark.list', function ($offset = null, $limit = null) {
    return Model\Bookmark\get_all_items($offset, $limit);
});

// Count bookmarks
$procedureHandler->withCallback('item.bookmark.count', function () {
    return Model\Bookmark\count_items();
});

// Add a bookmark
$procedureHandler->withCallback('item.bookmark.create', function ($item_id) {
    return Model\Bookmark\set_flag($item_id, 1);
});

// Remove a bookmark
$procedureHandler->withCallback('item.bookmark.delete', function ($item_id) {
    return Model\Bookmark\set_flag($item_id, 0);
});

// Get all unread items
$procedureHandler->withCallback('item.list_unread', function ($offset = null, $limit = null) {
    return Model\Item\get_all_by_status('unread', array(), $offset, $limit);
});

// Count all unread items
$procedureHandler->withCallback('item.count_unread', function () {
    return Model\Item\count_by_status('unread');
});

// Get all read items
$procedureHandler->withCallback('item.list_read', function ($offset = null, $limit = null) {
    return Model\Item\get_all_by_status('read', array(), $offset, $limit);
});

// Count all read items
$procedureHandler->withCallback('item.count_read', function () {
    return Model\Item\count_by_status('read');
});

// Get one item
$procedureHandler->withCallback('item.info', function ($item_id) {
    return Model\Item\get($item_id);
});

// Delete an item
$procedureHandler->withCallback('item.delete', function($item_id) {
    return Model\Item\set_removed($item_id);
});

// Mark item as read
$procedureHandler->withCallback('item.mark_as_read', function($item_id) {
    return Model\Item\set_read($item_id);
});

// Mark item as unread
$procedureHandler->withCallback('item.mark_as_unread', function($item_id) {
    return Model\Item\set_unread($item_id);
});

// Change the status of list of items
$procedureHandler->withCallback('item.set_list_status', function($status, array $items) {
    return Model\Item\set_status($status, $items);
});

// Flush all read items
$procedureHandler->withCallback('item.flush', function() {
    return Model\Item\mark_all_as_removed();
});

// Mark all unread items as read
$procedureHandler->withCallback('item.mark_all_as_read', function() {
    return Model\Item\mark_all_as_read();
});

// Get all items with the content
$procedureHandler->withCallback('item.get_all', function() {
    return Model\Item\get_all();
});

// Get all items since a date
$procedureHandler->withCallback('item.get_all_since', function($timestamp) {
    return Model\Item\get_all_since($timestamp);
});

// Get all items id and status
$procedureHandler->withCallback('item.get_all_status', function() {
    return Model\Item\get_all_status();
});

echo $server->execute();
