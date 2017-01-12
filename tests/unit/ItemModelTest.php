<?php

use Miniflux\Model;

require_once __DIR__.'/BaseTest.php';

class ItemModelTest extends BaseTest
{
    public function testGetItem()
    {
        $this->assertCreateFeed($this->buildFeed());

        $item = Model\Item\get_item(1, 1);
        $this->assertNotEmpty($item);
        $this->assertEquals('1', $item['id']);
        $this->assertEquals('ID 1', $item['checksum']);
        $this->assertEquals('1', $item['feed_id']);
        $this->assertEquals('1', $item['user_id']);
        $this->assertEquals('Item #1', $item['title']);
        $this->assertEquals('some url', $item['url']);
        $this->assertEquals('some content', $item['content']);
        $this->assertEquals(Model\Item\STATUS_UNREAD, $item['status']);
        $this->assertEquals(time(), $item['updated'], '', 2);
        $this->assertEquals('', $item['author']);
        $this->assertEquals(0, $item['bookmark']);
        $this->assertEquals('', $item['enclosure_url']);
        $this->assertEquals('', $item['enclosure_type']);
        $this->assertEquals('', $item['language']);

        $item = Model\Item\get_item(2, 1);
        $this->assertNull($item);
    }

    public function testUpdateItemContent()
    {
        $feed = $this->buildFeed();
        $this->assertCreateFeed($feed);

        $feed->items[1]->setContent('new content');
        Model\Item\update_feed_items(1, 1, $feed->items);

        $item = Model\Item\get_item(1, 2);
        $this->assertNotEmpty($item);
        $this->assertEquals('new content', $item['content']);
    }

    public function testUpdateItemEnclosure()
    {
        $feed = $this->buildFeed();
        $this->assertCreateFeed($feed);

        $feed->items[1]->setEnclosureUrl('some enclosure url');
        $feed->items[1]->setEnclosureType('some enclosure type');
        Model\Item\update_feed_items(1, 1, $feed->items);

        $item = Model\Item\get_item(1, 2);
        $this->assertNotEmpty($item);
        $this->assertEquals('some enclosure url', $item['enclosure_url']);
        $this->assertEquals('some enclosure type', $item['enclosure_type']);
    }

    public function testChangeItemStatus()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertTrue(Model\Item\change_item_status(1, 1, Model\Item\STATUS_READ));

        $item = Model\Item\get_item(1, 1);
        $this->assertNotEmpty($item);
        $this->assertEquals(Model\Item\STATUS_READ, $item['status']);
    }

    public function testChangeItemsStatus()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertTrue(Model\Item\change_item_ids_status(1, array(2), Model\Item\STATUS_READ));

        $item = Model\Item\get_item(1, 1);
        $this->assertNotEmpty($item);
        $this->assertEquals(Model\Item\STATUS_UNREAD, $item['status']);

        $item = Model\Item\get_item(1, 2);
        $this->assertNotEmpty($item);
        $this->assertEquals(Model\Item\STATUS_READ, $item['status']);

        $this->assertFalse(Model\Item\change_item_ids_status(1, array(), Model\Item\STATUS_REMOVED));
    }

    public function testChangeAllItemsStatus()
    {
        $this->assertCreateFeed($this->buildFeed());

        $this->assertTrue(Model\Item\change_items_status(1, Model\Item\STATUS_UNREAD, Model\Item\STATUS_UNREAD));
        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_UNREAD);
        $this->assertCount(2, $items);
        $this->assertEquals(2, Model\Item\count_by_status(1, Model\Item\STATUS_UNREAD));

        $this->assertTrue(Model\Item\change_items_status(1, Model\Item\STATUS_UNREAD, Model\Item\STATUS_READ));

        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_UNREAD);
        $this->assertCount(0, $items);

        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_READ);
        $this->assertCount(2, $items);

        $this->assertEquals(2, Model\Item\count_by_status(1, Model\Item\STATUS_READ));

        $this->assertTrue(Model\Item\change_items_status(1, Model\Item\STATUS_READ, Model\Item\STATUS_REMOVED));

        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_REMOVED);
        $this->assertCount(2, $items);

        $this->assertEquals(2, Model\Item\count_by_status(1, Model\Item\STATUS_REMOVED));
    }

    public function testCountItemByStatus()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertEquals(2, Model\Item\count_by_status(1, Model\Item\STATUS_UNREAD));
        $this->assertEquals(0, Model\Item\count_by_status(0, Model\Item\STATUS_UNREAD));
        $this->assertEquals(0, Model\Item\count_by_status(1, Model\Item\STATUS_UNREAD, array(2)));
        $this->assertEquals(2, Model\Item\count_by_status(1, Model\Item\STATUS_UNREAD, array(1)));
    }

    public function testIsItemExists()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertEquals(1, Model\Item\get_item_id_from_checksum(1, 'ID 1'));
        $this->assertEquals(2, Model\Item\get_item_id_from_checksum(1, 'ID 2'));
        $this->assertSame(0, Model\Item\get_item_id_from_checksum(1, 'nofound'));
        $this->assertSame(0, Model\Item\get_item_id_from_checksum(2, 'nofound'));
    }

    public function testCleanupItems()
    {
        $feed = $this->buildFeed();
        $feed->items[] = $this->buildItem('ID 3');
        $feed->items[] = $this->buildItem('ID 4');

        $this->assertCreateFeed($feed);
        $this->assertTrue(Model\Item\change_items_status(1, Model\Item\STATUS_UNREAD, Model\Item\STATUS_REMOVED));
        $this->assertEquals(4, Model\Item\count_by_status(1, Model\Item\STATUS_REMOVED));

        // ID 1 => buffer
        // ID 2 => buffer
        // ID 3 => must be removed
        // ID 4 => present in feed
        Model\Item\cleanup_feed_items(1, array(4));

        $this->assertEquals(3, Model\Item\count_by_status(1, Model\Item\STATUS_REMOVED));
        $this->assertNull(Model\Item\get_item(1, 3));
    }

    public function testGetItemNav()
    {
        $feed = $this->buildFeed();
        $feed->items[] = $this->buildItem('ID 3');
        $feed->items[] = $this->buildItem('ID 4');

        $this->assertCreateFeed($feed);

        $item = Model\Item\get_item(1, 2);
        $nav = Model\Item\get_item_nav(1, $item);

        $this->assertEquals(1, $nav['next']['id']);
        $this->assertEquals(3, $nav['previous']['id']);
    }

    public function testGetItemByStatus()
    {
        $this->assertCreateFeed($this->buildFeed());

        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_UNREAD);
        $this->assertCount(2, $items);

        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_UNREAD, array(2));
        $this->assertCount(0, $items);

        $items = Model\Item\get_items_by_status(1, Model\Item\STATUS_REMOVED);
        $this->assertCount(0, $items);
    }

    public function testGetItems()
    {
        $this->assertCreateFeed($this->buildFeed());

        $items = Model\Item\get_items(1);
        $this->assertCount(2, $items);

        $items = Model\Item\get_items(1, 1);
        $this->assertCount(1, $items);
        $this->assertEquals(2, $items[0]['id']);

        $items = Model\Item\get_items(1, null, array(2));
        $this->assertCount(1, $items);
        $this->assertEquals(2, $items[0]['id']);
    }
}
