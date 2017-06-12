<?php

namespace Miniflux\Handler\Service;

use Miniflux\Model;
use Miniflux\Helper;
use PicoFeed\Client\Client;
use PicoFeed\Client\ClientException;

function sync($user_id, $item_id)
{
    $item = Model\Item\get_item($user_id, $item_id);

    if (Helper\bool_config('pinboard_enabled')) {
        pinboard_sync($item);
    }

    if (Helper\bool_config('instapaper_enabled')) {
        instapaper_sync($item);
    }

    if (Helper\bool_config('wallabag_enabled')) {
        wallabag_sync($item);
    }

    if (Helper\bool_config('shaarli_enabled')) {
        shaarli_sync($item);
    }
}

function instapaper_sync(array $item)
{
    $params = array(
        'username' => Helper\config('instapaper_username'),
        'password' => Helper\config('instapaper_password'),
        'url' => $item['url'],
        'title' => $item['title'],
    );

    $url = 'https://www.instapaper.com/api/add?'.http_build_query($params);

    $client = api_get_call($url);

    if ($client !== false) {
        return $client->getStatusCode() === 201;
    }

    return false;
}

function pinboard_sync(array $item)
{
    $params = array(
        'auth_token' => Helper\config('pinboard_token'),
        'format' => 'json',
        'url' => $item['url'],
        'description' => $item['title'],
        'tags' => Helper\config('pinboard_tags'),
        'toread' => Helper\bool_config('pinboard_mark_unread')  ? 'yes' : 'no',
    );

    $url = 'https://api.pinboard.in/v1/posts/add?'.http_build_query($params);

    $client = api_get_call($url);

    if ($client !== false) {
        $response = json_decode($client->getContent(), true);
        return is_array($response) && $response['result_code'] === 'done';
    }

    return false;
}

function wallabag_sync(array $item)
{
    return wallabag_has_url($item['url'])
        ? false
        : wallabag_add_item($item['url'], $item['title']);
}

function wallabag_has_url($url)
{
    $token = wallabag_get_access_token();
    if ($token === false) {
        return false;
    }
    $apiUrl = rtrim(Helper\config('wallabag_url'), '\/') . '/api/entries/exists.json?url=' . urlencode($url);
    $headers = array('Authorization: Bearer ' . $token);
    $response = api_get_call($apiUrl, $headers);
    if ($response !== false) {
        $response = json_decode($response->getContent(), true);
    }
    return !empty($response['exists']);
}

function wallabag_add_item($url, $title)
{
    $token = wallabag_get_access_token();
    if ($token === false) {
        return false;
    }
    $apiUrl = rtrim(Helper\config('wallabag_url'), '\/') . '/api/entries.json';
    $headers = array('Authorization: Bearer ' . $token);
    $data = array(
        'url' => $url,
        'title' => $title,
    );
    $response = api_post_call($apiUrl, $data, $headers);
    if ($response !== false) {
        $response = json_decode($response, true);
    }
    return !empty($response['id']);
}

function wallabag_get_access_token()
{
    if (!empty($_SESSION['wallabag_access_token'])) {
        return $_SESSION['wallabag_access_token'];
    }
    $url = rtrim(Helper\config('wallabag_url'), '\/') . '/oauth/v2/token';
    $data = array(
        'grant_type' => 'password',
        'client_id' => Helper\config('wallabag_client_id'),
        'client_secret' => Helper\config('wallabag_client_secret'),
        'username' => Helper\config('wallabag_username'),
        'password' => Helper\config('wallabag_password')
    );
    $response = api_post_call($url, $data);
    if ($response !== false) {
        $response = json_decode($response, true);
        if (!empty($response['access_token'])) {
            $_SESSION['wallabag_access_token'] = $response['access_token'];
            return $_SESSION['wallabag_access_token'];
        }
    }
    return false;
}

function shaarli_sync(array $item)
{
    $headers = array(
        'Authorization: Bearer ' . shaarli_get_access_token()
    );
    $apiUrl = rtrim(Helper\config('shaarli_url'), '\/') . '/api/v1/links';
    $tags = explode(',', Helper\config('shaarli_tags'));
    $data = array(
       'url' => $item['url'],
       'title' => $item['title'],
       'description' => '',
       'tags' => $tags,
       'private' => Helper\bool_config('shaarli_private'),
    );
    $response = api_post_json_call($apiUrl, $data, $headers);
    if ($response !== false) {
        $response = json_decode($response, true);
    }
    return empty($response['id']);
}

function shaarli_get_access_token()
{
    $secret = Helper\config('shaarli_secret');
    $header = base64_encode('{
        "typ": "JWT",
        "alg": "HS512"
    }');
    $payload = base64_encode('{
        "iat": '. time() .'
    }');
    $signature = hash_hmac('sha512', $header . '.' . $payload, $secret, true);
    // Base64Url Encoding:
    $signature = rtrim(strtr(base64_encode($signature), '+/' , '-_' ), '=' );
    return $header . '.' . $payload . '.' . $signature;
}

function api_get_call($url, array $headers = array())
{
    try {
        $client = Client::getInstance();
        $client->setUserAgent(HTTP_USER_AGENT);
        if ($headers) {
            $client->setHeaders($headers);
        }
        $client->execute($url);
        return $client;
    } catch (ClientException $e) {
        return false;
    }
}

function api_post_call($url, array $data, array $headers = array())
{
    $data = http_build_query($data);
    $additionalHeaders = array(
        'Content-Type: application/x-www-form-urlencoded'
    );
    $headers = $headers
        ? array_merge($headers, $additionalHeaders)
        : $additionalHeaders;
    return api_post($url, $data, $headers);
}

function api_post_json_call($url, array $data, array $headers = array())
{
    $data = json_encode($data);
    $additionalHeaders = array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($data)
    );
    $headers = $headers
        ? array_merge($headers, $additionalHeaders)
        : $additionalHeaders;
    return api_post($url, $data, $headers);
}

function api_post($url, $data, array $headers)
{
    return function_exists('curl_init')
        ? post_curl($url, $data, $headers)
        : post_stream($url, $data, $headers);
}

function post_stream($url, $data, array $headers = array())
{
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $data
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

function post_curl($url, $data, array $headers = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
