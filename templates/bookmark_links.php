<?php if ($item['bookmark']): ?>
    <span
        class="bookmark-icon"
        href="?action=bookmark&amp;value=0&amp;id=<?= $item['id'] ?>&amp;menu=<?= $menu ?>&amp;offset=<?= $offset ?>&amp;source=<?= $source ?>"
        data-action="bookmark"
        data-reverse-title="<?= t('bookmark') ?>"
        title="<?= t('remove bookmark') ?>"
    ></span>
<?php else: ?>
    <span
        class="bookmark-icon"
        href="?action=bookmark&amp;value=1&amp;id=<?= $item['id'] ?>&amp;menu=<?= $menu ?>&amp;offset=<?= $offset ?>&amp;source=<?= $source ?>"
        data-action="bookmark"
        data-reverse-title="<?= t('remove bookmark') ?>"
        title="<?= t('bookmark') ?>"
    ></span>
<?php endif ?>
