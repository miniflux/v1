<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=database"><?php echo t('database') ?></a></li>
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
                <a href="<?php echo Miniflux\Helper\get_current_base_url(), '?action=bookmark-feed&amp;database=', urlencode($db_name), '&amp;token=', urlencode($config['feed_token']) ?>" target="_blank"><?php echo t('Bookmark RSS Feed') ?></a>
            </li>
        </ul>
    </div>
    <div class="panel panel-default">
        <h3><?php echo t('Bookmarklet') ?></h3>
        <a class="bookmarklet" href="javascript:location.href='<?php echo Miniflux\Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?php echo urlencode($config['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"><?php echo t('Subscribe with Miniflux') ?></a> (<?php echo t('Drag and drop this link to your bookmarks') ?>)
        <input type="text" class="auto-select" readonly="readonly" value="javascript:location.href='<?php echo Miniflux\Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?php echo urlencode($config['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"/>
    </div>
    <div class="panel panel-default">
        <h3><?php echo t('About') ?></h3>
        <ul>
            <?php if (! empty($config['last_login'])): ?>
                <li><?php echo t('Last login:') ?> <strong><?php echo date('Y-m-d H:i', $config['last_login']) ?></strong></li>
            <?php endif ?>
            <li><?php echo t('Miniflux version:') ?> <strong><?php echo APP_VERSION ?></strong></li>
            <li><?php echo t('Official website:') ?> <a href="https://miniflux.net" rel="noreferrer" target="_blank">https://miniflux.net</a></li>
            <li><a href="?action=console"><?php echo t('Console') ?></a></li>
        </ul>
    </div>
</section>
