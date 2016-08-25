<article
    id="item-<?= $item['id'] ?>"
    class="feed-<?= $item['feed_id'] ?>"
    data-item-id="<?= $item['id'] ?>"
    data-item-status="<?= $item['status'] ?>"
    data-item-bookmark="<?= $item['bookmark'] ?>"
    <?= $hide ? 'data-hide="true"' : '' ?>
    >
    <h2 <?= Miniflux\Helper\is_rtl($item) ? 'dir="rtl"' : 'dir="ltr"' ?>>
        <span class="item-icons">
            <a
                class="bookmark-icon"
                href="?action=bookmark&amp;value=<?= (int)!$item['bookmark'] ?>&amp;id=<?= $item['id'] ?>&amp;offset=<?= $offset ?>&amp;redirect=<?= $menu ?>&amp;feed_id=<?= $item['feed_id'] ?>"
                title="<?= ($item['bookmark']) ? t('remove bookmark') : t('bookmark') ?>"
                data-action="bookmark"
                data-reverse-title="<?= ($item['bookmark']) ? t('bookmark') : t('remove bookmark') ?>"
            ></a>
            <a
                class="read-icon"
                href="?action=<?= ($item['status'] === 'unread') ? 'mark-item-read' : 'mark-item-unread' ?>&amp;id=<?= $item['id'] ?>&amp;offset=<?= $offset ?>&amp;redirect=<?= $menu ?>&amp;feed_id=<?= $item['feed_id'] ?>"
                title="<?= ($item['status'] === 'unread') ? t('mark as read') : t('mark as unread') ?>"
                data-action="<?= ($item['status'] === 'unread') ? 'mark-read' : 'mark-unread' ?>"
                data-reverse-title="<?= ($item['status'] === 'unread') ? t('mark as unread') : t('mark as read') ?>"
            ></a>
        </span>
        <span class="item-title">
        <?= Miniflux\Helper\favicon($favicons, $item['feed_id']) ?>
        <?php if ($display_mode === 'full' || $item_title_link == 'original'): ?>
            <a class="original" rel="noreferrer" target="_blank"
               href="<?= $item['url'] ?>"
               <?= ($original_marks_read) ? 'data-action="mark-read"' : '' ?>
               title="<?= Miniflux\Helper\escape($item['title']) ?>"
            ><?= Miniflux\Helper\escape($item['title']) ?></a>
        <?php else: ?>
            <a
                href="?action=show&amp;menu=<?= $menu ?><?= isset($group_id) ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?= $item['id'] ?>"
                class="show"
                title="<?= Miniflux\Helper\escape($item['title']) ?>"
            ><?= Miniflux\Helper\escape($item['title']) ?></a>
        <?php endif ?>
        </span>
    </h2>
    <ul class="item-menu">
         <?php if ($menu !== 'feed-items'): ?>
        <li>
            <?php if (! isset($item['feed_title'])): ?>
                <?= Miniflux\Helper\get_host_from_url($item['url']) ?>
            <?php else: ?>
                <a href="?action=feed-items&amp;feed_id=<?= $item['feed_id'] ?>" title="<?= t('Show only this subscription') ?>"><?= Miniflux\Helper\escape($item['feed_title']) ?></a>
            <?php endif ?>
        </li>
        <?php endif ?>
        <?php if (!empty($item['author'])): ?>
            <li>
                <?= Miniflux\Helper\escape($item['author']) ?>
            </li>
        <?php endif ?>
        <li class="hide-mobile">
            <span title="<?= dt('%e %B %Y %k:%M', $item['updated']) ?>"><?= Miniflux\Helper\relative_time($item['updated']) ?></span>
        </li>
        <?php if ($display_mode === 'full' || $item_title_link == 'original'): ?>
            <li>
                <a
                    href="?action=show&amp;menu=<?= $menu ?><?= isset($group_id) ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?= $item['id'] ?>"
                    class="show"
                ><?= t('view') ?></a>
            </li>
        <?php else: ?>
            <li class="hide-mobile">
                <a href="<?= $item['url'] ?>" class="original" rel="noreferrer" target="_blank" <?= ($original_marks_read) ? 'data-action="mark-read"' : '' ?>><?= t('original link') ?></a>
            </li>
        <?php endif ?>
        <?php if ($item['enclosure']): ?>
            <li>
            <?php if (strpos($item['enclosure_type'], 'video/') === 0): ?>
                <a href="<?= $item['enclosure'] ?>" class="video-enclosure" rel="noreferrer" target="_blank"><?= t('attachment') ?></a>
            <?php elseif(strpos($item['enclosure_type'], 'audio/') === 0): ?>
                <a href="<?= $item['enclosure'] ?>" class="audio-enclosure" rel="noreferrer" target="_blank"><?= t('attachment') ?></a>
            <?php elseif(strpos($item['enclosure_type'], 'image/') === 0): ?>
                <a href="<?= $item['enclosure'] ?>" class="image-enclosure" rel="noreferrer" target="_blank"><?= t('attachment') ?></a>
            <?php else: ?>
                <a href="<?= $item['enclosure'] ?>" class="enclosure" rel="noreferrer" target="_blank"><?= t('attachment') ?></a>
            <?php endif ?>
            </li>
        <?php endif ?>
        <?= Miniflux\Template\load('bookmark_links', array('item' => $item, 'menu' => $menu, 'offset' => $offset)) ?>
        <?= Miniflux\Template\load('status_links', array('item' => $item, 'menu' => $menu, 'offset' => $offset)) ?>
    </ul>
    <?php if ($display_mode === 'full'): ?>
        <div class="preview-full-content" <?= Miniflux\Helper\is_rtl($item) ? 'dir="rtl"' : 'dir="ltr"' ?>><?= $item['content'] ?></div>
    <?php else: ?>
        <p class="preview" <?= Miniflux\Helper\is_rtl($item) ? 'dir="rtl"' : 'dir="ltr"' ?>><?= Miniflux\Helper\escape(Miniflux\Helper\summary(strip_tags($item['content']), 50, 300)) ?></p>
    <?php endif ?>
</article>
