<article
    id="item-<?php echo $item['id'] ?>"
    class="feed-<?php echo $item['feed_id'] ?>"
    data-item-id="<?php echo $item['id'] ?>"
    data-item-status="<?php echo $item['status'] ?>"
    data-item-bookmark="<?php echo $item['bookmark'] ?>"
    <?php echo $hide ? 'data-hide="true"' : '' ?>
    >
    <h2 <?php echo Miniflux\Helper\rtl($item) ?>>
        <span class="item-icons">
            <a
                class="bookmark-icon"
                href="?action=bookmark&amp;value=<?php echo (int)!$item['bookmark'] ?>&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
                title="<?php echo ($item['bookmark']) ? t('remove bookmark') : t('bookmark') ?>"
                data-action="bookmark"
                data-reverse-title="<?php echo ($item['bookmark']) ? t('bookmark') : t('remove bookmark') ?>"
            ></a>
            <a
                class="read-icon"
                href="?action=<?php echo ($item['status'] === 'unread') ? 'mark-item-read' : 'mark-item-unread' ?>&amp;id=<?php echo $item['id'] ?>&amp;offset=<?php echo $offset ?>&amp;redirect=<?php echo $menu ?>&amp;feed_id=<?php echo $item['feed_id'] ?>"
                title="<?php echo ($item['status'] === 'unread') ? t('mark as read') : t('mark as unread') ?>"
                data-action="<?php echo ($item['status'] === 'unread') ? 'mark-read' : 'mark-unread' ?>"
                data-reverse-title="<?php echo ($item['status'] === 'unread') ? t('mark as unread') : t('mark as read') ?>"
            ></a>
        </span>
        <span class="item-title">
        <?php echo Miniflux\Helper\favicon($favicons, $item['feed_id']) ?>
        <?php if ($display_mode === 'full' || $item_title_link == 'original'): ?>
            <a class="original" rel="noreferrer" target="_blank"
               href="<?php echo $item['url'] ?>"
               <?php echo ($original_marks_read) ? 'data-action="mark-read"' : '' ?>
               title="<?php echo Miniflux\Helper\escape($item['title']) ?>"
            ><?php echo Miniflux\Helper\escape($item['title']) ?></a>
        <?php else: ?>
            <a
                href="?action=show&amp;menu=<?php echo $menu ?><?php echo isset($group_id) ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?php echo $item['id'] ?>"
                class="show"
                title="<?php echo Miniflux\Helper\escape($item['title']) ?>"
            ><?php echo Miniflux\Helper\escape($item['title']) ?></a>
        <?php endif ?>
        </span>
    </h2>
    <ul class="item-menu">
         <?php if ($menu !== 'feed-items'): ?>
        <li>
            <?php if (! isset($item['feed_title'])): ?>
                <?php echo Miniflux\Helper\get_host_from_url($item['url']) ?>
            <?php else: ?>
                <a href="?action=feed-items&amp;feed_id=<?php echo $item['feed_id'] ?>" title="<?php echo t('Show only this subscription') ?>"><?php echo Miniflux\Helper\escape($item['feed_title']) ?></a>
            <?php endif ?>
        </li>
        <?php endif ?>
        <?php if (!empty($item['author'])): ?>
            <li>
                <?php echo Miniflux\Helper\escape($item['author']) ?>
            </li>
        <?php endif ?>
        <li class="hide-mobile">
            <span title="<?php echo dt('%e %B %Y %k:%M', $item['updated']) ?>"><?php echo Miniflux\Helper\relative_time($item['updated']) ?></span>
        </li>
        <?php if ($display_mode === 'full' || $item_title_link == 'original'): ?>
            <li>
                <a
                    href="?action=show&amp;menu=<?php echo $menu ?><?php echo isset($group_id) ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?php echo $item['id'] ?>"
                    class="show"
                ><?php echo t('view') ?></a>
            </li>
        <?php else: ?>
            <li class="hide-mobile">
                <a href="<?php echo $item['url'] ?>" class="original" rel="noreferrer" target="_blank" <?php echo ($original_marks_read) ? 'data-action="mark-read"' : '' ?>><?php echo t('original link') ?></a>
            </li>
        <?php endif ?>
        <?php if ($item['enclosure_url']): ?>
            <li>
            <?php if (strpos($item['enclosure_type'], 'video/') === 0): ?>
                <a href="<?php echo $item['enclosure_url'] ?>" class="video-enclosure" rel="noreferrer" target="_blank"><?php echo t('attachment') ?></a>
            <?php elseif(strpos($item['enclosure_type'], 'audio/') === 0): ?>
                <a href="<?php echo $item['enclosure_url'] ?>" class="audio-enclosure" rel="noreferrer" target="_blank"><?php echo t('attachment') ?></a>
            <?php elseif(strpos($item['enclosure_type'], 'image/') === 0): ?>
                <a href="<?php echo $item['enclosure_url'] ?>" class="image-enclosure" rel="noreferrer" target="_blank"><?php echo t('attachment') ?></a>
            <?php else: ?>
                <a href="<?php echo $item['enclosure_url'] ?>" class="enclosure" rel="noreferrer" target="_blank"><?php echo t('attachment') ?></a>
            <?php endif ?>
            </li>
        <?php endif ?>
        <?php echo Miniflux\Template\load('items/bookmark_links', array('item' => $item, 'menu' => $menu, 'offset' => $offset)) ?>
        <?php echo Miniflux\Template\load('items/status_links', array('item' => $item, 'menu' => $menu, 'offset' => $offset)) ?>
    </ul>
    <?php if ($display_mode === 'full'): ?>
        <div class="preview-full-content" <?php echo Miniflux\Helper\rtl($item) ?>><?php echo $item['content'] ?></div>
    <?php elseif ($display_mode === 'summaries'): ?>
        <p class="preview" <?php echo Miniflux\Helper\rtl($item) ?>><?php echo Miniflux\Helper\escape(Miniflux\Helper\summary(strip_tags($item['content']), 50, 300)) ?></p>
    <?php else: ?>
        <p class="no-preview"></p>
    <?php endif ?>
</article>
