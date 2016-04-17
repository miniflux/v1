<?php

class pageHistoryTest extends minifluxTestCase
{
    const DEFAULT_COUNTER_PAGE = 6;
    const DEFAULT_COUNTER_UNREAD = 6;

    public function setUpPage()
    {
        $url = $this->getURLPageHistory();
        $this->doLoginIfRequired($url);
        ;

        $this->basePageHeading = $this->getBasePageHeading();
        $this->expectedPageUrl = $url;
    }

    public function getExpectedPageTitle()
    {
        return "$this->basePageHeading ($this->expectedCounterPage)";
    }

    public function testNoAlertShown()
    {
        $alertBox = $this->getAlertBox();
        $this->assertEmpty($alertBox, 'Unexpected alert box found');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testItemsFromAllFeeds()
    {
        $articles = $this->getArticlesNotFromFeedOne();
        $this->assertNotEmpty($articles, 'no articles from other feeds found');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testOnlyReadArticles()
    {
        $articles = $this->getArticlesUnread();
        $this->assertEmpty($articles, 'found unread articles.');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testMarkUnreadNotBookmarkedArticleLink()
    {
        $article = $this->getArticleReadNotBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $visible = $this->waitForArticleInvisible($article);
        $this->assertTrue($visible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadNotBookmarkedArticle', 'fixture_feed2');
    }

    /**
     * @group moz_unsupported
     */
    public function testMarkUnreadNotBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleReadNotBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $visible = $this->waitForArticleInvisible($article);
        $this->assertTrue($visible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadNotBookmarkedArticle', 'fixture_feed2');
    }

    public function testMarkUnreadBookmarkedArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $visible = $this->waitForArticleInvisible($article);
        $this->assertTrue($visible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadBookmarkedArticle', 'fixture_feed2');
    }

    /**
     * @group moz_unsupported
     */
    public function testMarkUnreadBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleReadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $visible = $this->waitForArticleInvisible($article);
        $this->assertTrue($visible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadBookmarkedArticle', 'fixture_feed2');
    }

    public function testBookmarkReadArticleLink()
    {
        $article = $this->getArticleReadNotBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $visible = $this->waitForIconBookmarkVisible($article);
        $this->assertTrue($visible, 'bookmark icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_BookmarkReadArticle', 'fixture_feed2');
    }

    /**
     * @group moz_unsupported
     */
    public function testBookmarkReadArticleKeyboard()
    {
        $article = $this->getArticleReadNotBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $visible = $this->waitForIconBookmarkVisible($article);
        $this->assertTrue($visible, 'bookmark icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_BookmarkReadArticle', 'fixture_feed2');
    }

    public function testUnbookmarkReadArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $invisible = $this->waitForIconBookmarkInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle', 'fixture_feed2');
    }

    /**
     * @group moz_unsupported
     */
    public function testUnbookmarkReadArticleKeyboard()
    {
        $article = $this->getArticleReadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $invisible = $this->waitForIconBookmarkInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle', 'fixture_feed2');
    }

    public function testRemoveReadNotBookmarkedArticleLink()
    {
        $article = $this->getArticleReadNotBookmarked();

        $link = $this->getLinkRemove($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_RemoveReadNotBookmarkedArticle', 'fixture_feed2');
    }

    public function testRemoveReadBookmarkedArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkRemove($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_RemoveReadBookmarkedArticle', 'fixture_feed2');
    }

    public function testFlushAllKeepsBookmarkedAndUnread()
    {
        $link = $this->getLinkFlushHistory();
        $link->click();

        $destructiveLink = $this->getLinkDestructive();
        $destructiveLink->click();

        $this->expectedCounterPage = 3;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_NoReadNotBookmarkedArticles');

        $this->ignorePageTitle = true;
    }

    public function testUnreadCounterFromNothingToValue()
    {
        // load different fixture and reload the page
        $backupDataTester = static::$databaseTester;

        static::$databaseTester = null;

        $dataset = $this->getDataSet('fixture_OnlyReadArticles');
        $this->getDatabaseTester($dataset)->onSetUp();

        static::$databaseTester = $backupDataTester;
        $this->refresh();

        // start the "real" test
        // dont't trust the name! The Article is read+bookmarked here
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE + static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedCounterUnread = 1;
        $this->expectedDataSet = $this->getDataSet('fixture_OneUnreadArticle');
    }

    public function testRedirectWithZeroArticles()
    {
        $articles = $this->getArticles();
        $this->assertGreaterThanOrEqual(1, count($articles), 'no articles found');

        foreach ($articles as $article) {
            $link = $this->getLinkReadStatusToogle($article);
            $link->click();

            $this->waitForArticleInvisible($article);
        }

        $visible = $this->waitForAlert();
        $this->assertTrue($visible, 'alert box did not appear');

        $this->expectedCounterPage = null;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + static::DEFAULT_COUNTER_PAGE;
        $this->expectedDataSet = $this->getDataSet('expected_NoReadArticles');

        $this->ignorePageTitle = true;
    }
}
