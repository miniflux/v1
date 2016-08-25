<?php

namespace Miniflux\Validator\User;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Miniflux\Model\Config;
use Miniflux\Model\User as UserModel;
use Miniflux\Model\Database as DatabaseModel;
use Miniflux\Model\RememberMe;
use Miniflux\Request;

function validate_creation(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('name', t('The database name is required')),
        new Validators\AlphaNumeric('name', t('The name must have only alpha-numeric characters')),
        new Validators\Required('username', t('The user name is required')),
        new Validators\MaxLength('username', t('The maximum length is 50 characters'), 50),
        new Validators\Required('password', t('The password is required')),
        new Validators\MinLength('password', t('The minimum length is 6 characters'), 6),
        new Validators\Required('confirmation', t('The confirmation is required')),
        new Validators\Equals('password', 'confirmation', t('Passwords don\'t match')),
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
        $credentials = UserModel\get_credentials();

        if ($credentials && $credentials['username'] === $values['username'] && password_verify($values['password'], $credentials['password'])) {
            UserModel\set_last_login();
            $_SESSION['loggedin'] = true;
            $_SESSION['config'] = Config\get_all();

            // Setup the remember me feature
            if (! empty($values['remember_me'])) {
                $cookie = RememberMe\create(DatabaseModel\select(), $values['username'], Request\get_ip_address(), Request\get_user_agent());
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
