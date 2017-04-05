<div class="page-header">
    <h2><?php echo t('Edit group') ?></h2>
    <nav>
        <ul>
            <li><a href="?action=add"><?php echo t('add') ?></a></li>
            <li><a href="?action=feeds"><?php echo t('feeds') ?></a></li>
            <li class="active"><a href="?action=groups"><?php echo t('groups') ?></a></li>
            <li><a href="?action=import"><?php echo t('import') ?></a></li>
            <li><a href="?action=export"><?php echo t('export') ?></a></li>
        </ul>
    </nav>
</div>

<form method="post" action="?action=edit-group" autocomplete="off">
    <?php echo Miniflux\Helper\form_hidden('id', $values) ?>

    <?php echo Miniflux\Helper\form_label(t('Title'), 'title') ?>
    <?php echo Miniflux\Helper\form_text('title', $values, $errors, array('required', 'autofocus')) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?php echo t('Save') ?></button>
        <?php echo t('or') ?> <?php echo Miniflux\Helper\link(t('cancel'), 'groups') ?>
    </div>
</form>
