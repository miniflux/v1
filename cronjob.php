<?php

require __DIR__.'/app/common.php';

use Miniflux\Handler;
use Miniflux\Model;
use Miniflux\Session\SessionStorage;

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
$session = SessionStorage::getInstance();

foreach (Model\User\get_all_user_ids() as $user_id) {
    $session->flush();
    $session->setUser(Model\User\get_user_by_id($user_id));

    if ($update_interval !== null && $call_interval !== null && $limit === null && $update_interval >= $call_interval) {
        $feeds_count = Model\Feed\count_feeds($user_id);
        $current_limit = ceil($feeds_count / ($update_interval / $call_interval));
    } else {
        $current_limit = $limit;
    }

    echo 'Update feeds for user_id='.$user_id.', limit='.$current_limit.PHP_EOL;

    Handler\Feed\update_feeds($user_id, $current_limit);
    Model\Item\autoflush_read($user_id);
    Model\Item\autoflush_unread($user_id);
    Miniflux\Helper\write_debug_file();
}
