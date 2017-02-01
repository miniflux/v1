<?php

namespace Miniflux\Controller;

use Miniflux\Session\SessionStorage;
use Miniflux\Validator;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Template;
use Miniflux\Handler;
use Miniflux\Model;

// Display all groups
Router\get_action('groups', function () {
    $user_id = SessionStorage::getInstance()->getUserId();

    Response\html(Template\layout('groups/list', array(
        'groups' => Model\Group\get_all($user_id),
        'menu'   => 'feeds',
        'title'  => t('Groups'),
    )));
});

// Confirmation dialog to remove a group
Router\get_action('confirm-remove-group', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $group_id = Request\int_param('group_id');

    Response\html(Template\layout('groups/remove', array(
        'group' => Model\Group\get_group($user_id, $group_id),
        'menu' => 'feeds',
        'title' => t('Confirmation')
    )));
});

// Remove a group
Router\get_action('remove-group', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $group_id = Request\int_param('group_id');

    if (Model\Group\remove_group($user_id, $group_id)) {
        SessionStorage::getInstance()->setFlashMessage(t('This group has been removed successfully.'));
    } else {
        SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to remove this group.'));
    }

    Response\redirect('?action=groups');
});

// Edit group form
Router\get_action('edit-group', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $group_id = Request\int_param('group_id');
    $values = Model\Group\get_group($user_id, $group_id);

    Response\html(Template\layout('groups/edit', array(
        'values' => $values,
        'errors' => array(),
        'menu' => 'feeds',
        'title' => t('Edit group')
    )));
});

// Submit edit group form
Router\post_action('edit-group', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    $values = Request\values();

    list($valid, $errors) = Validator\Group\validate_modification($values);

    if ($valid) {
        if (Model\Group\update_group($user_id, $values['id'], $values['title'])) {
            SessionStorage::getInstance()->setFlashMessage(t('Group updated successfully.'));
            Response\redirect('?action=groups');
        } else {
            SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to edit this group.'));
        }
    }

    Response\html(Template\layout('groups/edit', array(
        'values' => $values,
        'errors' => $errors,
        'menu' => 'feeds',
        'title' => t('Edit group')
    )));
});
