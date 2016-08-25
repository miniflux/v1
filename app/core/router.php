<?php

namespace Miniflux\Router;

use Closure;

function bootstrap()
{
    $files = func_get_args();
    $base_path = array_shift($files);

    foreach ($files as $file) {
        require $base_path.'/'.$file.'.php';
    }
}

// Execute a callback before each action
function before($value = null)
{
    static $before_callback = null;

    if (is_callable($value)) {
        $before_callback = $value;
    } elseif (is_callable($before_callback)) {
        $before_callback($value);
    }
}

// Execute a callback before a specific action
function before_action($name, $value = null)
{
    static $callbacks = array();

    if (is_callable($value)) {
        $callbacks[$name] = $value;
    } elseif (isset($callbacks[$name]) && is_callable($callbacks[$name])) {
        $callbacks[$name]($value);
    }
}

// Execute an action
function action($name, Closure $callback)
{
    $handler = isset($_GET['action']) ? $_GET['action'] : 'default';

    if ($handler === $name) {
        before($name);
        before_action($name);
        $callback();
    }
}

// Execute an action only for POST requests
function post_action($name, Closure $callback)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        action($name, $callback);
    }
}

// Execute an action only for GET requests
function get_action($name, Closure $callback)
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        action($name, $callback);
    }
}

// Run when no action have been executed before
function notfound(Closure $callback)
{
    before('notfound');
    before_action('notfound');
    $callback();
}
