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

Add a PHP file to the directory `rules`, the filename must be the domain name with the suffix `.php`:

Example with the BBC website, `www.bbc.co.uk.php`:

```php
<?php

return array(
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
);
```

Actually, only the keys `body`, `strip` and `test_url` are supported.

Miniflux will try first to find the file in the [default bundled rules directory](https://github.com/miniflux/miniflux/tree/master/vendor/fguillot/picofeed/lib/PicoFeed/Rules), then it will try to load your custom rules.

Sharing your custom rules with the community
--------------------------------------------

If you would like to share your custom rules with everybody, send a pull-request to the project [PicoFeed](https://github.com/fguillot/picofeed).
That will be merged in the Miniflux code base.

List of content grabber rules
-----------------------------

[List of rules included by default](https://github.com/miniflux/miniflux/tree/master/vendor/fguillot/picofeed/lib/PicoFeed/Rules).
