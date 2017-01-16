<?php

use Miniflux\Handler;
use Miniflux\Model;
use Miniflux\Session\SessionStorage;
use Pheanstalk\Pheanstalk;

require __DIR__.'/app/common.php';

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$connection = new Pheanstalk(BEANSTALKD_HOST);
$session = SessionStorage::getInstance();

while ($job = $connection->reserveFromTube(BEANSTALKD_QUEUE)) {
    $payload = unserialize($job->getData());
    $start_time = microtime(true);

    echo 'Processing feed_id=', $payload['feed_id'], ' for user_id=', $payload['user_id'];
    $user = Model\User\get_user_by_id($payload['user_id']);

    if (empty($user)) {
        echo ', user not found (removed?)'.PHP_EOL;
    } else {
        $session->flush();
        $session->setUser($user);

        Handler\Feed\update_feed($payload['user_id'], $payload['feed_id']);
        Model\Item\autoflush_read($payload['user_id']);
        Model\Item\autoflush_unread($payload['user_id']);

        echo ', duration='.(microtime(true) - $start_time).' seconds', PHP_EOL;

        Miniflux\Helper\write_debug_file();
    }

    $connection->delete($job);
}
