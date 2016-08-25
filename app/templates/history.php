<?php if (empty($items)): ?>
    <p class="alert alert-info"><?= t('No history') ?></p>
<?php else: ?>
    <?= Miniflux\Template\load('search_form') ?>
    <div class="page-header">
        <h2><?= t('History') ?><span id="page-counter"><?= isset($nb_items) ? $nb_items : '' ?></span></h2>
        <?php if (!empty($groups)): ?>
        <nav>
            <ul id="grouplist">
                <?php foreach ($groups as $group): ?>
                <li  <?= $group['id'] == $group_id ? 'class="active"' : '' ?>>
                    <a href="?action=history&group_id=<?=$group['id']?>"><?=$group['title']?></a>
                </li>
                <?php endforeach ?>
            </ul>
        </nav>
        <?php endif ?>

        <ul>
            <li>
                <a href="?action=history<?= $group_id === null ? '' : '&amp;group_id='.$group_id ?>&amp;order=updated&amp;direction=<?= $direction == 'asc' ? 'desc' : 'asc' ?>"><?= tne('sort by date %s(%s)%s', '<span class="hide-mobile">', $direction == 'desc' ? t('older first') : t('most recent first'), '</span>') ?></a>
            </li>
            <li><a href="?action=confirm-flush-history<?= $group_id === null ? '' : '&amp;group_id='.$group_id ?>"><?= t('flush all items') ?></a></li>
        </ul>
    </div>

    <?php if ($nothing_to_read): ?>
        <p class="alert alert-info"><?= t('There is nothing new to read, enjoy your previous readings!') ?></p>
    <?php endif ?>

    <section class="items" id="listing">
        <?php foreach ($items as $item): ?>
            <?= Miniflux\Template\load('item', array(
                'item' => $item,
                'menu' => $menu,
                'offset' => $offset,
                'hide' => true,
                'display_mode' => $display_mode,
                'item_title_link' => $item_title_link,
                'favicons' => $favicons,
                'original_marks_read' => $original_marks_read,
            )) ?>
        <?php endforeach ?>

        <?= Miniflux\Template\load('paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'order' => $order, 'direction' => $direction, 'group_id' => $group_id)) ?>
    </section>

<?php endif ?>
