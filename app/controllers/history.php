<?php

use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Model;

// Display history page
Router\get_action('history', function () {
    $order = Request\param('order', 'updated');
    $direction = Request\param('direction', Model\Config\get('items_sorting_direction'));
    $offset = Request\int_param('offset', 0);
    $group_id = Request\int_param('group_id', null);
    $feed_ids = array();
    
    if ($group_id !== null) {
        $feed_ids = Model\Group\get_feeds_by_group($group_id);
    }

    $items = Model\Item\get_all_by_status(
        'read',
        $feed_ids,
        $offset,
        Model\Config\get('items_per_page'),
        $order,
        $direction
    );

    $nb_items = Model\Item\count_by_status('read', $feed_ids);

    Response\html(Template\layout('history', array(
        'favicons' => Model\Favicon\get_item_favicons($items),
        'original_marks_read' => Model\Config\get('original_marks_read'),
        'items' => $items,
        'order' => $order,
        'direction' => $direction,
        'display_mode' => Model\Config\get('items_display_mode'),
        'item_title_link' => Model\Config\get('item_title_link'),
        'group_id' => $group_id,
        'nb_items' => $nb_items,
        'nb_unread_items' => Model\Item\count_by_status('unread'),
        'offset' => $offset,
        'items_per_page' => Model\Config\get('items_per_page'),
        'nothing_to_read' => Request\int_param('nothing_to_read'),
        'menu' => 'history',
        'groups' => Model\Group\get_all(),
        'title' => t('History').' ('.$nb_items.')'
    )));
});

// Confirmation box to flush history
Router\get_action('confirm-flush-history', function () {
    $group_id = Request\int_param('group_id', null);
    
    Response\html(Template\layout('confirm_flush_items', array(
        'group_id' => $group_id,
        'nb_unread_items' => Model\Item\count_by_status('unread'),
        'menu' => 'history',
        'title' => t('Confirmation')
    )));
});

// Flush history
Router\get_action('flush-history', function () {
    $group_id = Request\int_param('group_id', null);
    
    if ($group_id !== null) {
        Model\ItemGroup\mark_all_as_removed($group_id);
    } else {
        Model\Item\mark_all_as_removed();
    }
    
    Response\redirect('?action=history');
});
