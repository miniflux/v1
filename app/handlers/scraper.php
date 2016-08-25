<?php

namespace Miniflux\Handler\Scraper;

use PicoFeed\Scraper\Scraper;
use Miniflux\Model\Config;

function download_contents($url)
{
    $contents = '';

    $scraper = new Scraper(Config\get_reader_config());
    $scraper->setUrl($url);
    $scraper->execute();

    if ($scraper->hasRelevantContent()) {
        $contents = $scraper->getFilteredContent();
    }

    return $contents;
}
