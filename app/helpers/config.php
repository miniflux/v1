<?php

namespace Miniflux\Helper;

use Miniflux\Model;
use Miniflux\Session\SessionStorage;

function config($parameter, $default = null)
{
    $value = null;
    $session = SessionStorage::getInstance();

    if ($session->isLogged()) {
        $cache = $session->getConfig();

        if (empty($cache)) {
            $cache = Model\Config\get_all($session->getUserId());
            $session->setConfig($cache);
        }

        if (array_key_exists($parameter, $cache)) {
            $value = $cache[$parameter];
        }
    }

    if ($value === null) {
        $value = $default;
    }

    return $value;
}

function bool_config($parameter, $default = false)
{
    return (bool) config($parameter, $default);
}

function int_config($parameter, $default = false)
{
    return (int) config($parameter, $default);
}
