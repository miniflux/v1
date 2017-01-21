<?php

namespace Miniflux\Controller;

use Miniflux\Session\SessionStorage;
use Miniflux\Validator;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Model;

// Re-generate tokens
Router\get_action('generate-tokens', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    if (Helper\check_csrf(Request\param('csrf'))) {
        Model\User\regenerate_tokens($user_id);
    }

    Response\redirect('?action=config');
});

// Display preferences page
Router\get_action('config', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('config/preferences', array(
        'errors' => array(),
        'values' => Model\Config\get_all($user_id) + array('csrf' => Helper\generate_csrf()),
        'languages' => Model\Config\get_languages(),
        'timezones' => Model\Config\get_timezones(),
        'autoflush_read_options' => Model\Config\get_autoflush_read_options(),
        'autoflush_unread_options' => Model\Config\get_autoflush_unread_options(),
        'paging_options' => Model\Config\get_paging_options(),
        'theme_options' => Model\Config\get_themes(),
        'sorting_options' => Model\Config\get_sorting_directions(),
        'display_mode' => Model\Config\get_display_mode(),
        'item_title_link' => Model\Config\get_item_title_link(),
        'redirect_nothing_to_read_options' => Model\Config\get_nothing_to_read_redirections(),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});

// Update preferences
Router\post_action('config', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $values = Request\values() + array('nocontent' => 0, 'image_proxy' => 0, 'favicons' => 0, 'original_marks_read' => 0);
    Helper\check_csrf_values($values);
    list($valid, $errors) = Validator\Config\validate_modification($values);

    if ($valid) {
        if (Model\Config\save($user_id, $values)) {
            SessionStorage::getInstance()->setFlashMessage(t('Your preferences are updated.'));
        } else {
            SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to update your preferences.'));
        }

        Response\redirect('?action=config');
    }

    Response\html(Template\layout('config/preferences', array(
        'errors' => $errors,
        'values' => Model\Config\get_all($user_id) + array('csrf' => Helper\generate_csrf()),
        'languages' => Model\Config\get_languages(),
        'timezones' => Model\Config\get_timezones(),
        'autoflush_read_options' => Model\Config\get_autoflush_read_options(),
        'autoflush_unread_options' => Model\Config\get_autoflush_unread_options(),
        'paging_options' => Model\Config\get_paging_options(),
        'theme_options' => Model\Config\get_themes(),
        'sorting_options' => Model\Config\get_sorting_directions(),
        'redirect_nothing_to_read_options' => Model\Config\get_nothing_to_read_redirections(),
        'display_mode' => Model\Config\get_display_mode(),
        'item_title_link' => Model\Config\get_item_title_link(),
        'menu' => 'config',
        'title' => t('Preferences')
    )));
});

// Get configuration parameters (AJAX request)
Router\post_action('get-config', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $return = array();
    $options = Request\values();

    if (empty($options)) {
        $return = Model\Config\get_all($user_id);
    } else {
        foreach ($options as $name) {
            $return[$name] = Helper\config($name);
        }
    }

    Response\json($return);
});
