<?php

namespace Miniflux\Handler\Service;

use PicoFeed\Client\Client;
use PicoFeed\Client\ClientException;
use Miniflux\Model\Config;
use Miniflux\Model\Item;

function sync($item_id)
{
    $item = Item\get($item_id);

    if ((bool) Config\get('pinboard_enabled')) {
        pinboard_sync($item);
    }

    if ((bool) Config\get('instapaper_enabled')) {
        instapaper_sync($item);
    }
}

function instapaper_sync(array $item)
{
    $params = array(
        'username' => Config\get('instapaper_username'),
        'password' => Config\get('instapaper_password'),
        'url' => $item['url'],
        'title' => $item['title'],
    );

    $url = 'https://www.instapaper.com/api/add?'.http_build_query($params);

    $client = api_call($url);

    if ($client !== false) {
        return $client->getStatusCode() === 201;
    }

    return false;
}

function pinboard_sync(array $item)
{
    $params = array(
        'auth_token' => Config\get('pinboard_token'),
        'format' => 'json',
        'url' => $item['url'],
        'description' => $item['title'],
        'tags' => Config\get('pinboard_tags'),
    );

    $url = 'https://api.pinboard.in/v1/posts/add?'.http_build_query($params);

    $client = api_call($url);

    if ($client !== false) {
        $response = json_decode($client->getContent(), true);
        return is_array($response) && $response['result_code'] === 'done';
    }

    return false;
}

function api_call($url)
{
    try {
        $client = Client::getInstance();
        $client->setUserAgent(Config\HTTP_USER_AGENT);
        $client->execute($url);
        return $client;
    } catch (ClientException $e) {
        return false;
    }
}
