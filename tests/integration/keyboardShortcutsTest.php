<?php

require_once 'minifluxTestCase.php';

class keyboardShortcutTest extends minifluxTestCase
{
    const DEFAULT_COUNTER_PAGE = 8;
    const DEFAULT_COUNTER_UNREAD = 6;

    public function setUpPage()
    {
        $url = $this->getURLPageFirstFeed();
        $this->doLoginIfRequired($url);

        $this->basePageHeading = $this->getBasePageHeading();
        $this->expectedPageUrl = $url;
    }

    protected function getExpectedPageTitle()
    {
        return "($this->expectedCounterPage) $this->basePageHeading";
    }

    public function testNextItemShortcutA()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys($this->getShortcutNextItemA());

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);
        $secondIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[1]);

        $this->assertTrue($firstIsNotCurrentArticle, 'The first Article is still the current Article');
        $this->assertTrue($secondIsCurrentArticle, 'The second Article is not the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }

    public function testNextItemShortcutB()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys($this->getShortcutNextItemB());

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);
        $secondIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[1]);

        $this->assertTrue($firstIsNotCurrentArticle, 'The first Article is still the current Article');
        $this->assertTrue($secondIsCurrentArticle, 'The second Article is not the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');;
    }

    public function testPreviousItemA()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[1]);
        $this->keys($this->getShortcutPreviousItemA());

        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);
        $secondIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[1]);

        $this->assertTrue($firstIsCurrentArticle, 'The first Article is not the current Article');
        $this->assertTrue($secondIsNotCurrentArticle, 'The second Article is still the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }

    public function testPreviousItemB()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[1]);
        $this->keys($this->getShortcutPreviousItemB());

        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);
        $secondIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[1]);

        $this->assertTrue($firstIsCurrentArticle, 'The first Article is not the current Article');
        $this->assertTrue($secondIsNotCurrentArticle, 'The second Article is still the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }

    public function testNextStopsAtLastArticle()
    {
        $articles = $this->getArticles();
        $lastIndex = count($articles) - 1;

        $this->setArticleAsCurrentArticle($articles[$lastIndex]);
        $this->keys($this->getShortcutNextItemA());

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);
        $lastIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[$lastIndex]);

        $this->assertTrue($firstIsNotCurrentArticle, 'The first Article is still the current Article');
        $this->assertTrue($lastIsCurrentArticle, 'The last Article is not the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }

    public function testPreviousStopsAtFirstArticle()
    {
        $articles = $this->getArticles();
        $lastIndex = count($articles) - 1;

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys($this->getShortcutPreviousItemA());

        $lastIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[$lastIndex]);
        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);

        $this->assertTrue($lastIsNotCurrentArticle, 'The last Article is still the current Article');
        $this->assertTrue($firstIsCurrentArticle, 'The first Article is not the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }

    public function testShortcutsOnInputFiledAreDisabled()
    {
        $url = $this->getURLPagePreferences();

        $this->url($url);
        $this->byId('form-username')->value($this->getShortcutGoToUnread());

        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $url;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');

        $this->ignorePageTitle = TRUE;
    }

    public function testGoToBookmarks()
    {
        $this->sendKeysAndWaitForPageLoaded('gb');

        $this->expectedCounterPage = '6';
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPageBookmarks();
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');

        $this->ignorePageTitle = TRUE;
    }

    public function testGoToHistory()
    {
        $this->sendKeysAndWaitForPageLoaded('gh');

        $this->expectedCounterPage = '6';
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPageHistory();
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');

        $this->ignorePageTitle = TRUE;
    }

    public function testGoToUnread()
    {
        $this->sendKeysAndWaitForPageLoaded($this->getShortcutGoToUnread());

        $this->expectedCounterPage = '6';
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPageUnread();
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');

        $this->ignorePageTitle = TRUE;
    }

    public function testGoToSubscriptions()
    {
        $this->sendKeysAndWaitForPageLoaded('gs');

        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=feeds';
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');

        $this->ignorePageTitle = TRUE;
    }

    public function testGoToPreferences()
    {
        $this->sendKeysAndWaitForPageLoaded('gp');

        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPagePreferences();
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');

        $this->ignorePageTitle = TRUE;
    }
}
?>