<?php

namespace Miniflux\Model\Group;

use PicoDb\Database;

const TABLE      = 'groups';
const JOIN_TABLE = 'feeds_groups';

function get_all($user_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->orderBy('title')
        ->findAll();
}

function get_group($user_id, $group_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $group_id)
        ->findOne();
}

function remove_group($user_id, $group_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $group_id)
        ->remove();
}

function update_group($user_id, $group_id, $title)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->eq('user_id', $user_id)
        ->eq('id', $group_id)
        ->update(array('title' => $title));
}

function get_groups_feed_ids($user_id)
{
    $result = array();
    $rows = Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->columns('feed_id', 'group_id')
        ->join(TABLE, 'id', 'group_id')
        ->eq('user_id', $user_id)
        ->findAll();

    foreach ($rows as $row) {
        $group_id = $row['group_id'];
        $feed_id = $row['feed_id'];

        if (isset($result[$group_id])) {
            $result[$group_id][] = $feed_id;
        } else {
            $result[$group_id] = array($feed_id);
        }
    }

    return $result;
}

function get_feed_group_ids($feed_id)
{
    return Database::getInstance('db')
            ->table(TABLE)
            ->join(JOIN_TABLE, 'group_id', 'id')
            ->eq('feed_id', $feed_id)
            ->findAllByColumn('id');
}

function get_feed_groups($feed_id)
{
    return Database::getInstance('db')
        ->table(TABLE)
        ->columns('groups.id', 'groups.title')
        ->join(JOIN_TABLE, 'group_id', 'id')
        ->eq('feed_id', $feed_id)
        ->findAll();
}

function get_group_id_from_title($user_id, $title)
{
    return Database::getInstance('db')
        ->table('groups')
        ->eq('user_id', $user_id)
        ->eq('title', $title)
        ->findOneColumn('id');
}

function get_feed_ids_by_group($group_id)
{
    return Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->eq('group_id', $group_id)
        ->findAllByColumn('feed_id');
}

function create_group($user_id, $title)
{
    $group_id = get_group_id_from_title($user_id, $title);

    if ($group_id === false) {
        $group_id = Database::getInstance('db')
            ->table(TABLE)
            ->persist(array('title' => $title, 'user_id' => $user_id));
    }

    return $group_id;
}

function update_feed_groups($user_id, $feed_id, array $group_ids, $group_name = '')
{
    if ($group_name !== '') {
        $group_id = create_group($user_id, $group_name);
        if ($group_id === false) {
            return false;
        }

        if (! in_array($group_id, $group_ids)) {
            $group_ids[] = $group_id;
        }
    }

    $assigned = get_feed_group_ids($feed_id);
    $superfluous = array_diff($assigned, $group_ids);
    $missing = array_diff($group_ids, $assigned);

    if (! empty($superfluous) && ! dissociate_feed_groups($feed_id, $superfluous)) {
        return false;
    }

    if (! empty($missing) && ! associate_feed_groups($feed_id, $missing)) {
        return false;
    }

    return true;
}

function associate_feed_groups($feed_id, array $group_ids)
{
    foreach ($group_ids as $group_id) {
        $result = Database::getInstance('db')
            ->table(JOIN_TABLE)
            ->insert(array('feed_id' => $feed_id, 'group_id' => $group_id));

        if ($result === false) {
            return false;
        }
    }

    return true;
}

function dissociate_feed_groups($feed_id, array $group_ids)
{
    if (empty($group_ids)) {
        return false;
    }

    return Database::getInstance('db')
        ->table(JOIN_TABLE)
        ->eq('feed_id', $feed_id)
        ->in('group_id', $group_ids)
        ->remove();
}
