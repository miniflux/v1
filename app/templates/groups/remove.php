<div class="page-header">
    <h2><?php echo t('Confirmation') ?></h2>
</div>

<p class="alert alert-info"><?php echo t('Do you really want to remove this group: "%s"?', Miniflux\Helper\escape($group['title'])) ?></p>

<div class="form-actions">
    <?php echo Miniflux\Helper\button('red', t('Remove'), 'remove-group', array('group_id' => $group['id'])) ?>
    <?php echo t('or') ?> <?php echo Miniflux\Helper\link(t('cancel'), 'groups') ?>
</div>
