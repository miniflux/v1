<?php

namespace Miniflux\Model\ItemGroup;

use Miniflux\Model\Item;
use Miniflux\Model\Group;
use PicoDb\Database;

function change_items_status($user_id, $group_id, $current_status, $new_status, $before = null)
{
    $feed_ids = Group\get_feed_ids_by_group($group_id);

    if (empty($feed_ids)) {
        return false;
    }

    $query = Database::getInstance('db')
        ->table(Item\TABLE)
        ->eq('user_id', $user_id)
        ->eq('status', $current_status)
        ->in('feed_id', $feed_ids);

    if ($before !== null) {
        $query->lte('updated', $before);
    }

    return $query->update(array('status' => $new_status));
}
