<div class="page-header">
    <h2><?= t('Database') ?></h2>
    <ul>
        <li><a href="?action=config"><?= t('settings') ?></a></li>
        <li><a href="?action=services"><?= t('external services') ?></a></li>
        <li><a href="?action=about"><?= t('about') ?></a></li>
        <li><a href="?action=help"><?= t('help') ?></a></li>
        <li><a href="?action=api"><?= t('api') ?></a></li>
    </ul>
</div>
<section>
    <div class="alert alert-normal">
        <ul>
            <li><?= t('Database size:') ?> <strong><?= Helper\format_bytes($db_size) ?></strong></li>
            <li><a href="?action=optimize-db&amp;csrf=<?= $csrf ?>"><?= t('Optimize the database') ?></a> <?= t('(VACUUM command)') ?></li>
            <li><a href="?action=download-db&amp;csrf=<?= $csrf ?>"><?= t('Download the entire database') ?></a> <?= t('(Gzip compressed Sqlite file)') ?></li>
            <?php if (ENABLE_MULTIPLE_DB): ?>
            <li>
                <a href="?action=new-db"><?= t('Add a new database (new user)') ?></a>
            </li>
            <?php endif ?>
        </ul>
    </div>
</section>