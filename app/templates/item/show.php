<?php if (empty($item)): ?>
    <p class="alert alert-error"><?php echo t('Item not found') ?></p>
<?php else: ?>
    <article
        class="item"
        id="current-item"
        data-item-id="<?php echo $item['id'] ?>"
        data-item-status="<?php echo $item['status'] ?>"
        data-item-bookmark="<?php echo $item['bookmark'] ?>"
    >

        <?php if (isset($item_nav)): ?>
        <nav class="top">
            <span class="nav-left">
                <?php if ($item_nav['previous']): ?>
                    <a href="?action=show&amp;menu=<?php echo $menu ?><?php echo $group_id ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?php echo $item_nav['previous']['id'] ?>" id="previous-item" title="<?php echo Miniflux\Helper\escape($item_nav['previous']['title']) ?>"><?php echo t('Previous') ?></a>
                <?php else: ?>
                    <?php echo t('Previous') ?>
                <?php endif ?>
            </span>

            <span class="nav-right">
                <?php if ($item_nav['next']): ?>
                    <a href="?action=show&amp;menu=<?php echo $menu ?><?php echo $group_id ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?php echo $item_nav['next']['id'] ?>" id="next-item" title="<?php echo Miniflux\Helper\escape($item_nav['next']['title']) ?>"><?php echo t('Next') ?></a>
                <?php else: ?>
                    <?php echo t('Next') ?>
                <?php endif ?>
            </span>
        </nav>
        <?php endif ?>

        <h1 <?php echo Miniflux\Helper\rtl($item) ?>>
            <a href="<?php echo $item['url'] ?>" rel="noreferrer" target="_blank" class="original"><?php echo Miniflux\Helper\escape($item['title']) ?></a>
        </h1>

        <ul class="item-infos">
            <li>
                <a
                    class="bookmark-icon"
                    href="?action=bookmark&amp;value=<?php echo (int)!$item['bookmark'] ?>&amp;id=<?php echo $item['id'] ?>&amp;redirect=show&amp;menu=<?php echo $menu ?>"
                    title="<?php echo ($item['bookmark']) ? t('remove bookmark') : t('bookmark') ?>"
                    data-reverse-title="<?php echo ($item['bookmark']) ? t('bookmark') :t('remove bookmark') ?>"
                    data-action="bookmark"
                ></a>
            </li>
            <li>
                <a href="?action=feed-items&amp;feed_id=<?php echo $feed['id'] ?>"><?php echo Miniflux\Helper\escape($feed['title']) ?></a>
            </li>
            <?php if (!empty($item['author'])): ?>
                <li>
                    <?php echo Miniflux\Helper\escape($item['author']) ?>
                </li>
            <?php endif ?>
            <li class="hide-mobile">
                <span title="<?php echo dt('%e %B %Y %k:%M', $item['updated']) ?>"><?php echo Miniflux\Helper\relative_time($item['updated']) ?></span>
            </li>
            <?php if ($item['enclosure_url']): ?>
            <li>
                <a href="<?php echo $item['enclosure_url'] ?>" rel="noreferrer" target="_blank"><?php echo t('attachment') ?></a>
            </li>
            <?php endif ?>
            <li class="hide-mobile">
                <span id="download-item"
                      data-failure-message="<?php echo t('unable to fetch content') ?>"
                      data-before-message="<?php echo t('in progress...') ?>"
                      data-after-message="<?php echo t('content downloaded') ?>">
                    <a href="#" data-action="download-item"><?php echo t('download content') ?></a>
                </span>
            </li>
            <?php if ($group_id): ?>
            <li>
                <a href="?action=unread&amp;group_id=<?php echo $group_id ?>"><?php echo t('Back to the group') ?></a>
            </li>
            <?php endif; ?>
        </ul>

        <div id="item-content" <?php echo Miniflux\Helper\rtl($item) ?>>

            <?php if ($item['enclosure_url']): ?>
                <?php if (strpos($item['enclosure_type'], 'audio') !== false): ?>
                <div id="item-content-enclosure">
                    <audio controls>
                        <source src="<?php echo $item['enclosure_url'] ?>" type="<?php echo $item['enclosure_type'] ?>">
                    </audio>
                </div>
                <?php elseif (strpos($item['enclosure_type'], 'video') !== false): ?>
                <div id="item-content-enclosure">
                    <video controls>
                        <source src="<?php echo $item['enclosure_url'] ?>" type="<?php echo $item['enclosure_type'] ?>">
                    </video>
                </div>
                <?php elseif (strpos($item['enclosure_type'], 'image') !== false && $item['content'] === ''): ?>
                <div id="item-content-enclosure">
                    <img src="<?php echo $item['enclosure_url'] ?>" alt="enclosure"/>
                </div>
                <?php endif ?>
            <?php endif ?>

            <?php echo $item['content'] ?>
        </div>

        <?php if (isset($item_nav)): ?>
        <nav class="bottom">
            <span class="nav-left">
                <?php if ($item_nav['previous']): ?>
                    <a href="?action=show&amp;menu=<?php echo $menu ?><?php echo $group_id ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?php echo $item_nav['previous']['id'] ?>" id="previous-item" title="<?php echo Miniflux\Helper\escape($item_nav['previous']['title']) ?>"><?php echo t('Previous') ?></a>
                <?php else: ?>
                    <?php echo t('Previous') ?>
                <?php endif ?>
            </span>

            <span class="nav-right">
                <?php if ($item_nav['next']): ?>
                    <a href="?action=show&amp;menu=<?php echo $menu ?><?php echo $group_id ? '&amp;group_id='.$group_id : '' ?>&amp;id=<?php echo $item_nav['next']['id'] ?>" id="next-item" title="<?php echo Miniflux\Helper\escape($item_nav['next']['title']) ?>"><?php echo t('Next') ?></a>
                <?php else: ?>
                    <?php echo t('Next') ?>
                <?php endif ?>
            </span>
        </nav>
        <?php endif ?>
    </article>

<?php endif ?>
