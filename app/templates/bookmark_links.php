<li class="hide-mobile">
<?php if ($item['bookmark']): ?>
    <a
        class="bookmark"
        href="?action=bookmark&amp;value=0&amp;id=<?= $item['id'] ?>&amp;offset=<?= $offset ?>&amp;redirect=<?= $menu ?>&amp;feed_id=<?= $item['feed_id'] ?>"
        data-action="bookmark"
        data-reverse-label="<?= t('bookmark') ?>"
    ><?= t('remove bookmark') ?></a>
<?php else: ?>
    <a
        class="bookmark"
        href="?action=bookmark&amp;value=1&amp;id=<?= $item['id'] ?>&amp;offset=<?= $offset ?>&amp;redirect=<?= $menu ?>&amp;feed_id=<?= $item['feed_id'] ?>"
        data-action="bookmark"
        data-reverse-label="<?= t('remove bookmark') ?>"
    ><?= t('bookmark') ?></a>
<?php endif ?>
</li>