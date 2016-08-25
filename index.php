<?php

require __DIR__.'/app/common.php';

use Miniflux\Router;
use Miniflux\Response;

Router\bootstrap(
    __DIR__.'/app/controllers',
    'common',
    'console',
    'user',
    'config',
    'item',
    'history',
    'bookmark',
    'feed',
    'search'
);

Router\notfound(function() {
    Response\redirect('?action=unread');
});
