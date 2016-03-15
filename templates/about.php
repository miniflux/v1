<div class="page-header">
    <h2><?= $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?= t('general') ?></a></li>
            <li><a href="?action=services"><?= t('external services') ?></a></li>
            <li><a href="?action=api"><?= t('api') ?></a></li>
            <li><a href="?action=database"><?= t('database') ?></a></li>
            <li><a href="?action=help"><?= t('help') ?></a></li>
            <li class="active"><a href="?action=about"><?= t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
    <div class="panel panel-default">
        <h3><?= t('Bookmarks') ?></h3>
        <ul>
            <li>
                <a href="<?= Helper\get_current_base_url().'?action=bookmark-feed&amp;database='.urlencode($db_name).'&amp;token='.urlencode($config['feed_token']) ?>" target="_blank"><?= t('Bookmark RSS Feed') ?></a>
            </li>
        </ul>
    </div>
    <div class="panel panel-default">
        <h3><?= t('Bookmarklet') ?></h3>
        <a class="bookmarklet" href="javascript:location.href='<?= Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?= urlencode($config['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"><?= t('Subscribe with Miniflux') ?></a> (<?= t('Drag and drop this link to your bookmarks') ?>)
        <input type="text" class="auto-select" readonly="readonly" value="javascript:location.href='<?= Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?= urlencode($config['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"/>
    </div>
    <div class="panel panel-default">
        <h3><?= t('About') ?></h3>
        <ul>
            <?php if (! empty($config['last_login'])): ?>
                <li><?= t('Last login:') ?> <strong><?= date('Y-m-d H:i', $config['last_login']) ?></strong></li>
            <?php endif ?>
            <li><?= t('Miniflux version:') ?> <strong><?= APP_VERSION ?></strong></li>
            <li><?= t('Official website:') ?> <a href="https://miniflux.net" rel="noreferrer" target="_blank">https://miniflux.net</a></li>
            <li><a href="?action=console"><?= t('Console') ?></a></li>
        </ul>
    </div>
</section>
