<div class="page-header">
    <h2><?php echo t('Confirmation') ?></h2>
</div>

<p class="alert alert-info"><?php echo t('Do you really want to remove this user: "%s"?', Miniflux\Helper\escape($user['username'])) ?></p>

<div class="form-actions">
    <a href="?action=remove-user&amp;user_id=<?php echo $user['id'] ?>&amp;csrf=<?php echo $csrf_token ?>" class="btn btn-red"><?php echo t('Remove') ?></a>
    <?php echo t('or') ?> <a href="?action=users"><?php echo t('cancel') ?></a>
</div>
