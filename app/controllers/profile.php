<?php

namespace Miniflux\Controller;

use Miniflux\Model;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Validator;

Router\get_action('profile', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('users/profile', array(
        'errors' => array(),
        'values' => Model\User\get_user_by_id_without_password($user_id) + array('csrf' => Helper\generate_csrf()),
        'menu' => 'config',
        'title' => t('User Profile')
    )));
});

Router\post_action('profile', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $values = Request\values();
    Helper\check_csrf_values($values);
    list($valid, $errors) = Validator\User\validate_profile_modification($user_id, $values);

    if ($valid) {
        $new_password = empty($values['password']) ? null : $values['password'];
        if (Model\User\update_user($user_id, $values['username'], $new_password)) {
            SessionStorage::getInstance()->setFlashMessage(t('Your preferences are updated.'));
            SessionStorage::getInstance()->setUser(Model\User\get_user_by_id($user_id));
        } else {
            SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to update your preferences.'));
        }

        Response\redirect('?action=profile');
    }

    Response\html(Template\layout('users/profile', array(
        'errors' => $errors,
        'values' => Model\User\get_user_by_id_without_password($user_id) + array('csrf' => Helper\generate_csrf()),
        'menu' => 'config',
        'title' => t('User Profile')
    )));
});
