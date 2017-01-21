<?php

namespace Miniflux\Controller;

use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Model;

// Display history page
Router\get_action('history', function () {
    $params = items_list(Model\Item\STATUS_READ);

    Response\html(Template\layout('history/items', $params + array(
        'title' => t('History') . ' (' . $params['nb_items'] . ')',
        'menu'  => 'history',
    )));
});

// Confirmation box to flush history
Router\get_action('confirm-flush-history', function () {
    $group_id = Request\int_param('group_id');
    
    Response\html(Template\layout('history/flush', array(
        'group_id' => $group_id,
        'menu' => 'history',
        'title' => t('Confirmation')
    )));
});

// Flush history
Router\get_action('flush-history', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $group_id = Request\int_param('group_id');
    
    if ($group_id !== 0) {
        Model\ItemGroup\change_items_status($user_id, $group_id, Model\Item\STATUS_READ, Model\Item\STATUS_REMOVED);
    } else {
        Model\Item\change_items_status($user_id, Model\Item\STATUS_READ, Model\Item\STATUS_REMOVED);
    }
    
    Response\redirect('?action=history');
});
