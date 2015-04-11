<article
    id="item-<?= $item['id'] ?>"
    class="feed-<?= $item['feed_id'] ?>"
    data-item-id="<?= $item['id'] ?>"
    data-item-status="<?= $item['status'] ?>"
    data-item-bookmark="<?= $item['bookmark'] ?>"
    <?= $hide ? 'data-hide="true"' : '' ?>
    >
    <h2 <?= Helper\is_rtl($item) ? 'dir="rtl"' : 'dir="ltr"' ?>>
        <span class="bookmark-icon"></span>
        <span class="read-icon"></span>
        <?= Helper\favicon($favicons, $item['feed_id']) ?>
        <a
            href="?action=show&amp;menu=<?= $menu ?>&amp;id=<?= $item['id'] ?>"
            class="show"
        ><?= Helper\escape($item['title']) ?></a>
    </h2>
    <?php if ($display_mode === 'full'): ?>
        <div class="preview" <?= Helper\is_rtl($item) ? 'dir="rtl"' : 'dir="ltr"' ?>>
            <?= $item['content'] ?>
        </div>
    <?php else: ?>
        <p class="preview" <?= Helper\is_rtl($item) ? 'dir="rtl"' : 'dir="ltr"' ?>>
            <?= Helper\escape(Helper\summary(strip_tags($item['content']), 50, 300)) ?>
        </p>
    <?php endif ?>
    <ul class="item-menu">
        <li>
            <?php if (! isset($item['feed_title'])): ?>
                <?= Helper\get_host_from_url($item['url']) ?>
            <?php else: ?>
                <a href="?action=feed-items&amp;feed_id=<?= $item['feed_id'] ?>" title="<?= t('Show only this subscription') ?>"><?= Helper\escape($item['feed_title']) ?></a>
            <?php endif ?>
        </li>
        <li class="hide-mobile">
            <span title="<?= dt('%e %B %Y %k:%M', $item['updated']) ?>"><?= Helper\relative_time($item['updated']) ?></span>
        </li>
        <li class="hide-mobile">
            <a href="<?= $item['url'] ?>" class="original" rel="noreferrer" target="_blank" <?= ($original_marks_read) ? 'data-action="mark-read"' : '' ?>><?= t('original link') ?></a>
        </li>
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
        <?= \PicoFarad\Template\load('bookmark_links', array('item' => $item, 'menu' => $menu, 'offset' => $offset, 'source' => '')) ?>
        <?= \PicoFarad\Template\load('status_links', array('item' => $item, 'redirect' => $menu, 'offset' => $offset)) ?>
    </ul>
</article>
