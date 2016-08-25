<?php

namespace Miniflux\Helper;

function generate_csrf()
{
    if (! isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = array();
    }

    $token = generate_token();
    $_SESSION['csrf'][$token] = true;

    return $token;
}

function check_csrf_values(array &$values)
{
    if (empty($values['csrf']) || ! isset($_SESSION['csrf'][$values['csrf']])) {
        $values = array();
    } else {
        unset($_SESSION['csrf'][$values['csrf']]);
        unset($values['csrf']);
    }
}

function check_csrf($token)
{
    if (isset($_SESSION['csrf'][$token])) {
        unset($_SESSION['csrf'][$token]);
        return true;
    }

    return false;
}
