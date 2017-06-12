Full article download
=====================

For feeds that accept only a summary, it's possible to download the full content directly from the original website.

How the content grabber works?
------------------------------

1. Try with rules first (Xpath patterns) for the domain name
2. Try to find the text content by using common attributes for class and id
3. Finally, if nothing is found, the feed content is displayed

However the content grabber doesn't work very well with all websites.
Especially websites that use a lot of Javascript to generate the content.

**The best results are obtained with Xpath rules file.**

How to write a grabber rules file?
----------------------------------

Miniflux will try first to find the file in the [default bundled rules directory](https://github.com/miniflux/miniflux/tree/master/vendor/fguillot/picofeed/lib/PicoFeed/Rules), then it will try to load your custom rules.

You can create custom rules, by adding a PHP file to the directory `rules`. The filename must be the domain name with the suffix `.php`.

Each rule has the following keys:
* **body**: An array of xpath expressions which will be extracted from the page
* **strip**: An array of xpath expressions which will be removed from the matched content
* **test_url**: A test url to a matching page to test the grabber

Example for the BBC website, `www.bbc.co.uk.php`:

```php
<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.bbc.co.uk/news/world-middle-east-23911833',
            'body' => array(
                '//div[@class="story-body"]',
            ),
            'strip' => array(
                '//script',
                '//form',
                '//style',
                '//*[@class="story-date"]',
                '//*[@class="story-header"]',
                '//*[@class="story-related"]',
                '//*[contains(@class, "byline")]',
                '//*[contains(@class, "story-feature")]',
                '//*[@id="video-carousel-container"]',
                '//*[@id="also-related-links"]',
                '//*[contains(@class, "share") or contains(@class, "hidden") or contains(@class, "hyper")]',
            )
        )
    )
);
```

Each rule file can contain rules for different subdivisions of a website. Those subdivisions are distinguished by their URL. The first level array key of a rule file will be matched against the full path of the URL using **preg_match**, e.g. for **http://www.bbc.co.uk/news/world-middle-east-23911833?test=1** the URL that would be matched is **/news/world-middle-east-23911833?test=1**

Let's say you want to extract a div with the id **video** if the article points to an URL like **http://comix.com/videos/423**, **audio** if the article points to an URL like **http://comix.com/podcasts/5** and all other links to the page should instead take the div with the id **content**. The following rulefile ```comix.com.php``` would fit that requirement:

```php
return array(
    'grabber' => array(
        '%^/videos.*%' => array(
            'test_url' => 'http://comix.com/videos/423',
            'body' => array(
                '//div[@id="video"]',
            ),
            'strip' => array()
        ),
        '%^/podcasts.*%' => array(
            'test_url' => 'http://comix.com/podcasts/5',
            'body' => array(
                '//div[@id="audio"]',
            ),
            'strip' => array()
        ),
        '%.*%' => array(
            'test_url' => 'http://comix.com/blog/1',
            'body' => array(
                '//div[@id="content"]',
            ),
            'strip' => array()
        )
    )
);
```

Sharing your custom rules with the community
--------------------------------------------

If you would like to share your custom rules with everybody, send a pull-request to the project [PicoFeed](https://github.com/miniflux/picofeed).
That will be merged in the Miniflux code base.

List of content grabber rules
-----------------------------

[List of rules included by default](https://github.com/miniflux/miniflux/tree/master/vendor/miniflux/picofeed/lib/PicoFeed/Rules).