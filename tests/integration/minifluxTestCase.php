<?php

use PHPUnit_Extensions_Selenium2TestCase_Keys as Keys;

abstract class minifluxTestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    protected $basePageHeading = null;
    protected $expectedPageUrl = null;
    protected $expectedDataSet = null;
    protected $expectedCounterPage = null;
    protected $expectedCounterUnread = '';

    protected $ignorePageTitle = false;

    protected static $databaseConnection = null;
    protected static $databaseTester = null;

    private $waitTimeout = 5000;

    public static function browsers()
    {
        return json_decode(PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BROWSERS, true);
    }

    protected function setUp()
    {
        parent::setUp();

        // trigger database fixtures onSetUp routines
        $dataset = $this->getDataSet('fixture_feed1', 'fixture_feed2');
        $this->getDatabaseTester($dataset)->onSetUp();

        // Set the base URL for the tests.
        $this->setBrowserUrl(PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL);
    }

    public function doLoginIfRequired($url)
    {
        // (re)load the requested page
        $this->url($url);

        // check if login is need and login
        $elements = $this->elements($this->using('css selector')->value('body#login-page'));

        if (count($elements) === 1) {
            $this->byCssSelector("input[value='".DB_FILENAME."']")->click();

            $this->byId('form-username')->click();
            $this->keys('admin');
            $this->byId('form-password')->click();
            $this->keys('admin');
            $this->byTag('form')->submit();

            $this->url($url);
        }
    }

    public static function tearDownAfterClass()
    {
        static::$databaseConnection = null;
        static::$databaseTester = null;
    }

    protected function assertPostConditions()
    {
        // counter exists on every page
        $this->assertTrue($this->waitForElementByIdText('page-counter', $this->expectedCounterPage), 'page-counter differ from expected');
        $this->assertTrue($this->waitForElementByIdText('nav-counter', $this->expectedCounterUnread), 'unread counter differ from expectation');

        // url has not been changed (its likely that everything was done via javascript then)
        $this->assertEquals($this->expectedPageUrl, $this->url(), 'URL has been changed.');

        // some tests switch to a page where no counter exists and the expected
        // pagetitle doesn't match to definition.
        if ($this->ignorePageTitle === false) {
            //remove LEFT-TO-RIGHT MARK char from string as the webdriver does it when using text() on the page <h[1|2|3]>
            $pagetitle = preg_replace('/\x{200E}/u', '', $this->title());
            $this->assertEquals($this->getExpectedPageTitle(), $pagetitle, 'page title differ from expectation');
        }

        // assert that the current database matches the expected database
        $expectedDataSetFiltered = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($this->expectedDataSet);
        $expectedDataSetFiltered->addIncludeTables(array('items'));
        $expectedDataSetFiltered->setExcludeColumnsForTable('items', array('updated'));

        // TODO: changes row order, why?
        //$actualDataSet = $this->getConnection()->createDataSet();
        $actualDataSet = new PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
        $actualDataSet->addTable('items', 'SELECT * FROM items');
        $actualDataSetFiltered = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($actualDataSet);
        $actualDataSetFiltered->setExcludeColumnsForTable('items', array('updated'));

        PHPUnit_Extensions_Database_TestCase::assertDataSetsEqual($expectedDataSetFiltered, $actualDataSetFiltered, 'Unexpected changes in database');
    }

    protected function getDataSet()
    {
        $compositeDs = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet();
        $dataSetFiles = func_get_args();

        foreach ($dataSetFiles as $dataSetFile) {
            $ds = new PHPUnit_Extensions_Database_DataSet_XmlDataSet(dirname(__FILE__).DIRECTORY_SEPARATOR.'datasets'.DIRECTORY_SEPARATOR.$dataSetFile.'.xml');
            $compositeDs->addDataSet($ds);
        }

        return $compositeDs;
    }

    protected function getConnection()
    {
        if (static::$databaseConnection === null) {
            // let Miniflux setup the database
            require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'common.php';

            if (!ENABLE_MULTIPLE_DB) {
                throw new Exception('Enable multiple databases support to run the tests!');
            }

            $picoDb = new PicoDb\Database(array(
                'driver' => 'sqlite',
                'filename' => \Model\Database\get_path(),
            ));

            $picoDb->schema()->check(Schema\VERSION);

            // make the database world writeable, maybe the current
            // user != webserver user
            chmod(\Model\Database\get_path(), 0666);

            // get pdo object
            $pdo = $picoDb->getConnection();

            // disable fsync! its awefull slow without transactions and I found
            // no way to use setDataSet function with transactions
            $pdo->exec("pragma synchronous = off;");

            static::$databaseConnection = new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($pdo, 'sqlite');
        }

        return static::$databaseConnection;
    }

    protected function getDatabaseTester($dataset)
    {
        if (static::$databaseTester === null) {
            $rdataset = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataset);
            $rdataset->addSubStrReplacement('##TIMESTAMP##', substr((string)(time()-100), 0, -2));

            // article/feed import on database->onSetUp();
            $tester = new PHPUnit_Extensions_Database_DefaultTester($this->getConnection());
            $tester->setSetUpOperation(PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT());
            $tester->setDataSet($rdataset);

            static::$databaseTester = $tester;
        }

        return static::$databaseTester;
    }

    // public to be accessible within an closure
    public function isElementVisible($element)
    {
        $displaySize = $element->size();

        return ($element->displayed() && $displaySize['height']>0 && $displaySize['width']>0);
    }

    // public to be accessible within an closure
    public function isElementInvisible($element)
    {
        $displaySize = $element->size();

        return ($element->displayed() === false || $displaySize['height']=0 || $displaySize['width']=0);
    }

    private function waitForElementVisibility($element, $visible)
    {
        // return false in case of timeout
        try {
            // Workaround for PHP < 5.4
            $CI = $this;

            $value = $this->waitUntil(function () use ($CI, $element,$visible) {
                // a "No such Element" or "Stale Element Reference" exception is
                // valid if an object should disappear
                try {
                    if (($visible && $CI->isElementVisible($element))
                         || (! $visible && $CI->isElementInvisible($element))) {
                        return true;
                    }
                } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
                    $noSuchElement = ($e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::NoSuchElement
                                   || $e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::StaleElementReference);

                    if (($visible === false) && ($noSuchElement)) {
                        return true;
                    } else {
                        throw $e;
                    }
                }
            }, $this->waitTimeout);
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            if ($e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::Timeout) {
                return false;
            } else {
                throw $e;
            }
        }

        return $value;
    }

    private function waitForElementCountByCssSelector($cssSelector, $elementCount)
    {
        // return false in case of timeout
        try {
            // Workaround for PHP < 5.4
            $CI = $this;

            $value = $this->waitUntil(function () use ($cssSelector, $elementCount,$CI) {
                $elements = $CI->elements($CI->using('css selector')->value($cssSelector));

                if (count($elements) === $elementCount) {
                    return true;
                }
            }, $this->waitTimeout);
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            if ($e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::Timeout) {
                return false;
            } else {
                throw $e;
            }
        }

        return $value;
    }

    private function waitForElementByIdText($id, $text)
    {
        // return false in case of timeout
        try {
            // Workaround for PHP < 5.4
            $CI = $this;

            $value = $this->waitUntil(function () use ($CI, $id,$text) {
                try {
                    $elements = $this->elements($this->using('id')->value($id));

                    if (count($elements) === 1 && $elements[0]->text() == $text
                        || count($elements) === 0 && $text === null) {
                        return true;
                    }
                } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
                    $noSuchElement = ($e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::NoSuchElement
                                   || $e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::StaleElementReference);

                    // everything else than "No such Element" or
                    // "Stale Element Reference" is unexpected
                    if (! $noSuchElement) {
                        throw $e;
                    }
                }
            }, $this->waitTimeout);
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            if ($e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::Timeout) {
                return false;
            } else {
                throw $e;
            }
        }

        return $value;
    }

    private function waitForElementAttributeHasValue($element, $attribute, $attributeValue, $invertMatch = false)
    {
        // return false in case of timeout
        try {
            $value = $this->waitUntil(function () use ($element, $attribute, $attributeValue,$invertMatch) {
                $attributeHasValue = ($element->attribute($attribute) === $attributeValue);

                if (($attributeHasValue && !$invertMatch) || (!$attributeHasValue && $invertMatch)) {
                    return true;
                }
            }, $this->waitTimeout);
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
            if ($e->getCode() === PHPUnit_Extensions_Selenium2TestCase_WebDriverException::Timeout) {
                return false;
            } else {
                throw $e;
            }
        }

        return $value;
    }

    private function waitForIconMarkRead($article, $visible)
    {
        $icon = $article->elements($article->using('css selector')->value('span.read-icon'));

        $value = $this->waitForElementVisibility($icon[0], $visible);
        return $value;
    }

    private function waitForIconBookmark($article, $visible)
    {
        $icon = $article->elements($article->using('css selector')->value('span.bookmark-icon'));

        $value = $this->waitForElementVisibility($icon[0], $visible);
        return $value;
    }

    public function getBasePageHeading()
    {
        /*
         * WORKAROUND: Its not possible to get an elements text content without
         * the text of its childs. Thats why we have to differ between
         * pageheadings with counter and without counter.
         */

        // text of its childs
        $pageHeading = $this->byCssSelector('div.page-header > h2:first-child')->text();

        // Some PageHeadings have a counter included
        $innerHeadingElements = $this->elements($this->using('css selector')->value('div.page-header > h2:first-child *'));

        if (count($innerHeadingElements) > 0) {
            $innerHeading = $innerHeadingElements[0]->text();
            $pageHeading = substr($pageHeading, 0, (strlen($innerHeading) * -1));
        }

        return $pageHeading;
    }

    public function getURLPageUnread()
    {
        return PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=unread';
    }

    public function getURLPageBookmarks()
    {
        return PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=bookmarks';
    }

    public function getURLPageHistory()
    {
        return PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=history';
    }

    public function getURLPageFirstFeed()
    {
        return PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=feed-items&feed_id=1';
    }

    public function getURLPagePreferences()
    {
        return PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=config';
    }

    public function getURLPageSubscriptions()
    {
        return PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL.'?action=feeds';
    }

    public function getShortcutNextItemA()
    {
        return 'n';
    }

    public function getShortcutNextItemB()
    {
        return 'j';
    }

    public function getShortcutNextItemC()
    {
        return PHPUnit_Extensions_Selenium2TestCase_Keys::RIGHT;
    }

    public function getShortcutPreviousItemA()
    {
        return 'p';
    }

    public function getShortcutPreviousItemB()
    {
        return 'k';
    }

    public function getShortcutPreviousItemC()
    {
        return PHPUnit_Extensions_Selenium2TestCase_Keys::LEFT;
    }

    public function getShortcutToogleReadStatus()
    {
        return 'm';
    }

    public function getShortcutToogleBookmarkStatus()
    {
        return 'f';
    }

    public function getShortcutGoToUnread()
    {
        return 'gu';
    }

    public function getArticles()
    {
        $cssSelector = 'article';

        $articles = $this->elements($this->using('css selector')->value($cssSelector));
        return $articles;
    }

    public function getArticlesUnread()
    {
        $cssSelector = 'article[data-item-status="unread"]';

        $articles = $this->elements($this->using('css selector')->value($cssSelector));
        return $articles;
    }

    public function getArticlesRead()
    {
        $cssSelector = 'article[data-item-status="read"]';

        $articles = $this->elements($this->using('css selector')->value($cssSelector));
        return $articles;
    }

    public function getArticlesNotBookmarked()
    {
        $cssSelector = 'article[data-item-bookmark="0"]';

        $articles = $this->elements($this->using('css selector')->value($cssSelector));
        return $articles;
    }

    public function getArticlesNotFromFeedOne()
    {
        $cssSelector = 'article:not(.feed-1)';

        $articles = $this->elements($this->using('css selector')->value($cssSelector));
        return $articles;
    }

    public function getFeedFailed()
    {
        $cssSelector = 'article[data-feed-id="4"]';

        $feed = $this->element($this->using('css selector')->value($cssSelector));
        return $feed;
    }

    public function getFeedDisabled()
    {
        $cssSelector = 'article[data-feed-id="2"]';

        $feed = $this->element($this->using('css selector')->value($cssSelector));
        return $feed;
    }

    public function getFeedErrorMessages()
    {
        $cssSelector = 'article .feed-parsing-error';

        if (func_num_args() === 0) {
            $feed = $this;
        } else {
            $feed = func_get_arg(0);
        }

        $feeds = $feed->elements($this->using('css selector')->value($cssSelector));

        // Workaround for PHP < 5.4
        $CI = $this;

        return array_filter($feeds, function ($feed) use ($CI) {
             return $CI->isElementVisible($feed);
        });
    }

    public function getArticleUnreadNotBookmarked()
    {
        $cssSelector = 'article[data-item-id="7c6afaa5"]';

        $article = $this->element($this->using('css selector')->value($cssSelector));
        return $article;
    }

    public function getArticleReadNotBookmarked()
    {
        $cssSelector = 'article[data-item-id="9b20eb66"]';

        $article = $this->element($this->using('css selector')->value($cssSelector));
        return $article;
    }

    public function getArticleUnreadBookmarked()
    {
        $cssSelector = 'article[data-item-id="7cb2809d"]';

        $article = $this->element($this->using('css selector')->value($cssSelector));
        return $article;
    }

    public function getArticleReadBookmarked()
    {
        $cssSelector = 'article[data-item-id="9fa78b54"]';

        $articles = $this->element($this->using('css selector')->value($cssSelector));
        return $articles;
    }

    public function getLinkReadStatusToogle($article)
    {
        $link = $article->element($article->using('css selector')->value('a.mark'));
        return $link;
    }

    public function getLinkBookmarkStatusToogle($article)
    {
        $link = $article->element($article->using('css selector')->value('a.bookmark'));
        return $link;
    }

    public function getLinkRemove($article)
    {
        $link = $article->element($article->using('css selector')->value('a.delete'));
        return $link;
    }

    public function getLinkFeedMarkReadHeader()
    {
        $link = $this->element($this->using('css selector')->value('div.page-header a[data-action="mark-feed-read"]'));
        return $link;
    }

    public function getLinkFeedMarkReadBottom()
    {
        $link = $this->element($this->using('css selector')->value('div#bottom-menu a[data-action="mark-feed-read"]'));
        return $link;
    }

    public function getLinkMarkAllReadHeader()
    {
        $link = $this->element($this->using('css selector')->value('div.page-header a[href|="?action=mark-all-read"]'));
        return $link;
    }

    public function getLinkMarkAllReadBottom()
    {
        $link = $this->element($this->using('css selector')->value('div#bottom-menu a[href|="?action=mark-all-read"]'));
        return $link;
    }

    public function getLinkFlushHistory()
    {
        $link = $this->element($this->using('css selector')->value('div.page-header a[href="?action=confirm-flush-history"]'));
        return $link;
    }

    public function getLinkDestructive()
    {
        $link = $this->element($this->using('css selector')->value('a.btn-red'));
        return $link;
    }

    public function getAlertBox()
    {
        $cssSelector = 'p.alert';

        $alertBox = $this->elements($this->using('css selector')->value($cssSelector));
        return $alertBox;
    }

    public function waitForArticleIsCurrentArticle($article)
    {
        $isCurrent = $this->waitForElementAttributeHasValue($article, 'id', 'current-item');
        return $isCurrent;
    }

    public function waitForArticleIsNotCurrentArticle($article)
    {
        $isCurrent = $this->waitForElementAttributeHasValue($article, 'id', 'current-item', true);
        return $isCurrent;
    }

    public function waitForIconMarkReadVisible($article)
    {
        $visible = $this->waitForIconMarkRead($article, true);
        return $visible;
    }

    public function waitForIconMarkReadInvisible($article)
    {
        $invisible = $this->waitForIconMarkRead($article, false);
        return $invisible;
    }

    public function waitForIconBookmarkVisible($article)
    {
        $visible = $this->waitForIconBookmark($article, true);
        return $visible;
    }

    public function waitForIconBookmarkInvisible($article)
    {
        $invisible = $this->waitForIconBookmark($article, false);
        return $invisible;
    }

    public function waitForArticleInvisible($article)
    {
        $invisible = $this->waitForElementVisibility($article, false);
        return $invisible;
    }

    public function waitForArticlesMarkRead()
    {
        $cssSelector = 'article[data-item-status="unread"]';

        $read = $this->waitForElementCountByCssSelector($cssSelector, 0);
        return $read;
    }

    public function waitForAlert()
    {
        $cssSelector = 'p.alert';

        $visible = $this->waitForElementCountByCssSelector($cssSelector, 1);
        return $visible;
    }

    public function sendKeysAndWaitForPageLoaded($keys)
    {
        $this->keys($keys);

        // Workaround for PHP < 5.4
        $CI = $this;

        $this->waitUntil(function () use ($CI) {
            $readyState = $CI->execute(array(
                'script' => 'return document.readyState;',
                'args'   => array()
            ));

            if ($readyState === 'complete') {
                return true;
            }
        }, $this->waitTimeout);
    }

    public function setArticleAsCurrentArticle($article)
    {
        $script = 'document.getElementById("' .$article->attribute('id') .'").id = "current-item";'
                . 'return true';

        $this->execute(array(
            'script' => $script,
            'args'   => array()
        ));

        $result = $this->waitForArticleIsCurrentArticle($article);
        if ($result === false) {
            throw new Exception('the article could not be set as current article.');
        }
    }
}
