<?php

require_once __DIR__.'/BaseApiTest.php';

class ApiTest extends BaseApiTest
{
    public function testGetVersion()
    {
        $this->assertEquals('master', $this->getApiClient()->getVersion());
    }

    public function testCreateUser()
    {
        $this->assertFalse($this->getApiClient()->createUser('admin', 'test123'));
        $this->assertNotFalse($this->getApiClient()->createUser(array(
            'username' => 'api_test',
            'password' => 'test123',
        )));
    }

    public function testGetUser()
    {
        $this->assertNull($this->getApiClient()->getUserByUsername('notfound'));

        $user = $this->getApiClient()->getUserByUsername('api_test');
        $this->assertEquals('api_test', $user['username']);
        $this->assertFalse((bool) $user['is_admin']);
        $this->assertArrayHasKey('password', $user);
        $this->assertArrayHasKey('api_token', $user);
    }

    public function testCreateUserAsNonAdmin()
    {
        $user = $this->getApiClient()->getUserByUsername('api_test');

        $this->setExpectedException('JsonRPC\Exception\AccessDeniedException');
        $this->getApiClient($user)->createUser('someone', 'secret');
    }

    public function testGetUserAsNonAdmin()
    {
        $user = $this->getApiClient()->getUserByUsername('api_test');

        $this->setExpectedException('JsonRPC\Exception\AccessDeniedException');
        $this->getApiClient($user)->getUserByUsername('admin');
    }

    public function testRemoveUser()
    {
        $userId = $this->getApiClient()->createUser(array(
            'username' => 'api_test2',
            'password' => 'test123',
        ));

        $this->assertNotFalse($userId);
        $this->assertTrue($this->getApiClient()->removeUser($userId));
    }

    public function testCreateFeed()
    {
        $this->assertNotFalse($this->getApiClient()->createFeed(array(
            'url' => FEED_URL,
            'group_name' => 'open source software',
        )));
    }

    public function testGetAllFeeds()
    {
        $feeds = $this->getApiClient()->getFeeds();
        $this->assertCount(1, $feeds);
        $this->assertEquals(1, $feeds[0]['id']);
        $this->assertEquals(FEED_URL, $feeds[0]['feed_url']);
        $this->assertTrue((bool) $feeds[0]['enabled']);
        $this->assertEquals('open source software', $feeds[0]['groups'][0]['title']);
    }

    public function testGetFeed()
    {
        $this->assertNull($this->getApiClient()->getFeed(999));

        $feed = $this->getApiClient()->getFeed(1);
        $this->assertEquals(FEED_URL, $feed['feed_url']);
        $this->assertTrue((bool) $feed['enabled']);
        $this->assertEquals('open source software', $feed['groups'][0]['title']);
    }

    public function testRefreshFeed()
    {
        $this->assertTrue($this->getApiClient()->refreshFeed(1));
    }

    public function testGetItems()
    {
        $items = $this->getApiClient()->getItems();
        $this->assertNotEmpty($items);
        $this->assertEquals(1, $items[0]['id']);
        $this->assertEquals(1, $items[0]['feed_id']);
        $this->assertNotEmpty($items[0]['title']);
        $this->assertNotEmpty($items[0]['author']);
        $this->assertNotEmpty($items[0]['content']);
        $this->assertNotEmpty($items[0]['url']);
    }

    public function testGetItemsByStatusUnread()
    {
        $items = $this->getApiClient()->getItemsByStatus(array('status' => 'unread'));
        $this->assertNotEmpty($items);
        $this->assertEquals(1, $items[0]['id']);
        $this->assertEquals(1, $items[0]['feed_id']);
        $this->assertNotEmpty($items[0]['title']);
        $this->assertNotEmpty($items[0]['author']);
        $this->assertNotEmpty($items[0]['content']);
        $this->assertNotEmpty($items[0]['url']);
        $this->assertEquals('unread', $items[0]['status']);
    }

    public function testGetItemsByStatusRead()
    {
        $items = $this->getApiClient()->getItemsByStatus(array('status' => 'read'));
        $this->assertEmpty($items);
    }

    public function testGetItemsByFeedIds()
    {
        $items = $this->getApiClient()->getItemsByStatus(array(
          'status' => 'unread',
          'feed_ids' => array(1)
        ));
        $this->assertNotEmpty($items);
        $this->assertEquals(1, $items[0]['id']);
        $this->assertEquals(1, $items[0]['feed_id']);
        $this->assertNotEmpty($items[0]['title']);
        $this->assertNotEmpty($items[0]['author']);
        $this->assertNotEmpty($items[0]['content']);
        $this->assertNotEmpty($items[0]['url']);
        $this->assertEquals('unread', $items[0]['status']);
    }

    public function testGetItemsByFeedIdsNonExist()
    {
        $items = $this->getApiClient()->getItemsByStatus(array(
          'status' => 'unread',
          'feed_ids' => array(2)
        ));
        $this->assertEmpty($items);
    }

    public function testGetItemsSinceId()
    {
        $items = $this->getApiClient()->getItems(array('since_id' => 2));
        $this->assertNotEmpty($items);
        $this->assertEquals(3, $items[0]['id']);
    }

    public function testGetSpecificItems()
    {
        $items = $this->getApiClient()->getItems(array('item_ids' => array(2, 3)));
        $this->assertNotEmpty($items);
        $this->assertEquals(2, $items[0]['id']);
        $this->assertEquals(3, $items[1]['id']);
    }

    public function testGetItem()
    {
        $this->assertNull($this->getApiClient()->getItem(999));

        $item = $this->getApiClient()->getItem(1);
        $this->assertNotEmpty($item);
        $this->assertEquals(1, $item['id']);
        $this->assertEquals(1, $item['feed_id']);
        $this->assertEquals('unread', $item['status']);
        $this->assertNotEmpty($item['title']);
        $this->assertNotEmpty($item['author']);
        $this->assertNotEmpty($item['content']);
        $this->assertNotEmpty($item['url']);
    }

    public function testChangeItemsStatus()
    {
        $this->assertTrue($this->getApiClient()->changeItemsStatus(array(1), 'read'));

        $item = $this->getApiClient()->getItem(1);
        $this->assertEquals('read', $item['status']);

        $item = $this->getApiClient()->getItem(2);
        $this->assertEquals('unread', $item['status']);
    }

    public function testAddBookmark()
    {
        $this->assertTrue($this->getApiClient()->addBookmark(1));

        $item = $this->getApiClient()->getItem(1);
        $this->assertTrue((bool) $item['bookmark']);
    }

    public function testRemoveBookmark()
    {
        $this->assertTrue($this->getApiClient()->removeBookmark(1));

        $item = $this->getApiClient()->getItem(1);
        $this->assertFalse((bool) $item['bookmark']);
    }

    public function testGetGroups()
    {
        $groups = $this->getApiClient()->getGroups();
        $this->assertCount(1, $groups);

        $this->assertEquals(1, $groups[0]['id']);
        $this->assertEquals(1, $groups[0]['user_id']);
        $this->assertEquals('open source software', $groups[0]['title']);
    }

    public function testCreateGroup()
    {
        $this->assertEquals(2, $this->getApiClient()->createGroup('foobar'));
        $this->assertEquals(2, $this->getApiClient()->createGroup('foobar'));

        $groups = $this->getApiClient()->getGroups();
        $this->assertCount(2, $groups);
    }

    public function testSetFeedGroups()
    {
        $this->assertTrue($this->getApiClient()->setFeedGroups(1, array(2)));

        $feed = $this->getApiClient()->getFeed(1);
        $this->assertCount(1, $feed['groups']);
        $this->assertEquals('foobar', $feed['groups'][0]['title']);
    }

    public function testGetFavicons()
    {
        $favicons = $this->getApiClient()->getFavicons();

        $this->assertCount(1, $favicons);
        $this->assertEquals(1, $favicons[0]['feed_id']);
        $this->assertNotEmpty($favicons[0]['hash']);
        $this->assertNotEmpty($favicons[0]['type']);
        $this->assertNotEmpty($favicons[0]['data_url']);
    }

    public function testDeleteFeed()
    {
        $this->assertTrue($this->getApiClient()->removeFeed(1));
    }
}
