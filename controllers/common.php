<?php

use PicoFarad\Router;
use PicoFarad\Response;
use PicoFarad\Request;
use PicoFarad\Session;
use PicoFarad\Template;

// Called before each action
Router\before(function($action) {

    Session\open(BASE_URL_DIRECTORY, SESSION_SAVE_PATH);

    // Select another database
    if (! empty($_SESSION['database'])) {
        Model\Database\select($_SESSION['database']);
    }

    // Redirect to the login form if the user is not authenticated
    $ignore_actions = array('login', 'google-auth', 'google-redirect-auth', 'mozilla-auth', 'bookmark-feed', 'select-db');

    if (! isset($_SESSION['user']) && ! in_array($action, $ignore_actions)) {

        if (! Model\RememberMe\authenticate()) {
            Response\redirect('?action=login');
        }
    }
    else if (Model\RememberMe\has_cookie()) {
        Model\RememberMe\refresh();
    }

    // Load translations
    $language = Model\Config\get('language') ?: 'en_US';
    if ($language !== 'en_US') \Translator\load($language);

    // Set timezone
    date_default_timezone_set(Model\Config\get('timezone') ?: 'UTC');

    // HTTP secure headers
    $frame_src = Model\Config\get_iframe_whitelist();;
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
