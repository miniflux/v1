<div class="page-header">
    <h2><?= t('Preferences') ?></h2>
    <ul>
        <li><a href="?action=about"><?= t('about') ?></a></li>
        <li><a href="?action=help"><?= t('help') ?></a></li>
        <li><a href="?action=database"><?= t('database') ?></a></li>
        <li><a href="?action=api"><?= t('api') ?></a></li>
    </ul>
</div>
<section>
<form method="post" action="?action=config" autocomplete="off">

    <?= Helper\form_hidden('csrf', $values) ?>

    <?= Helper\form_label(t('Username'), 'username') ?>
    <?= Helper\form_text('username', $values, $errors, array('required')) ?><br/>

    <?= Helper\form_label(t('Password'), 'password') ?>
    <?= Helper\form_password('password', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Confirmation'), 'confirmation') ?>
    <?= Helper\form_password('confirmation', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Timezone'), 'timezone') ?>
    <?= Helper\form_select('timezone', $timezones, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Language'), 'language') ?>
    <?= Helper\form_select('language', $languages, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Remove automatically read items'), 'autoflush') ?>
    <?= Helper\form_select('autoflush', $autoflush_options, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Items per page'), 'items_per_page') ?>
    <?= Helper\form_select('items_per_page', $paging_options, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Default sorting order for items'), 'items_sorting_direction') ?>
    <?= Helper\form_select('items_sorting_direction', $sorting_options, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Display items on lists'), 'items_display_mode') ?>
    <?= Helper\form_select('items_display_mode', $display_mode, $values, $errors) ?><br/>

    <?= Helper\form_label(t('When there is nothing to read, redirect me to this page'), 'redirect_nothing_to_read') ?>
    <?= Helper\form_select('redirect_nothing_to_read', $redirect_nothing_to_read_options, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Theme'), 'theme') ?>
    <?= Helper\form_select('theme', $theme_options, $values, $errors) ?><br/>

    <?= Helper\form_checkbox('nocontent', t('Do not fetch the content of articles'), 1, isset($values['nocontent']) ? $values['nocontent'] : false) ?><br />

    <?php if (ENABLE_AUTO_UPDATE): ?>
        <?= Helper\form_label(t('Auto-Update URL'), 'auto_update_url') ?>
        <?= Helper\form_text('auto_update_url', $values, $errors, array('required')) ?><br/>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>

<?php if (ENABLE_AUTO_UPDATE): ?>
<div class="page-section">
    <h2><?= t('Advanced') ?></h2>
</div>
<section class="alert alert-error">
<ul>
    <li><a href="?action=confirm-auto-update"><?= t('Update Miniflux') ?></a> (<?= t('Don\'t forget to backup your database') ?>)</li>
</ul>
</section>
<?php endif ?>
