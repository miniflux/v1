<?php

use Pheanstalk\Pheanstalk;
use Miniflux\Model;

require __DIR__.'/app/common.php';

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$options = getopt('', array(
    'limit::',
    'user::',
));

$limit = get_cli_option('limit', $options);
$user_id = get_cli_option('user', $options);
$connection = new Pheanstalk(BEANSTALKD_HOST);

if ($user_id) {
    $user_ids = array($user_id);
} else {
    $user_ids = Model\User\get_all_user_ids();
}

foreach ($user_ids as $user_id) {
    foreach (Model\Feed\get_feed_ids_to_refresh($user_id, $limit) as $feed_id) {
        $payload = serialize(array(
            'feed_id' => $feed_id,
            'user_id' => $user_id,
        ));

        $connection
            ->useTube(BEANSTALKD_QUEUE)
            ->put($payload, Pheanstalk::DEFAULT_PRIORITY, Pheanstalk::DEFAULT_DELAY, BEANSTALKD_TTL);
    }
}

