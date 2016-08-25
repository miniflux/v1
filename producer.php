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

$limit = ! empty($options['limit']) && ctype_digit($options['limit']) ? (int) $options['limit'] : Model\Feed\LIMIT_ALL;
$connection = new Pheanstalk(BEANSTALKD_HOST);

foreach (Model\Feed\get_ids($limit) as $feed_id) {
    $connection
        ->useTube(BEANSTALKD_QUEUE)
        ->put($feed_id, Pheanstalk::DEFAULT_PRIORITY, Pheanstalk::DEFAULT_DELAY, BEANSTALKD_TTL);
}
