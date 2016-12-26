<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li><a href="?action=profile"><?php echo t('profile') ?></a></li>
            <?php if (Miniflux\Helper\is_admin()): ?>
                <li><a href="?action=users"><?php echo t('users') ?></a></li>
            <?php endif ?>
            <li><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=help"><?php echo t('help') ?></a></li>
            <li class="active"><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
    <div class="panel panel-default">
        <h3><?php echo t('Bookmarks') ?></h3>
        <ul>
            <li>
                <a href="<?php echo Miniflux\Helper\get_current_base_url(), '?action=bookmark-feed&amp;token=', urlencode($user['feed_token']) ?>" target="_blank"><?php echo t('Bookmark RSS Feed') ?></a>
            </li>
        </ul>
    </div>
    <div class="panel panel-default">
        <h3><?php echo t('Bookmarklet') ?></h3>
        <a class="bookmarklet" href="javascript:location.href='<?php echo Miniflux\Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?php echo urlencode($user['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"><?php echo t('Subscribe with Miniflux') ?></a> (<?php echo t('Drag and drop this link to your bookmarks') ?>)
        <input type="text" class="auto-select" readonly="readonly" value="javascript:location.href='<?php echo Miniflux\Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?php echo urlencode($user['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"/>
    </div>
    <?php if (ENABLE_CRONJOB_HTTP_ACCESS): ?>
    <div class="panel panel-default">
        <h3><?php echo t('Cronjob URL') ?></h3>
        <input type="text" class="auto-select" readonly="readonly" value="<?php echo Miniflux\Helper\get_current_base_url(), 'cronjob.php?token=', urlencode($user['cronjob_token']) ?>">
    </div>
    <?php endif ?>
    <div class="panel panel-default">
        <h3><?php echo t('About') ?></h3>
        <ul>
            <li><?php echo t('Miniflux version:') ?> <strong><?php echo APP_VERSION ?></strong></li>
            <li><?php echo t('Official website:') ?> <a href="https://miniflux.net" rel="noreferrer" target="_blank">https://miniflux.net</a></li>
        </ul>
    </div>
</section>
