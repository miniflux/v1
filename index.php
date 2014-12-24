<?php

require __DIR__.'/common.php';
require __DIR__.'/vendor/fguillot/picofarad/lib/PicoFarad/Template.php';
require __DIR__.'/vendor/fguillot/picofarad/lib/PicoFarad/Response.php';
require __DIR__.'/vendor/fguillot/picofarad/lib/PicoFarad/Request.php';
require __DIR__.'/vendor/fguillot/picofarad/lib/PicoFarad/Session.php';
require __DIR__.'/vendor/fguillot/picofarad/lib/PicoFarad/Router.php';
require __DIR__.'/lib/helpers.php';

use PicoFarad\Router;
use PicoFarad\Response;

Router\bootstrap(__DIR__.'/controllers', 'common', 'console', 'user', 'config', 'item', 'history', 'bookmark', 'feed');

// Page not found
Router\notfound(function() {
    Response\redirect('?action=unread');
});
