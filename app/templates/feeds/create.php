<div class="page-header">
    <h2><?php echo t('New subscription') ?></h2>
    <nav>
        <ul>
            <li class="active"><a href="?action=add"><?php echo t('add') ?></a></li>
            <li><a href="?action=feeds"><?php echo t('feeds') ?></a></li>
            <li><a href="?action=groups"><?php echo t('groups') ?></a></li>
            <li><a href="?action=import"><?php echo t('import') ?></a></li>
            <li><a href="?action=export"><?php echo t('export') ?></a></li>
        </ul>
    </nav>
</div>

<form method="post" action="?action=subscribe" autocomplete="off">
    <?php echo Miniflux\Helper\form_hidden('csrf', $values) ?>

    <?php echo Miniflux\Helper\form_label(t('Website or Feed URL'), 'url') ?>
    <?php echo Miniflux\Helper\form_text('url', $values, array(), array('required', 'autofocus', 'placeholder="'.t('http://website/').'"')) ?><br/><br/>

    <?php echo Miniflux\Helper\form_checkbox('rtl', t('Force RTL mode (Right-to-left language)'), 1, $values['rtl']) ?><br/>
    <?php echo Miniflux\Helper\form_checkbox('download_content', t('Download full content'), 1, $values['download_content']) ?><br/>
    <?php echo Miniflux\Helper\form_checkbox('cloak_referrer', t('Cloak the image referrer'), 1, $values['cloak_referrer']) ?><br />

    <p class="form-help"><?php echo t('Downloading full content is slower because Miniflux grab the content from the original website. You should use that for subscriptions that display only a summary. This feature doesn\'t work with all websites.') ?></p>

    <?php echo Miniflux\Helper\form_label(t('Groups'), 'group_name'); ?>

    <div id="grouplist">
        <?php foreach ($groups as $group): ?>
            <?php echo Miniflux\Helper\form_checkbox('feed_group_ids[]', $group['title'], $group['id'], in_array($group['id'], $values['feed_group_ids']), 'hide') ?>
        <?php endforeach ?>
        <?php echo Miniflux\Helper\form_text('group_name', $values, array(), array('placeholder="'.t('add a new group').'"')) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?php echo t('Add') ?></button>
        <?php echo t('or') ?> <a href="?action=feeds"><?php echo t('cancel') ?></a>
    </div>
</form>
