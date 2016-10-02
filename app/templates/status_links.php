<li class="hide-mobile">
    <a
        href="?action=mark-item-removed&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
        data-action="mark-removed"
        class="delete"
    ><?php echo t('remove') ?></a>
</li>
<li class="hide-mobile">
    <?php if ($item['status'] == 'unread'): ?>
        <a
            class="mark"
            href="?action=mark-item-read&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
            data-action="mark-read"
            data-reverse-label="<?php echo t('mark as unread') ?>"
        ><?php echo t('mark as read') ?></a>
    <?php else: ?>
        <a
            class="mark"
            href="?action=mark-item-unread&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
            data-action="mark-unread"
            data-reverse-label="<?php echo t('mark as read') ?>"
        ><?php echo t('mark as unread') ?></a>
    <?php endif ?>
</li>