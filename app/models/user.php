<?php

namespace Miniflux\Model\User;

use PicoDb\Database;
use Miniflux\Model;
use Miniflux\Helper;

const TABLE = 'users';

function create_user($username, $password, $is_admin = false)
{
    $username = trim($username);
    $password = trim($password);
    list($fever_token, $fever_api_key) = generate_fever_api_key($username);

    return Database::getInstance('db')
        ->table(TABLE)
        ->persist(array(
            'username'          => $username,
            'password'          => password_hash($password, PASSWORD_BCRYPT),
            'is_admin'          => (int) $is_admin,
            'api_token'         => Helper\generate_token(),
            'bookmarklet_token' => Helper\generate_token(),
            'cronjob_token'     => Helper\generate_token(),
            'feed_token'        => Helper\generate_token(),
            'fever_token'       => $fever_token,
            'fever_api_key'     => $fever_api_key,
        ));
}

function update_user($user_id, $username, $password = null, $is_admin = null)
{
    $user = get_user_by_id($user_id);
    $values = array();

    if ($user['username'] !== $username) {
        list($fever_token, $fever_api_key) = generate_fever_api_key($user['username']);

        $values['username'] = $username;
        $values['fever_token'] = $fever_token;
        $values['fever_api_key'] = $fever_api_key;
    }

    if ($password !== null) {
        $values['password'] = password_hash($password, PASSWORD_BCRYPT);
        Model\RememberMe\remove_user_sessions($user_id);
    }

    if ($is_admin !== null) {
        $values['is_admin'] = (int) $is_admin;
    }

    if (! empty($values)) {
        return Database::getInstance('db')
            ->table(TABLE)
            ->eq('id', $user_id)
            ->update($values);
    }

    return true;
}

function regenerate_tokens($user_id)
{
    $user = get_user_by_id($user_id);
    list($fever_token, $fever_api_key) = generate_fever_api_key($user['username']);

    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('id', $user_id)
        ->update(array(
            'api_token'         => Helper\generate_token(),
            'bookmarklet_token' => Helper\generate_token(),
            'cronjob_token'     => Helper\generate_token(),
            'feed_token'        => Helper\generate_token(),
            'fever_token'       => $fever_token,
            'fever_api_key'     => $fever_api_key,
        ));
}

function remove_user($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('id', $user_id)
        ->remove();
}

function generate_fever_api_key($username)
{
    $token = Helper\generate_token();
    $api_key = md5($username . ':' . $token);
    return array($token, $api_key);
}

function get_all_users()
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->columns('id', 'username', 'is_admin', 'last_login')
        ->asc('username')
        ->asc('id')
        ->findAll();
}

function get_all_user_ids()
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->asc('id')
        ->findAllByColumn('id');
}

function get_user_by_id($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('id', $user_id)
        ->findOne();
}

function get_user_by_id_without_password($user_id)
{
    $user = Database::getInstance('db')
        ->table(TABLE)
        ->eq('id', $user_id)
        ->findOne();

    unset($user['password']);
    return $user;
}

function get_user_by_username($username)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('username', $username)
        ->findOne();
}

function get_user_by_token($key, $token)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq($key, $token)
        ->findOne();
}

function set_last_login_date($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('id', $user_id)
        ->update(array('last_login' => time()));
}
