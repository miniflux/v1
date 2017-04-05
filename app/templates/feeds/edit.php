<div class="page-header">
    <h2><?php echo t('Edit subscription') ?></h2>
    <ul>
        <li><a href="?action=add"><?php echo t('add') ?></a></li>
        <li><a href="?action=feeds"><?php echo t('feeds') ?></a></li>
        <li><a href="?action=groups"><?php echo t('groups') ?></a></li>
        <li><a href="?action=import"><?php echo t('import') ?></a></li>
        <li><a href="?action=export"><?php echo t('export') ?></a></li>
    </ul>
</div>

<form method="post" action="?action=edit-feed" autocomplete="off">

    <?php echo Miniflux\Helper\form_hidden('id', $values) ?>

    <?php echo Miniflux\Helper\form_label(t('Title'), 'title') ?>
    <?php echo Miniflux\Helper\form_text('title', $values, $errors, array('required')) ?>

    <?php echo Miniflux\Helper\form_label(t('Website URL'), 'site_url') ?>
    <?php echo Miniflux\Helper\form_text('site_url', $values, $errors, array('required', 'placeholder="http://..."')) ?>

    <?php echo Miniflux\Helper\form_label(t('Feed URL'), 'feed_url') ?>
    <?php echo Miniflux\Helper\form_text('feed_url', $values, $errors, array('required', 'placeholder="http://..."')) ?>

    <?php echo Miniflux\Helper\form_checkbox('rtl', t('Force RTL mode (Right-to-left language)'), 1, $values['rtl']) ?><br>

    <?php echo Miniflux\Helper\form_checkbox('download_content', t('Download full content'), 1, $values['download_content']) ?><br>

    <?php echo Miniflux\Helper\form_checkbox('cloak_referrer', t('Cloak the image referrer'), 1, $values['cloak_referrer']) ?><br>

    <?php echo Miniflux\Helper\form_checkbox('ignore_expiration', t('Ignore expiration date'), 1, isset($values['ignore_expiration']) && $values['ignore_expiration'] == 1) ?><br>

    <?php echo Miniflux\Helper\form_checkbox('enabled', t('Activated'), 1, $values['enabled']) ?><br>

    <?php echo Miniflux\Helper\form_label(t('Groups'), 'group_name'); ?>

    <div id="grouplist">
        <?php foreach ($groups as $group): ?>
            <?php echo Miniflux\Helper\form_checkbox('feed_group_ids[]', $group['title'], $group['id'], in_array($group['id'], $values['feed_group_ids']), 'hide') ?>
        <?php endforeach ?>
        <?php echo Miniflux\Helper\form_text('group_name', $values, array(), array('placeholder="'.t('add a new group').'"')) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?php echo t('Save') ?></button>
        <?php echo t('or') ?> <a href="?action=feeds"><?php echo t('cancel') ?></a>
    </div>
</form>

<br>

<div class="panel panel-default">
    <ul>
        <li><?= t('Last checked: ') ?> <?= empty($values['last_checked']) ? t('None') : dt('%e %B %Y %k:%M', $values['last_checked']) ?></li>
        <li><?= t('Last modified: ') ?> <?= empty($values['last_modified']) ? t('None') : dt('%e %B %Y %k:%M', strtotime($values['last_modified'])) ?></li>
        <li><?= t('Expiration date: ') ?> <?= empty($values['expiration']) ? t('None') : dt('%e %B %Y %k:%M', $values['expiration']) ?></li>
        <li><?= t('ETag: ') ?> <?= empty($values['etag']) ? t('None') : \Miniflux\Helper\escape($values['etag']) ?></li>
        <li><?= t('Last parsing error: ') ?> <?= empty($values['parsing_error_message']) ? t('None') : \Miniflux\Helper\escape($values['parsing_error_message']) ?></li>
    </ul>
</div>

<div class="form-actions">
    <a href="?action=confirm-remove-feed&amp;feed_id=<?php echo $values['id'] ?>" class="btn btn-red"><?php echo t('Remove this feed') ?></a>
</div>
