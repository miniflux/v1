<?php

function tne()
{
    return call_user_func_array('\Miniflux\Translator\translate_no_escaping', func_get_args());
}

function t()
{
    return call_user_func_array('\Miniflux\Translator\translate', func_get_args());
}

function c()
{
    return call_user_func_array('\Miniflux\Translator\currency', func_get_args());
}

function n()
{
    return call_user_func_array('\Miniflux\Translator\number', func_get_args());
}

function dt()
{
    return call_user_func_array('\Miniflux\Translator\datetime', func_get_args());
}

function get_cli_option($option, array $options)
{
    $value = null;

    if (! empty($options[$option]) && ctype_digit($options[$option])) {
        $value = (int) $options[$option];
    }

    return $value;
}
