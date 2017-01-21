<?php

namespace Miniflux\Controller;

use Miniflux\Model;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Helper;

Router\get_action('about', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('config/about', array(
        'csrf'   => Helper\generate_csrf(),
        'config' => Model\Config\get_all($user_id),
        'user'   => Model\User\get_user_by_id_without_password($user_id),
        'menu'   => 'config',
        'title'  => t('About'),
    )));
});
