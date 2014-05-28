<?php

namespace PicoFarad\Session;

const SESSION_LIFETIME = 0;


function open($base_path = '/', $save_path = '')
{
    if ($save_path !== '') session_save_path($save_path);

    // HttpOnly and secure flags for session cookie
    session_set_cookie_params(
        SESSION_LIFETIME,
        $base_path ?: '/',
        null,
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        true
    );

    // Avoid session id in the URL
    ini_set('session.use_only_cookies', true);

    // Ensure session ID integrity
    ini_set('session.entropy_file', '/dev/urandom');
    ini_set('session.entropy_length', '32');
    ini_set('session.hash_bits_per_character', 6);

    // Custom session name
    session_name('__$');

    session_start();

    // Regenerate the session id to avoid session fixation issue
    if (empty($_SESSION['__validated'])) {
        session_regenerate_id(true);
        $_SESSION['__validated'] = 1;
    }
}


function close()
{
    // Flush all sessions variables
    $_SESSION = array();

    // Destroy the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Destroy session data
    session_destroy();
}


function flash($message)
{
    $_SESSION['flash_message'] = $message;
}


function flash_error($message)
{
    $_SESSION['flash_error_message'] = $message;
}