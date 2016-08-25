<?php

namespace Miniflux\Request;

function get_server_variable($variable)
{
    return isset($_SERVER[$variable]) ? $_SERVER[$variable] : '';
}

function param($name, $default_value = null)
{
    return isset($_GET[$name]) ? $_GET[$name] : $default_value;
}

function int_param($name, $default_value = 0)
{
    return isset($_GET[$name]) && ctype_digit($_GET[$name]) ? (int) $_GET[$name] : $default_value;
}

function value($name)
{
    $values = values();
    return isset($values[$name]) ? $values[$name] : null;
}

function values()
{
    if (! empty($_POST)) {
        return $_POST;
    }

    $result = json_decode(body(), true);

    if ($result) {
        return $result;
    }

    return array();
}

function body()
{
    return file_get_contents('php://input');
}

function file_content($field)
{
    if (isset($_FILES[$field])) {
        return file_get_contents($_FILES[$field]['tmp_name']);
    }

    return '';
}

function uri()
{
    return $_SERVER['REQUEST_URI'];
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function get_user_agent()
{
    return get_server_variable('HTTP_USER_AGENT') ?: 'Unknown';
}

function get_ip_address()
{
    $keys = array(
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    );

    foreach ($keys as $key) {
        $value = get_server_variable($key);
        if ($value !== '') {
            foreach (explode(',', $value) as $ip_address) {
                return trim($ip_address);
            }
        }
    }

    return 'Unknown';
}
