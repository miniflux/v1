<div class="page-header">
    <h2><?php echo t('OPML Import') ?></h2>
    <nav>
        <ul>
            <li><a href="?action=add"><?php echo t('add') ?></a></li>
            <li><a href="?action=feeds"><?php echo t('feeds') ?></a></li>
            <li class="active"><a href="?action=import"><?php echo t('import') ?></a></li>
            <li><a href="?action=export"><?php echo t('export') ?></a></li>
        </ul>
    </nav>
</div>

<form method="post" action="?action=import" enctype="multipart/form-data">
    <label for="file"><?php echo t('OPML file') ?></label>
    <input type="file" name="file" required/>
    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?php echo t('Import') ?></button>
        <?php echo t('or') ?> <a href="?action=feeds"><?php echo t('cancel') ?></a>
    </div>
</form>