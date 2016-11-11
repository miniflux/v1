<div class="page-header">
    <h2><?php echo t('Confirmation') ?></h2>
</div>

<p class="alert alert-info"><?php echo t('This action will update Miniflux with the last development version, are you sure?') ?></p>

<div class="form-actions">
    <a href="?action=auto-update" class="btn btn-red"><?php echo t('Update Miniflux') ?></a>
    <?php echo t('or') ?> <a href="?action=config"><?php echo t('cancel') ?></a>
</div>