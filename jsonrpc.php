<?php

require __DIR__.'/app/common.php';

use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Server;
use Miniflux\Handler;
use Miniflux\Model;
use Miniflux\Session\SessionStorage;
use Miniflux\Validator;

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
    return APP_VERSION;
});

// Create user
$procedureHandler->withCallback('createUser', function ($username, $password, $is_admin = false) {
    if (! SessionStorage::getInstance()->isAdmin()) {
        throw new AccessDeniedException('Reserved to administrators');
    }

    $values = array(
        'username' => $username,
        'password' => $password,
        'confirmation' => $password,
    );

    list($valid) = Validator\User\validate_creation($values);

    if ($valid) {
        return Model\User\create_user($username, $password, $is_admin);
    }

    return false;
});

// Get user
$procedureHandler->withCallback('getUserByUsername', function ($username) {
    if (! SessionStorage::getInstance()->isAdmin()) {
        throw new AccessDeniedException('Reserved to administrators');
    }

    return Model\User\get_user_by_username($username);
});

// Remove user
$procedureHandler->withCallback('removeUser', function ($user_id) {
    if (! SessionStorage::getInstance()->isAdmin()) {
        throw new AccessDeniedException('Reserved to administrators');
    }

    return Model\User\remove_user($user_id);
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
    $feed = Model\Feed\get_feed($user_id, $feed_id);

    if (! empty($feed)) {
        $feed['groups'] = Model\Group\get_feed_groups($feed['id']);
    }

    return $feed;
});

// Add a new feed
$procedureHandler->withCallback('createFeed', function ($url, $download_content = false, $rtl = false, $group_name = null) {
    $user_id = SessionStorage::getInstance()->getUserId();
    list($feed_id,) = Handler\Feed\create_feed(
        $user_id,
        $url,
        $download_content,
        $rtl,
        false,
        array(),
        $group_name
    );

    if ($feed_id > 0) {
        return $feed_id;
    }

    return false;
});

// Delete a feed
$procedureHandler->withCallback('removeFeed', function ($feed_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Feed\remove_feed($user_id, $feed_id);
});

// Refresh a feed
$procedureHandler->withCallback('refreshFeed', function ($feed_id) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Handler\Feed\update_feed($user_id, $feed_id);
});

// Get all items
$procedureHandler->withCallback('getItems', function ($since_id = null, array $item_ids = array(), $limit = 50) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Item\get_items($user_id, $since_id, $item_ids, $limit);
});

// Get items by status
$procedureHandler->withCallback('getItemsByStatus', function ($status, array $feed_ids = array(), $offset = null, $limit = 50, $order_column = 'updated', $order_direction = 'desc') {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Item\get_items_by_status($user_id, $status, $feed_ids, $offset, $limit, $order_column, $order_direction);
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
$procedureHandler->withCallback('setFeedGroups', function ($feed_id, array $group_ids) {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Group\update_feed_groups($user_id, $feed_id, $group_ids);
});

// Get favicons
$procedureHandler->withCallback('getFavicons', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    return Model\Favicon\get_favicons_with_data_url($user_id);
});

echo $server->execute();
