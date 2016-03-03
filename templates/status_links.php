<?php if ($item['status'] == 'unread'): ?>
    <span
        class="read-icon"
        href="?action=mark-item-read&amp;id=<?= $item['id'] ?>&amp;offset=<?= $offset ?>&amp;redirect=<?= $redirect ?>&amp;feed_id=<?= $item['feed_id'] ?>"
        data-action="mark-read"
        data-reverse-title="<?= t('mark as unread') ?>"
        title="<?= t('mark as read') ?>"
    ></span>
<?php else: ?>
    <span
        class="read-icon"
        href="?action=mark-item-unread&amp;id=<?= $item['id'] ?>&amp;offset=<?= $offset ?>&amp;redirect=<?= $redirect ?>&amp;feed_id=<?= $item['feed_id'] ?>"
        data-action="mark-unread"
        data-reverse-title="<?= t('mark as read') ?>"
        title="<?= t('mark as unread') ?>"
    ></span>
<?php endif ?>
