<?php

class pageBookmarksTest extends minifluxTestCase
{
    const DEFAULT_COUNTER_PAGE = 6;
    const DEFAULT_COUNTER_UNREAD = 6;

    public function setUpPage()
    {
        $url = $this->getURLPageBookmarks();
        $this->doLoginIfRequired($url);

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

    public function testOnlyBookmarkedArticles()
    {
        $articles = $this->getArticlesNotBookmarked();
        $this->assertEmpty($articles, 'found not bookmarked articles.');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = static::$databaseTester->getDataSet();
    }

    public function testMarkReadBookmarkedArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $visible = $this->waitForIconMarkReadVisible($article);
        $this->assertTrue($visible, 'read icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadBookmarkedArticle', 'fixture_feed2');
    }

    /**
     * @group moz_unsupported
     */
    public function testMarkReadBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleUnreadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $visible = $this->waitForIconMarkReadVisible($article);
        $this->assertTrue($visible, 'read icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadBookmarkedArticle', 'fixture_feed2');
    }

    public function testMarkUnreadBookmarkedArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $invisible = $this->waitForIconMarkReadInvisible($article);
        $this->assertTrue($invisible, 'read icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
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

        $invisible = $this->waitForIconMarkReadInvisible($article);
        $this->assertTrue($invisible, 'read icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadBookmarkedArticle', 'fixture_feed2');
    }

    public function testUnbookmarkReadArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
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

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle', 'fixture_feed2');
    }

    public function testUnbookmarkUnreadArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkUnreadArticle', 'fixture_feed2');
    }

    /**
     * @group moz_unsupported
     */
    public function testUnbookmarkUnreadArticleKeyboard()
    {
        $article = $this->getArticleUnreadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkUnreadArticle', 'fixture_feed2');
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

    public function testRemoveUnreadBookmarkedArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkRemove($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_RemoveUnreadBookmarkedArticle', 'fixture_feed2');
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

        $this->waitForIconMarkReadInvisible($article);

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = 1;
        $this->expectedDataSet = $this->getDataSet('fixture_OneUnreadArticle');
    }

    public function testUnreadCounterFromValueToNothing()
    {
        // load different fixture and reload the page
        $backupDataTester = static::$databaseTester;

        static::$databaseTester = null;

        $dataset = $this->getDataSet('fixture_OneUnreadArticle');
        $this->getDatabaseTester($dataset)->onSetUp();

        static::$databaseTester = $backupDataTester;
        $this->refresh();

        // start the "real" test
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $this->waitForIconMarkReadVisible($article);

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = '';
        $this->expectedDataSet = $this->getDataSet('fixture_OnlyReadArticles');
    }

    public function testRedirectWithZeroArticles()
    {
        $articles = $this->getArticles();

        foreach ($articles as $article) {
            $link = $this->getLinkBookmarkStatusToogle($article);
            $link->click();

            $this->waitForArticleInvisible($article);
        }

        $visible = $this->waitForAlert();
        $this->assertTrue($visible, 'alert box did not appear');

        $this->expectedCounterPage = null;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_NoBookmarkedArticles');

        $this->ignorePageTitle = true;
    }
}
