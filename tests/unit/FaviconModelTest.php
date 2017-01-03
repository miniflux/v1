<?php

namespace Miniflux\Model\Favicon;

use BaseTest;
use Miniflux\Model;

function file_put_contents($filename, $data)
{
    return FaviconModelTest::$functions->file_put_contents($filename, $data);
}

function file_get_contents($filename)
{
    return FaviconModelTest::$functions->file_get_contents($filename);
}

function file_exists($filename)
{
    return FaviconModelTest::$functions->file_exists($filename);
}

function unlink($filename)
{
    return FaviconModelTest::$functions->unlink($filename);
}

class FaviconModelTest extends BaseTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    public static $functions;

    public function setUp()
    {
        parent::setUp();

        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array(
                'file_put_contents',
                'file_get_contents',
                'file_exists',
                'unlink'
            ))
            ->getMock();
    }

    public function testCreateFeedFavicon()
    {
        $this->assertCreateFeed($this->buildFeed());

        self::$functions
            ->expects($this->once())
            ->method('file_put_contents')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png'),
                $this->equalTo('binary data')
            )
            ->will($this->returnValue(true));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
    }

    public function testCreateEmptyFavicon()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertFalse(create_feed_favicon(1, 'image/png', ''));
    }

    public function testCreateFeedFaviconWithUnableToWriteOnDisk()
    {
        $this->assertCreateFeed($this->buildFeed());

        self::$functions
            ->expects($this->once())
            ->method('file_put_contents')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png'),
                $this->equalTo('binary data')
            )
            ->will($this->returnValue(false));

        $this->assertFalse(create_feed_favicon(1, 'image/png', 'binary data'));
    }

    public function testCreateFeedFaviconAlreadyExists()
    {
        $this->assertCreateFeed($this->buildFeed());

        self::$functions
            ->expects($this->once())
            ->method('file_put_contents')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png'),
                $this->equalTo('binary data')
            )
            ->will($this->returnValue(true));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertFalse(create_feed_favicon(1, 'image/png', 'binary data'));
    }

    public function testGetFaviconsWithDataUrl()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertCreateFeed($this->buildFeed('another feed url'));
        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertEquals(2, create_feed_favicon(2, 'image/gif', 'some binary data'));

        self::$functions
            ->expects($this->at(0))
            ->method('file_get_contents')
            ->with(
                $this->stringEndsWith('57978a20204f7af6967571041c79d907a8a8072c.png')
            )
            ->will($this->returnValue('binary data'));

        self::$functions
            ->expects($this->at(1))
            ->method('file_get_contents')
            ->with(
                $this->stringEndsWith('36242b50974c41478569d66616346ee5f2ad7b6e.gif')
            )
            ->will($this->returnValue('some binary data'));

        $favicons = get_favicons_with_data_url(1);
        $this->assertCount(2, $favicons);

        $this->assertEquals(1, $favicons[0]['feed_id']);
        $this->assertEquals('57978a20204f7af6967571041c79d907a8a8072c', $favicons[0]['hash']);
        $this->assertEquals('image/png', $favicons[0]['type']);
        $this->assertEquals('data:image/png;base64,YmluYXJ5IGRhdGE=', $favicons[0]['data_url']);

        $this->assertEquals(2, $favicons[1]['feed_id']);
        $this->assertEquals('36242b50974c41478569d66616346ee5f2ad7b6e', $favicons[1]['hash']);
        $this->assertEquals('image/gif', $favicons[1]['type']);
        $this->assertEquals('data:image/gif;base64,c29tZSBiaW5hcnkgZGF0YQ==', $favicons[1]['data_url']);
    }

    public function testGetItemsFavicons()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertCreateFeed($this->buildFeed('another feed url'));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertEquals(2, create_feed_favicon(2, 'image/gif', 'some binary data'));

        $items = Model\Item\get_items(1);
        $favicons = get_items_favicons($items);
        $this->assertCount(2, $favicons);

        $this->assertEquals(1, $favicons[1]['feed_id']);
        $this->assertEquals('57978a20204f7af6967571041c79d907a8a8072c', $favicons[1]['hash']);
        $this->assertEquals('image/png', $favicons[1]['type']);

        $this->assertEquals(2, $favicons[2]['feed_id']);
        $this->assertEquals('36242b50974c41478569d66616346ee5f2ad7b6e', $favicons[2]['hash']);
        $this->assertEquals('image/gif', $favicons[2]['type']);
    }

    public function testGetFeedsFavicons()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertCreateFeed($this->buildFeed('another feed url'));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertEquals(2, create_feed_favicon(2, 'image/gif', 'some binary data'));

        $feeds = Model\Feed\get_feeds(1);
        $favicons = get_feeds_favicons($feeds);
        $this->assertCount(2, $favicons);

        $this->assertEquals(1, $favicons[1]['feed_id']);
        $this->assertEquals('57978a20204f7af6967571041c79d907a8a8072c', $favicons[1]['hash']);
        $this->assertEquals('image/png', $favicons[1]['type']);

        $this->assertEquals(2, $favicons[2]['feed_id']);
        $this->assertEquals('36242b50974c41478569d66616346ee5f2ad7b6e', $favicons[2]['hash']);
        $this->assertEquals('image/gif', $favicons[2]['type']);
    }

    public function testHasFavicon()
    {
        $this->assertCreateFeed($this->buildFeed());

        self::$functions
            ->expects($this->once())
            ->method('file_put_contents')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png'),
                $this->equalTo('binary data')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->once())
            ->method('file_exists')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png')
            )
            ->will($this->returnValue(true));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertTrue(has_favicon(1));
        $this->assertFalse(has_favicon(2));
    }

    public function testHasFaviconWhenFileMissing()
    {
        $this->assertCreateFeed($this->buildFeed());

        self::$functions
            ->expects($this->any())
            ->method('file_put_contents')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png'),
                $this->equalTo('binary data')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->once())
            ->method('file_exists')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png')
            )
            ->will($this->returnValue(false));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertFalse(has_favicon(1));
    }

    public function testPurgeFavicons()
    {
        $this->assertCreateFeed($this->buildFeed());
        $this->assertCreateFeed($this->buildFeed('another feed url'));

        $this->assertEquals(1, create_feed_favicon(1, 'image/png', 'binary data'));
        $this->assertEquals(2, create_feed_favicon(2, 'image/gif', 'some binary data'));

        self::$functions
            ->expects($this->any())
            ->method('file_exists')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->once())
            ->method('unlink')
            ->with(
                $this->stringEndsWith('data/favicons/57978a20204f7af6967571041c79d907a8a8072c.png')
            );

        $this->assertTrue(Model\Feed\remove_feed(1, 1));

        $favicons = get_favicons_with_data_url(1);
        $this->assertCount(1, $favicons);
        $this->assertEquals(2, $favicons[0]['feed_id']);
    }
}
