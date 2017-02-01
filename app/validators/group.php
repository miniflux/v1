<?php

namespace Miniflux\Validator\Group;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

function validate_modification(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('id', t('This field is required')),
        new Validators\Integer('id', t('This value must be an integer')),
        new Validators\Required('title', t('The title is required')),
        new Validators\MaxLength('title', t('This text is too long (max. %d)', 255), 255),
    ));

    return array(
        $v->execute(),
        $v->getErrors(),
    );
}
