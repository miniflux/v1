<?php

use Miniflux\Model;

require_once __DIR__.'/BaseTest.php';

class BookmarkModelTest extends BaseTest
{
    public function testSetBookmark()
    {
        $this->assertCreateFeed($this->buildFeed());

        $this->assertTrue(Model\Bookmark\set_flag(1, 1, 1));
        $item = Model\Item\get_item(1, 1);
        $this->assertEquals(1, $item['bookmark']);

        $this->assertTrue(Model\Bookmark\set_flag(1, 1, 0));
        $item = Model\Item\get_item(1, 1);
        $this->assertEquals(0, $item['bookmark']);
    }

    public function testCountBookmarkedItems()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertTrue(Model\Bookmark\set_flag(1, 1, 1));
        $this->assertEquals(1, Model\Bookmark\count_bookmarked_items(1));
        $this->assertEquals(0, Model\Bookmark\count_bookmarked_items(1, array(2)));
    }

    public function testGetBookmarkedItems()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertTrue(Model\Bookmark\set_flag(1, 1, 1));

        $items = Model\Bookmark\get_bookmarked_items(1);
        $this->assertCount(1, $items);
    }
}
