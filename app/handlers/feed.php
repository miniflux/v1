<?php

namespace Miniflux\Handler\Feed;

use Miniflux\Helper;
use Miniflux\Model;
use PicoFeed;
use PicoFeed\Config\Config as ReaderConfig;
use PicoFeed\Logging\Logger;
use PicoFeed\Reader\Favicon;
use PicoFeed\Reader\Reader;

function fetch_feed($url, $download_content = false, $etag = '', $last_modified = '', array $item_urls = array())
{
    $error_message = '';
    $feed = null;
    $resource = null;

    try {
        $reader = new Reader(get_reader_config());
        $resource = $reader->discover($url, $last_modified, $etag);

        if ($resource->isModified()) {
            $parser = $reader->getParser(
                $resource->getUrl(),
                $resource->getContent(),
                $resource->getEncoding()
            );

            if ($download_content) {
                $parser->enableContentGrabber();
                $parser->setGrabberIgnoreUrls($item_urls);
            }

            $feed = $parser->execute();
        }
    } catch (PicoFeed\Client\InvalidCertificateException $e) {
        $error_message = t('Invalid SSL certificate.');
    } catch (PicoFeed\Client\InvalidUrlException $e) {
        $error_message = $e->getMessage();
    } catch (PicoFeed\Client\MaxRedirectException $e) {
        $error_message = t('Maximum number of HTTP redirection exceeded.');
    } catch (PicoFeed\Client\MaxSizeException $e) {
        $error_message = t('The content size exceeds to maximum allowed size.');
    } catch (PicoFeed\Client\TimeoutException $e) {
        $error_message = t('Connection timeout.');
    } catch (PicoFeed\Client\ForbiddenException $e) {
        $error_message = t('Not allowed to fetch feed.');
    } catch (PicoFeed\Client\UnauthorizedException $e) {
        $error_message = t('Not allowed to fetch feed.');
    } catch (PicoFeed\Parser\MalformedXmlException $e) {
        $error_message = t('Feed is malformed.');
    } catch (PicoFeed\Reader\SubscriptionNotFoundException $e) {
        $error_message = t('Unable to find a subscription.');
    } catch (PicoFeed\Reader\UnsupportedFeedFormatException $e) {
        $error_message = t('Unable to detect the feed format.');
    } catch (PicoFeed\PicoFeedException $e) {
        $error_message = $e->getMessage();
    }

    return array($feed, $resource, $error_message);
}

function create_feed($user_id, $url, $download_content = false, $rtl = false, $cloak_referrer = false, array $feed_group_ids = array(), $group_name = null)
{
    $feed_id = null;
    $url = trim($url);
    list($feed, $resource, $error_message) = fetch_feed($url, $download_content);

    if ($feed !== null) {
        // Feed URL defined in XML could be wrong
        $feed->setFeedUrl($resource->getUrl());

        $feed_id = Model\Feed\create(
            $user_id,
            $feed,
            $resource->getEtag(),
            $resource->getLastModified(),
            $resource->getExpiration()->getTimestamp(),
            $rtl,
            $download_content,
            $cloak_referrer
        );

        if ($feed_id === -1) {
            $error_message = t('This subscription already exists.');
        } else if ($feed_id === false) {
            $error_message = t('Unable to save this subscription in the database.');
        } else {
            fetch_favicon($feed_id, $feed->getSiteUrl(), $feed->getIcon());

            if (! empty($feed_group_ids) || ! empty($group_name)) {
                Model\Group\update_feed_groups($user_id, $feed_id, $feed_group_ids, $group_name);
            }
        }
    }

    return array($feed_id, $error_message);
}

function update_feed($user_id, $feed_id)
{
    $subscription = Model\Feed\get_feed($user_id, $feed_id);
    $item_urls = array();

    if ($subscription['enabled'] == 0) {
        return false;
    }

    if ($subscription['download_content']) {
        $item_urls = Model\Item\get_item_urls($user_id, $feed_id);
    }

    list($feed, $resource, $error_message) = fetch_feed(
        $subscription['feed_url'],
        (bool) $subscription['download_content'],
        $subscription['etag'],
        $subscription['last_modified'],
        $item_urls
    );

    if (! empty($error_message)) {
        $error_count = $subscription['parsing_error'] + 1;
        Model\Feed\update_feed($user_id, $feed_id, array(
            'last_checked'          => time(),
            'parsing_error'         => $error_count,
            'parsing_error_message' => $error_message,
            'enabled'               => $error_count > SUBSCRIPTION_DISABLE_THRESHOLD_ERROR ? 0 : 1,
        ));

        return false;
    } else if (Model\Feed\is_duplicated_feed($user_id, $feed_id, $resource->getUrl())) {
        Model\Feed\update_feed($user_id, $feed_id, array(
            'enabled'               => 0,
            'last_checked'          => time(),
            'parsing_error'         => 1,
            'parsing_error_message' => t('Duplicated feed'),
        ));
    } else {
        Model\Feed\update_feed($user_id, $feed_id, array(
            'feed_url'              => $resource->getUrl(),
            'etag'                  => $resource->getEtag(),
            'last_modified'         => $resource->getLastModified(),
            'last_checked'          => time(),
            'expiration'            => $subscription['ignore_expiration'] == 1 ? 0 : $resource->getExpiration()->getTimestamp(),
            'parsing_error'         => 0,
            'parsing_error_message' => '',
        ));
    }

    if ($feed !== null) {
        Model\Item\update_feed_items($user_id, $feed_id, $feed->getItems(), $subscription['rtl'], $item_urls);
        fetch_favicon($feed_id, $feed->getSiteUrl(), $feed->getIcon());
    }

    return true;
}

function update_feeds($user_id, $limit = null)
{
    foreach (Model\Feed\get_feed_ids_to_refresh($user_id, $limit) as $feed_id) {
        update_feed($user_id, $feed_id);
    }
}

function fetch_favicon($feed_id, $site_url, $icon_link)
{
    if (Helper\bool_config('favicons') && ! Model\Favicon\has_favicon($feed_id)) {
        $favicon = new Favicon();
        $icon_url = $favicon->find($site_url, $icon_link);

        if (! empty($icon_url)) {
            Model\Favicon\create_feed_favicon($feed_id, $favicon->getType(), $favicon->getContent());
        }
    }
}

function get_reader_config()
{
    $config = new ReaderConfig;
    $config->setTimezone(Helper\config('timezone'));

    // Client
    $config->setClientTimeout(HTTP_TIMEOUT);
    $config->setClientUserAgent(HTTP_USER_AGENT);
    $config->setMaxBodySize(HTTP_MAX_RESPONSE_SIZE);

    // Grabber
    $config->setGrabberRulesFolder(RULES_DIRECTORY);

    // Proxy
    $config->setProxyHostname(PROXY_HOSTNAME);
    $config->setProxyPort(PROXY_PORT);
    $config->setProxyUsername(PROXY_USERNAME);
    $config->setProxyPassword(PROXY_PASSWORD);

    // Filter
    $config->setFilterIframeWhitelist(Model\Config\get_iframe_whitelist());

    // Parser
    $config->setParserHashAlgo('crc32b');

    if (DEBUG_MODE) {
        Logger::enable();
    }

    return $config;
}
