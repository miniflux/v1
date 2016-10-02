<div class="page-header">
    <h2><?php echo t('New database') ?></h2>
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

<form method="post" action="?action=new-db" autocomplete="off">

    <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>

    <?php echo Miniflux\Helper\form_label(t('Database name'), 'name') ?>
    <?php echo Miniflux\Helper\form_text('name', $values, $errors, array('required', 'autofocus')) ?>
    <p class="form-help"><?php echo t('The name must have only alpha-numeric characters') ?></p>

    <?php echo Miniflux\Helper\form_label(t('Username'), 'username') ?>
    <?php echo Miniflux\Helper\form_text('username', $values, $errors, array('required')) ?><br/>

    <?php echo Miniflux\Helper\form_label(t('Password'), 'password') ?>
    <?php echo Miniflux\Helper\form_password('password', $values, $errors, array('required')) ?>

    <?php echo Miniflux\Helper\form_label(t('Confirmation'), 'confirmation') ?>
    <?php echo Miniflux\Helper\form_password('confirmation', $values, $errors, array('required')) ?><br/>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?php echo t('Create') ?></button>
        <?php echo t('or') ?> <a href="?action=config"><?php echo t('cancel') ?></a>
    </div>
</form>
