<?php

namespace Model\Proxy;

use Model\Config;
use PicoFeed\Client\Client;
use PicoFeed\Client\ClientException;

function download($url)
{
    try {
        $client = Client::getInstance();
        $client->setUserAgent(Config\HTTP_USER_AGENT);
        $client->execute($url);

        $content = array(
            $client->getContent(),
            $client->getContentType(),
        );
    }
    catch (ClientException $e) {
        $content = array(false, false);
    }

    Config\write_debug();

    return $content;
}
