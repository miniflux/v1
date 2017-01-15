<?php

namespace Miniflux\Controller;

use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionManager;
use Miniflux\Session\SessionStorage;
use Miniflux\Helper;
use Miniflux\Model;
use Miniflux\Translator;
use Miniflux\Handler;

// Called before each action
Router\before(function ($action) {
    SessionManager::open(BASE_URL_DIRECTORY, SESSION_SAVE_PATH, 0);

    // These actions are considered to be safe even for unauthenticated users
    $safe_actions = array('login', 'bookmark-feed', 'logout', 'notfound');
    if (! SessionStorage::getInstance()->isLogged() && ! in_array($action, $safe_actions)) {
        if (! Model\RememberMe\authenticate()) {
            Response\redirect('?action=login');
        }
    }

    // Load translations
    $language = Helper\config('language', 'en_US');
    Translator\load($language);

    // Set timezone
    date_default_timezone_set(Helper\config('timezone', 'UTC'));

    // HTTP secure headers
    Response\csp(array(
        'media-src' => '*',
        'img-src' => '* data:',
        'frame-src' => Model\Config\get_iframe_whitelist(),
        'child-src' => Model\Config\get_iframe_whitelist(),
        'referrer' => 'no-referrer',
    ));

    Response\xss();
    Response\nosniff();

    if (ENABLE_XFRAME) {
        Response\xframe();
    }

    if (ENABLE_HSTS && Helper\is_secure_connection()) {
        Response\hsts();
    }

    if (SessionStorage::getInstance()->isLogged()) {
        $user_id = SessionStorage::getInstance()->getUserId();
        Model\Item\autoflush_read($user_id);
        Model\Item\autoflush_unread($user_id);
    }
});

// Image proxy (avoid SSL mixed content warnings)
Router\get_action('proxy', function () {
    Handler\Proxy\download(rawurldecode(Request\param('url')));
    exit;
});

function items_list($status)
{
    $order = Request\param('order', 'updated');
    $direction = Request\param('direction', Helper\config('items_sorting_direction'));
    $offset = Request\int_param('offset', 0);
    $group_id = Request\int_param('group_id', null);
    $nb_items_page = Helper\config('items_per_page');
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_ids = array();

    if ($group_id !== null) {
        $feed_ids = Model\Group\get_feed_ids_by_group($group_id);
    }

    $items = Model\Item\get_items_by_status(
        $user_id,
        $status,
        $feed_ids,
        $offset,
        $nb_items_page,
        $order,
        $direction
    );

    $nb_items = Model\Item\count_by_status($user_id, $status, $feed_ids);
    $nb_unread_items = Model\Item\count_by_status($user_id, $status);

    return array(
        'nothing_to_read'     => Request\int_param('nothing_to_read'),
        'favicons'            => Model\Favicon\get_items_favicons($items),
        'original_marks_read' => Helper\bool_config('original_marks_read'),
        'display_mode'        => Helper\config('items_display_mode'),
        'item_title_link'     => Helper\config('item_title_link'),
        'items_per_page'      => $nb_items_page,
        'offset'              => $offset,
        'direction'           => $direction,
        'order'               => $order,
        'items'               => $items,
        'nb_items'            => $nb_items,
        'nb_unread_items'     => $nb_unread_items,
        'group_id'            => $group_id,
        'groups'              => Model\Group\get_all($user_id),
    );
}
