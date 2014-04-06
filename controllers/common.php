<?php

use PicoFarad\Router;
use PicoFarad\Response;
use PicoFarad\Request;
use PicoFarad\Session;
use PicoFarad\Template;

// Called before each action
Router\before(function($action) {

    Session\open(dirname($_SERVER['PHP_SELF']), SESSION_SAVE_PATH);

    // Select another database
    if (! empty($_SESSION['database'])) {
        Model\Database\select($_SESSION['database']);
    }

    // Redirect to the login form if the user is not authenticated
    $ignore_actions = array('login', 'google-auth', 'google-redirect-auth', 'mozilla-auth', 'bookmark-feed', 'select-db');

    if (! isset($_SESSION['user']) && ! in_array($action, $ignore_actions)) {
        Response\redirect('?action=login');
    }

    // Load translations
    $language = Model\Config\get('language') ?: 'en_US';
    if ($language !== 'en_US') \Translator\load($language);

    // Set timezone
    date_default_timezone_set(Model\Config\get('timezone') ?: 'UTC');

    // HTTP secure headers
    $frame_src = \PicoFeed\Filter::$iframe_whitelist;
    $frame_src[] = 'https://login.persona.org';

    Response\csp(array(
        'media-src' => '*',
        'img-src' => '*',
        'frame-src' => $frame_src
    ));

    Response\xframe();
    Response\xss();
    Response\nosniff();
});

// Show help
Router\get_action('show-help', function() {

    Response\html(Template\load('show_help'));
});

// Show the menu for the mobile view
Router\get_action('more', function() {

    Response\html(Template\layout('show_more', array('menu' => 'more')));
});

// Select another database
Router\get_action('select-db', function() {

    if (ENABLE_MULTIPLE_DB) {
        $_SESSION['database'] = \Model\Database\select(Request\param('database'));
    }

    Response\redirect('?action=login');
});
