<?php

require __DIR__.'/app/common.php';

use Miniflux\Handler;
use Miniflux\Model;

if (php_sapi_name() === 'cli') {
    $options = getopt('', array(
        'limit::',
        'call-interval::',
        'update-interval::',
    ));
} else {
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $user = Model\User\get_user_by_token('cronjob_token', $token);

    if (empty($user) || !ENABLE_CRONJOB_HTTP_ACCESS) {
        die('Access Denied');
    }

    $options = $_GET;
}

$limit = get_cli_option('limit', $options);
$update_interval = get_cli_option('update-interval', $options);
$call_interval = get_cli_option('call-interval', $options);

foreach (Model\User\get_all_users() as $user) {
    if ($update_interval !== null && $call_interval !== null && $limit === null && $update_interval >= $call_interval) {
        $feeds_count = Model\Feed\count_feeds($user['id']);
        $limit = ceil($feeds_count / ($update_interval / $call_interval));
    }

    Handler\Feed\update_feeds($user['id'], $limit);
    Model\Item\autoflush_read($user['id']);
    Model\Item\autoflush_unread($user['id']);
    Miniflux\Helper\write_debug_file();
}
