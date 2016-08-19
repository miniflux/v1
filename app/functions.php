<?php

function tne()
{
    return call_user_func_array('\Translator\translate_no_escaping', func_get_args());
}

function t()
{
    return call_user_func_array('\Translator\translate', func_get_args());
}

function c()
{
    return call_user_func_array('\Translator\currency', func_get_args());
}

function n()
{
    return call_user_func_array('\Translator\number', func_get_args());
}

function dt()
{
    return call_user_func_array('\Translator\datetime', func_get_args());
}
