<?php

use Miniflux\Model;
use Miniflux\Session\SessionStorage;
use PicoDb\Database;
use PicoFeed\Parser\Feed;
use PicoFeed\Parser\Item;

require_once __DIR__.'/../../app/common.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = null;
        } else if (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = null;
        }

        PicoDb\Database::setInstance('db', function () {
            return Miniflux\Database\get_connection();
        });

        SessionStorage::getInstance()->flush();
    }

    public function tearDown()
    {
        Database::getInstance('db')->closeConnection();
    }

    public function buildItem($itemId)
    {
        $item = new Item();
        $item->setId($itemId);
        $item->setTitle('Item #1');
        $item->setUrl('some url');
        $item->setContent('some content');
        $item->setDate(new DateTime());
        return $item;
    }

    public function buildFeed($feedUrl = 'feed url')
    {
        $items = array();

        $item = new Item();
        $item->setId('ID 1');
        $item->setTitle('Item #1');
        $item->setUrl('some url');
        $item->setContent('some content');
        $item->setDate(new DateTime());
        $items[] = $item;

        $item = new Item();
        $item->setId('ID 2');
        $item->setTitle('Item #2');
        $item->setUrl('some url');
        $item->setDate(new DateTime());
        $items[] = $item;

        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl($feedUrl);
        $feed->setSiteUrl('site url');
        $feed->setItems($items);

        return $feed;
    }

    public function assertCreateFeed(Feed $feed)
    {
        $this->assertNotFalse(Model\Feed\create(1, $feed, 'etag', 'last modified'));
    }
}
