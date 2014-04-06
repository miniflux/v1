Full article download
=====================

For feeds that accept only a summary, it's possible to download the full content directly from the original website.

How the content grabber works?
------------------------------

1. Try with rules first (xpath patterns) for the domain name (see `PicoFeed\Rules\`)
2. Try to find the text content by using common attributes for class and id
3. Fallback to Readability if no content is found
4. Finally, if nothing is found, the feed content is displayed

The content downloader use a fake user agent, actually Google Chrome under Mac Os X.

However the content grabber doesn't work very well with all websites.

**The best results are obtained with Xpath rules file.**


How to write a grabber rules file?
----------------------------------

Add a PHP file to the directory `PicoFeed\Rules`, the filename must be the domain name:

Example with the BBC website, `www.bbc.co.uk.php`:

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

Actually, only `body`, `strip` and `test_url` are supported.

Don't forget to send a pull request or a ticket to share your contribution with everybody.

List of content grabber rules
-----------------------------

Rules are stored here: [vendor/PicoFeed/Rules](https://github.com/fguillot/miniflux/tree/master/vendor/PicoFeed/Rules)

If you want to add new rules, just open a ticket and I will do it.
