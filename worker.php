<?php

use Pheanstalk\Pheanstalk;

require __DIR__.'/common.php';

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$connection = new Pheanstalk(BEANSTALKD_HOST);

while ($job = $connection->reserveFromTube(BEANSTALKD_QUEUE)) {
    $feed_id = $job->getData();
    echo 'Processing feed_id='.$feed_id.PHP_EOL;
    Model\Feed\refresh($feed_id);
    $connection->delete($job);
}
