<?php

require __DIR__.'/app/common.php';

use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Server;
use Miniflux\Handler;
use Miniflux\Model;
use Miniflux\Session\SessionStorage;

class AuthMiddleware implements MiddlewareInterface
{
    public function execute($username, $password, $procedureName)
    {
        $user = Model\User\get_user_by_token('api_token', $password);
        if (empty($user)) {
            throw new AuthenticationFailureException('Wrong credentials!');
        }

        SessionStorage::getInstance()->setUser($user);
    }
}

$server = new Server();
$server->getMiddlewareHandler()->withMiddleware(new AuthMiddleware());
$procedureHandler = $server->getProcedureHandler();

// Get version
$procedureHandler->withCallback('getVersion', function () {
    return array('version' => APP_VERSION);
});

// Get all feeds
$procedureHandler->withCallback('getFeeds', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feeds = Model\Feed\get_feeds($user_id);

    foreach ($feeds as &$feed) {
        $feed['groups'] = Model\Group\get_feed_groups($feed['id']);
    }

    return $feeds;
});

// Get one feed
$procedureHandler->withCallback('getFeed', function ($feed_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Feed\get_feed($user_id, $feed_id);
});

// Add a new feed
$procedureHandler->withCallback('createFeed', function ($url) {
    $user_id = SessionStorage::getInstance()->getUserId();
    list($feed_id,) = Handler\Feed\create_feed($user_id, $url);

    if ($feed_id > 0) {
        return $feed_id;
    }

    return false;
});

// Delete a feed
$procedureHandler->withCallback('deleteFeed', function ($feed_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Feed\remove_feed($user_id, $feed_id);
});

// Refresh a feed
$procedureHandler->withCallback('refreshFeed', function ($feed_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Handler\Feed\update_feed($user_id, $feed_id);
});

// Get all items
$procedureHandler->withCallback('getItems', function ($since_id = null, array $item_ids = array(), $offset = 50) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Item\get_items($user_id, $since_id, $item_ids, $offset);
});

// Get one item
$procedureHandler->withCallback('getItem', function ($item_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Item\get_item($user_id, $item_id);
});

// Change items status
$procedureHandler->withCallback('changeItemsStatus', function (array $item_ids, $status) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Item\change_item_ids_status($user_id, $item_ids, $status);
});

// Add a bookmark
$procedureHandler->withCallback('addBookmark', function ($item_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Bookmark\set_flag($user_id, $item_id, 1);
});

// Remove a bookmark
$procedureHandler->withCallback('removeBookmark', function ($item_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Bookmark\set_flag($user_id, $item_id, 0);
});

// Get all groups
$procedureHandler->withCallback('getGroups', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Group\get_all($user_id);
});

// Add a new group
$procedureHandler->withCallback('createGroup', function ($title) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Group\create_group($user_id, $title);
});

// Add/Update groups for a feed
$procedureHandler->withCallback('setFeedGroups', function ($feed_id, $group_ids) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Group\update_feed_groups($user_id, $feed_id, $group_ids);
});

// Get favicons
$procedureHandler->withCallback('getFavicons', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Favicon\get_favicons_with_data_url($user_id);
});

echo $server->execute();
