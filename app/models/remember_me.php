<?php

namespace Miniflux\Model\RememberMe;

use Miniflux\Session\SessionStorage;
use Miniflux\Helper;
use Miniflux\Model\User;
use PicoDb\Database;

const TABLE       = 'remember_me';
const COOKIE_NAME = '_R_';
const EXPIRATION  = 5184000;

function get_record($token, $sequence)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('token', $token)
        ->eq('sequence', $sequence)
        ->gt('expiration', time())
        ->findOne();
}

function authenticate()
{
    $credentials = read_cookie();

    if ($credentials !== false) {
        $record = get_record($credentials['token'], $credentials['sequence']);

        if ($record) {
            $user = User\get_user_by_id($record['user_id']);
            SessionStorage::getInstance()->setUser($user);
            return true;
        }
    }

    return false;
}

function destroy()
{
    $credentials = read_cookie();

    if ($credentials !== false) {
        Database::getInstance('db')
             ->table(TABLE)
             ->eq('token', $credentials['token'])
             ->remove();
    }

    delete_cookie();
}

function create($user_id, $ip, $user_agent)
{
    $token = hash('sha256', $user_id.$user_agent.$ip.Helper\generate_token());
    $sequence = Helper\generate_token();
    $expiration = time() + EXPIRATION;

    cleanup();

    Database::getInstance('db')
         ->table(TABLE)
         ->insert(array(
            'user_id' => $user_id,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'token' => $token,
            'sequence' => $sequence,
            'expiration' => $expiration,
            'date_creation' => time(),
         ));

    return array(
        'token' => $token,
        'sequence' => $sequence,
        'expiration' => $expiration,
    );
}

function cleanup()
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->lt('expiration', time())
        ->remove();
}

function remove_user_sessions($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->remove();
}

function update($token)
{
    $new_sequence = Helper\generate_token();

    Database::getInstance('db')
         ->table(TABLE)
         ->eq('token', $token)
         ->update(array('sequence' => $new_sequence));

    return $new_sequence;
}

function encode_cookie($token, $sequence)
{
    return implode('|', array($token, $sequence));
}

function decode_cookie($value)
{
    @list($token, $sequence) = explode('|', $value);

    return array(
        'token' => $token,
        'sequence' => $sequence,
    );
}

function has_cookie()
{
    return ! empty($_COOKIE[COOKIE_NAME]);
}

function write_cookie($token, $sequence, $expiration)
{
    setcookie(
        COOKIE_NAME,
        encode_cookie($token, $sequence),
        $expiration,
        BASE_URL_DIRECTORY,
        null,
        Helper\is_secure_connection(),
        true
    );
}

function read_cookie()
{
    if (empty($_COOKIE[COOKIE_NAME])) {
        return false;
    }

    return decode_cookie($_COOKIE[COOKIE_NAME]);
}

function delete_cookie()
{
    setcookie(
        COOKIE_NAME,
        '',
        time() - 3600,
        BASE_URL_DIRECTORY,
        null,
        Helper\is_secure_connection(),
        true
    );
}
