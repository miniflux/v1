<div class="page-header">
    <h2><?= t('About') ?></h2>
    <ul>
        <li><a href="?action=config"><?= t('settings') ?></a></li>
        <li><a href="?action=help"><?= t('help') ?></a></li>
        <li><a href="?action=database"><?= t('database') ?></a></li>
        <li><a href="?action=api"><?= t('api') ?></a></li>
    </ul>
</div>
<section>
    <div class="alert alert-normal">
        <h3 id="bookmarks"><?= t('Bookmarks') ?></h3>
        <ul>
            <li>
                <?= t('Bookmarklet:') ?>
                <a href="javascript:location.href='<?= Helper\get_current_base_url() ?>?action=subscribe&amp;token=<?= urlencode($config['bookmarklet_token']) ?>&amp;url='+encodeURIComponent(location.href)"><?= t('Subscribe with Miniflux') ?></a> (<?= t('Drag and drop this link to your bookmarks') ?>)
            <li>
                <a href="<?= Helper\get_current_base_url().'?action=bookmark-feed&amp;token='.urlencode($config['feed_token']) ?>" target="_blank"><?= t('Bookmark RSS Feed') ?></a>
            </li>
        </ul>
    </div>
    <div class="alert alert-normal">
        <h3><?= t('About') ?></h3>
        <ul>
            <li><?= t('Miniflux version:') ?> <strong><?= APP_VERSION ?></strong></li>
            <li><?= t('Official website:') ?> <a href="http://miniflux.net" rel="noreferrer" target="_blank">http://miniflux.net</a></li>
            <li><a href="?action=console"><?= t('Console') ?></a></li>
        </ul>
    </div>
</section>