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
        <script type="text/javascript" src="assets/js/all.js?<?php echo filemtime('assets/js/all.js') ?>" defer></script>
    </head>
    <body>
        <header>
            <nav>
                <a class="logo" href="?"><?php echo tne('mini%sflux%s','<span>','</span>') ?></a>
                <ul>
                    <li<?php echo isset($menu) && $menu === 'unread' ? ' class="active"' : '' ?>>
                        <a href="?action=unread"><?php echo t('unread') ?><span id="nav-counter"><?php echo empty($nb_unread_items) ? '' : $nb_unread_items ?></span></a>
                    </li>
                    <li class="hide-mobile<?php echo isset($menu) && $menu === 'bookmarks' ? ' active' : '' ?>">
                        <a href="?action=bookmarks"><?php echo t('bookmarks') ?></a>
                    </li>
                    <li class="hide-mobile<?php echo isset($menu) && $menu === 'history' ? ' active' : '' ?>">
                        <a href="?action=history"><?php echo t('history') ?></a>
                    </li>
                    <li class="hide-mobile<?php echo isset($menu) && $menu === 'feeds' ? ' active' : '' ?>">
                        <a href="?action=feeds"><?php echo t('subscriptions') ?></a>
                    </li>
                    <li class="hide-mobile<?php echo isset($menu) && $menu === 'config' ? ' active' : '' ?>">
                        <a href="?action=config"><?php echo t('preferences') ?></a>
                    </li>
                    <li class="hide-mobile">
                        <a href="?action=logout"><?php echo t('logout') ?></a>
                    </li>
                    <li class="hide-desktop">
                        <span data-action="toggle-menu-more" class="menu-more-switcher" href="#">∨ <?php echo t('menu') ?></span>
                    </li>
                </ul>
            </nav>
        </header>
        <div id="menu-more" class="hide">
            <ul>
                <li<?php echo isset($menu) && $menu === 'unread' ? ' class="active"' : '' ?>><a href="?action=unread"><?= t('unread') ?></a></li>
                <li<?php echo isset($menu) && $menu === 'bookmarks' ? ' class="active"' : '' ?>><a href="?action=bookmarks"><?= t('bookmarks') ?></a></li>
                <li<?php echo isset($menu) && $menu === 'history' ? ' class="active"' : '' ?>><a href="?action=history"><?= t('history') ?></a></li>
                <li<?php echo isset($menu) && $menu === 'feeds' ? ' class="active"' : '' ?>><a href="?action=feeds"><?= t('subscriptions') ?></a></li>
                <li<?php echo isset($menu) && $menu === 'config' ? ' class="active"' : '' ?>><a href="?action=config"><?= t('preferences') ?></a></li>
                <li><a href="?action=logout"><?= t('logout') ?></a></li>
            </ul>
        </div>
        <section class="page" data-item-page="<?= $menu ?>">
            <?php echo Miniflux\Helper\flash('flash_message', '<div class="alert alert-success">%s</div>') ?>
            <?php echo Miniflux\Helper\flash('flash_error_message', '<div class="alert alert-error">%s</div>') ?>
            <?php echo $content_for_layout ?>
        </section>
        <?php echo Miniflux\Template\load('help_layer') ?>
    </body>
</html>
