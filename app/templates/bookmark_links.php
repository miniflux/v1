<li class="hide-mobile">
<?php if ($item['bookmark']): ?>
    <a
        class="bookmark"
        href="?action=bookmark&amp;value=0&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
        data-action="bookmark"
        data-reverse-label="<?php echo t('bookmark') ?>"
    ><?php echo t('remove bookmark') ?></a>
<?php else: ?>
    <a
        class="bookmark"
        href="?action=bookmark&amp;value=1&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
        data-action="bookmark"
        data-reverse-label="<?php echo t('remove bookmark') ?>"
    ><?php echo t('bookmark') ?></a>
<?php endif ?>
</li>