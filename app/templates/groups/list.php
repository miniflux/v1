<div class="page-header">
    <h2><?php echo t('Groups') ?></h2>
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

<?php if (empty($groups)): ?>
    <p class="alert alert-info"><?php echo t('There is no group.') ?></p>
<?php else: ?>
    <section class="items">
        <?php foreach ($groups as $group): ?>
            <article>
                <h2><?php echo Miniflux\Helper\escape($group['title']) ?></h2>
                <ul class="item-menu">
                    <li>
                        <?php echo Miniflux\Helper\link(t('edit'), 'edit-group', array('group_id' => $group['id'])) ?>
                    </li>
                    <li>
                        <?php echo Miniflux\Helper\link(t('remove'), 'confirm-remove-group', array('group_id' => $group['id'])) ?>
                    </li>
                </ul>
            </article>
        <?php endforeach ?>
    </section>
<?php endif ?>
