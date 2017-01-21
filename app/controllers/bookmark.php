<?php

namespace Miniflux\Controller;

use DateTime;
use Miniflux\Session\SessionStorage;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Model;
use Miniflux\Handler\Service;
use PicoFeed\Syndication\AtomFeedBuilder;
use PicoFeed\Syndication\AtomItemBuilder;

// Ajax call to add or remove a bookmark
Router\post_action('bookmark', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    $value = Request\int_param('value');

    if ($value == 1) {
        Service\sync($user_id, $item_id);
    }

    Response\json(array(
        'id' => $item_id,
        'value' => $value,
        'result' => Model\Bookmark\set_flag($user_id, $item_id, $value),
    ));
});

// Add new bookmark
Router\get_action('bookmark', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $item_id = Request\int_param('id');
    $menu = Request\param('menu');
    $redirect = Request\param('redirect', 'unread');
    $offset = Request\int_param('offset', 0);
    $feed_id = Request\int_param('feed_id', 0);
    $value = Request\int_param('value');

    if ($value == 1) {
        Service\sync($user_id, $item_id);
    }

    Model\Bookmark\set_flag($user_id, $item_id, $value);

    if ($redirect === 'show') {
        Response\redirect('?action=show&menu='.$menu.'&id='.$item_id);
    }

    Response\redirect('?action='.$redirect.'&offset='.$offset.'&feed_id='.$feed_id.'#item-'.$item_id);
});

// Display bookmarks page
Router\get_action('bookmarks', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $offset = Request\int_param('offset', 0);
    $group_id = Request\int_param('group_id', null);
    $feed_ids = array();

    if ($group_id !== null) {
        $feed_ids = Model\Group\get_feed_ids_by_group($group_id);
    }

    $nb_items = Model\Bookmark\count_bookmarked_items($user_id, $feed_ids);
    $items = Model\Bookmark\get_bookmarked_items(
        $user_id,
        $offset,
        Helper\config('items_per_page'),
        $feed_ids
    );

    Response\html(Template\layout('bookmarks/items', array(
        'favicons' => Model\Favicon\get_items_favicons($items),
        'original_marks_read' => Helper\config('original_marks_read'),
        'order' => '',
        'direction' => '',
        'display_mode' => Helper\config('items_display_mode'),
        'item_title_link' => Helper\config('item_title_link'),
        'group_id' => $group_id,
        'items' => $items,
        'nb_items' => $nb_items,
        'offset' => $offset,
        'items_per_page' => Helper\config('items_per_page'),
        'nothing_to_read' => Request\int_param('nothing_to_read'),
        'menu' => 'bookmarks',
        'groups' => Model\Group\get_all($user_id),
        'title' => t('Bookmarks').' ('.$nb_items.')'
    )));
});

// Display bookmark feeds
Router\get_action('bookmark-feed', function () {
    $token = Request\param('token');
    $user = Model\User\get_user_by_token('feed_token', $token);

    if (empty($user)) {
        Response\text('Unauthorized', 401);
    }

    $bookmarks = Model\Bookmark\get_bookmarked_items($user['id']);

    $feedBuilder = AtomFeedBuilder::create()
        ->withTitle(t('Bookmarks').' - Miniflux')
        ->withFeedUrl(Helper\get_current_base_url().'?action=bookmark-feed&token='.urlencode($user['feed_token']))
        ->withSiteUrl(Helper\get_current_base_url())
        ->withDate(new DateTime())
    ;

    foreach ($bookmarks as $bookmark) {
        $articleDate = new DateTime();
        $articleDate->setTimestamp($bookmark['updated']);

        $feedBuilder
            ->withItem(AtomItemBuilder::create($feedBuilder)
                ->withId($bookmark['id'])
                ->withTitle($bookmark['title'])
                ->withUrl($bookmark['url'])
                ->withUpdatedDate($articleDate)
                ->withPublishedDate($articleDate)
                ->withContent($bookmark['content'])
            );
    }

    Response\xml($feedBuilder->build());
});
