<?php

use Pheanstalk\Pheanstalk;
use Miniflux\Model;

require __DIR__.'/app/common.php';

if (php_sapi_name() !== 'cli') {
    die('This script can run only from the command line.'.PHP_EOL);
}

$options = getopt('', array(
    'stop',
));

function check_job_left(Pheanstalk $connection, array $options)
{
    if (isset($options['stop'])) {
        $queues = $connection->listTubes();

        if (in_array(BEANSTALKD_QUEUE, $queues)) {
            $stats = $connection->statsTube(BEANSTALKD_QUEUE);
            echo 'Jobs in queue: ', $stats->current_jobs_ready, PHP_EOL;
            (int) $stats->current_jobs_ready === 0 && exit(0);
        } else {
            echo 'No queue', PHP_EOL;
            exit(0);
        }
    }
}

$connection = new Pheanstalk(BEANSTALKD_HOST);

check_job_left($connection, $options);

while ($job = $connection->reserveFromTube(BEANSTALKD_QUEUE)) {
    $feed_id = $job->getData();

    echo 'Processing feed_id=', $feed_id, PHP_EOL;

    Model\Feed\refresh($feed_id);
    Model\Item\autoflush_read();
    Model\Item\autoflush_unread();

    $connection->delete($job);

    check_job_left($connection, $options);
}
