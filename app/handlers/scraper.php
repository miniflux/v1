<?php

namespace Miniflux\Handler\Scraper;

use PicoFeed\Scraper\Scraper;
use Miniflux\Handler;

function download_content($url)
{
    $contents = '';

    $scraper = new Scraper(Handler\Feed\get_reader_config());
    $scraper->setUrl($url);
    $scraper->execute();

    if ($scraper->hasRelevantContent()) {
        $contents = $scraper->getFilteredContent();
    }

    return $contents;
}
