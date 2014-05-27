<?php

namespace Model\User;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use PicoDb\Database;
use Model\Config;
use Model\RememberMe;
use Model\Database as DatabaseModel;

// Get a user by username
function get($username)
{
    return Database::get('db')
        ->table('config')
        ->columns('username', 'password', 'language')
        ->eq('username', $username)
        ->findOne();
}

// Validate authentication
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

        $user = get($values['username']);

        if ($user && \password_verify($values['password'], $user['password'])) {

            unset($user['password']);

            $_SESSION['user'] = $user;
            $_SESSION['config'] = Config\get_all();

            // Setup the remember me feature
            if (! empty($values['remember_me'])) {
                $credentials = RememberMe\create(DatabaseModel\select(), $values['username'], Config\get_ip_address(), Config\get_user_agent());
                RememberMe\write_cookie($credentials['token'], $credentials['sequence'], $credentials['expiration']);
            }
        }
        else {

            $result = false;
            $errors['login'] = t('Bad username or password');
        }
    }

    return array(
        $result,
        $errors
    );
}
