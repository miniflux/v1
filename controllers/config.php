<?php

use PicoFarad\Router;
use PicoFarad\Response;
use PicoFarad\Request;
use PicoFarad\Session;
use PicoFarad\Template;
use PicoDb\Database;

// Display a form to add a new database
Router\get_action('new-db', function() {

    if (ENABLE_MULTIPLE_DB) {

        Response\html(Template\layout('new_db', array(
            'errors' => array(),
            'values' => array(
                'csrf' => Model\Config\generate_csrf(),
            ),
            'menu' => 'config',
            'title' => t('New database')
        )));
    }

    Response\redirect('?action=database');
});

// Create a new database
Router\post_action('new-db', function() {

    if (ENABLE_MULTIPLE_DB) {

        $values = Request\values();
        Model\Config\check_csrf_values($values);
        list($valid, $errors) = Model\Database\validate($values);

        if ($valid) {

            if (Model\Database\create(strtolower($values['name']).'.sqlite', $values['username'], $values['password'])) {
                Session\flash(t('Database created successfully.'));
            }
            else {
                Session\flash_error(t('Unable to create the new database.'));
            }

            Response\redirect('?action=database');
        }

        Response\html(Template\layout('new_db', array(
            'errors' => $errors,
            'values' => $values + array('csrf' => Model\Config\generate_csrf()),
            'menu' => 'config',
            'title' => t('New database')
        )));
    }

    Response\redirect('?action=database');
});

// Comfirmation box before auto-update
Router\get_action('confirm-auto-update', function() {

    Response\html(Template\layout('confirm_auto_update', array(
        'menu' => 'config',
        'title' => t('Confirmation')
    )));
});

// Auto-update
Router\get_action('auto-update', function() {

    if (ENABLE_AUTO_UPDATE) {

        if (Model\AutoUpdate\execute(Model\Config\get('auto_update_url'))) {
            Session\flash(t('Miniflux is updated!'));
        }
        else {
            Session\flash_error(t('Unable to update Miniflux, check the console for errors.'));
        }
    }

    Response\redirect('?action=config');
});

// Re-generate tokens
Router\get_action('generate-tokens', function() {

    if (Model\Config\check_csrf(Request\param('csrf'))) {
        Model\Config\new_tokens();
    }

    Response\redirect('?action=api');
});

// Optimize the database manually
Router\get_action('optimize-db', function() {

    if (Model\Config\check_csrf(Request\param('csrf'))) {
        Database::get('db')->getConnection()->exec('VACUUM');
    }

    Response\redirect('?action=database');
});

// Download the compressed database
Router\get_action('download-db', function() {

    if (Model\Config\check_csrf(Request\param('csrf'))) {
        Response\force_download('db.sqlite.gz');
        Response\binary(gzencode(file_get_contents(Model\Database\get_path())));
    }
});

// Display preferences page
Router\get_action('config', function() {

    Response\html(Template\layout('config', array(
        'errors' => array(),
        'values' => Model\Config\get_all() + array('csrf' => Model\Config\generate_csrf()),
        'languages' => Model\Config\get_languages(),
        'timezones' => Model\Config\get_timezones(),
        'autoflush_read_options' => Model\Config\get_autoflush_read_options(),
        'autoflush_unread_options' => Model\Config\get_autoflush_unread_options(),
        'paging_options' => Model\Config\get_paging_options(),
        'theme_options' => Model\Config\get_themes(),
        'sorting_options' => Model\Config\get_sorting_directions(),
        'display_mode' => Model\Config\get_display_mode(),
        'redirect_nothing_to_read_options' => Model\Config\get_nothing_to_read_redirections(),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});

// Update preferences
Router\post_action('config', function() {

    $values = Request\values() + array('nocontent' => 0);
    Model\Config\check_csrf_values($values);
    list($valid, $errors) = Model\Config\validate_modification($values);

    if ($valid) {

        if (Model\Config\save($values)) {
            Session\flash(t('Your preferences are updated.'));
        }
        else {
            Session\flash_error(t('Unable to update your preferences.'));
        }

        Response\redirect('?action=config');
    }

    Response\html(Template\layout('config', array(
        'errors' => $errors,
        'values' => Model\Config\get_all() + array('csrf' => Model\Config\generate_csrf()),
        'languages' => Model\Config\get_languages(),
        'timezones' => Model\Config\get_timezones(),
        'autoflush_options' => Model\Config\get_autoflush_options(),
        'paging_options' => Model\Config\get_paging_options(),
        'theme_options' => Model\Config\get_themes(),
        'sorting_options' => Model\Config\get_sorting_directions(),
        'redirect_nothing_to_read_options' => Model\Config\get_nothing_to_read_redirections(),
        'display_mode' => Model\Config\get_display_mode(),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});

// Display help page
Router\get_action('help', function() {

    Response\html(Template\layout('help', array(
        'config' => Model\Config\get_all(),
        'menu' => 'config',
        'title' => t('Help')
    )));
});

// Display about page
Router\get_action('about', function() {

    Response\html(Template\layout('about', array(
        'csrf' => Model\Config\generate_csrf(),
        'config' => Model\Config\get_all(),
        'menu' => 'config',
        'title' => t('About')
    )));
});

// Display database page
Router\get_action('database', function() {

    Response\html(Template\layout('database', array(
        'csrf' => Model\Config\generate_csrf(),
        'config' => Model\Config\get_all(),
        'db_size' => filesize(\Model\Database\get_path()),
        'menu' => 'config',
        'title' => t('Database')
    )));
});

// Display API page
Router\get_action('api', function() {

    Response\html(Template\layout('api', array(
        'csrf' => Model\Config\generate_csrf(),
        'config' => Model\Config\get_all(),
        'menu' => 'config',
        'title' => t('API')
    )));
});
