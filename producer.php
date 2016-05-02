<?php

use Pheanstalk\Pheanstalk;

require __DIR__.'/common.php';

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$connection = new Pheanstalk(BEANSTALKD_HOST);

foreach (Model\Feed\get_ids() as $feed_id) {
    $connection
        ->useTube(BEANSTALKD_QUEUE)
        ->put($feed_id, Pheanstalk::DEFAULT_PRIORITY, Pheanstalk::DEFAULT_DELAY, BEANSTALKD_TTL);
}
