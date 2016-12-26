<?php

namespace Miniflux\Validator\Config;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

function validate_modification(array $values)
{
    $rules = array(
        new Validators\Required('autoflush', t('Value required')),
        new Validators\Required('autoflush_unread', t('Value required')),
        new Validators\Required('items_per_page', t('Value required')),
        new Validators\Integer('items_per_page', t('Must be an integer')),
        new Validators\Required('theme', t('Value required')),
        new Validators\Integer('frontend_updatecheck_interval', t('Must be an integer')),
        new Validators\Integer('nocontent', t('Must be an integer')),
        new Validators\Integer('favicons', t('Must be an integer')),
        new Validators\Integer('original_marks_read', t('Must be an integer')),
    );

    $v = new Validator($values, $rules);

    return array(
        $v->execute(),
        $v->getErrors()
    );
}
