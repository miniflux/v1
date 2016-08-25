<?php

use PicoFeed\Syndication\AtomFeedBuilder;
use PicoFeed\Syndication\AtomItemBuilder;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Model;

// Ajax call to add or remove a bookmark
Router\post_action('bookmark', function () {
    $id = Request\param('id');
    $value = Request\int_param('value');

    Response\json(array(
        'id' => $id,
        'value' => $value,
        'result' => Model\Bookmark\set_flag($id, $value),
    ));
});

// Add new bookmark
Router\get_action('bookmark', function () {
    $id = Request\param('id');
    $menu = Request\param('menu');
    $redirect = Request\param('redirect', 'unread');
    $offset = Request\int_param('offset', 0);
    $feed_id = Request\int_param('feed_id', 0);

    Model\Bookmark\set_flag($id, Request\int_param('value'));

    if ($redirect === 'show') {
        Response\redirect('?action=show&menu='.$menu.'&id='.$id);
    }

    Response\redirect('?action='.$redirect.'&offset='.$offset.'&feed_id='.$feed_id.'#item-'.$id);
});

// Display bookmarks page
Router\get_action('bookmarks', function () {
    $offset = Request\int_param('offset', 0);
    $group_id = Request\int_param('group_id', null);
    $feed_ids = array();

    if ($group_id !== null) {
        $feed_ids = Model\Group\get_feeds_by_group($group_id);
    }

    $nb_items = Model\Bookmark\count_items($feed_ids);
    $items = Model\Bookmark\get_all_items(
        $offset,
        Model\Config\get('items_per_page'),
        $feed_ids
    );

    Response\html(Template\layout('bookmarks', array(
        'favicons' => Model\Favicon\get_item_favicons($items),
        'original_marks_read' => Model\Config\get('original_marks_read'),
        'order' => '',
        'direction' => '',
        'display_mode' => Model\Config\get('items_display_mode'),
        'item_title_link' => Model\Config\get('item_title_link'),
        'group_id' => $group_id,
        'items' => $items,
        'nb_items' => $nb_items,
        'offset' => $offset,
        'items_per_page' => Model\Config\get('items_per_page'),
        'nothing_to_read' => Request\int_param('nothing_to_read'),
        'nb_unread_items' => Model\Item\count_by_status('unread'),
        'menu' => 'bookmarks',
        'groups' => Model\Group\get_all(),
        'title' => t('Bookmarks').' ('.$nb_items.')'
    )));
});

// Display bookmark feeds
Router\get_action('bookmark-feed', function () {
    // Select database if the parameter is set
    $database = Request\param('database');

    if (!empty($database)) {
        Model\Database\select($database);
    }

    // Check token
    $feed_token = Model\Config\get('feed_token');
    $request_token = Request\param('token');

    if ($feed_token !== $request_token) {
        Response\text('Access Forbidden', 403);
    }

    // Build Feed
    $bookmarks = Model\Bookmark\get_all_items();

    $feedBuilder = AtomFeedBuilder::create()
        ->withTitle(t('Bookmarks').' - Miniflux')
        ->withFeedUrl(Helper\get_current_base_url().'?action=bookmark-feed&token='.urlencode($feed_token))
        ->withSiteUrl(Helper\get_current_base_url())
        ->withDate(new DateTime())
    ;

    foreach ($bookmarks as $bookmark) {
        $article = Model\Item\get($bookmark['id']);
        $articleDate = new DateTime();
        $articleDate->setTimestamp($article['updated']);

        $feedBuilder
            ->withItem(AtomItemBuilder::create($feedBuilder)
                ->withId($article['id'])
                ->withTitle($article['title'])
                ->withUrl($article['url'])
                ->withUpdatedDate($articleDate)
                ->withPublishedDate($articleDate)
                ->withContent($article['content'])
            );
    }

    Response\xml($feedBuilder->build());
});
