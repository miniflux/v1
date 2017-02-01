<!DOCTYPE html>
<html
    <?php if (Miniflux\Model\Config\is_language_rtl()): ?>
        dir="rtl"
    <?php endif ?>
>
    <head>
        <?php echo Miniflux\Template\load('common/head') ?>
        <script type="text/javascript" src="assets/js/app.min.js?<?php echo filemtime('assets/js/app.min.js') ?>" defer></script>
    </head>
    <body>
        <?php echo Miniflux\Template\load('common/menu', array('menu' => isset($menu) ? $menu : '')) ?>

        <section class="page" data-item-page="<?= $menu ?>">
            <?php echo Miniflux\Helper\flash('flash_message', '<div class="alert alert-success">%s</div>') ?>
            <?php echo Miniflux\Helper\flash('flash_error_message', '<div class="alert alert-error">%s</div>') ?>
            <?php echo $content_for_layout ?>
        </section>

        <?php echo Miniflux\Template\load('common/help') ?>
    </body>
</html>
