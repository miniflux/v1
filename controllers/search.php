<?php

// Display search results page
Router\get_action('search', function() {

    $text = Request\param('text', '');
    $offset = Request\int_param('offset', 0);

    $items = array();
    $nb_items = 0;
    if ($text) {
        $items = Model\Item\search_all($text, $offset, Model\Config\get('items_per_page'));
        $nb_items = Model\Item\count_by_search($text);
    }

    Response\html(Template\layout('search', array(
        'favicons' => Model\Favicon\get_item_favicons($items),
        'original_marks_read' => Model\Config\get('original_marks_read'),
        'text' => $text,
        'items' => $items,
        'order' => '',
        'direction' => '',
        'display_mode' => Model\Config\get('items_display_mode'),
        'item_title_link' => Model\Config\get('item_title_link'),
        'group_id' => array(),
        'nb_items' => $nb_items,
        'nb_unread_items' => Model\Item\count_by_status('unread'),
        'offset' => $offset,
        'items_per_page' => Model\Config\get('items_per_page'),
        'nothing_to_read' => Request\int_param('nothing_to_read'),
        'menu' => 'search',
        'title' => t('Search').' ('.$nb_items.')'
    )));
});
