<?php

namespace Model\Service;

use Model\Config;
use Model\Item;
use PicoFeed\Client\Client;
use PicoFeed\Client\ClientException;

// Sync the item to an external service
function push($item_id)
{
    if ((bool) Config\get('pinboard_enabled')) {
        pinboard_add(Item\get($item_id));
    }
}

// Add a Pinboard bookmark
function pinboard_add(array $item)
{
    return pinboard_api('/posts/add', array(
        'url' => $item['url'],
        'description' => $item['title'],
        'tags' => Config\get('pinboard_tags'),
    ));
}

// Pinboard API client
function pinboard_api($method, array $params)
{
    try {
        $params += array('auth_token' => Config\get('pinboard_token'), 'format' => 'json');
        $url = 'https://api.pinboard.in/v1'.$method.'?'.http_build_query($params);

        $client = Client::getInstance();
        $client->setUserAgent(Config\HTTP_USER_AGENT);
        $client->execute($url);

        $response = json_decode($client->getContent(), true);

        return is_array($response) && $response['result_code'] === 'done';
    }
    catch (ClientException $e) {
        return false;
    }
}
