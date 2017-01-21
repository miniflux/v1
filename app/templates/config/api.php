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
            <li class="active"><a href="?action=api"><?php echo t('api') ?></a></li>
            <li><a href="?action=help"><?php echo t('help') ?></a></li>
            <li><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
    <div class="panel panel-default">
        <h3 id="fever"><?php echo t('Fever API') ?></h3>
        <ul>
            <li><?php echo t('API endpoint:') ?> <strong><?php echo Miniflux\Helper\get_current_base_url(), 'fever/' ?></strong></li>
            <li><?php echo t('API username:') ?> <strong><?php echo Miniflux\Helper\escape($user['username']) ?></strong></li>
            <li><?php echo t('API token:') ?> <strong><?php echo Miniflux\Helper\escape($user['fever_token']) ?></strong></li>
        </ul>
    </div>
    <div class="panel panel-default">
        <h3 id="api"><?php echo t('Miniflux API') ?></h3>
        <ul>
            <li><?php echo t('API endpoint:') ?> <strong><?php echo Miniflux\Helper\get_current_base_url(), 'jsonrpc.php' ?></strong></li>
            <li><?php echo t('API username:') ?> <strong><?php echo Miniflux\Helper\escape($user['username']) ?></strong></li>
            <li><?php echo t('API token:') ?> <strong><?php echo Miniflux\Helper\escape($user['api_token']) ?></strong></li>
        </ul>
    </div>
</section>
