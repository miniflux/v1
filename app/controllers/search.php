<?php

namespace Miniflux\Controller;

use Miniflux\Helper;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Model;

Router\get_action('search', function() {
    $user_id = SessionStorage::getInstance()->getUserId();
    $text = Request\param('text', '');
    $offset = Request\int_param('offset', 0);

    $items = array();
    $nb_items = 0;
    if ($text) {
        $items = Model\ItemSearch\get_all_items($user_id, $text, $offset, Helper\config('items_per_page'));
        $nb_items = Model\ItemSearch\count_items($user_id, $text);
    }

    Response\html(Template\layout('search/items', array(
        'favicons' => Model\Favicon\get_items_favicons($items),
        'original_marks_read' => Helper\config('original_marks_read'),
        'text' => $text,
        'items' => $items,
        'order' => '',
        'direction' => '',
        'display_mode' => Helper\config('items_display_mode'),
        'item_title_link' => Helper\config('item_title_link'),
        'group_id' => array(),
        'nb_items' => $nb_items,
        'offset' => $offset,
        'items_per_page' => Helper\config('items_per_page'),
        'nothing_to_read' => Request\int_param('nothing_to_read'),
        'menu' => 'search',
        'title' => t('Search').' ('.$nb_items.')'
    )));
});
