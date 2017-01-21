<div class="page-header">
    <h2><?php echo t('Confirmation') ?></h2>
</div>

<p class="alert alert-info"><?php echo t('Do you really want to remove this subscription: "%s"?', Miniflux\Helper\escape($feed['title'])) ?></p>

<div class="form-actions">
    <a href="?action=remove-feed&amp;feed_id=<?php echo $feed['id'] ?>" class="btn btn-red"><?php echo t('Remove') ?></a>
    <?php echo t('or') ?> <a href="?action=feeds"><?php echo t('cancel') ?></a>
</div>
