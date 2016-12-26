<?php

namespace Miniflux\Handler\Item;

use Miniflux\Handler;
use Miniflux\Helper;
use Miniflux\Model;
use PicoDb\Database;

function download_item_content($user_id, $item_id)
{
    $item = Model\Item\get_item($user_id, $item_id);
    $content = Handler\Scraper\download_content($item['url']);

    if (! empty($content)) {
        if (! Helper\config('nocontent')) {
            Database::getInstance('db')
                ->table('items')
                ->eq('id', $item['id'])
                ->save(array('content' => $content));
        }

        return array(
            'result' => true,
            'content' => $content
        );
    }

    return array(
        'result' => false,
        'content' => ''
    );
}
