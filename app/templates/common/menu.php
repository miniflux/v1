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
