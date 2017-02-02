<?php

namespace Miniflux\Controller;

use Miniflux\Session\SessionStorage;
use Miniflux\Validator;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Helper;
use Miniflux\Handler;
use Miniflux\Model;

// Refresh all feeds, used when Javascript is disabled
Router\get_action('refresh-all', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    Handler\Feed\update_feeds($user_id);
    SessionStorage::getInstance()->setFlashErrorMessage(t('Your subscriptions are updated'));
    Response\redirect('?action=unread');
});

// Edit feed form
Router\get_action('edit-feed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id');

    $values = Model\Feed\get_feed($user_id, $feed_id);
    $values += array(
        'feed_group_ids' => Model\Group\get_feed_group_ids($feed_id)
    );

    Response\html(Template\layout('feeds/edit', array(
        'values' => $values,
        'errors' => array(),
        'groups' => Model\Group\get_all($user_id),
        'menu' => 'feeds',
        'title' => t('Edit subscription')
    )));
});

// Submit edit feed form
Router\post_action('edit-feed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $values = Request\values();
    $values += array(
        'enabled'               => 0,
        'download_content'      => 0,
        'rtl'                   => 0,
        'cloak_referrer'        => 0,
        'parsing_error'         => 0,
        'parsing_error_message' => '',
        'feed_group_ids'        => array(),
    );

    list($valid, $errors) = Validator\Feed\validate_modification($values);

    if ($valid) {
        if (Model\Feed\update_feed($user_id, $values['id'], $values)) {
            SessionStorage::getInstance()->setFlashMessage(t('Your subscription has been updated.'));
            Response\redirect('?action=feeds');
        } else {
            SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to edit your subscription.'));
        }
    }

    Response\html(Template\layout('feeds/edit', array(
        'values' => $values,
        'errors' => $errors,
        'groups' => Model\Group\get_all($user_id),
        'menu' => 'feeds',
        'title' => t('Edit subscription')
    )));
});

// Confirmation box to remove a feed
Router\get_action('confirm-remove-feed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id');

    Response\html(Template\layout('feeds/remove', array(
        'feed' => Model\Feed\get_feed($user_id, $feed_id),
        'menu' => 'feeds',
        'title' => t('Confirmation')
    )));
});

// Remove a feed
Router\get_action('remove-feed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id');

    if (Model\Feed\remove_feed($user_id, $feed_id)) {
        SessionStorage::getInstance()->setFlashMessage(t('This subscription has been removed successfully.'));
    } else {
        SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to remove this subscription.'));
    }

    Response\redirect('?action=feeds');
});

// Refresh one feed and redirect to unread items
Router\get_action('refresh-feed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id');
    $redirect = Request\param('redirect', 'unread');

    Handler\Feed\update_feed($user_id, $feed_id);
    Response\redirect('?action='.$redirect.'&feed_id='.$feed_id);
});

// Ajax call to refresh one feed
Router\post_action('refresh-feed', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $feed_id = Request\int_param('feed_id', 0);

    Response\json(array(
        'feed_id'     => $feed_id,
        'result'      => Handler\Feed\update_feed($user_id, $feed_id),
        'feed'        => Model\Feed\get_feed($user_id, $feed_id),
        'items_count' => Model\ItemFeed\count_items_by_status($user_id, $feed_id),
    ));
});

// Display all feeds
Router\get_action('feeds', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $nothing_to_read = Request\int_param('nothing_to_read');
    $nb_unread_items = Model\Item\count_by_status($user_id, 'unread');
    $feeds = Model\Feed\get_feeds_with_items_count_and_groups($user_id);

    if ($nothing_to_read === 1 && $nb_unread_items > 0) {
        Response\redirect('?action=unread');
    }

    Response\html(Template\layout('feeds/list', array(
        'favicons'        => Model\Favicon\get_feeds_favicons($feeds),
        'feeds'           => $feeds,
        'nothing_to_read' => $nothing_to_read,
        'nb_unread_items' => $nb_unread_items,
        'nb_failed_feeds' => Model\Feed\count_failed_feeds($user_id),
        'menu'            => 'feeds',
        'title'           => t('Subscriptions'),
    )));
});

// Display form to add one feed
Router\get_action('add', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $values = array(
        'download_content' => 0,
        'rtl'              => 0,
        'cloak_referrer'   => 0,
        'create_group'     => '',
        'feed_group_ids'   => array(),
    );

    Response\html(Template\layout('feeds/create', array(
        'values' => $values + array('csrf' => Helper\generate_csrf()),
        'errors' => array(),
        'groups' => Model\Group\get_all($user_id),
        'menu'   => 'feeds',
        'title'  => t('New subscription'),
    )));
});

// Add a feed with the form or directly from the url, it can be used by a bookmarklet for example
Router\action('subscribe', function () {
    if (Request\is_post()) {
        $values = Request\values();
        Helper\check_csrf_values($values);
        $url = isset($values['url']) ? $values['url'] : '';
        $user_id = SessionStorage::getInstance()->getUserId();
    } else {
        $url = Request\param('url');
        $token = Request\param('token');
        $user = Model\User\get_user_by_token('bookmarklet_token', $token);
        $values = array();

        if (empty($user)) {
            Response\text('Unauthorized', 401);
        }

        $user_id = $user['id'];
    }

    $values += array(
        'url'              => trim($url),
        'download_content' => 0,
        'rtl'              => 0,
        'cloak_referrer'   => 0,
        'feed_group_ids'   => array(),
        'group_name'       => '',
    );

    list($feed_id, $error_message) = Handler\Feed\create_feed(
        $user_id,
        $values['url'],
        $values['download_content'],
        $values['rtl'],
        $values['cloak_referrer'],
        $values['feed_group_ids'],
        $values['group_name']
    );

    if ($feed_id >= 1) {
        SessionStorage::getInstance()->setFlashMessage(t('Subscription added successfully.'));
        Response\redirect('?action=feed-items&feed_id='.$feed_id);
    } else {
        SessionStorage::getInstance()->setFlashErrorMessage($error_message);
    }

    Response\html(Template\layout('feeds/create', array(
        'values' => $values + array('csrf' => Helper\generate_csrf()),
        'groups' => Model\Group\get_all($user_id),
        'menu'   => 'feeds',
        'title'  => t('Subscriptions'),
    )));
});
