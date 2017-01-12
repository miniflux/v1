<?php

require_once __DIR__.'/../app/common.php';

use Miniflux\Helper;
use Miniflux\Model;

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$options = getopt('', array(
    'sqlite-db:',
    'admin::',
));

if (empty($options)) {
    die('Usage: '.$argv[0].' --sqlite-db=/path/to/my/db.sqlite --admin=1|0'.PHP_EOL);
}

$src_file = $options['sqlite-db'];
$is_admin = isset($options['admin']) ? (int) $options['admin'] : 0;

$src = new PDO('sqlite:' . $src_file);
$src->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db = PicoDb\Database::getInstance('db');
$dst = $db->getConnection();


function get_last_id(PDO $pdo)
{
    if (DB_DRIVER === 'postgres') {
        $rq = $pdo->prepare('SELECT LASTVAL()');
        $rq->execute();
        return $rq->fetchColumn();
    }

    return $pdo->lastInsertId();
}

function get_settings(PDO $db)
{
    $rq = $db->prepare('SELECT * FROM settings');
    $rq->execute();
    $rows = $rq->fetchAll(PDO::FETCH_ASSOC);
    $settings = array();

    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }

    return $settings;
}

function get_table(PDO $db, $table)
{
    $rq = $db->prepare('SELECT * FROM '.$table);
    $rq->execute();
    return $rq->fetchAll(PDO::FETCH_ASSOC);
}

function create_user(PDO $db, array $settings, $is_admin)
{
    $rq = $db->prepare('
      INSERT INTO users
      (username, password, is_admin, last_login, api_token, bookmarklet_token, cronjob_token, feed_token, fever_token, fever_api_key)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $rq->execute(array(
        $settings['username'],
        $settings['password'],
        $is_admin,
        isset($settings['last_login']) ? $settings['last_login'] : 0,
        $settings['api_token'],
        $settings['bookmarklet_token'],
        Helper\generate_token(),
        $settings['feed_token'],
        $settings['fever_token'],
        md5($settings['username'] . ':' . $settings['fever_token']),
    ));

    return get_last_id($db);
}

function copy_settings(\PicoDb\Database $db, $user_id,  array $settings)
{
    $exclude_keys = array(
        'username',
        'password',
        'last_login',
        'api_token',
        'bookmarklet_token',
        'feed_token',
        'fever_token',
        'debug_mode',
        'auto_update_url',
    );

    foreach ($settings as $key => $value) {
        if (! in_array($key, $exclude_keys)) {
            $db->table('user_settings')->insert(array(
                'user_id' => $user_id,
                'key'     => $key,
                'value'   => $value ?: '',
            ));
        }
    }
}

function copy_feeds(PDO $db, $user_id, array $feeds)
{
    $feed_ids = array();
    $rq = $db->prepare('INSERT INTO feeds 
      (user_id, feed_url, site_url, title, enabled, download_content, rtl, cloak_referrer)
      VALUES
      (?, ?, ?, ?, ?, ?, ?, ?)
    ');

    foreach ($feeds as $feed) {
        $rq->execute(array(
            $user_id,
            $feed['feed_url'],
            $feed['site_url'],
            $feed['title'],
            isset($feed['enabled']) ? (int) $feed['enabled'] : 1,
            isset($feed['download_content']) ? (int) $feed['download_content'] : 0,
            isset($feed['rtl']) ? (int) $feed['rtl'] : 0,
            isset($feed['cloak_referrer']) ? (int) $feed['cloak_referrer'] : 0,
        ));

        $feed_ids[$feed['id']] = get_last_id($db);
    }

    return $feed_ids;
}

function copy_items(PDO $db, $user_id, array $feed_ids, array $items)
{
    $rq = $db->prepare('INSERT INTO items 
      (user_id, feed_id, checksum, status, bookmark, url, title, author, content, updated, enclosure_url, enclosure_type, language)
      VALUES
      (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    foreach ($items as $item) {
        $rq->execute(array(
            $user_id,
            $feed_ids[$item['feed_id']],
            $item['id'],
            $item['status'],
            isset($item['bookmark']) ? (int) $item['bookmark'] : 0,
            $item['url'],
            $item['title'],
            $item['author'],
            $item['content'],
            isset($item['updated']) ? (int) $item['updated'] : 0,
            $item['enclosure'],
            $item['enclosure_type'],
            $item['language'],
        ));
    }
}

function copy_favicons(PDO $db, array $feed_ids, array $favicons, array $favicons_feeds)
{
    $favicon_ids = array();

    foreach ($favicons as $favicon) {
        $rq = $db->prepare('SELECT id from favicons WHERE "hash"=?');
        $rq->execute(array($favicon['hash']));
        $favicon_id = $rq->fetch(PDO::FETCH_COLUMN);

        if ($favicon_id) {
            $favicon_ids[$favicon['id']] = $favicon_id;
        } else {
            $rq = $db->prepare('INSERT INTO favicons 
              (hash, type)
              VALUES
              (?, ?)
            ');

            $rq->execute(array(
                $favicon['hash'],
                $favicon['type'],
            ));

            $favicon_ids[$favicon['id']] = get_last_id($db);
        }
    }

    $rq = $db->prepare('INSERT INTO favicons_feeds 
      (feed_id, favicon_id)
      VALUES
      (?, ?)
    ');

    foreach ($favicons_feeds as $row) {
        $rq->execute(array(
            $feed_ids[$row['feed_id']],
            $favicon_ids[$row['favicon_id']],
        ));
    }
}

function copy_groups(PDO $db, $user_id, array $feed_ids, array $groups, array $feeds_groups)
{
    $group_ids = array();

    foreach ($groups as $group) {
        $rq = $db->prepare('INSERT INTO groups 
          (user_id, title)
          VALUES
          (?, ?)
        ');

        $rq->execute(array(
            $user_id,
            $group['title'],
        ));

        $group_ids[$group['id']] = get_last_id($db);
    }

    $rq = $db->prepare('INSERT INTO feeds_groups 
      (feed_id, group_id)
      VALUES
      (?, ?)
    ');

    foreach ($feeds_groups as $row) {
        $rq->execute(array(
            $feed_ids[$row['feed_id']],
            $group_ids[$row['group_id']],
        ));
    }
}

$settings = get_settings($src);
$feeds = get_table($src, 'feeds');
$items = get_table($src, 'items');
$groups = get_table($src, 'groups');
$feeds_groups = get_table($src, 'feeds_groups');
$favicons = get_table($src, 'favicons');
$favicons_feeds = get_table($src, 'favicons_feeds');

try {
    $dst->beginTransaction();

    echo '* Create user'.PHP_EOL;
    $user_id = create_user($dst, $settings, $is_admin);

    echo '* Copy user settings'.PHP_EOL;
    copy_settings($db, $user_id, $settings);

    echo '* Copy feeds'.PHP_EOL;
    $feed_ids = copy_feeds($dst, $user_id, $feeds);

    echo '* Copy items'.PHP_EOL;
    copy_items($dst, $user_id, $feed_ids, $items);

    echo '* Copy favicons'.PHP_EOL;
    copy_favicons($dst, $feed_ids, $favicons, $favicons_feeds);

    echo '* Copy groups'.PHP_EOL;
    copy_groups($dst, $user_id, $feed_ids, $groups, $feeds_groups);

    $dst->commit();
    echo $user_id.PHP_EOL;
} catch (PDOException $e) {
    $dst->rollBack();
    echo $e->getMessage().PHP_EOL;
}
