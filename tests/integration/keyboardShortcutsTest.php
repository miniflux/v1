<?php

class keyboardShortcutTest extends minifluxTestCase
{
    const DEFAULT_COUNTER_PAGE = 8;
    const DEFAULT_COUNTER_UNREAD = 6;

    protected function setUp()
    {
        parent::setUp();
    }

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

    public function testNoAlertShown()
    {
        $alertBox = $this->getAlertBox();
        $this->assertEmpty($alertBox, 'Unexpected alert box found');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
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
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
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
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
    public function testNextItemShortcutC()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys($this->getShortcutNextItemC());

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);
        $secondIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[1]);

        $this->assertTrue($firstIsNotCurrentArticle, 'The first Article is still the current Article');
        $this->assertTrue($secondIsCurrentArticle, 'The second Article is not the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
    public function testPreviousItemA()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[1]);
        $this->keys($this->getShortcutPreviousItemA());

        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);
        $secondIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[1]);

        $this->assertTrue($firstIsCurrentArticle, 'The first article is not the current article');
        $this->assertTrue($secondIsNotCurrentArticle, 'The second Article is still the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
    public function testPreviousItemB()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[1]);
        $this->keys($this->getShortcutPreviousItemB());

        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);
        $secondIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[1]);

        $this->assertTrue($firstIsCurrentArticle, 'The first article is not the current article');
        $this->assertTrue($secondIsNotCurrentArticle, 'The second Article is still the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
    public function testPreviousItemC()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[1]);
        $this->keys($this->getShortcutPreviousItemC());

        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);
        $secondIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[1]);

        $this->assertTrue($firstIsCurrentArticle, 'The first article is not the current article');
        $this->assertTrue($secondIsNotCurrentArticle, 'The second Article is still the current Article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
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
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
    public function testPreviousStopsAtFirstArticle()
    {
        $articles = $this->getArticles();
        $lastIndex = count($articles) - 1;

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys($this->getShortcutPreviousItemA());

        $lastIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[$lastIndex]);
        $firstIsCurrentArticle = $this->waitForArticleIsCurrentArticle($articles[0]);

        $this->assertTrue($lastIsNotCurrentArticle, 'The last Article is still the current Article');
        $this->assertTrue($firstIsCurrentArticle, 'The first article is not the current article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group ie_unsupported
     * @group moz_unsupported
     */
    public function testSHIFTModifierIsDisabled()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys(PHPUnit_Extensions_Selenium2TestCase_Keys::SHIFT.$this->getShortcutNextItemC());
        $this->keys(PHPUnit_Extensions_Selenium2TestCase_Keys::SHIFT);

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);

        $this->assertFalse($firstIsNotCurrentArticle, 'The first article is not the current article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group ie_unsupported
     * @group moz_unsupported
     */
    public function testALTModifierIsDisabled()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys(PHPUnit_Extensions_Selenium2TestCase_Keys::ALT.$this->getShortcutNextItemB());
        $this->keys(PHPUnit_Extensions_Selenium2TestCase_Keys::ALT);

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);

        $this->assertFalse($firstIsNotCurrentArticle, 'The first article is not the current article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group ie_unsupported
     * @group moz_unsupported
     */
    public function testCTRLModifierIsDisabled()
    {
        $articles = $this->getArticles();

        $this->setArticleAsCurrentArticle($articles[0]);
        $this->keys(PHPUnit_Extensions_Selenium2TestCase_Keys::CONTROL.$this->getShortcutNextItemB());
        $this->keys(PHPUnit_Extensions_Selenium2TestCase_Keys::CONTROL);

        $firstIsNotCurrentArticle = $this->waitForArticleIsNotCurrentArticle($articles[0]);

        $this->assertFalse($firstIsNotCurrentArticle, 'The first article is not the current article');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    /**
     * @group moz_unsupported
     */
    public function testShortcutsOnInputFiledAreDisabled()
    {
        $url = $this->getURLPagePreferences();

        $this->url($url);

        $this->byId('form-username')->click();
        $this->keys($this->getShortcutGoToUnread());

        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $url;
        $this->expectedDataSet = static::$databaseTester->getDataSet();

        $this->ignorePageTitle = true;
    }

    /**
     * @group moz_unsupported
     */
    public function testGoToBookmarks()
    {
        $this->sendKeysAndWaitForPageLoaded('gb');

        $this->expectedCounterPage = '6';
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPageBookmarks();
        $this->expectedDataSet = static::$databaseTester->getDataSet();

        $this->ignorePageTitle = true;
    }

    /**
     * @group moz_unsupported
     */
    public function testGoToHistory()
    {
        $this->sendKeysAndWaitForPageLoaded('gh');

        $this->expectedCounterPage = '6';
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPageHistory();
        $this->expectedDataSet = static::$databaseTester->getDataSet();

        $this->ignorePageTitle = true;
    }

    /**
     * @group moz_unsupported
     */
    public function testGoToUnread()
    {
        $this->sendKeysAndWaitForPageLoaded($this->getShortcutGoToUnread());

        $this->expectedCounterPage = '6';
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPageUnread();
        $this->expectedDataSet = static::$databaseTester->getDataSet();

        $this->ignorePageTitle = true;
    }

    /**
     * @group moz_unsupported
     */
    public function testGoToSubscriptions()
    {
        $this->sendKeysAndWaitForPageLoaded('gs');

        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=feeds';
        $this->expectedDataSet = static::$databaseTester->getDataSet();

        $this->ignorePageTitle = true;
    }

    /**
     * @group moz_unsupported
     */
    public function testGoToPreferences()
    {
        $this->sendKeysAndWaitForPageLoaded('gp');

        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedPageUrl = $this->getURLPagePreferences();
        $this->expectedDataSet = static::$databaseTester->getDataSet();

        $this->ignorePageTitle = true;
    }
}
