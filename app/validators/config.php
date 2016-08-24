<?php

namespace Miniflux\Validator\Config;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

function validate_modification(array $values)
{
    $rules = array(
        new Validators\Required('username', t('The user name is required')),
        new Validators\MaxLength('username', t('The maximum length is 50 characters'), 50),
        new Validators\Required('autoflush', t('Value required')),
        new Validators\Required('autoflush_unread', t('Value required')),
        new Validators\Required('items_per_page', t('Value required')),
        new Validators\Integer('items_per_page', t('Must be an integer')),
        new Validators\Required('theme', t('Value required')),
        new Validators\Integer('frontend_updatecheck_interval', t('Must be an integer')),
        new Validators\Integer('debug_mode', t('Must be an integer')),
        new Validators\Integer('nocontent', t('Must be an integer')),
        new Validators\Integer('favicons', t('Must be an integer')),
        new Validators\Integer('original_marks_read', t('Must be an integer')),
    );

    if (ENABLE_AUTO_UPDATE) {
        $rules[] = new Validators\Required('auto_update_url', t('Value required'));
    }

    if (! empty($values['password'])) {
        $rules[] = new Validators\Required('password', t('The password is required'));
        $rules[] = new Validators\MinLength('password', t('The minimum length is 6 characters'), 6);
        $rules[] = new Validators\Required('confirmation', t('The confirmation is required'));
        $rules[] = new Validators\Equals('password', 'confirmation', t('Passwords don\'t match'));
    }

    $v = new Validator($values, $rules);

    return array(
        $v->execute(),
        $v->getErrors()
    );
}
