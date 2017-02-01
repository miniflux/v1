<?php

namespace Miniflux\Controller;

use Miniflux\Helper;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Handler;
use Miniflux\Model;

// Display unread items
Router\get_action('unread', function () {
    $params = items_list(Model\Item\STATUS_UNREAD);

    if ($params['nb_unread_items'] === 0) {
        $action = Helper\config('redirect_nothing_to_read', 'feeds');

        if ($action !== 'nowhere') {
            Response\redirect('?action='.$action.'&nothing_to_read=1');
        }
    }

    Response\html(Template\layout('unread/items', $params + array(
        'title' => 'Miniflux (' . $params['nb_items'] . ')',
        'menu'  => 'unread',
    )));
});

// Show item
Router\get_action('show', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    $menu = Request\param('menu');
    $item = Model\Item\get_item($user_id, $item_id);
    $feed = Model\Feed\get_feed($user_id, $item['feed_id']);
    $group_id = Request\int_param('group_id', null);

    if ($item['status'] !== Model\Item\STATUS_READ) {
        Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_READ);
        $item['status'] = Model\Item\STATUS_READ;
    }

    switch ($menu) {
        case 'unread':
            $nav = Model\Item\get_item_nav($user_id, $item, array('unread'), array(1, 0), null, $group_id);
            break;
        case 'history':
            $nav = Model\Item\get_item_nav($user_id, $item, array('read'));
            break;
        case 'feed-items':
            $nav = Model\Item\get_item_nav($user_id, $item, array('unread', 'read'), array(1, 0), $item['feed_id']);
            break;
        case 'bookmarks':
            $nav = Model\Item\get_item_nav($user_id, $item, array('unread', 'read'), array(1));
            break;
    }

    $image_proxy = (bool) Helper\config('image_proxy');

    // add the image proxy if requested and required
    $item['content'] = Handler\Proxy\rewrite_html($item['content'], $item['url'], $image_proxy, $feed['cloak_referrer']);

    if ($image_proxy && strpos($item['enclosure_type'], 'image') === 0) {
        $item['enclosure_url'] = Handler\Proxy\rewrite_link($item['enclosure_url']);
    }

    Response\html(Template\layout('item/show', array(
        'item' => $item,
        'feed' => $feed,
        'item_nav' => isset($nav) ? $nav : null,
        'menu' => $menu,
        'title' => $item['title'],
        'group_id' => $group_id,
    )));
});

// Display feed items page
Router\get_action('feed-items', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id', 0);
    $offset = Request\int_param('offset', 0);
    $feed = Model\Feed\get_feed($user_id, $feed_id);
    $order = Request\param('order', 'updated');
    $direction = Request\param('direction', Helper\config('items_sorting_direction'));
    $items = Model\ItemFeed\get_all_items($user_id, $feed_id, $offset, Helper\config('items_per_page'), $order, $direction);
    $nb_items = Model\ItemFeed\count_items($user_id, $feed_id);

    Response\html(Template\layout('feeds/items', array(
        'favicons' => Model\Favicon\get_favicons_by_feed_ids(array($feed['id'])),
        'original_marks_read' => Helper\config('original_marks_read'),
        'order' => $order,
        'direction' => $direction,
        'display_mode' => Helper\config('items_display_mode'),
        'feed' => $feed,
        'items' => $items,
        'nb_items' => $nb_items,
        'offset' => $offset,
        'items_per_page' => Helper\config('items_per_page'),
        'item_title_link' => Helper\config('item_title_link'),
        'menu' => 'feed-items',
        'title' => '('.$nb_items.') '.$feed['title']
    )));
});

// Ajax call to download an item (fetch the full content from the original website)
Router\post_action('download-item', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');

    $item = Model\Item\get_item($user_id, $item_id);
    $feed = Model\Feed\get_feed($user_id, $item['feed_id']);

    $download = Handler\Item\download_item_content($user_id, $item_id);
    $download['content'] = Handler\Proxy\rewrite_html(
        $download['content'],
        $item['url'],
        Helper\bool_config('image_proxy'),
        (bool) $feed['cloak_referrer']
    );

    Response\json($download);
});

// Ajax call to mark item read
Router\post_action('mark-item-read', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_READ);
    Response\json(array('Ok'));
});

// Ajax call to mark item as removed
Router\post_action('mark-item-removed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_REMOVED);
    Response\json(array('Ok'));
});

// Ajax call to mark item unread
Router\post_action('mark-item-unread', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_UNREAD);
    Response\json(array('Ok'));
});

// Mark unread items as read
Router\get_action('mark-all-read', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $group_id = Request\int_param('group_id', null);

    if ($group_id !== null) {
        Model\ItemGroup\change_items_status($user_id, $group_id, Model\Item\STATUS_UNREAD, Model\Item\STATUS_READ);
    } else {
        Model\Item\change_items_status($user_id, Model\Item\STATUS_UNREAD, Model\Item\STATUS_READ);
    }

    Response\redirect('?action=unread');
});

// Mark all unread items as read for a specific feed
Router\get_action('mark-feed-as-read', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id');

    Model\ItemFeed\change_items_status($user_id, $feed_id, Model\Item\STATUS_UNREAD, Model\Item\STATUS_READ);

    Response\redirect('?action=feed-items&feed_id='.$feed_id);
});

// Mark all unread items as read for a specific feed (Ajax request) and return
// the number of unread items. It's not possible to get the number of items
// that where marked read from the frontend, since the number of unread items
// on page 2+ is unknown.
Router\post_action('mark-feed-as-read', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id');

    Model\ItemFeed\change_items_status($user_id, $feed_id, Model\Item\STATUS_UNREAD, Model\Item\STATUS_READ);

    $nb_items = Model\Item\count_by_status($user_id, Model\Item\STATUS_READ);
    Response\raw($nb_items);
});

// Mark item as read and redirect to the listing page
Router\get_action('mark-item-read', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    $redirect = Request\param('redirect', 'unread');
    $offset = Request\int_param('offset', 0);
    $feed_id = Request\int_param('feed_id', 0);

    Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_READ);
    Response\redirect('?action='.$redirect.'&offset='.$offset.'&feed_id='.$feed_id.'#item-'.$item_id);
});

// Mark item as unread and redirect to the listing page
Router\get_action('mark-item-unread', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    $redirect = Request\param('redirect', 'history');
    $offset = Request\int_param('offset', 0);
    $feed_id = Request\int_param('feed_id', 0);

    Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_UNREAD);
    Response\redirect('?action='.$redirect.'&offset='.$offset.'&feed_id='.$feed_id.'#item-'.$item_id);
});

// Mark item as removed and redirect to the listing page
Router\get_action('mark-item-removed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    $redirect = Request\param('redirect', 'history');
    $offset = Request\int_param('offset', 0);
    $feed_id = Request\int_param('feed_id', 0);

    Model\Item\change_item_status($user_id, $item_id, Model\Item\STATUS_REMOVED);
    Response\redirect('?action='.$redirect.'&offset='.$offset.'&feed_id='.$feed_id);
});

Router\get_action('latest-feeds-items', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $items_timestamps = Model\Item\get_latest_unread_items_timestamps($user_id);
    $nb_unread_items = Model\Item\count_by_status($user_id, 'unread');

    Response\json(array(
        'last_items_timestamps' => $items_timestamps,
        'nb_unread_items' => $nb_unread_items
    ));
});
