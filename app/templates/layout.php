<!DOCTYPE html>
<html
    <?php if (Miniflux\Model\Config\is_language_rtl()): ?>
        dir="rtl"
    <?php endif ?>
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">
        <meta name="referrer" content="no-referrer">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title><?php echo isset($title) ? Miniflux\Helper\escape($title) : t('Miniflux') ?></title>

        <link rel="icon" type="image/png" href="assets/img/favicon.png">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="apple-touch-icon" href="assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/touch-icon-ipad-retina.png">

        <link href="<?php echo Miniflux\Helper\css() ?>" rel="stylesheet" media="screen">
        <script type="text/javascript" src="assets/js/app.min.js?<?php echo filemtime('assets/js/app.min.js') ?>" defer></script>
    </head>
    <body>
        <?php echo Miniflux\Template\load('common/menu') ?>

        <section class="page" data-item-page="<?= $menu ?>">
            <?php echo Miniflux\Helper\flash('flash_message', '<div class="alert alert-success">%s</div>') ?>
            <?php echo Miniflux\Helper\flash('flash_error_message', '<div class="alert alert-error">%s</div>') ?>
            <?php echo $content_for_layout ?>
        </section>

        <?php echo Miniflux\Template\load('config/help_layer') ?>
    </body>
</html>
