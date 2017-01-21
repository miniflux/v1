<?php if (empty($items)): ?>
    <p class="alert alert-info">
        <?php echo tne('This subscription is empty, %sgo back to unread items%s','<a href="?action=unread">','</a>') ?>
    </p>
<?php else: ?>

    <div class="page-header">
        <h2><?php echo Miniflux\Helper\escape($feed['title']) ?>&lrm;<span id="page-counter"><?php echo isset($nb_items) ? $nb_items : '' ?></span></h2>
        <ul>
            <li>
                <a href="?action=refresh-feed&amp;feed_id=<?php echo $feed['id'] ?>&amp;redirect=feed-items"><?php echo t('refresh') ?></a>
            </li>
            <li>
                <a href="?action=edit-feed&amp;feed_id=<?php echo $feed['id'] ?>"><?php echo t('edit') ?></a>
            </li>
            <li>
                <a href="?action=feed-items&amp;feed_id=<?php echo $feed['id'] ?>&amp;order=updated&amp;direction=<?php echo $direction == 'asc' ? 'desc' : 'asc' ?>"><?php echo tne('sort by date %s(%s)%s', '<span class="hide-mobile">', $direction == 'desc' ? t('older first') : t('most recent first'), '</span>') ?></a>
            </li>
            <li>
                <a href="?action=mark-feed-as-read&amp;feed_id=<?php echo $feed['id'] ?>" data-action="mark-feed-read"><?php echo t('mark all as read') ?></a>
            </li>
        </ul>
    </div>

    <?php if ($feed['parsing_error'] > 0): ?>
        <p class="alert alert-error">
            <?php echo t('An error occurred during the last check: "%s".', $feed['parsing_error_message']) ?>
        </p>
    <?php endif; ?>

    <section class="items" id="listing" data-feed-id="<?php echo $feed['id'] ?>">
        <?php foreach ($items as $item): ?>
            <?php echo Miniflux\Template\load('items/item', array(
                'feed' => $feed,
                'item' => $item,
                'menu' => $menu,
                'offset' => $offset,
                'hide' => false,
                'display_mode' => $display_mode,
                'favicons' => $favicons,
                'original_marks_read' => $original_marks_read,
                'item_title_link' => $item_title_link,
            )) ?>
        <?php endforeach ?>

        <div id="bottom-menu">
            <a href="?action=mark-feed-as-read&amp;feed_id=<?php echo $feed['id'] ?>" data-action="mark-feed-read"><?php echo t('mark all as read') ?></a>
        </div>

        <?php echo Miniflux\Template\load('items/paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'order' => $order, 'direction' => $direction, 'feed_id' => $feed['id'])) ?>
    </section>

<?php endif ?>
