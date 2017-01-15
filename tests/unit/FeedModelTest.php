<?php

use Miniflux\Model;
use PicoFeed\Parser\Feed;
use PicoFeed\Parser\Item;

require_once __DIR__.'/BaseTest.php';

class FeedModelTest extends BaseTest
{
    public function testCreate()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));
        $this->assertEquals(-1, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $subscription = Model\Feed\get_feed(1, 1);
        $this->assertNotEmpty($subscription);
        $this->assertEquals('1', $subscription['user_id']);
        $this->assertEquals('My feed', $subscription['title']);
        $this->assertEquals('site url', $subscription['site_url']);
        $this->assertEquals('feed url', $subscription['feed_url']);
        $this->assertEquals('etag', $subscription['etag']);
        $this->assertEquals('last modified', $subscription['last_modified']);
        $this->assertFalse((bool) $subscription['download_content']);
        $this->assertFalse((bool) $subscription['rtl']);
        $this->assertFalse((bool) $subscription['cloak_referrer']);
        $this->assertTrue((bool) $subscription['enabled']);
    }

    public function testGetAll()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $feed = new Feed();
        $feed->setTitle('Some feed');
        $feed->setFeedUrl('another feed url');
        $feed->setSiteUrl('site url');
        $this->assertEquals(2, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $feeds = Model\Feed\get_feeds(1);
        $this->assertCount(2, $feeds);
    }

    public function testGetFeedIds()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $feed = new Feed();
        $feed->setTitle('Some feed');
        $feed->setFeedUrl('another feed url');
        $feed->setSiteUrl('site url');
        $this->assertEquals(2, Model\Feed\create(1, $feed, 'etag', 'last modified', time()));

        $feed = new Feed();
        $feed->setTitle('Some feed');
        $feed->setFeedUrl('some other feed url');
        $feed->setSiteUrl('site url');
        $this->assertEquals(3, Model\Feed\create(1, $feed, 'etag', 'last modified', strtotime('-1 week')));

        $feed_ids = Model\Feed\get_feed_ids_to_refresh(1);
        $this->assertEquals(array(1, 2, 3), $feed_ids);

        $feed_ids = Model\Feed\get_feed_ids_to_refresh(1, 1);
        $this->assertEquals(array(1), $feed_ids);

        $feed_ids = Model\Feed\get_feed_ids_to_refresh(1, null, strtotime('-2 days'));
        $this->assertEquals(array(1, 3), $feed_ids);
    }

    public function testGetFeedWithItemsCount()
    {
        $item = new Item();
        $item->setId('ID 1');
        $item->setTitle('Item #1');
        $item->setUrl('some url');
        $item->setDate(new DateTime());

        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');
        $feed->setItems(array($item));

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $feed = new Feed();
        $feed->setTitle('Some feed');
        $feed->setFeedUrl('another feed url');
        $feed->setSiteUrl('site url');
        $this->assertEquals(2, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $feeds = Model\Feed\get_feeds_with_items_count_and_groups(1);
        $this->assertCount(2, $feeds);

        $this->assertEquals(1, $feeds[0]['items_unread']);
        $this->assertEquals(1, $feeds[0]['items_total']);

        $this->assertEquals(0, $feeds[1]['items_unread']);
        $this->assertEquals(0, $feeds[1]['items_total']);
    }

    public function testUpdate()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));
        $this->assertTrue(Model\Feed\update_feed(1, 1, array('title' => 'new title')));

        $subscription = Model\Feed\get_feed(1, 1);
        $this->assertNotEmpty($subscription);
        $this->assertEquals('1', $subscription['user_id']);
        $this->assertEquals('new title', $subscription['title']);
    }

    public function testChangeStatus()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $this->assertTrue(Model\Feed\change_feed_status(1, 1, Model\Feed\STATUS_INACTIVE));
        $subscription = Model\Feed\get_feed(1, 1);
        $this->assertEquals(0, $subscription['enabled']);
    }

    public function testRemoveFeed()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));
        $this->assertTrue(Model\Feed\remove_feed(1, 1));
        $this->assertNull(Model\Feed\get_feed(1, 1));
    }

    public function testPeopleCanHaveSameFeed()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(2, Model\User\create_user('foobar', 'test'));

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));
        $this->assertEquals(2, Model\Feed\create(2, $feed, 'etag', 'last modified'));
    }

    public function testIsDuplicatedFeed()
    {
        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(1, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $feed = new Feed();
        $feed->setTitle('My feed');
        $feed->setFeedUrl('another feed url');
        $feed->setSiteUrl('site url');

        $this->assertEquals(2, Model\Feed\create(1, $feed, 'etag', 'last modified'));

        $this->assertFalse(Model\Feed\is_duplicated_feed(1, 1, 'feed url'));
        $this->assertTrue(Model\Feed\is_duplicated_feed(1, 1, 'another feed url'));

        $this->assertFalse(Model\Feed\is_duplicated_feed(1, 2, 'another feed url'));
        $this->assertTrue(Model\Feed\is_duplicated_feed(1, 2, 'feed url'));
    }
}
