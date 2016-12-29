<?php

use Pheanstalk\Pheanstalk;
use Miniflux\Model;

require __DIR__.'/app/common.php';

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$options = getopt('', array(
    'limit::',
));

$limit = get_cli_option('limit', $options);
$connection = new Pheanstalk(BEANSTALKD_HOST);

foreach (Model\User\get_all_users() as $user) {
    foreach (Model\Feed\get_feed_ids_to_refresh($user['id'], $limit) as $feed_id) {
        $payload = serialize(array(
            'feed_id' => $feed_id,
            'user_id' => $user['id'],
        ));

        $connection
            ->useTube(BEANSTALKD_QUEUE)
            ->put($payload, Pheanstalk::DEFAULT_PRIORITY, Pheanstalk::DEFAULT_DELAY, BEANSTALKD_TTL);
    }
}

