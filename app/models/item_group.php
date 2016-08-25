<?php

namespace Miniflux\Model\ItemGroup;

use PicoDb\Database;
use Miniflux\Model\Group;

function mark_all_as_read($group_id)
{
    $feed_ids = Group\get_feeds_by_group($group_id);

    return Database::getInstance('db')
        ->table('items')
        ->eq('status', 'unread')
        ->in('feed_id', $feed_ids)
        ->update(array('status' => 'read'));
}

function mark_all_as_removed($group_id)
{
    $feed_ids = Group\get_feeds_by_group($group_id);

    return Database::getInstance('db')
        ->table('items')
        ->eq('status', 'read')
        ->eq('bookmark', 0)
        ->in('feed_id', $feed_ids)
        ->save(array('status' => 'removed', 'content' => ''));
}
