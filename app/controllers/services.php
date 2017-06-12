<?php

namespace Miniflux\Controller;

use Miniflux\Model;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Helper;

// Display bookmark services page
Router\get_action('services', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('config/services', array(
        'errors' => array(),
        'values' => Model\Config\get_all($user_id) + array('csrf' => Helper\generate_csrf()),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});

// Update bookmark services
Router\post_action('services', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $values = Request\values() + array('pinboard_enabled' => 0,
                                       'pinboard_mark_unread' => 0,
                                       'instapaper_enabled' => 0,
                                       'wallabag_enabled' => 0,
                                       'shaarli_enabled' => 0,
                                       'shaarli_private' => 0);
    Helper\check_csrf_values($values);

    if (Model\Config\save($user_id, $values)) {
        SessionStorage::getInstance()->setFlashMessage(t('Your preferences are updated.'));
    } else {
        SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to update your preferences.'));
    }

    Response\redirect('?action=services');
});
