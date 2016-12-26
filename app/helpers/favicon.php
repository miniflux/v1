<?php

namespace Miniflux\Helper;

function favicon_extension($type)
{
    $types = array(
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/x-icon' => '.ico',
        'image/jpeg' => '.jpg',
        'image/jpg' => '.jpg'
    );

    if (array_key_exists($type, $types)) {
        return $types[$type];
    } else {
        return '.ico';
    }
}

function favicon(array $favicons, $feed_id)
{
    if (! empty($favicons[$feed_id])) {
        return '<img src="'.FAVICON_URL_PATH.'/'.$favicons[$feed_id]['hash'].favicon_extension($favicons[$feed_id]['type']).'" class="favicon">';
    }

    return '';
}
