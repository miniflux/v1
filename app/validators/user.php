<?php

namespace Miniflux\Validator\User;

use Miniflux\Session\SessionStorage;
use Miniflux\Model\User as UserModel;
use Miniflux\Model\RememberMe;
use Miniflux\Request;
use PicoDb\Database;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

function validate_profile_modification($user_id, array $values)
{
    list($result, $errors) = validate_modification($values);

    if ($result) {
        $user = UserModel\get_user_by_id($user_id);
        $password = ! empty($values['current_password']) ? $values['current_password'] : '';

        if (! password_verify($password, $user['password'])) {
            $result = false;
            $errors['current_password'][] = t('Wrong password');
        }
    }

    return array(
        $result,
        $errors,
    );
}

function validate_modification(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('id', t('The user id required')),
        new Validators\Required('username', t('The user name is required')),
        new Validators\MaxLength('username', t('The maximum length is 50 characters'), 50),
        new Validators\MinLength('password', t('The minimum length is 6 characters'), 6),
        new Validators\Equals('password', 'confirmation', t('Passwords don\'t match')),
        new Validators\Unique('username', t('The username must be unique'), Database::getInstance('db')->getConnection(), 'users', 'id'),
    ));

    return array(
        $v->execute(),
        $v->getErrors()
    );
}

function validate_creation(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('username', t('The user name is required')),
        new Validators\MaxLength('username', t('The maximum length is 50 characters'), 50),
        new Validators\Required('password', t('The password is required')),
        new Validators\MinLength('password', t('The minimum length is 6 characters'), 6),
        new Validators\Required('confirmation', t('The confirmation is required')),
        new Validators\Equals('password', 'confirmation', t('Passwords don\'t match')),
        new Validators\Unique('username', t('The username must be unique'), Database::getInstance('db')->getConnection(), 'users', 'id'),
    ));

    return array(
        $v->execute(),
        $v->getErrors()
    );
}

function validate_login(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('username', t('The user name is required')),
        new Validators\MaxLength('username', t('The maximum length is 50 characters'), 50),
        new Validators\Required('password', t('The password is required'))
    ));

    $result = $v->execute();
    $errors = $v->getErrors();

    if ($result) {
        $user = UserModel\get_user_by_username($values['username']);

        if (! empty($user) && password_verify($values['password'], $user['password'])) {
            SessionStorage::getInstance()->setUser($user);
            UserModel\set_last_login_date($user['id']);

            // Setup the remember me feature
            if (! empty($values['remember_me'])) {
                $cookie = RememberMe\create($user['id'], Request\get_ip_address(), Request\get_user_agent());
                RememberMe\write_cookie($cookie['token'], $cookie['sequence'], $cookie['expiration']);
            }
        } else {
            $result = false;
            $errors['login'] = t('Bad username or password');
        }
    }

    return array(
        $result,
        $errors
    );
}
