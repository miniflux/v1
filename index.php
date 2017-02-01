<?php

require __DIR__.'/app/common.php';

use Miniflux\Router;
use Miniflux\Response;

register_shutdown_function(function () {
    Miniflux\Helper\write_debug_file();
});

Router\bootstrap(
    __DIR__.'/app/controllers',
    'common',
    'about',
    'api',
    'auth',
    'bookmark',
    'config',
    'feed',
    'groups',
    'help',
    'history',
    'item',
    'opml',
    'profile',
    'search',
    'services',
    'users'
);

Router\notfound(function() {
    Response\redirect('?action=unread');
});
