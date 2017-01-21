<div class="page-header">
    <h2><?php echo t('Confirmation') ?></h2>
</div>

<p class="alert alert-info"><?php echo t('Do you really want to remove these items from your history?') ?></p>

<div class="form-actions">
    <a href="?action=flush-history<?php echo $group_id === null ? '' : '&amp;group_id='.$group_id ?>" class="btn btn-red"><?php echo t('Remove') ?></a>
    <?php echo t('or') ?> <a href="?action=history<?php echo $group_id === null ? '' : '&amp;group_id='.$group_id ?>"><?php echo t('cancel') ?></a>
</div>