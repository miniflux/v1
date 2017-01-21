<?php

namespace Miniflux\Controller;

use Miniflux\Session\SessionManager;
use Miniflux\Session\SessionStorage;
use Miniflux\Validator;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Model\RememberMe;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Model;

// Logout and destroy session
Router\get_action('logout', function () {
    SessionStorage::getInstance()->flush();
    SessionManager::close();
    RememberMe\destroy();
    Response\redirect('?action=login');
});

// Display form login
Router\get_action('login', function () {
    if (SessionStorage::getInstance()->isLogged()) {
        Response\redirect('?action=unread');
    }

    Response\html(Template\load('auth/login', array(
        'errors' => array(),
        'values' => array(
            'csrf' => Helper\generate_csrf(),
        ),
    )));
});

// Check credentials and redirect to unread items
Router\post_action('login', function () {
    $values = Request\values();
    Helper\check_csrf_values($values);
    list($valid, $errors) = Validator\User\validate_login($values);

    if ($valid) {
        Response\redirect('?action=unread');
    }

    Response\html(Template\load('auth/login', array(
        'errors' => $errors,
        'values' => $values + array('csrf' => Helper\generate_csrf()),
    )));
});
