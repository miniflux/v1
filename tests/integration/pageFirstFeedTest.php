<?php

require_once 'minifluxTestCase.php';

class pageFirstFeedTest extends minifluxTestCase
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

    public function getExpectedPageTitle()
    {
        return "($this->expectedCounterPage) $this->basePageHeading";
    }

    public function testOnlyItemsFromFirstFeed()
    {
        $articles = $this->getArticlesNotFromFeedOne();
        $this->assertEmpty($articles, 'found articles from other feeds on page for first feed.');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }

    public function testMarkReadNotBookmarkedArticleLink()
    {
        $article = $this->getArticleUnreadNotBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $visible = $this->waitForIconMarkReadVisible($article);
        $this->assertTrue($visible, 'read icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadNotBookmarkedArticle');
    }

    public function testMarkReadNotBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleUnreadNotBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $visible = $this->waitForIconMarkReadVisible($article);
        $this->assertTrue($visible, 'read icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadNotBookmarkedArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadBookmarkedArticle');
    }

    public function testMarkReadBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleUnreadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $visible = $this->waitForIconMarkReadVisible($article);
        $this->assertTrue($visible, 'read icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadBookmarkedArticle');
    }

    public function testMarkUnreadNotBookmarkedArticleLink()
    {
        $article = $this->getArticleReadNotBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $invisible = $this->waitForIconMarkReadInvisible($article);
        $this->assertTrue($invisible, 'read icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadNotBookmarkedArticle');
    }

    public function testMarkUnreadNotBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleReadNotBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $invisible = $this->waitForIconMarkReadInvisible($article);
        $this->assertTrue($invisible, 'read icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadNotBookmarkedArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadBookmarkedArticle');
    }

    public function testMarkUnreadBookmarkedArticleKeyboard()
    {
        $article = $this->getArticleReadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleReadStatus());

        $invisible = $this->waitForIconMarkReadInvisible($article);
        $this->assertTrue($invisible, 'read icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD + 1;
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadBookmarkedArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_BookmarkReadArticle');
    }

    public function testBookmarkReadArticleKeyboard()
    {
        $article = $this->getArticleReadNotBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $visible = $this->waitForIconBookmarkVisible($article);
        $this->assertTrue($visible, 'bookmark icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_BookmarkReadArticle');
    }

    public function testBookmarkUnreadArticleLink()
    {
        $article = $this->getArticleUnreadNotBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $visible = $this->waitForIconBookmarkVisible($article);
        $this->assertTrue($visible, 'bookmark icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_BookmarkUnreadArticle');
    }

    public function testBookmarkUnreadArticleKeyboard()
    {
        $article = $this->getArticleUnreadNotBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $visible = $this->waitForIconBookmarkVisible($article);
        $this->assertTrue($visible, 'bookmark icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_BookmarkUnreadArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle');
    }

    public function testUnbookmarkReadArticleKeyboard()
    {
        $article = $this->getArticleReadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $invisible = $this->waitForIconBookmarkInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle');
    }

    public function testUnbookmarkUnreadArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $invisible = $this->waitForIconBookmarkInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkUnreadArticle');
    }

    public function testUnbookmarkUnreadArticleKeyboard()
    {
        $article = $this->getArticleUnreadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $invisible = $this->waitForIconBookmarkInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkUnreadArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_RemoveReadNotBookmarkedArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_RemoveReadBookmarkedArticle');
    }

    public function testRemoveUnreadNotBookmarkedArticleLink()
    {
        $article = $this->getArticleUnreadNotBookmarked();

        $link = $this->getLinkRemove($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedCounterUnread = static::DEFAULT_COUNTER_UNREAD - 1;
        $this->expectedDataSet = $this->getDataSet('expected_RemoveUnreadNotBookmarkedArticle');
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
        $this->expectedDataSet = $this->getDataSet('expected_RemoveUnreadBookmarkedArticle');
    }

    public function testMarkFeedReadHeaderLink()
    {
        $link = $this->getLinkFeedMarkReadHeader();
        $link->click();

        $read = $this->waitForArticlesMarkRead();
        $this->assertTrue($read, 'there are still unread articles');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = 2;
        $this->expectedDataSet = $this->getDataSet('expected_MarkFeedRead');
    }

    public function testMarkFeedReadBottomLink()
    {
        $link = $this->getLinkFeedMarkReadBottom();
        $link->click();

        $read = $this->waitForArticlesMarkRead();
        $this->assertTrue($read, 'there are still unread articles');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = 2;
        $this->expectedDataSet = $this->getDataSet('expected_MarkFeedRead');
    }

    public function testUnreadCounterFromNothingToValue()
    {
        // load different fixture and reload the page
        $backupDataTester = static::$databaseTester;

        static::$databaseTester = NULL;
        $this->getDatabaseTester('fixture_OnlyReadArticles', FALSE)->onSetUp();

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
        $this->expectedDataSet = $this->getDataSet('fixture_OneUnreadArticle',FALSE);
    }

    public function testUnreadCounterFromValueToNothing()
    {
        // load different fixture and reload the page
        $backupDataTester = static::$databaseTester;

        static::$databaseTester = NULL;
        $this->getDatabaseTester('fixture_OneUnreadArticle', FALSE)->onSetUp();

        static::$databaseTester = $backupDataTester;
        $this->refresh();

        // start the "real" test
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $this->waitForIconMarkReadVisible($article);

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedCounterUnread = '';
        $this->expectedDataSet = $this->getDataSet('fixture_OnlyReadArticles',FALSE);
    }

    public function testRedirectWithZeroArticles()
    {
        $articles = $this->getArticles();
        $this->assertGreaterThanOrEqual(1, count($articles), 'no articles found');

        foreach($articles as $article) {
            $link = $this->getLinkRemove($article);
            $link->click();

            $this->waitForArticleInvisible($article);
        }

        $visible = $this->waitForAlert();
        $this->assertTrue($visible, 'alert box did not appear');

        $this->expectedCounterPage = NULL;
        $this->expectedCounterUnread = 2;
        $this->expectedDataSet = $this->getDataSet('expected_FirstFeedAllRemoved');

        $this->ignorePageTitle = TRUE;
    }
}
?>