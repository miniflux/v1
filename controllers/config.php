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
            'values' => array(),
            'menu' => 'config',
            'title' => t('New database')
        )));
    }

    Response\redirect('?action=config');
});

// Create a new database
Router\post_action('new-db', function() {

    if (ENABLE_MULTIPLE_DB) {

        $values = Request\values();
        list($valid, $errors) = Model\Database\validate($values);

        if ($valid) {

            if (Model\Database\create(strtolower($values['name']).'.sqlite', $values['username'], $values['password'])) {
                Session\flash(t('Database created successfully.'));
            }
            else {
                Session\flash_error(t('Unable to create the new database.'));
            }

            Response\redirect('?action=config');
        }

        Response\html(Template\layout('new_db', array(
            'errors' => $errors,
            'values' => $values,
            'menu' => 'config',
            'title' => t('New database')
        )));
    }

    Response\redirect('?action=config');
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

    Model\Config\new_tokens();
    Response\redirect('?action=config#api');
});

// Optimize the database manually
Router\get_action('optimize-db', function() {

    Database::get('db')->getConnection()->exec('VACUUM');
    Response\redirect('?action=config');
});

// Download the compressed database
Router\get_action('download-db', function() {

    Response\force_download('db.sqlite.gz');
    Response\binary(gzencode(file_get_contents(\Model\Database\get_path())));
});

// Display preferences page
Router\get_action('config', function() {

    Response\html(Template\layout('config', array(
        'errors' => array(),
        'values' => Model\Config\get_all(),
        'db_size' => filesize(\Model\Database\get_path()),
        'languages' => Model\Config\get_languages(),
        'timezones' => Model\Config\get_timezones(),
        'autoflush_options' => Model\Config\get_autoflush_options(),
        'paging_options' => Model\Config\get_paging_options(),
        'theme_options' => Model\Config\get_themes(),
        'sorting_options' => Model\Config\get_sorting_directions(),
        'redirect_nothing_to_read_options' => Model\Config\get_nothing_to_read_redirections(),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});

// Update preferences
Router\post_action('config', function() {

    $values = Request\values() + array('nocontent' => 0);
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
        'values' => Model\Config\get_all(),
        'db_size' => filesize(\Model\Database\get_path()),
        'languages' => Model\Config\get_languages(),
        'timezones' => Model\Config\get_timezones(),
        'autoflush_options' => Model\Config\get_autoflush_options(),
        'paging_options' => Model\Config\get_paging_options(),
        'theme_options' => Model\Config\get_themes(),
        'sorting_options' => Model\Config\get_sorting_directions(),
        'redirect_nothing_to_read_options' => Model\Config\get_nothing_to_read_redirections(),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});
