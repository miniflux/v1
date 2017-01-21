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
            <li class="active"><a href="?action=help"><?php echo t('help') ?></a></li>
            <li><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
    <?php echo Miniflux\Template\load('common/keyboard_shortcuts') ?>
</section>
<div class="page-section">
    <h2><?php echo t('Documentation') ?></h2>
</div>
<section>
<ul>
    <li><a href="https://miniflux.net/documentation/installation" rel="noreferrer" target="_blank"><?php echo t('Installation instructions') ?></a></li>
    <li><a href="https://miniflux.net/documentation/upgrade" rel="noreferrer" target="_blank"><?php echo t('Upgrade to a new version') ?></a></li>
    <li><a href="https://miniflux.net/documentation/cronjob" rel="noreferrer" target="_blank"><?php echo t('Cronjob') ?></a></li>
    <li><a href="https://miniflux.net/documentation/config" rel="noreferrer" target="_blank"><?php echo t('Advanced configuration') ?></a></li>
    <li><a href="https://miniflux.net/documentation/full-article-download" rel="noreferrer" target="_blank"><?php echo t('Full article download') ?></a></li>
    <li><a href="https://miniflux.net/documentation/themes" rel="noreferrer" target="_blank"><?php echo t('Themes') ?></a></li>
    <li><a href="https://miniflux.net/documentation/json-rpc-api" rel="noreferrer" target="_blank"><?php echo t('Json-RPC API') ?></a></li>
    <li><a href="https://miniflux.net/documentation/fever" rel="noreferrer" target="_blank"><?php echo t('Fever API') ?></a></li>
    <li><a href="https://miniflux.net/documentation/translations" rel="noreferrer" target="_blank"><?php echo t('Translations') ?></a></li>
    <li><a href="https://miniflux.net/documentation/docker" rel="noreferrer" target="_blank"><?php echo t('Run Miniflux with Docker') ?></a></li>
    <li><a href="https://miniflux.net/documentation/faq" rel="noreferrer" target="_blank"><?php echo t('FAQ') ?></a></li>
</ul>
</section>
