<?php

namespace Miniflux\Model\User;

use PicoDb\Database;
use Miniflux\Session;
use Miniflux\Request;
use Miniflux\Model\Config;
use Miniflux\Model\RememberMe;
use Miniflux\Model\Database as DatabaseModel;

// Check if the user is logged in
function is_loggedin()
{
    return ! empty($_SESSION['loggedin']);
}

// Destroy the session and the rememberMe cookie
function logout()
{
    RememberMe\destroy();
    Session\close();
}

// Get the credentials from the current selected database
function get_credentials()
{
    return Database::getInstance('db')
        ->hashtable('settings')
        ->get('username', 'password');
}

// Set last login date
function set_last_login()
{
    return Database::getInstance('db')
        ->hashtable('settings')
        ->put(array('last_login' => time()));
}
