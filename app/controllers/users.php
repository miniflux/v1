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

Router\get_action('users', function () {
    if (! SessionStorage::getInstance()->isAdmin()) {
        Response\text('Access Forbidden', 403);
    }

    Response\html(Template\layout('users/list', array(
        'users'      => Model\User\get_all_users(),
        'menu'       => 'config',
        'title'      => t('Users'),
    )));
});

Router\get_action('new-user', function () {
    if (! SessionStorage::getInstance()->isAdmin()) {
        Response\text('Access Forbidden', 403);
    }

    Response\html(Template\layout('users/create', array(
        'values' => array('csrf' => Helper\generate_csrf()),
        'errors' => array(),
        'menu'   => 'config',
        'title'  => t('New User'),
    )));
});

Router\post_action('new-user', function () {
    if (! SessionStorage::getInstance()->isAdmin()) {
        Response\text('Access Forbidden', 403);
    }

    $values = Request\values() + array('is_admin' => 0);
    Helper\check_csrf_values($values);
    list($valid, $errors) = Validator\User\validate_creation($values);

    if ($valid) {
        if (Model\User\create_user($values['username'], $values['password'], (bool) $values['is_admin'])) {
            SessionStorage::getInstance()->setFlashMessage(t('New user created successfully.'));
        } else {
            SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to create this user.'));
        }

        Response\redirect('?action=users');
    }

    Response\html(Template\layout('users/create', array(
        'values' => $values + array('csrf' => Helper\generate_csrf()),
        'errors' => $errors,
        'menu'   => 'config',
        'title'  => t('New User'),
    )));
});

Router\get_action('edit-user', function () {
    if (! SessionStorage::getInstance()->isAdmin()) {
        Response\text('Access Forbidden', 403);
    }

    $user = Model\User\get_user_by_id_without_password(Request\int_param('user_id'));

    if (empty($user)) {
        Response\redirect('?action=users');
    }

    Response\html(Template\layout('users/edit', array(
        'values' => $user + array('csrf' => Helper\generate_csrf()),
        'errors' => array(),
        'menu'   => 'config',
        'title'  => t('Edit User'),
    )));
});

Router\post_action('edit-user', function () {
    if (! SessionStorage::getInstance()->isAdmin()) {
        Response\text('Access Forbidden', 403);
    }

    $values = Request\values() + array('is_admin' => 0);
    Helper\check_csrf_values($values);
    list($valid, $errors) = Validator\User\validate_modification($values);

    if ($valid) {
        $new_password = empty($values['password']) ? null : $values['password'];
        $is_admin = $values['is_admin'] == 1 ? 1 : 0;
        if (Model\User\update_user($values['id'], $values['username'], $new_password, $is_admin)) {
            SessionStorage::getInstance()->setFlashMessage(t('User modified successfully.'));
        } else {
            SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to edit this user.'));
        }

        Response\redirect('?action=users');
    }

    Response\html(Template\layout('users/edit', array(
        'values' => $values + array('csrf' => Helper\generate_csrf()),
        'errors' => $errors,
        'menu'   => 'config',
        'title'  => t('Edit User'),
    )));
});

Router\get_action('confirm-remove-user', function () {
    if (! SessionStorage::getInstance()->isAdmin()) {
        Response\text('Access Forbidden', 403);
    }

    Response\html(Template\layout('users/remove', array(
        'user'       => Model\User\get_user_by_id_without_password(Request\int_param('user_id')),
        'csrf_token' => Helper\generate_csrf(),
        'menu'       => 'config',
        'title'      => t('Remove User'),
    )));
});

Router\get_action('remove-user', function () {
    if (! SessionStorage::getInstance()->isAdmin() || ! Helper\check_csrf(Request\param('csrf'))) {
        Response\text('Access Forbidden', 403);
    }

    Model\User\remove_user(Request\int_param('user_id'));
    Response\redirect('?action=users');
});