<?php

class pageSubscriptionTest extends minifluxTestCase
{
    const DEFAULT_COUNTER_PAGE = null;
    const DEFAULT_COUNTER_UNREAD = 3;

    protected function setUp()
    {
        // trigger database fixtures onSetUp routines
        $dataset = $this->getDataSet('fixture_feed_error_disabled_normal');
        $this->getDatabaseTester($dataset)->onSetUp();

        // Set the base URL for the tests.
        $this->setBrowserUrl(PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL);
    }

    public function setUpPage()
    {
        $url = $this->getURLPageSubscriptions();
        $this->doLoginIfRequired($url);

        $this->basePageHeading = $this->getBasePageHeading();
        $this->expectedPageUrl = $url;
    }

    public function getExpectedPageTitle()
    {
        return "$this->basePageHeading";
    }

    public function testNoAlertShown()
    {
        // load different fixture and reload the page
        $backupDataTester = static::$databaseTester;

        static::$databaseTester = null;

        $dataset = $this->getDataSet('fixture_feed1', 'fixture_feed2');
        $this->getDatabaseTester($dataset)->onSetUp();

        static::$databaseTester = $backupDataTester;
        $this->refresh();

        $alertBox = $this->getAlertBox();
        $this->assertEmpty($alertBox, 'Unexpected alert box found');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = 6;
        $this->expectedDataSet = $dataset;
    }

    public function testNoPerFeedErrorMessages()
    {
        // load different fixture and reload the page
        $backupDataTester = static::$databaseTester;

        static::$databaseTester = null;

        $dataset = $this->getDataSet('fixture_feed1', 'fixture_feed2');
        $this->getDatabaseTester($dataset)->onSetUp();

        static::$databaseTester = $backupDataTester;
        $this->refresh();

        $messages = $this->getFeedErrorMessages();

        $this->assertCount(0, $messages, 'Feeds have unexpected error messages visible.');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = 6;
        $this->expectedDataSet = $dataset;
    }

    public function testAlertOnParsingError()
    {
        $alertBox = $this->getAlertBox();
        $this->assertCount(1, $alertBox, 'No alert box found');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testFailedFeedIsFirstFeed()
    {
        $feeds = $this->getArticles();

        $this->assertEquals($this->getFeedFailed(), $feeds[0], 'The first feed is not the failed feed');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testFailedFeedIsHighlighted()
    {
        $feeds = $this->getArticles();

        $this->assertNotEquals($feeds[0]->css('background-color'), $feeds[1]->css('background-color'), 'The failed feed is not highlighted');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testFailedHasErrorMessageVisible()
    {
        $feed = $this->getFeedFailed();

        $this->assertCount(1, $this->getFeedErrorMessages($feed), 'The failed feed has no error message');
        $this->assertCount(1, $this->getFeedErrorMessages(), 'Another than the failed feed has an error message');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testDisabledIsLastFeed()
    {
        $feeds = $this->getArticles();

        $this->assertEquals($this->getFeedDisabled(), $feeds[count($feeds)-1], 'The disabled feed is not the last feed');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }
}
