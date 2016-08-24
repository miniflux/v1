<?php

namespace Miniflux\Validator\Feed;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

function validate_modification(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('id', t('The feed id is required')),
        new Validators\Required('title', t('The title is required')),
        new Validators\Required('site_url', t('The site url is required')),
        new Validators\Required('feed_url', t('The feed url is required')),
    ));

    return array(
        $v->execute(),
        $v->getErrors(),
    );
}
