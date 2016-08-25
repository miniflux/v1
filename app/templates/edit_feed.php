<div class="page-header">
    <h2><?= t('Edit subscription') ?></h2>
    <ul>
        <li><a href="?action=add"><?= t('add') ?></a></li>
        <li><a href="?action=feeds"><?= t('feeds') ?></a></li>
        <li><a href="?action=import"><?= t('import') ?></a></li>
        <li><a href="?action=export"><?= t('export') ?></a></li>
    </ul>
</div>

<form method="post" action="?action=edit-feed" autocomplete="off">

    <?= Miniflux\Helper\form_hidden('id', $values) ?>

    <?= Miniflux\Helper\form_label(t('Title'), 'title') ?>
    <?= Miniflux\Helper\form_text('title', $values, $errors, array('required')) ?>

    <?= Miniflux\Helper\form_label(t('Website URL'), 'site_url') ?>
    <?= Miniflux\Helper\form_text('site_url', $values, $errors, array('required', 'placeholder="http://..."')) ?>

    <?= Miniflux\Helper\form_label(t('Feed URL'), 'feed_url') ?>
    <?= Miniflux\Helper\form_text('feed_url', $values, $errors, array('required', 'placeholder="http://..."')) ?>

    <?= Miniflux\Helper\form_checkbox('rtl', t('Force RTL mode (Right-to-left language)'), 1, $values['rtl']) ?><br />

    <?= Miniflux\Helper\form_checkbox('download_content', t('Download full content'), 1, $values['download_content']) ?><br />

    <?= Miniflux\Helper\form_checkbox('cloak_referrer', t('Cloak the image referrer'), 1, $values['cloak_referrer']) ?><br />

    <?= Miniflux\Helper\form_checkbox('enabled', t('Activated'), 1, $values['enabled']) ?><br />

    <?= Miniflux\Helper\form_label(t('Groups'), 'groups'); ?>

    <div id="grouplist">
        <?php foreach ($groups as $group): ?>
            <?= Miniflux\Helper\form_checkbox('feed_group_ids[]', $group['title'], $group['id'], in_array($group['id'], $values['feed_group_ids']), 'hide') ?>
        <?php endforeach ?>
        <?= Miniflux\Helper\form_text('create_group', $values, array(), array('placeholder="'.t('add a new group').'"')) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?> <a href="?action=feeds"><?= t('cancel') ?></a>
    </div>
</form>
<div class="form-actions">
    <a href="?action=confirm-remove-feed&amp;feed_id=<?= $values['id'] ?>" class="btn btn-red"><?= t('Remove this feed') ?></a>
</div>
