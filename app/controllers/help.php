<?php

namespace Miniflux\Controller;

use Miniflux\Model;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;

// Display help page
Router\get_action('help', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('config/help', array(
        'config' => Model\Config\get_all($user_id),
        'menu' => 'config',
        'title' => t('Help')
    )));
});
