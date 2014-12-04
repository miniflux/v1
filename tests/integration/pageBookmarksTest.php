<?php

require_once 'minifluxTestCase.php';

class pageBookmarksTest extends minifluxTestCase
{
    const DEFAULT_COUNTER_PAGE = 6;
    
    public function setUpPage()
    {
        $url = $this->getURLPageBookmarks();
        parent::setUpPage($url);
        
        $this->basePageHeading = $this->getBasePageHeading();
        $this->expectedPageUrl = $url;
    }

    public function getExpectedPageTitle()
    {
        return "$this->basePageHeading ($this->expectedCounterPage)";
    }

    public function testItemsFromAllFeeds()
    {
        $articles = $this->getArticlesNotFromFeedOne();
        $this->assertNotEmpty($articles, 'no articles from other feeds found');
        
        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }
    
    public function testOnlyBookmarkedArticles()
    {
        $articles = $this->getArticlesNotBookmarked();
        $this->assertEmpty($articles, 'found not bookmarked articles.');
        
        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
        $this->expectedDataSet = $this->getDataSet('fixture_feed1');
    }
    
    public function testMarkReadBookmarkedArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $visible = $this->waitForIconMarkReadVisible($article);
        $this->assertTrue($visible, 'read icon is not visible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
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
        $this->expectedDataSet = $this->getDataSet('expected_MarkReadBookmarkedArticle');
    }

    public function testMarkUnreadBookmarkedArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkReadStatusToogle($article);
        $link->click();

        $invisible = $this->waitForIconMarkReadInvisible($article);
        $this->assertTrue($invisible, 'read icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE;
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
        $this->expectedDataSet = $this->getDataSet('expected_MarkUnreadBookmarkedArticle');
    }

    public function testUnbookmarkReadArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle');
        
    }

    public function testUnbookmarkReadArticleKeyboard()
    {
        $article = $this->getArticleReadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkReadArticle');
    }

    public function testUnbookmarkUnreadArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkBookmarkStatusToogle($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');
        
        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkUnreadArticle');
    }

    public function testUnbookmarkUnreadArticleKeyboard()
    {
        $article = $this->getArticleUnreadBookmarked();

        $this->setArticleAsCurrentArticle($article);
        $this->keys($this->getShortcutToogleBookmarkStatus());

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'bookmark icon is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedDataSet = $this->getDataSet('expected_UnbookmarkUnreadArticle');
    }

    public function testRemoveReadBookmarkedArticleLink()
    {
        $article = $this->getArticleReadBookmarked();

        $link = $this->getLinkRemove($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedDataSet = $this->getDataSet('expected_RemoveReadBookmarkedArticle');
    }

    public function testRemoveUnreadBookmarkedArticleLink()
    {
        $article = $this->getArticleUnreadBookmarked();

        $link = $this->getLinkRemove($article);
        $link->click();

        $invisible = $this->waitForArticleInvisible($article);
        $this->assertTrue($invisible, 'article is is not invisible');

        $this->expectedCounterPage = static::DEFAULT_COUNTER_PAGE - 1;
        $this->expectedDataSet = $this->getDataSet('expected_RemoveUnreadBookmarkedArticle');
    }
    
    public function testRedirectWithZeroArticles()
    {
        $articles = $this->getArticles();
        
        foreach($articles as $article) {
            $link = $this->getLinkBookmarkStatusToogle($article);
            $link->click();
            
            $this->waitForArticleInvisible($article);
        }
        
        $visible = $this->waitForAlert();
        $this->assertTrue($visible, 'alert box did not appear');
        
        $this->expectedDataSet = $this->getDataSet('expected_NoBookmarkedArticles', FALSE);

        $this->ignorePageTitle = TRUE;
    }
}
?>