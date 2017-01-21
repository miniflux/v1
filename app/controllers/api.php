<?php

namespace Miniflux\Controller;

use Miniflux\Model;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;

Router\get_action('api', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('config/api', array(
        'config' => Model\Config\get_all($user_id),
        'user'   => Model\User\get_user_by_id_without_password($user_id),
        'menu'   => 'config',
        'title'  => t('Preferences'),
    )));
});
