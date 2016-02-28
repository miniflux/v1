<?php if (empty($items) && is_null($group_id)): ?>
    <p class="alert alert-info"><?= t('No bookmark') ?></p>
<?php else: ?>

    <div class="page-header">
        <h2><?= t('Bookmarks') ?><span id="page-counter"><?= isset($nb_items) ? $nb_items : '' ?></span></h2>
        <?php if (!empty($groups)): ?>
        <nav>
            <ul id="grouplist">
                <?php foreach ($groups as $group): ?>
                <li  <?= $group['id'] == $group_id ? 'class="active"' : '' ?>>
                    <a href="?action=bookmarks&group_id=<?=$group['id']?>"><?=$group['title']?></a>
                </li>
                <?php endforeach ?>
            </ul>
        </nav>
        <?php endif ?>

    </div>

    <?php if ($nothing_to_read): ?>
        <p class="alert alert-info"><?= t('There is nothing new to read, enjoy your favorites articles!') ?></p>
    <?php endif ?>

    <section class="items" id="listing">
        <?php foreach ($items as $item): ?>
            <?= \Template\load('item', array(
                'item' => $item,
                'menu' => $menu,
                'offset' => $offset,
                'hide' => false,
                'display_mode' => $display_mode,
                'item_title_link' => $item_title_link,
                'favicons' => $favicons,
                'original_marks_read' => $original_marks_read,
            )) ?>
        <?php endforeach ?>

        <?= \Template\load('paging', array('menu' => $menu, 'nb_items' => $nb_items, 'items_per_page' => $items_per_page, 'offset' => $offset, 'order' => $order, 'direction' => $direction, 'group_id' => $group_id)) ?>
    </section>

<?php endif ?>
