<?php

require __DIR__.'/../app/common.php';

use Miniflux\Handler;
use Miniflux\Model;
use Miniflux\Session\SessionStorage;

register_shutdown_function(function () {
    Miniflux\Helper\write_debug_file();
});

// Route handler
function route($name, Closure $callback = null)
{
    static $routes = array();

    if ($callback !== null) {
        $routes[$name] = $callback;
    } elseif (isset($routes[$name])) {
        $routes[$name]();
    }
}

// Serialize the payload in Json (XML is not supported)
function response(array $response)
{
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Fever authentication
function auth()
{
    $api_key = isset($_POST['api_key']) && ctype_alnum($_POST['api_key']) ? $_POST['api_key'] : null;
    $user = Model\User\get_user_by_token('fever_api_key', $api_key);
    $authenticated = $user !== null;

    if ($authenticated) {
        SessionStorage::getInstance()->setUser($user);
    }

    $response = array(
        'api_version' => 3,
        'auth' => (int) $authenticated,
        'last_refreshed_on_time' => time(),
    );

    return array($user, $authenticated, $response);
}

// Call: ?api&groups
route('groups', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated) {
        $response['groups'] = array();
        $response['feeds_groups'] = array();

        $groups = Model\Group\get_all($user['id']);
        $feed_groups = Model\Group\get_groups_feed_ids($user['id']);

        foreach ($groups as $group) {
            $response['groups'][] = array(
                'id'    => $group['id'],
                'title' => $group['title'],
            );
        }

        foreach ($feed_groups as $group_id => $feed_ids) {
            $response['feeds_groups'][] = array(
                'group_id' => $group_id,
                'feed_ids' => implode(',', $feed_ids)
            );
        }
    }

    response($response);
});

// Call: ?api&feeds
route('feeds', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated) {
        $response['feeds'] = array();
        $response['feeds_groups'] = array();

        $feeds = Model\Feed\get_feeds($user['id']);

        foreach ($feeds as $feed) {
            $response['feeds'][] = array(
                'id' => (int) $feed['id'],
                'favicon_id' => (int) $feed['id'],
                'title' => $feed['title'],
                'url' => $feed['feed_url'],
                'site_url' => $feed['site_url'],
                'is_spark' => 0,
                'last_updated_on_time' => $feed['last_checked'] ?: time(),
            );
        }

        $group_map = Model\Group\get_groups_feed_ids($user['id']);
        foreach ($group_map as $group_id => $feed_ids) {
            $response['feeds_groups'][] = array(
                'group_id' => $group_id,
                'feed_ids' => implode(',', $feed_ids)
            );
        }
    }

    response($response);
});

// Call: ?api&favicons
route('favicons', function () {
    list($user, $authenticated, $response) = auth();
    if ($authenticated) {
        $favicons = Model\Favicon\get_favicons_with_data_url($user['id']);
        $response['favicons'] = array();

        foreach ($favicons as $favicon) {
            $response['favicons'][] = array(
                'id' => (int) $favicon['feed_id'],
                'data' => $favicon['data_url'],
            );
        }
    }

    response($response);
});

// Call: ?api&items
route('items', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated) {
        $since_id = isset($_GET['since_id']) && ctype_digit($_GET['since_id']) ? $_GET['since_id'] : null;
        $item_ids = ! empty($_GET['with_ids']) ? explode(',', $_GET['with_ids']) : array();
        $items = Model\Item\get_items($user['id'], $since_id, $item_ids);
        $response['items'] = array();

        foreach ($items as $item) {
            $response['items'][] = array(
                'id' => (int) $item['id'],
                'feed_id' => (int) $item['feed_id'],
                'title' => $item['title'],
                'author' => $item['author'],
                'html' => $item['content'],
                'url' => $item['url'],
                'is_saved' => (int) $item['bookmark'],
                'is_read' => $item['status'] == 'read' ? 1 : 0,
                'created_on_time' => $item['updated'],
            );
        }

        $response['total_items'] = Model\Item\count_by_status(
            $user['id'],
            array(Model\Item\STATUS_READ, Model\Item\STATUS_UNREAD)
        );
    }

    response($response);
});

// Call: ?api&links
route('links', function () {
    list(, $authenticated, $response) = auth();

    if ($authenticated) {
        $response['links'] = array();
    }

    response($response);
});

// Call: ?api&unread_item_ids
route('unread_item_ids', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated) {
        $item_ids = Model\Item\get_item_ids_by_status($user['id'], Model\Item\STATUS_UNREAD);
        $response['unread_item_ids'] = implode(',', $item_ids);
    }

    response($response);
});

// Call: ?api&saved_item_ids
route('saved_item_ids', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated) {
        $item_ids = Model\Bookmark\get_bookmarked_item_ids($user['id']);
        $response['saved_item_ids'] = implode(',', $item_ids);
    }

    response($response);
});

// handle write items
route('write_items', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated && ctype_digit($_POST['id'])) {
        $item_id = $_POST['id'];

        if ($_POST['as'] === 'saved') {
            Model\Bookmark\set_flag($user['id'], $item_id, 1);
            Handler\Service\sync($user['id'], $item_id);
        } elseif ($_POST['as'] === 'unsaved') {
            Model\Bookmark\set_flag($user['id'], $item_id, 0);
        } elseif ($_POST['as'] === 'read') {
            Model\Item\change_item_status($user['id'], $item_id, Model\Item\STATUS_READ);
        } elseif ($_POST['as'] === 'unread') {
            Model\Item\change_item_status($user['id'], $item_id, Model\Item\STATUS_UNREAD);
        }
    }

    response($response);
});

// handle write feeds
route('write_feeds', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated && ctype_digit($_POST['id']) && ctype_digit($_POST['before'])) {
        Model\ItemFeed\change_items_status(
            $user['id'],
            $_POST['id'],
            Model\Item\STATUS_UNREAD,
            Model\Item\STATUS_READ,
            $_POST['before']
        );
    }

    response($response);
});

// handle write groups
route('write_groups', function () {
    list($user, $authenticated, $response) = auth();

    if ($authenticated && ctype_digit($_POST['id']) && ctype_digit($_POST['before'])) {
        if ($_POST['id'] == 0) {
            Model\Item\change_items_status(
                $user['id'],
                Model\Item\STATUS_UNREAD,
                Model\Item\STATUS_READ,
                $_POST['before']
            );
        } else {
            Model\ItemGroup\change_items_status(
                $user['id'],
                $_POST['id'],
                Model\Item\STATUS_UNREAD,
                Model\Item\STATUS_READ,
                $_POST['before']
            );
        }
    }

    response($response);
});

foreach (array_keys($_GET) as $action) {
    route($action);
}

if (! empty($_POST['mark']) && ! empty($_POST['as'])
    && filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, array('options' => array('default' => null, 'min_range' => -1))) !== null) {
    if ($_POST['mark'] === 'item') {
        route('write_items');
    } elseif ($_POST['mark'] === 'feed' && ! empty($_POST['before'])) {
        route('write_feeds');
    } elseif ($_POST['mark'] === 'group' && ! empty($_POST['before'])) {
        route('write_groups');
    }
}

list(, , $response) = auth();
response($response);
