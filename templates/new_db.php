<div class="page-header">
    <h2><?= t('New database') ?></h2>
    <ul>
        <li><a href="?action=config"><?= t('preferences') ?></a></li>
        <li><a href="?action=about"><?= t('about') ?></a></li>
        <li><a href="?action=help"><?= t('help') ?></a></li>
        <li><a href="?action=api"><?= t('api') ?></a></li>
    </ul>
</div>

<form method="post" action="?action=new-db" autocomplete="off">

    <?= Helper\form_hidden('csrf', $values) ?>

    <?= Helper\form_label(t('Database name'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('required', 'autofocus')) ?>
    <p class="form-help"><?= t('The name must have only alpha-numeric characters') ?></p>

    <?= Helper\form_label(t('Username'), 'username') ?>
    <?= Helper\form_text('username', $values, $errors, array('required')) ?><br/>

    <?= Helper\form_label(t('Password'), 'password') ?>
    <?= Helper\form_password('password', $values, $errors, array('required')) ?>

    <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
    <?= Helper\form_password('confirmation', $values, $errors, array('required')) ?><br/>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Create') ?></button>
        <?= t('or') ?> <a href="?action=config"><?= t('cancel') ?></a>
    </div>
</form>
