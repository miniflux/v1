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

    // Authentication
    if (Model\User\is_logged()) {

        if (! Model\User\is_user_session()) {
            Session\close();
            Response\redirect('?action=login');
        }

        if (Model\RememberMe\has_cookie()) {
            Model\RememberMe\refresh();
        }
    }
    else {

        if (! in_array($action, array('login', 'bookmark-feed', 'select-db'))) {

            if (! Model\RememberMe\authenticate()) {
               Response\redirect('?action=login');
            }
        }
    }

    // Load translations
    $language = Model\Config\get('language') ?: 'en_US';
    if ($language !== 'en_US') Translator\load($language);

    // Set timezone
    date_default_timezone_set(Model\Config\get('timezone') ?: 'UTC');

    // HTTP secure headers
    Response\csp(array(
        'media-src' => '*',
        'img-src' => '*',
        'frame-src' => Model\Config\get_iframe_whitelist(),
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
