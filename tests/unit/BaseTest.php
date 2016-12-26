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
        SessionStorage::getInstance()->flush();

        PicoDb\Database::setInstance('db', function () {
            $db = new PicoDb\Database(array(
                'driver'   => 'sqlite',
                'filename' => DB_FILENAME,
            ));

            $db->getStatementHandler()->withLogging();
            if (! $db->schema('\Miniflux\Schema')->check(Miniflux\Schema\VERSION)) {
                var_dump($db->getLogMessages());
            }
            return $db;
        });
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

    public function buildFeed()
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
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');
        $feed->setItems($items);

        return $feed;
    }

    public function assertCreateFeed(Feed $feed)
    {
        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));
    }
}
