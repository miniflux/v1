<?php

require_once __DIR__.'/../app/common.php';

use Miniflux\Model;
use PicoDb\Database;

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$options = getopt('', array(
    'dsn:',
));

if (empty($options)) {
    die('Usage: '.$argv[0].' --dsn="pgsql:host=localhost;dbname=miniflux2;user=postgres;password=postgres"'.PHP_EOL);
}

function migrate_user(PDO $dst, array $user, array $settings)
{
    $rq = $dst->prepare('
        INSERT INTO users (
            username,
            password,
            is_admin,
            language
        ) VALUES (?, ?, ?, ?) RETURNING id');
    $rq->execute(array(
        strtolower($user['username']),
        $user['password'],
        $user['is_admin'] == 1 ? 1 : 0,
        isset($settings['language']) && $settings['language'] == 'fr_FR' ? 'fr_FR' : 'en_US',
    ));

    return $rq->fetchColumn();
}

function migrate_integrations(PDO $dst, $dst_user_id, array $user, array $settings)
{
    $rq = $dst->prepare('
        INSERT INTO integrations (
            user_id,
            instapaper_enabled,
            instapaper_username,
            instapaper_password,
            pinboard_enabled,
            pinboard_tags,
            pinboard_token,
            wallabag_enabled,
            wallabag_url,
            wallabag_client_id,
            wallabag_client_secret,
            wallabag_username,
            wallabag_password,
            fever_enabled,
            fever_username,
            fever_password,
            fever_token
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $rq->execute(array(
        $dst_user_id,
        array_key_exists('instapaper_enabled', $settings) && $settings['instapaper_enabled'] == 1 ? 1 : 0,
        isset($settings['instapaper_username']) ? $settings['instapaper_username'] : '',
        isset($settings['instapaper_password']) ? $settings['instapaper_password'] : '',
        array_key_exists('pinboard_enabled', $settings) && $settings['pinboard_enabled'] == 1 ? 1 : 0,
        isset($settings['pinboard_tags']) ? $settings['pinboard_tags'] : '',
        isset($settings['pinboard_token']) ? $settings['pinboard_token'] : '',
        array_key_exists('wallabag_enabled', $settings) && $settings['wallabag_enabled'] == 1 ? 1 : 0,
        isset($settings['wallabag_url']) ? $settings['wallabag_url'] : '',
        isset($settings['wallabag_client_id']) ? $settings['wallabag_client_id'] : '',
        isset($settings['wallabag_client_secret']) ? $settings['wallabag_client_secret'] : '',
        isset($settings['wallabag_username']) ? $settings['wallabag_username'] : '',
        isset($settings['wallabag_password']) ? $settings['wallabag_password'] : '',
        1,
        strtolower($user['username']),
        isset($user['fever_token']) ? $user['fever_token'] : '',
        isset($user['fever_api_key']) ? $user['fever_api_key'] : '',
    ));
}

function migrate_categories(PDO $dst, $dst_user_id, $src_user_id)
{
    $rq = $dst->prepare('INSERT INTO categories (user_id, title) VALUES (?, ?) RETURNING id');
    $rq->execute(array($dst_user_id, 'All'));
    $default_category_id = $rq->fetchColumn();

    $categories = array();
    $groups = Model\Group\get_all($src_user_id);
    foreach ($groups as $group) {
        $rq->execute(array($dst_user_id, trim($group['title'])));

        $category_id = $rq->fetchColumn();
        $categories[$group['id']] = $category_id;
    }

    return array($default_category_id, $categories);
}

function migrate_feeds(PDO $dst, $dst_user_id, $src_user_id, $default_category_id, array $categories)
{
    $mapping = array();
    $feeds = Model\Feed\get_feeds($src_user_id);
    $rq = $dst->prepare('
        INSERT INTO feeds (
            user_id,
            category_id,
            title,
            feed_url,
            site_url,
            crawler
        )
        VALUES (?, ?, ?, ?, ?, ?) RETURNING id
    ');

    foreach ($feeds as $feed) {
        $category_id = $default_category_id;
        $group_ids = Model\Group\get_feed_group_ids($feed['id']);

        if (count($group_ids) > 0) {
            $category_id = isset($categories[$group_ids[0]]) ? $categories[$group_ids[0]] : $default_category_id;
        }

        $rq->execute(array(
            $dst_user_id,
            $category_id,
            $feed['title'],
            $feed['feed_url'],
            $feed['site_url'],
            $feed['download_content'] == 1 ? 1 : 0,
        ));

        $feed_id = $rq->fetchColumn();
        $mapping[$feed['id']] = $feed_id;
    }

    return $mapping;
}

function migrate_entries(PDO $dst, $dst_user_id, $src_user_id, array $feeds)
{
    $rq = $dst->prepare('
        INSERT INTO entries (
            user_id,
            feed_id,
            hash,
            published_at,
            title,
            url,
            author,
            content,
            status,
            starred
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id
    ');

    $rq2 = $dst->prepare('
        INSERT INTO enclosures (
            user_id,
            entry_id,
            url,
            size,
            mime_type
        )
        VALUES (?, ?, ?, ?, ?)
    ');

    foreach ($feeds as $src_feed_id => $dst_feed_id) {
        $items = Database::getInstance('db')
            ->table('items')
            ->eq('feed_id', $src_feed_id)
            ->eq('bookmark', 1)
            ->findAll();

        foreach ($items as $item) {
            $rq->execute(array(
                $dst_user_id,
                $dst_feed_id,
                $item['checksum'],
                $item['updated'] > 0 ? date('Y-m-d H:i:s', $item['updated']) : date('Y-m-d H:i:s'),
                $item['title'],
                $item['url'],
                $item['author'],
                $item['content'],
                $item['status'],
                $item['bookmark'] == 1 ? 1 : 0,
            ));

            $item_id = $rq->fetchColumn();

            if (! empty($item['enclosure_url']) && ! empty($item['enclosure_type'])) {
                $rq2->execute(array(
                    $dst_user_id,
                    $item_id,
                    $item['enclosure_url'],
                    0,
                    $item['enclosure_type'],
                ));
            }
        }
    }
}

echo 'Destination is "'.$options['dsn'].'"'.PHP_EOL;
$dstDB = new PDO($options['dsn']);
$dstDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$srcDB = PicoDb\Database::getInstance('db')->getConnection();

try {
    $dstDB->beginTransaction();

    $rq = $srcDB->prepare('SELECT id, username, password, is_admin, fever_token, fever_api_key FROM users');
    $rq->execute();
    $users = $rq->fetchAll(PDO::FETCH_ASSOC);
    echo '* '.count($users).' user(s) to migrate'.PHP_EOL;

    foreach ($users as $user) {
        $src_user_id = $user['id'];
        $settings = Model\Config\get_all($src_user_id);

        echo '* Migrating user: #'.$src_user_id;
        $dst_user_id = migrate_user($dstDB, $user, $settings);
        echo ' => #'.$dst_user_id.PHP_EOL;

        echo '* Migrating integrations'.PHP_EOL;
        migrate_integrations($dstDB, $dst_user_id, $user, $settings);

        echo '* Migrating categories'.PHP_EOL;
        list($default_category_id, $categories) = migrate_categories($dstDB, $dst_user_id, $src_user_id);

        echo '* Migrating feeds'.PHP_EOL;
        $feeds = migrate_feeds($dstDB, $dst_user_id, $src_user_id, $default_category_id, $categories);

        echo '* Migrating entries'.PHP_EOL;
        migrate_entries($dstDB, $dst_user_id, $src_user_id, $feeds);
    }

    $dstDB->commit();
} catch (PDOException $e) {
    $dstDB->rollBack();
    echo PHP_EOL.$e->getMessage().PHP_EOL;
}
