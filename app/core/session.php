<?php

namespace Miniflux\Session;

use Miniflux\Helper;

class SessionManager
{
    const SESSION_LIFETIME = 2678400;

    public static function open($base_path = '/', $save_path = '', $duration = self::SESSION_LIFETIME)
    {
        if ($save_path !== '') {
            session_save_path($save_path);
        }

        // HttpOnly and secure flags for session cookie
        session_set_cookie_params(
            $duration,
            $base_path ?: '/',
            null,
            Helper\is_secure_connection(),
            true
        );

        // Avoid session id in the URL
        ini_set('session.use_only_cookies', true);

        // Ensure session ID integrity
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '32');
        ini_set('session.hash_bits_per_character', 6);

        // Custom session name
        session_name('MX_SID');

        session_start();

        // Regenerate the session id to avoid session fixation issue
        if (empty($_SESSION['__validated'])) {
            session_regenerate_id(true);
            $_SESSION['__validated'] = 1;
        }
    }

    public static function close()
    {
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

        session_unset();
        session_destroy();
    }
}


class SessionStorage
{
    private static $instance = null;

    public function __construct(array $session = null)
    {
        if (! isset($_SESSION)) {
            $_SESSION = array();
        }

        $_SESSION = $session ?: $_SESSION;
    }

    public static function getInstance(array $session = null)
    {
        if (self::$instance === null) {
            self::$instance = new static($session);
        }

        return self::$instance;
    }

    public function flush()
    {
        $_SESSION = array();
        return $this;
    }

    public function flushConfig()
    {
        unset($_SESSION['config']);
        return $this;
    }

    public function setConfig(array $config)
    {
        $_SESSION['config'] = $config;
        return $this;
    }

    public function getConfig()
    {
        return $this->getValue('config');
    }

    public function setUser(array $user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = (bool) $user['is_admin'];
        return $this;
    }

    public function getUserId()
    {
        return $this->getValue('user_id');
    }

    public function getUsername()
    {
        return $this->getValue('username');
    }

    public function isAdmin()
    {
        return $this->getValue('is_admin');
    }

    public function isLogged()
    {
        return $this->getValue('user_id') !== null;
    }

    public function setFlashMessage($message)
    {
        $_SESSION['flash_message'] = $message;
        return $this;
    }

    public function setFlashErrorMessage($message)
    {
        $_SESSION['flash_error_message'] = $message;
        return $this;
    }

    public function getFlashMessage()
    {
        $message = $this->getValue('flash_message');
        unset($_SESSION['flash_message']);
        return $message;
    }

    public function getFlashErrorMessage()
    {
        $message = $this->getValue('flash_error_message');
        unset($_SESSION['flash_error_message']);
        return $message;
    }

    protected function getValue($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
}
