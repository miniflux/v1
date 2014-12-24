<div class="page-header">
    <h2><?= t('External services') ?></h2>
    <ul>
        <li><a href="?action=config"><?= t('settings') ?></a></li>
        <li><a href="?action=about"><?= t('about') ?></a></li>
        <li><a href="?action=help"><?= t('help') ?></a></li>
        <li><a href="?action=database"><?= t('database') ?></a></li>
        <li><a href="?action=api"><?= t('api') ?></a></li>
    </ul>
</div>
<section>

<form method="post" action="?action=services" autocomplete="off">

    <h3><?= t('Pinboard') ?></h3>
    <?= Helper\form_hidden('csrf', $values) ?>

    <?= Helper\form_checkbox('pinboard_enabled', t('Send bookmarks to Pinboard'), 1, isset($values['pinboard_enabled']) && $values['pinboard_enabled'] == 1) ?><br />

    <?= Helper\form_label(t('Pinboard API token'), 'pinboard_token') ?>
    <?= Helper\form_text('pinboard_token', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Pinboard tags'), 'pinboard_tags') ?>
    <?= Helper\form_text('pinboard_tags', $values, $errors) ?>


    <h3><?= t('Instapaper') ?></h3>
    <?= Helper\form_checkbox('instapaper_enabled', t('Send bookmarks to Instapaper'), 1, isset($values['instapaper_enabled']) && $values['instapaper_enabled'] == 1) ?><br />

    <?= Helper\form_label(t('Instapaper username'), 'instapaper_username') ?>
    <?= Helper\form_text('instapaper_username', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Instapaper password'), 'instapaper_password') ?>
    <?= Helper\form_password('instapaper_password', $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>