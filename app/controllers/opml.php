<?php

namespace Miniflux\Controller;

use Exception;
use Miniflux\Router;
use Miniflux\Response;
use Miniflux\Request;
use Miniflux\Session\SessionStorage;
use Miniflux\Template;
use Miniflux\Handler;

// OPML export
Router\get_action('export', function () {
    $user_id = SessionStorage::getInstance()->getUserId();
    Response\force_download('feeds.opml');
    Response\xml(Handler\Opml\export_all_feeds($user_id));
});

// OPML import form
Router\get_action('import', function () {
    Response\html(Template\layout('config/import', array(
        'errors' => array(),
        'menu' => 'feeds',
        'title' => t('OPML Import')
    )));
});

// OPML importation
Router\post_action('import', function () {
    try {
        $user_id = SessionStorage::getInstance()->getUserId();
        Handler\Opml\import_opml($user_id, Request\file_content('file'));
        SessionStorage::getInstance()->setFlashMessage(t('Your feeds have been imported.'));
        Response\redirect('?action=feeds');
    } catch (Exception $e) {
        SessionStorage::getInstance()->setFlashErrorMessage(t('Unable to import your OPML file.').' ('.$e->getMessage().')');
        Response\redirect('?action=import');
    }
});
