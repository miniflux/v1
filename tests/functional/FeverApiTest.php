<?php

require_once __DIR__.'/BaseApiTest.php';

class FeverApiTest extends BaseApiTest
{
    public function testGetVersion()
    {
        $response = $this->executeFeverApiCall();
        $this->assertEquals(3, $response['api_version']);
        $this->assertEquals(1, $response['auth']);
        $this->assertArrayHasKey('last_refreshed_on_time', $response);
    }

    public function testGetLinks()
    {
        $response = $this->executeFeverApiCall('links');
        $this->assertEquals(3, $response['api_version']);
        $this->assertEquals(1, $response['auth']);
        $this->assertArrayHasKey('last_refreshed_on_time', $response);
        $this->assertSame(array(), $response['links']);
    }

    public function testGetEmptyFeeds()
    {
        $response = $this->executeFeverApiCall('feeds');
        $this->assertEquals(3, $response['api_version']);
        $this->assertEquals(1, $response['auth']);
        $this->assertArrayHasKey('last_refreshed_on_time', $response);
        $this->assertSame(array(), $response['feeds']);
        $this->assertSame(array(), $response['feeds_groups']);
    }

    public function testGetEmptyGroups()
    {
        $response = $this->executeFeverApiCall('groups');
        $this->assertEquals(3, $response['api_version']);
        $this->assertEquals(1, $response['auth']);
        $this->assertArrayHasKey('last_refreshed_on_time', $response);
        $this->assertSame(array(), $response['groups']);
        $this->assertSame(array(), $response['feeds_groups']);
    }

    public function testGetFeedsAndGroups()
    {
        $this->createFeedAndGroups();

        $response = $this->executeFeverApiCall('feeds');

        $this->assertEquals(1, $response['feeds'][0]['id']);
        $this->assertEquals(1, $response['feeds'][0]['favicon_id']);
        $this->assertNotEmpty($response['feeds'][0]['title']);
        $this->assertNotEmpty($response['feeds'][0]['url']);
        $this->assertNotEmpty($response['feeds'][0]['site_url']);
        $this->assertNotEmpty($response['feeds'][0]['last_updated_on_time']);
        $this->assertEquals(0, $response['feeds'][0]['is_spark']);

        $this->assertEquals(array(array('group_id' => 1, 'feed_ids' => '1')), $response['feeds_groups']);

        $response = $this->executeFeverApiCall('groups');

        $this->assertEquals(array(array('id' => 1, 'title' => 'open source software')), $response['groups']);
    }

    public function testGetFavicons()
    {
        $response = $this->executeFeverApiCall('favicons');

        $this->assertEquals(1, $response['favicons'][0]['id']);
        $this->assertNotEmpty($response['favicons'][0]['data']);
    }

    public function testGetItems()
    {
        $response = $this->executeFeverApiCall('items');

        $this->assertGreaterThan(2, $response['total_items']);
        $this->assertEquals(1, $response['items'][0]['id']);
        $this->assertEquals(1, $response['items'][0]['feed_id']);
        $this->assertNotEmpty($response['items'][0]['title']);
        $this->assertNotEmpty($response['items'][0]['author']);
        $this->assertNotEmpty($response['items'][0]['html']);
        $this->assertNotEmpty($response['items'][0]['url']);
        $this->assertEquals(0, $response['items'][0]['is_saved']);
        $this->assertEquals(0, $response['items'][0]['is_read']);
        $this->assertGreaterThan(0, $response['items'][0]['created_on_time']);
    }

    public function testGetItemsWithIds()
    {
        $response = $this->executeFeverApiCall('items&with_ids=2,3');

        $this->assertGreaterThan(2, $response['total_items']);
        $this->assertCount(2, $response['items']);
        $this->assertEquals(2, $response['items'][0]['id']);
        $this->assertEquals(3, $response['items'][1]['id']);
    }

    public function testGetItemsWithSinceId()
    {
        $response = $this->executeFeverApiCall('items&since_id=1');

        $this->assertGreaterThan(2, $response['total_items']);
        $this->assertEquals(2, $response['items'][0]['id']);
    }

    public function testGetUnreadItems()
    {
        $response = $this->executeFeverApiCall('unread_item_ids');
        $this->assertStringStartsWith('1,2,', $response['unread_item_ids']);
    }

    public function testMarkItemAsRead()
    {
        $this->assertNotNull($this->executeFeverApiCall('', array(
            'mark' => 'item',
            'as'   => 'read',
            'id'   =>  1,
        )));

        $response = $this->executeFeverApiCall('items&with_ids=1');
        $this->assertEquals(1, $response['items'][0]['id']);
        $this->assertEquals(1, $response['items'][0]['is_read']);
    }

    public function testMarkItemAsSaved()
    {
        $this->assertNotNull($this->executeFeverApiCall('', array(
            'mark' => 'item',
            'as'   => 'saved',
            'id'   =>  2,
        )));

        $response = $this->executeFeverApiCall('items&with_ids=2');
        $this->assertEquals(2, $response['items'][0]['id']);
        $this->assertEquals(0, $response['items'][0]['is_read']);
        $this->assertEquals(1, $response['items'][0]['is_saved']);

        $response = $this->executeFeverApiCall('saved_item_ids');
        $this->assertStringStartsWith('2', $response['saved_item_ids']);
    }

    public function testMarkItemAsUnSaved()
    {
        $this->assertNotNull($this->executeFeverApiCall('', array(
            'mark' => 'item',
            'as'   => 'unsaved',
            'id'   =>  2,
        )));

        $response = $this->executeFeverApiCall('items&with_ids=2');
        $this->assertEquals(2, $response['items'][0]['id']);
        $this->assertEquals(0, $response['items'][0]['is_read']);
        $this->assertEquals(0, $response['items'][0]['is_saved']);
    }

    public function testMarkFeedAsRead()
    {
        $response = $this->executeFeverApiCall('items');
        $items = $response['items'];
        $nbItems = count($items);

        $this->assertNotNull($this->executeFeverApiCall('', array(
            'mark'    => 'feed',
            'as'      => 'read',
            'id'      =>  1,
            'before'  =>  $items[$nbItems - 1]['created_on_time'],
        )));

        $response = $this->executeFeverApiCall('items&with_ids=' . $items[$nbItems - 2]['id']);
        $this->assertEquals(0, $response['items'][0]['is_read']);

        $response = $this->executeFeverApiCall('items&with_ids=' . $items[$nbItems - 1]['id']);
        $this->assertEquals(1, $response['items'][0]['is_read']);
    }

    public function testMarkGroupAsRead()
    {
        $this->assertNotNull($this->executeFeverApiCall('', array(
            'mark'    => 'group',
            'as'      => 'read',
            'id'      =>  1,
            'before'  =>  time(),
        )));

        $response = $this->executeFeverApiCall('unread_item_ids');
        $this->assertSame('', $response['unread_item_ids']);
    }

    protected function executeFeverApiCall($endpoint = '', array $data = array())
    {
        $url = FEVER_API_URL . '?api&' . $endpoint;
        $headers = array(
            'Content-type: application/x-www-form-urlencoded',
            'Accept: application/json',
        );

        $payload = array(
            'api_key' => $this->adminUser['fever_api_key'],
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'protocol_version' => 1.1,
                'timeout' => 2,
                'header' => implode("\r\n", $headers),
                'content' => http_build_query(array_merge($payload, $data)),
            ),
        ));

        $stream = fopen($url, 'r', false, $context);
        return json_decode(stream_get_contents($stream), true);
    }

    protected function createFeedAndGroups()
    {
        $this->assertNotFalse($this->getApiClient()->createFeed(array(
            'url' => FEED_URL,
            'group_name' => 'open source software',
        )));
    }
}
