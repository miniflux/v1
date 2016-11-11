<div class="page-header">
    <h2><?php echo $title ?></h2>
    <nav>
        <ul>
            <li><a href="?action=config"><?php echo t('general') ?></a></li>
            <li><a href="?action=services"><?php echo t('external services') ?></a></li>
            <li><a href="?action=api"><?php echo t('api') ?></a></li>
            <li class="active"><a href="?action=database"><?php echo t('database') ?></a></li>
            <li><a href="?action=help"><?php echo t('help') ?></a></li>
            <li><a href="?action=about"><?php echo t('about') ?></a></li>
        </ul>
    </nav>
</div>
<section>
    <div class="panel panel-default">
        <ul>
            <li><?php echo t('Database size:') ?> <strong><?php echo Miniflux\Helper\format_bytes($db_size) ?></strong></li>
            <li><a href="?action=optimize-db&amp;csrf=<?php echo $csrf ?>"><?php echo t('Optimize the database') ?></a> <?php echo t('(VACUUM command)') ?></li>
            <li><a href="?action=download-db&amp;csrf=<?php echo $csrf ?>"><?php echo t('Download the entire database') ?></a> <?php echo t('(Gzip compressed Sqlite file)') ?></li>
            <?php if (ENABLE_MULTIPLE_DB): ?>
            <li>
                <a href="?action=new-db"><?php echo t('Add a new database (new user)') ?></a>
            </li>
            <?php endif ?>
        </ul>
    </div>
</section>
